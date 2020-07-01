<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playinfo_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getPlayinfo($status, $u_seq = null) {
        $bind_array = array();
        if (!$status) {
            $stat_where = "";
        } else {
            $stat_where = " AND pi_status = ".$status;
        }
        if ($u_seq) {
            $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq inner join pr_charts on pi_c_seq = c_seq inner join pr_songs on c_s_seq = s_seq WHERE u_seq = ?" . $stat_where;
            array_push($bind_array, (int)$u_seq);
        } else {
            $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq inner join pr_charts on pi_c_seq = c_seq inner join pr_songs on c_s_seq = s_seq WHERE 1" . $stat_where;
        }
        $res = count($bind_array) ? $this->db->query($sql, $bind_array) : $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function searchFile($c_title) {
        $search_str = "%".$c_title."%";
        $sql = "SELECT c_seq, s_title, s_title_kr, c_type+0 as c_type, c_level FROM pr_charts as a, pr_songs as b WHERE a.c_s_seq = b.s_seq AND a.c_level >= 12 AND (b.s_title LIKE ? OR b.s_title_kr LIKE ?)";
        if ($res = $this->db->query($sql, array($search_str, $search_str))) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
    }
    public function writeAction($pi_file, $pi_data) {
        try {
            $upload_dir = get_root_dir() ."/pi_images";
            $allowed_ext = array("jpg", "jpeg", "png", "gif");

            $error = $pi_file["error"];
            $fm = explode(".", $pi_file["name"]);
            $ext = $fm[count($fm)-1];
            $pi_filename = date("Ymdhis") . "_" . $pi_data['u_id']. ".jpg";

            if( $error != UPLOAD_ERR_OK ) {
                switch( $error ) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        alert("파일이 너무 큽니다. ($error)");
                        return false;
                    case UPLOAD_ERR_NO_FILE:
                        alert("파일이 첨부되지 않았습니다. ($error)");
                        return false;
                    default:
                        alert("파일이 정상적으로 업로드되지 않았습니다. ($error)");
                        return false;
                }
            }
            // 확장자 확인
            if( !in_array($ext, $allowed_ext) ) {
                alert("허용되지 않는 확장자입니다.");
                exit();
            }
            // 파일 압축 후 이동
            $this->uploadFile($pi_file["tmp_name"], $upload_dir."/".$pi_filename, 90);
        //    move_uploaded_file($_FILES["pi_file"]["tmp_name"], $upload_dir."/".$pi_filename);
        } catch(Exception $e) {
            alert('파일 생성에 실패하였습니다.\n' . $e->getMessage());
        }
        try {
            $pi_skillp = $this->getSkillPoint($pi_data['pi_level'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_grade'], $pi_data['pi_break']);

            $sql = "INSERT INTO pr_playinfo(c_seq, u_seq, pi_grade, pi_break, pi_judge, pi_perfect, pi_great, pi_good, pi_bad, pi_miss, pi_maxcom, pi_score, pi_skillp, pi_filename) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $res = $this->db->query($sql, array($pi_data['c_seq'], $pi_data['u_seq'], $pi_data['pi_grade'], $pi_data['pi_break'], $pi_data['pi_judge'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_maxcom'], $pi_data['pi_score'], $pi_skillp, $pi_filename));
            if ($res) {
                alert("성공적으로 입력되었습니다.");
            } else {
                alert("오류가 발생하였습니다.\r\n관리자에게 문의하세요.");
                unlink($upload_dir."/".$pi_filename);
            }
        } catch(Exception $e) {
            alert($e->getMessage());
        }
    }
    function uploadFile($src, $dest, $quality) {
        $info = getimagesize($src);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($src);
        }
        elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($src);
        }
        elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($src);
        }
        $exif = exif_read_data($src);
        switch ($exif['Orientation']) {
            case 8:
                $image = imagerotate($image,90,0);
            break;
            case 3:
                $image = imagerotate($image,180,0);
            break;
            case 6:
                $image = imagerotate($image,-90,0);
            break;
            default:
            break;
        }

        imagejpeg($image, $dest, $quality);

        return $dest;
    }

    public function getSkillPoint($level, $perfect, $great, $good, $bad, $miss, $grade, $break) {
        $grade_bal = $this->admin_model->getConfig("cf_grade_balance");
        $judge_bal = $this->admin_model->getConfig("cf_judge_balance");
        $judge_point_bal = $this->admin_model->getConfig("cf_skillp_balance", "grade") / 100;

        $level_weight = $this->admin_model->getConfig("cf_level_weight", $level);

        if (!$grade_bal || !$judge_bal || !$level_weight) {
            alert("DB 로드 실패.");
            return false;
        }

        // 브렉오프시 이전 레벨로 가중
        $break_bal = ($break == "OFF") ? -$level : 0;

        $total_notes = $perfect+$great+$good+$bad+$miss;
        $judge_notes =
            (($judge_bal['perfect']/100)  * $perfect) +
            (($judge_bal['great']/100)    * $great) +
            (($judge_bal['good']/100)     * $good) +
            (($judge_bal['bad']/100)      * $bad) +
            (($judge_bal['miss']/100)     * $miss);
        $judge_point = ($judge_notes / $total_notes) * ($level_weight + $break_bal);
        $grade_point = ($grade_bal[$grade]/100) * ($level_weight + $break_bal) * $judge_point_bal;

        $skill_point = $judge_point + $grade_point;
        return $skill_point;
    }
}
?>