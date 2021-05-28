<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    public function getReservationInfo($year, $month, $day, $machines = null) {
        $t_start = strtotime("{$year}-{$month}-{$day} -12 hours");
        $t_end = strtotime("{$year}-{$month}-{$day} +2 days");
        $tc_start = date("Y-m-d H:i:s", $t_start);
        $tc_end = date("Y-m-d H:i:s", $t_end);

        $sql = "SELECT tc_type, tc_starttime, tc_endtime FROM dv_ticket WHERE tc_starttime >= ? AND tc_endtime <= ?";
        if ($machines) {
            $mc_str = implode("','", $machines);
            $sql .= " AND tc_type IN ('{$mc_str}')";
        }
        if ($this->check_studio()) {
            $sql .= " AND tc_tel != '0'";
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
        // 스튜디오 관리자는 과거 내역 예약 가능
        if (!$this->check_studio()) {
            $time_limit = time();
            if ($t_start <= $time_limit) {
                foreach ($machines as $m_val) {
                    for ($t = $t_start; $t < $t_end && $t < $time_limit; $t = strtotime("+1 hours", $t)) {
                        $idx = date("YmdG", $t);
                        $data[$m_val][$idx] = "disabled";
                    }
                }
            }
        }
        return $data;
    }

    public function getPrice($machines, $date, $start_idx, $end_idx) {
        $price_info = null;
        $price_info['total'] = 0;
        // 11시부터 7시까지 5시간 이상 대여시 특별 요금 적용
        if ($end_idx - 23 >= 5) {

        }
        // 기본 요금 계산
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

    public function insertTicket($machines, $u_id, $date, $start_idx, $end_idx, $tc_name, $tc_tel, $tc_email, $tc_person, $tc_price, $tc_version = 'XX') {
        $t_start = strtotime("{$date} {$start_idx} hours");
        $t_end = strtotime("{$date} {$end_idx} hours");
        $tc_start = date("Y-m-d H:i:s", $t_start);
        $tc_end = date("Y-m-d H:i:s", $t_end);

        $this->db->trans_start();
        $first_seq = null;
        foreach($machines as $m_val) {
            if ($this->check_studio() || $this->checkTicket($m_val, $tc_start, $tc_end)) {
                $sql = "INSERT INTO dv_ticket(tc_u_id, tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price, tc_version, tc_m_seq) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                $bind_array = array($u_id, $m_val, $tc_name, $tc_tel, $tc_email, $tc_start, $tc_end, $tc_person, $tc_price[$m_val], $tc_version, $first_seq);
                $res = $this->db->query($sql, $bind_array);
                $res_seq = $this->db->insert_id();
                if ($res && $first_seq === null) {
                    $first_seq = $res_seq;
                    $sql = "UPDATE dv_ticket SET tc_m_seq = {$first_seq} WHERE tc_seq = {$first_seq}";
                    $res = $this->db->query($sql);
                }
                $sql2 = "INSERT INTO dv_ticket_ready(tc_u_id, tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price, tc_version, tc_m_seq) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                $res2 = $this->db->query($sql2, $bind_array);
                if (!($res && $res2)) {
                    $this->db->trans_off();
                    return false;
                }
            } else {
                $this->db->trans_off();
                return false;
            }
        }
        $this->db->trans_complete();
        return $first_seq;
    }

    public function insertWall($tc_start, $tc_end) {
        $bind_array = array($tc_start, $tc_end);
        $sql_w = "INSERT INTO dv_ticket(tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price) VALUES('W', '막아둠', '0', '0', ?, ?, 1, 0)";
        $res_w = $this->db->query($sql_w, $bind_array);
        $sql_g = "INSERT INTO dv_ticket(tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price) VALUES('N', '막아둠', '0', '0', ?, ?, 1, 0)";
        $res_g = $this->db->query($sql_g, $bind_array);
        $sql_f = "INSERT INTO dv_ticket(tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price) VALUES('F', '막아둠', '0', '0', ?, ?, 1, 0)";
        $res_f = $this->db->query($sql_f, $bind_array);
        if ($res_w && $res_g && $res_f) {
            $sql2_w = "INSERT INTO dv_ticket_ready(tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price) VALUES('W', '막아둠', '0', '0', ?, ?, 1, 0)";
            $res2_w = $this->db->query($sql2_w, $bind_array);
            $sql2_g = "INSERT INTO dv_ticket_ready(tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price) VALUES('N', '막아둠', '0', '0', ?, ?, 1, 0)";
            $res2_g = $this->db->query($sql2_g, $bind_array);
            $sql2_f = "INSERT INTO dv_ticket_ready(tc_type, tc_name, tc_tel, tc_email, tc_starttime, tc_endtime, tc_person, tc_price) VALUES('F', '막아둠', '0', '0', ?, ?, 1, 0)";
            $res2_f = $this->db->query($sql2_f, $bind_array);
            if ($res2_w && $res2_g && $res2_f) {
                return true;
            }
        }
        return false;
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
        $sql = "SELECT tc_type, tc_starttime, tc_endtime, tc_price, tc_deposit, tc_version FROM dv_ticket WHERE tc_starttime > now() AND tc_name = ? AND tc_tel = ? ORDER BY tc_starttime";
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

    public function getTicketList($all = false) {
        $sql = "SELECT * FROM dv_ticket WHERE tc_endtime > now() AND tc_tel != '0' ORDER BY tc_starttime, tc_type";
        if ($all) {
            $sql = "SELECT * FROM dv_ticket WHERE tc_tel != '0' ORDER BY tc_starttime, tc_type";
        }
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

    public function getTicketInfo($tc_seq) {
        $sql = "SELECT * FROM dv_ticket WHERE tc_seq = ?";
        $res = $this->db->query($sql, array($tc_seq));
        return $res->row_array();
    }

    public function getFamilyTicketInfo($tc_seq) {
        $sql = "SELECT * FROM dv_ticket WHERE tc_m_seq = ?";
        $res = $this->db->query($sql, array($tc_seq));
        $res_data = null;
        foreach($res->result_array() as $row) {
            $res_data[] = $row;
        }
        return $res_data;
    }
    public function getSaleData($page = 0, $page_rows = 10) {
        if (!($page || $page_rows)) {
            // 페이지 입력하지 않을시 카운트 등록 ($val[0]['cnt'])
            $sql = "SELECT count(*) as cnt FROM dv_sales";
        } else {
            $sql = "SELECT * FROM dv_sales LEFT JOIN dv_products ON dp_seq = ds_dp_seq ORDER BY ds_datetime DESC";
        }
        if ($page && $page_rows) {
            $lim_start = ($page - 1) * $page_rows;
            $lim_end = $page_rows;
            $sql .= " LIMIT {$lim_start}, {$lim_end}";
        }
        $res = $this->db->query($sql);
        $res_data = null;
        if ($res->num_rows()) {
            if (!($page || $page_rows)) {
                if ($row = $res->row_array()) {
                    $res_data = $row['cnt'];
                }
            } else {
                foreach($res->result_array() as $row) {
                    $res_data[] = $row;
                }
            }
        }
        return $res_data;
    }

    public function getProductData() {
        $sql = "SELECT dp_seq, dp_name, dp_price, dp_count FROM dv_products";
        $res = $this->db->query($sql);
        $res_data = null;
        if ($res) {
            foreach($res->result_array() as $row) {
                $res_data[] = $row;
            }
        }
        return $res_data;
    }

    public function insertSales($ds_name, $ds_price, $dp_seq = null, $ds_memo = null, $ds_etc = null) {
        $this->db->trans_start();

        $sql = "INSERT INTO dv_sales(ds_name, ds_price, ds_dp_seq, ds_memo, ds_etc) VALUES (?,?,?,?,?)";
        $bind_array = array($ds_name, $ds_price, $dp_seq, $ds_memo, $ds_etc);
        $res1 = $this->db->query($sql, $bind_array);

        if ($dp_seq) {
            $sql = "UPDATE dv_products SET dp_count = dp_count - 1 where dp_seq = ?";
            $bind_array = array($dp_seq);
            $res2 = $this->db->query($sql, $bind_array);
        } else {
            $res2 = true;
        }
        if ($res1 && $res2) {
            $this->db->trans_complete();
            return true;
        } else {
            $this->db->trans_off();
            return false;
        }
    }

    public function getMachineName($tc_type) {
        $machine_name = array(
			"W" => "LX-W",
			"G" => "LX-G",
			"N" => "LX-N",
            "F" => "FX-정인",
			"total" => "총합",
        );
        if (isset($machine_name[$tc_type])) {
            return $machine_name[$tc_type];
        } else {
            return null;
        }
    }

    public function deleteTicket($tc_seq) {
        $this->db->trans_start();
        $bind_array = array($tc_seq);
        $sql1 = "DELETE FROM dv_ticket WHERE tc_seq = ?";
        $res1 = $this->db->query($sql1, $bind_array);
        $sql2 = "UPDATE dv_ticket_ready SET tc_disabled = 1 WHERE tc_seq = ?";
        $res2 = $this->db->query($sql2, $bind_array);
        $sl_res = $this->deleteSendLists($tc_seq);
        if ($res1 && $res2 && $sl_res) {
            $this->db->trans_complete();
            return true;
        } else {
            $this->db->trans_off();
            return false;
        }
    }

    public function setDeposit($tc_seq) {
        $this->db->trans_start();
        $bind_array = array($tc_seq);
        // deposit 세팅
        $sql1 = "UPDATE dv_ticket SET tc_deposit = sysdate() WHERE tc_seq = ?";
        $res1 = $this->db->query($sql1, $bind_array);
        $sql2 = "UPDATE dv_ticket_ready SET tc_deposit = sysdate() WHERE tc_seq = ?";
        $res2 = $this->db->query($sql2, $bind_array);
        // 리스트 추가
        $stk_res = $this->setSendStack($tc_seq, '-1');
        // 도어락 안내 여부 추가
        $door = $this->input->post_get('door') ?? 0;
        $door_res = $this->ticket_model->setDoorStatus($tc_seq, $door);
        if ($res1 && $res2 && $stk_res && $door_res) {
            $this->db->trans_complete();
            return true;
        } else {
            $this->db->trans_off();
            return false;
        }
    }

    public function setSentSms($tc_seq) {
        $this->db->trans_start();
        $bind_array = array($tc_seq);
        $sql1 = "UPDATE dv_ticket SET tc_sentsms = sysdate() WHERE tc_seq = ?";
        $res1 = $this->db->query($sql1, $bind_array);
        $sql2 = "UPDATE dv_ticket_ready SET tc_sentsms = sysdate() WHERE tc_seq = ?";
        $res2 = $this->db->query($sql2, $bind_array);
        if ($res1 && $res2) {
            $this->db->trans_complete();
            return true;
        } else {
            $this->db->trans_off();
            return false;
        }
    }

    public function sendSMS($send_num, $dest_num, $content = '', $request_date = null) {
        if (!$content) {
            return false;
        }
        if (!$dest_num) {
            return false;
        }
        $sms_url = $this->config->item('sms_url');

		$data = array(
			"body" => $content,
			"sendNo" => $send_num,
			"recipientList" => null,
			"userId" => "ticket",
		);
        if ($request_date) {
            $data['requestDate'] = date('Y-m-d H:i', strtotime($request_date));
        }
        if (is_array($dest_num)) {
            foreach($dest_num as $dest_row) {
		        $data["recipientList"][] = array("recipientNo" => $dest_row);
            }
        } else {
            $data["recipientList"][] = array("recipientNo" => $dest_num);
        }
        $json_data = json_encode($data);

		$header = array(
			'Content-Type: application/json;charset=UTF-8',
		);

		$ch = curl_init();                                 	// curl 초기화
        curl_setopt($ch, CURLOPT_URL, $sms_url);            // URL 지정하기
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    	// 요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      	// connection timeout 10초
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   	// 원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_POST, true);              	// true시 post 전송
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  	// POST data
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $curl_res = curl_exec($ch);

		$res_dec = json_decode($curl_res);

        return $res_dec;
    }

    public function sendLMS($send_num, $dest_num, $title = '', $content = '', $request_date = null) {
        if (!$content) {
            return false;
        }
        if (!$dest_num) {
            return false;
        }
        $sms_url = $this->config->item('mms_url');

		$data = array(
            "title" => $title,
			"body" => $content,
			"sendNo" => $send_num,
			"recipientList" => null,
			"userId" => "ticket",
		);
        if ($request_date) {
            $data['requestDate'] = date('Y-m-d H:i', strtotime($request_date));
        }
        if (is_array($dest_num)) {
            foreach($dest_num as $dest_row) {
		        $data["recipientList"][] = array("recipientNo" => $dest_row);
            }
        } else {
            $data["recipientList"][] = array("recipientNo" => $dest_num);
        }
        $json_data = json_encode($data);

		$header = array(
			'Content-Type: application/json;charset=UTF-8',
		);

		$ch = curl_init();                                 	// curl 초기화
        curl_setopt($ch, CURLOPT_URL, $sms_url);            // URL 지정하기
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    	// 요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      	// connection timeout 10초
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   	// 원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_POST, true);              	// true시 post 전송
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  	// POST data
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $curl_res = curl_exec($ch);

		$res_dec = json_decode($curl_res);

        return $res_dec;
    }

    public function sendTicketMessage($tc_seq, $price_data) {
        $ticket_data = $this->getTicketInfo($tc_seq);

        $msg_title = 'DIVISION STUDIO 입금 안내';
        $msg_content = $this->getDepositGuideMessage($ticket_data, $price_data);
        $send_num = $this->config->item('send_phone');
        $dest_num = $ticket_data['tc_tel'];
        $this->sendLMS($send_num, $dest_num, $msg_title, $msg_content);
    }
    private function getDepositGuideMessage($ticket_data, $price_data) {
        $t_start = strtotime("{$ticket_data['tc_starttime']}");
        $t_end = strtotime("{$ticket_data['tc_endtime']}");
        $krt_start = date('Y년 n월 j일 H시', $t_start);
        $krt_end = date('Y년 n월 j일 H시', $t_end);
        $deposit_limit_hour = $this->config->item('deposit_limit_hour') + 1;
        $deposit_date = date('Y년 n월 j일 H시', strtotime("+{$deposit_limit_hour} hour") < $t_start ? strtotime("+{$deposit_limit_hour} hour") : $t_start);
        $total_price = number_format($price_data['total']);

        $msg =
"[DIVISION STUDIO]
안녕하세요, 디비전 스튜디오입니다.
{$krt_start} ~ {$krt_end} 예약이 접수되었습니다.
예약 금액 {$total_price}원을 {$deposit_date}까지 아래 계좌로 입금해주시기 바랍니다.
< 입금계좌 >
{$this->config->item('deposit_addr')}
(예금주 : {$this->config->item('deposit_owner')})

※ 입금기한 내 입금하지 않을 경우 예약이 취소될 수 있습니다.

문의사항은 아래 연락처 및 링크로 문의해주시기 바랍니다.
< 연락처 >
{$this->config->item('profile_phone')}
< DIVISION STUDIO >
{$this->config->item('profile_dvs')}
< DIVISION STUDIO 오픈채팅방 >
{$this->config->item('chat_dvs')}
";
        return $msg;
    }

    public function sendDepositMessage($tc_seq) {
        if ($ticket_data = $this->getTicketInfo($tc_seq)) {
            // 기체명
            $tmp_type = $this->getMachineName($ticket_data['tc_type']);
            $tmp_date = date('y-m-d', strtotime($ticket_data['tc_starttime']));
            $tmp_price = number_format($ticket_data['tc_price']);
            $sms_content =
"[DIVISION STUDIO]
{$tmp_date}일 {$tmp_type} 예약금액
{$tmp_price}원 입금이 확인되었습니다.
감사합니다.
";
            $send_num = $this->config->item('send_phone');
            $sms_res = $this->sendSMS($send_num, $ticket_data['tc_tel'], $sms_content);
            if ($sms_res) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    private function setSendStack($tc_seq, $stack_val) {
        $sql = "UPDATE dv_send_list SET ml_send_stack = ml_send_stack + (?) WHERE ml_tc_seq = (SELECT tc_m_seq FROM dv_ticket where tc_seq = ?)";
        $bind_array = array($stack_val, $tc_seq);
        $res = $this->db->query($sql, $bind_array);
        return $res;
    }

    public function setDoorStatus($tc_seq, $door) {
        $sql = "UPDATE dv_send_list SET ml_door = ? WHERE ml_tc_seq = (SELECT tc_m_seq FROM dv_ticket where tc_seq = ?)";
        $bind_array = array($door, $tc_seq);
        $res = $this->db->query($sql, $bind_array);
        return $res;
    }

    // 예약 확정시 메시지 전송
    public function sendListMessage() {
        $sql = "SELECT * FROM dv_send_list INNER JOIN dv_ticket ON ml_tc_seq = tc_seq WHERE ml_send_stack <= 0";
        $res = $this->db->query($sql);
        foreach($res->result_array() as $row) {
            $send_res = $this->sendCompleteMessage($row['ml_tc_seq']);
            if ($row['ml_door']) {
                $this->requestEntranceMessage($row);
            }
            if ($send_res) {
                $this->deleteSendList($row['ml_seq']);
            }
        }
        return true;
    }
    private function deleteSendList($ml_seq) {
        $sql = "DELETE FROM dv_send_list WHERE ml_seq = ?";
        $res = $this->db->query($sql, array($ml_seq));
        return $res;
    }
    private function requestEntranceMessage($ticket_data) {
        $request_date = date('Y-m-d H:i', strtotime($ticket_data['tc_starttime'] . ' -10 min'));

        $random_pass = $this->getRandomPassword();
        $pass = '1207';
        $msg_cont =
"안녕하세요, 디비전 스튜디오 도어락 비밀번호 안내입니다.
비밀번호 : {$pass}*
감사합니다.";
        $res = $this->sendSMS($this->config->item('send_phone'), $ticket_data['tc_tel'], $msg_cont, $request_date);
        return $res;
    }
    private function getRandomPassword() {
        return '1207';
    }

    private function deleteSendLists($tc_seq) {
        $sql = "DELETE FROM dv_send_list WHERE ml_tc_seq = ?";
        $res = $this->db->query($sql, array($tc_seq));
        return $res;
    }

    // 예약 확정시 메시지
    public function sendCompleteMessage($tc_seq) {
        $ticket_data = $this->getTicketInfo($tc_seq);
        $t_start = strtotime("{$ticket_data['tc_starttime']}");
        $t_end = strtotime("{$ticket_data['tc_endtime']}");
        $krt_start = date('Y년 n월 j일 H시', $t_start);
        $krt_end = date('Y년 n월 j일 H시', $t_end);
        $msg_subj = 'DIVISION STUDIO 예약 확정 및 이용 안내';
        $msg_cont =
"[DIVISION STUDIO]
안녕하세요, 디비전 스튜디오입니다.
{$krt_start} ~ {$krt_end} 예약이 확정되었으며,
예약 상세 내역은 메일로 전송해드렸습니다.
디비전 스튜디오 무인 이용 매뉴얼 및 이용 수칙 문서를 첨부하여 드립니다.

< 오시는 길 >
https://piurank.com/guide/road

< LX 매뉴얼 >
https://piurank.com/manual_lx.pdf

FX 매뉴얼은 준비중입니다.

※ 스튜디오 도어락 오픈 안내는 대여 시작 10분 전에 안내하여 드립니다.
(도어락 오픈 안내는 상황에 따라 생략될 수 있습니다.)
※ 입장이 불가한 상황이 발생할 경우 즉시 문의하여 주시기 바랍니다.
※ 일행분들 모두 매뉴얼을 공유하여 확인해주시기 바랍니다.

문의사항은 아래 연락처 및 링크로 문의해주시기 바랍니다.
감사합니다.
< 연락처 >
{$this->config->item('profile_phone')}
< DIVISION STUDIO >
{$this->config->item('profile_dvs')}
< DIVISION STUDIO 오픈채팅방 >
{$this->config->item('chat_dvs')}
";

        // 문자 전송 (90바이트 초과시 LMS, 이하시 SMS)
        $send_phone = $this->config->item('send_phone');
        $req_date = date('Y-m-d H:i', strtotime('1 min'));
        if (mb_strwidth($msg_cont) > 90) {
            $msg_res = $this->sendLMS($send_phone, $ticket_data['tc_tel'], $msg_subj, $msg_cont, $req_date);
        } else {
            $msg_res = $this->sendSMS($send_phone, $ticket_data['tc_tel'], $msg_cont, $req_date);
        }

        // 이메일 전송
        $mail_addr = array(
            array('addr' => $ticket_data['tc_email'], 'name' => $ticket_data['tc_name'])
        );
        $mail_body = $this->getTicketMailBody($ticket_data);
        $mail_subj = '[DIVISION STUDIO] 예약이 접수되었습니다.';
        $mail_res = $this->sendEmail($mail_addr, $mail_subj, $mail_body);
        if ($msg_res && $mail_res) {
            return true;
        } else {
            return false;
        }
    }

    public function insertSendList($tc_seq, $send_stack) {

        // SendList 등록
        $bind_array = array($tc_seq, $send_stack);
        $sql = "INSERT INTO dv_send_list(ml_tc_seq, ml_send_stack) VALUES(?,?)";
        $res = $this->db->query($sql, $bind_array);

        // 관리자 메일 등록
        $ticket_data = $this->getTicketInfo($tc_seq);
        $mail_body = $this->getTicketMailBody($ticket_data);
        $t_start = strtotime("{$ticket_data['tc_starttime']}");
        $t_end = strtotime("{$ticket_data['tc_endtime']}");
        $start_date = date('Y-m-d H시', $t_start);
        $end_date = date('Y-m-d H시', $t_end);
        $mail_admin_subj = "{$start_date} ~ {$end_date} ({$ticket_data['tc_name']} / {$ticket_data['tc_tel']})예약 접수됨";
        $mail_admin_addr = array(
            array('addr' => 'ticket@piurank.com', 'name' => 'DIVISION STUDIO 관리자'),
            array('addr' => 'eodmalt@piurank.com', 'name' => 'WINDFORCE'),
        );
        $mail_res = $this->sendEmail($mail_admin_addr, $mail_admin_subj, $mail_body);
        return $res;
    }
    private function getTicketMailBody($ticket_data) {
        $t_start = strtotime("{$ticket_data['tc_starttime']}");
        $t_end = strtotime("{$ticket_data['tc_endtime']}");
        $krt_start = date('Y년 n월 j일 H시', $t_start);
        $krt_end = date('Y년 n월 j일 H시', $t_end);
        $ticket_date = date('Y년 n월 j일', strtotime($ticket_data['tc_tickettime']));
        // 메일 메시지 설정
        $mail_body =
"[DIVISION STUDIO]
안녕하세요, 디비전 스튜디오입니다.
{$ticket_date}에 예약하신 내역을 안내하여 드립니다.
예약시각 : {$krt_start} 부터 {$krt_end} 까지
이름(입금자명) : {$ticket_data['tc_name']} 님
연락처 : {$ticket_data['tc_tel']}
이메일 : {$ticket_data['tc_email']}
버전 : {$ticket_data['tc_version']}
인원 : {$ticket_data['tc_person']} 명
[가격]
";
        $family_ticket_data = $this->getFamilyTicketInfo($ticket_data['tc_m_seq']);
        $mc_price = array();
        $total_price = 0;
        foreach ($family_ticket_data as $family_ticket_row) {
            $mc_price[] = $this->getMachineName($family_ticket_row['tc_type']) . " : " . number_format($family_ticket_row['tc_price']) . "원";
            $total_price += $family_ticket_row['tc_price'];
        }
        $total_price = number_format($total_price);
        $mail_body .= implode(PHP_EOL, $mc_price);
        $mail_body .= "
< 총합 {$total_price}원 >

[주의사항 - 반드시 확인해주세요!]
 - 본 스튜디오는 무인으로 운영되며, CCTV가 실시간으로 녹화되고 있습니다. 이용 수칙을 반드시 지켜주세요.
 - 현재 사회적 거리두기 2단계 적용중이므로, 물과 무알콜 음료 이외의 음식 취식은 일절 금지되어 있습니다.
 - 예약시각에 맞춰 대여가 시작됩니다. 늦지 않게 도착해주세요.
 - 예약 당일 취소는 불가능하며, 이외 취소 요청은 개별 문의 바랍니다.
 - 다음 예약자를 위해 예약 종료 10분 전부터 퇴실 준비를 해주세요.
 - 예약한 기체 외에 다른 기체나 방에 접근하지 말아주세요. (예: LX기체 이용시 FX방 접근 금지)
 - LX 기체를 1대만 대여할 시 나머지 1대를 다른 팀에서 예약하여 같은 공간에서 이용하게 될 수 있습니다.
 - 개인 장비로 방송하실 때는 설치 및 철거 시간을 고려하여 예약해주세요.
 - 스튜디오 안에서 음주, 흡연을 하지 말아주세요.
 - 발판의 위치를 임의로 움직이지 말아주시고, 발판에 눕거나 앉지 말아주세요.
 - 발판의 봉에 매달리거나 무리한 힘을 사용하지 말아주세요.
 - 스튜디오의 벽이나 물건에 낙서를 하지 말아주세요.
 - 스튜디오에 비치된 공용 물품을 소중히 사용해주세요. 물품 도난 및 파손시 책임을 물을 수 있습니다.
 - 퇴실시 놓고 가시는 물건은 없으신지 확인해주세요. 디비전 스튜디오는 개인 분실물에 대하여 책임을 지지 않습니다.
 - 미성년자는 9시부터 22시까지 대여가 가능합니다. (22시 ~ 익일 9시 대여 불가)
 - 만 14세 미만의 미성년자는 법적보호자가 동반할 시에만 입장 가능합니다. (보호자 명의로 예약해주세요.)

[문의사항↓↓]
연락처 : {$this->config->item('profile_phone')}
DIVISION STUDIO : {$this->config->item('profile_dvs')}
DIVISION STUDIO 오픈채팅방 : {$this->config->item('chat_dvs')}
";
        return $mail_body;
    }

    public function sendEmail($dest_data, $title, $content) {
        $this->load->library('PHPMailer_Lib');
        $mail = $this->phpmailer_lib->load();
        try {
            // 기본 설정
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = "smtp.piurank.com";
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->item('mailer_id');
            $mail->Password = $this->config->item('mailer_pw');
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->CharSet = "utf-8";
            $mail->isHTML(false); // HTML 태그 사용 여부
            $mail->Subject = $title;
            $mail->Body = $content;
            $mail->setFrom("ticket@piurank.com", "DIVISION STUDIO 관리자");
            foreach($dest_data as $dest_row) {
                $mail->addAddress($dest_row['addr'], $dest_row['name']);
            }
            // 메일 전송
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error : ", $mail->ErrorInfo;
            return false;
        }
        return true;
    }

    // 사용 안함
    public function sendEmail_ticket($machines, $date, $start_idx, $end_idx, $tc_name, $tc_tel, $tc_email, $tc_person, $price_data, $tc_version = 'XX') {
        $this->load->library('PHPMailer_Lib');
        $mail = $this->phpmailer_lib->load();
        try {
            $t_start = strtotime("{$date} {$start_idx} hours");
            $t_end = strtotime("{$date} {$end_idx} hours");
            $start_date = date('Y-m-d H시', $t_start);
            $end_date = date('Y-m-d H시', $t_end);
            $krt_start = date('Y년 n월 j일 H시', $t_start);
            $krt_end = date('Y년 n월 j일 H시', $t_end);
            $ticket_date = date('Y년 n월 j일', strtotime($date));
            $deposit_date = date('Y년 n월 j일 H시', strtotime("+25 hour") < $t_start ? strtotime("+25 hour") : $t_start);
            // 기본 설정
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = "smtp.piurank.com";
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->item('mailer_id');
            $mail->Password = $this->config->item('mailer_pw');
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->CharSet = "utf-8";

            // 관리자에게 전송
            $mail->setFrom("ticket@piurank.com", "DIVISION STUDIO 관리자");
            $mail->addAddress("ticket@piurank.com", "DIVISION STUDIO 관리자");
            $mail->addAddress("eodmalt@piurank.com", "WINDFORCE");
            $mail->isHTML(false); // HTML 태그 사용 여부
            $tmp_subject = "{$start_date} ~ {$end_date} ({$tc_name} / {$tc_tel})예약 접수됨";
            $mail->Subject = $tmp_subject;
            $tmp_body = "
[DIVISION STUDIO]
안녕하세요, 디비전 스튜디오입니다.
{$ticket_date}자 예약 내역을 안내하여 드립니다.
예약시각 : {$krt_start} 부터 {$krt_end} 까지
이름(입금자명) : {$tc_name} 님
연락처 : {$tc_tel}
이메일 : {$tc_email}
버전 : {$tc_version}
인원 : {$tc_person} 명
[가격]
";
            $mc_price = array();
            foreach ($machines as $mc_code) {
                $mc_price[] = $this->getMachineName($mc_code) . " - " . number_format($price_data[$mc_code]) . "원";
            }
            $total_price = number_format($price_data['total']);
            $tmp_body .= implode(PHP_EOL, $mc_price);
            $tmp_body .= "
< 총합 {$total_price}원 >
입금계좌 : {$this->config->item('deposit_addr')} (예금주 : {$this->config->item('deposit_owner')})
[ {$deposit_date}까지 입금해주세요. 시간 내 입금이 되지 않을 경우 예약이 취소될 수 있습니다.]

[주의사항 - 반드시 확인해주세요!]
 - 이용 요금은 예약 후 24시간 내로, 예약시각을 넘어가지 않도록 입금해주세요. 입금이 완료되지 않을 경우 예약이 취소될 수 있습니다.
 - 현재 사회적 거리두기 2단계 적용중이므로, 물과 무알콜 음료 이외의 음식 취식은 일절 금지되어 있습니다.
 - 예약시각에 맞춰 대여가 시작됩니다. 늦지 않게 도착해주세요.
 - 무단 불참시 향후 예약이 불가할 수 있습니다.
 - 예약 당일 취소는 불가능하며, 취소 요청은 개별 문의 바랍니다.
 - 다음 예약자를 위해 예약 종료 10분 전부터 퇴실 준비를 해주세요.
 - 예약한 기체 외에 다른 기체나 방에 접근하지 말아주세요. (예: LX기체 이용시 FX방 접근 금지)
 - LX 기체를 1대만 대여할 시 나머지 1대를 다른 팀에서 예약하여 같은 공간에서 이용하게 될 수 있습니다.
 - 개인 장비로 방송하실 때는 설치 및 철거 시간을 고려하여 예약해주세요.
 - 스튜디오 안에서 음주, 흡연을 하지 말아주세요.
 - 발판의 위치를 임의로 움직이지 말아주시고, 발판에 눕거나 앉지 말아주세요.
 - 발판의 봉에 매달리거나 무리한 힘을 사용하지 말아주세요.
 - 스튜디오의 벽이나 물건에 낙서를 하지 말아주세요.
 - 스튜디오에 비치된 공용 물품을 소중히 사용해주세요. 물품 도난 및 파손시 민/형사 책임을 물을 수 있습니다.
 - 퇴실시 놓고 가시는 물건은 없으신지 확인해주세요. 디비전 스튜디오는 개인 분실물에 대하여 책임을 지지 않습니다.
 - 미성년자는 9시부터 22시까지 대여가 가능합니다. (22시 ~ 익일 9시 대여 불가)
 - 만 14세 미만의 미성년자는 법정대리인의 이용 동의서가 필요합니다.

[문의사항↓↓]
연락처 : {$this->config->item('profile_phone')}
DIVISION STUDIO : {$this->config->item('profile_dvs')}
DIVISION STUDIO 오픈채팅방 : {$this->config->item('chat_dvs')}
";
            $mail->Body = $tmp_body;
            // 메일 전송
            $mail->send();

            $mail->clearAddresses();
            $mail->addAddress($tc_email, $tc_name);
            $mail->Subject = "[DIVISION STUDIO] 예약이 접수되었습니다.";
            // 메일 전송
            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error : ", $mail->ErrorInfo;
        }
    }

    public function check_studio() {
        if (isset($this->session->u_class) && ($this->session->u_class == 3 || $this->session->u_class == 1)) {
            return true;
        } else {
            return false;
        }
    }

}
?>