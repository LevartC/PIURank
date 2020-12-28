<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    public function getReservationInfo($year, $month, $day, $machines = null) {
        $t_start = strtotime("{$year}-{$month}-{$day} -12 hours");
        $t_end = strtotime("{$year}-{$month}-{$day} 11:59:59 +1 days");
        $tc_start = date("Y-m-d H:i:s", $t_start);
        $tc_end = date("Y-m-d H:i:s", $t_end);

        $sql = "SELECT tc_type, tc_starttime, tc_endtime FROM dv_ticket WHERE tc_starttime >= ? AND tc_endtime <= ?";
        if ($machines) {
            $mc_str = implode("','", $machines);
            $sql .= " AND tc_type IN ('{$mc_str}')";
        }
        $bind_array = array($tc_start, $tc_end);
        $res = $this->db->query($sql, $bind_array);

        $data = null;
        if ($res) {
            foreach($res->result_array() as $row) {
                $start_time = strtotime($row['tc_starttime']);
                $end_time = strtotime($row['tc_endtime']);
                for ($t = $start_time; $t < $end_time; $t = strtotime("+1 hours", $t)) {
                    $idx = date("YmdG", $t);
                    $data[$row['tc_type']][$idx] = "disabled";
                }
            }
        }
        $time_limit = time();
        if ($t_start <= $time_limit) {
            foreach ($machines as $m_val) {
                for ($t = $t_start; $t < $t_end && $t < $time_limit; $t = strtotime("+1 hours", $t)) {
                    $idx = date("YmdG", $t);
                    $data[$m_val][$idx] = "disabled";
                }
            }
        }
        return $data;
    }

    public function getPrice($machines, $date, $start_idx, $end_idx) {
        $price_info = null;
        $price_info['total'] = 0;
        for ($i = $start_idx; $i < $end_idx; ++$i) {
            $current_datetime = date("Y-m-d H:i:s", strtotime("{$date} {$i} hours"));
            $sql = "SELECT * FROM dv_price_info WHERE pri_datetime <= ? ORDER BY pri_datetime DESC LIMIT 1";
            $bind_array = array($current_datetime);
            $res = $this->db->query($sql, $bind_array);
            if ($row = $res->row_array()) {
                foreach($machines as $m_val) {
                    if (!isset($price_info[$m_val])) {
                        $price_info[$m_val] = 0;
                    }
                    $m_idx = "price_" . strtolower($m_val);
                    $price_info[$m_val] += $row[$m_idx];
                    $price_info['total'] += $row[$m_idx];
                }
            }
        }
        return $price_info;
    }

    public function insertTicket($machines, $date, $start_idx, $end_idx, $tc_name, $tc_tel, $tc_person, $tc_price) {
        $t_start = strtotime("{$date} {$start_idx} hours");
        $t_end = strtotime("{$date} {$end_idx} hours");
        $tc_start = date("Y-m-d H:i:s", $t_start);
        $tc_end = date("Y-m-d H:i:s", $t_end);

        foreach($machines as $m_val) {
            if ($this->checkTicket($m_val, $tc_start, $tc_end)) {
                $sql = "INSERT INTO dv_ticket(tc_type, tc_name, tc_tel, tc_starttime, tc_endtime, tc_person, tc_price) VALUES(?,?,?,?,?,?,?)";
                $bind_array = array($m_val, $tc_name, $tc_tel, $tc_start, $tc_end, $tc_person, $tc_price[$m_val]);
                $res = $this->db->query($sql, $bind_array);
                if (!$res) {
                    return false;
                }
            } else {
                return false;
            }
        }
        foreach($machines as $m_val) {
            if ($this->checkTicket($m_val, $tc_start, $tc_end)) {
                $sql = "INSERT INTO dv_ticket(tc_type, tc_name, tc_tel, tc_starttime, tc_endtime, tc_person, tc_price) VALUES(?,?,?,?,?,?,?)";
                $bind_array = array($m_val, $tc_name, $tc_tel, $tc_start, $tc_end, $tc_person, $tc_price[$m_val]);
                $res = $this->db->query($sql, $bind_array);
                if (!$res) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return $res;
    }

    private function checkTicket($m_val, $tc_start, $tc_end) {
        $sql = "SELECT tc_seq FROM dv_ticket WHERE tc_type = ? AND ((tc_starttime <= ? AND tc_endtime > ?) OR (tc_starttime < ? AND tc_endtime >= ?))";
        $bind_array = array($m_val, $tc_start, $tc_start, $tc_end, $tc_end);
        $res = $this->db->query($sql, $bind_array);
        if ($res->num_rows()) {
            return false;
        } else {
            return true;
        }
    }

    public function searchTicket($tc_name, $tc_tel) {
        $sql = "SELECT tc_type, tc_starttime, tc_endtime, tc_price FROM dv_ticket WHERE tc_starttime > now() AND tc_name = ? AND tc_tel = ?";
        $bind_array = array($tc_name, $tc_tel);
        $res = $this->db->query($sql, $bind_array);
        $res_data = null;
        if ($res->num_rows()) {
            foreach($res->result_array() as $row) {
                $row['mc_name'] = $this->getMachineName($row['tc_type']);
                $res_data[] = $row;
            }
        }
        return $res_data;
    }

    public function getTicketInfo() {
        $sql = "SELECT * FROM dv_ticket WHERE tc_endtime > now() AND tc_tel != '0' ORDER BY tc_starttime, tc_type";
        $res = $this->db->query($sql);
        $res_data = null;
        if ($res->num_rows()) {
            foreach($res->result_array() as $row) {
                $row['mc_name'] = $this->getMachineName($row['tc_type']);
                $res_data[] = $row;
            }
        }
        return $res_data;
    }

    public function getMachineName($tc_type) {
        $machine_name = array(
			"W" => "LX-W",
			"G" => "LX-G",
			"F" => "FX-정인",
        );
        if (isset($machine_name[$tc_type])) {
            return $machine_name[$tc_type];
        } else {
            return null;
        }
    }

    public function deleteTicket($tc_seq) {
        $sql = "DELETE FROM dv_ticket WHERE tc_seq = ?";
        $bind_array = array($tc_seq);
        $res = $this->db->query($sql, $bind_array);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    
    public function sendEmail($machines, $date, $start_idx, $end_idx, $tc_name, $tc_tel, $tc_person, $price_data) {
        $this->load->library('PHPMailer');
        $mail = new PHPMailer(true);

        try {
            $t_start = strtotime("{$date} {$start_idx} hours");
            $t_end = strtotime("{$date} {$end_idx} hours");
            $start_date = date('Y-m-d H시', $t_start);
            $end_date = date('Y-m-d H시', $t_end);
            // 기본 설정
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = "smtp.piurank.com";
            $mail->SMTPAuth = true;
            $mail->Username = $this->load->config('mailer_id');
            $mail->Password = $this->load->config('mailer_pw');
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->CharSet = "utf-8";

            // 관리자에게 전송
            $mail->setFrom("ticket@piurank.com", "DIVISION STUDIO 관리자");
            $mail->addAddress("eodmalt@gmail.com", "WINDFORCE");
            $mail->addAddress("", "GIMGIMGI");
            $mail->isHTML(false); // HTML 태그 사용 여부
            $mail->Subject = "{$start_date} ~ {$end_date} ({$tc_name} / {$tc_tel})예약 접수됨";  // 메일 제목
            $mail->Body = "
            예약시각 : {$start_date} ~ {$end_date}
            이름(입금자명) : {$tc_name}
            연락처 : {$tc_tel}
            이메일 : {$tc_email}
            인원 : {$tc_person}
            가격 :
            ";
            $mc_name = array();
            foreach ($tc_price as $price_key => $price_row) {
                $mc_name[] = $this->getMachineName($price_key);
            }
            $mail->Body .= implode(" / ", $mc_name);

            // Gmail로 메일을 발송하기 위해서는 CA인증이 필요하다.
            // CA 인증을 받지 못한 경우에는 아래 설정하여 인증체크를 해지하여야 한다.
            /*
            $mail -> SMTPOptions = array(
                "ssl" => array(
                "verify_peer" => false
                , "verify_peer_name" => false
                , "allow_self_signed" => true
                )
            );
            */
            // 메일 전송
            $mail -> send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error : ", $mail -> ErrorInfo;
        }
    }
}
?>