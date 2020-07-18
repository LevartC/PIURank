<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playinfo_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getPlayinfo($status = "", $u_id = null) {
        $bind_array = array();
        if (!$status) {
            $stat_where = "";
        } else {
            $stat_where = " AND pi_status = ".$status;
        }
        $order_sql = " ORDER BY pi_createtime DESC";
        if ($u_id) {
            $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq inner join pr_charts on pi_c_seq = c_seq inner join pr_songs on c_s_seq = s_seq WHERE pi_enable = 1 AND u_id = ?" . $stat_where;
            array_push($bind_array, $u_id);
        } else {
            $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq inner join pr_charts on pi_c_seq = c_seq inner join pr_songs on c_s_seq = s_seq WHERE pi_enable = 1" . $stat_where;
        }
        $sql .= $order_sql;
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
            $upload_dir = get_root_dir() . PI_IMAGE_PATH;
            $allowed_ext = array("jpg", "jpeg", "png", "gif");

            $error = $pi_file["error"];
            $fm = explode(".", $pi_file["name"]);
            $ext = $fm[count($fm)-1];
            $pi_filename = date("YmdHis") . "_" . $pi_data['u_id']. ".jpg";

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
            if (!$this->uploadFile($pi_file["tmp_name"], $upload_dir."/".$pi_filename, 90)) {
                alert('파일 생성에 실패하였습니다.');
                exit();
            }
        //    move_uploaded_file($_FILES["pi_file"]["tmp_name"], $upload_dir."/".$pi_filename);
        } catch(Exception $e) {
            alert('파일 생성에 실패하였습니다.\n' . $e->getMessage());
        }
        try {
            $pi_skillp = $this->getSkillPoint($pi_data['pi_level'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_grade'], $pi_data['pi_break']);
            $pi_xscore = $this->getXScore($pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_break']);

            $sql = "INSERT INTO pr_playinfo(pi_c_seq, pi_u_seq, pi_grade, pi_break, pi_judge, pi_perfect, pi_great, pi_good, pi_bad, pi_miss, pi_maxcom, pi_score, pi_skillp, pi_xscore, pi_filename) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $res = $this->db->query($sql, array($pi_data['pi_c_seq'], $pi_data['u_seq'], $pi_data['pi_grade'], $pi_data['pi_break'], $pi_data['pi_judge'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_maxcom'], $pi_data['pi_score'], $pi_skillp, $pi_xscore, $pi_filename));
            if ($res) {
                alert("성공적으로 입력되었습니다.", "/playinfo/write");
            } else {
                alert("오류가 발생하였습니다.\r\n관리자에게 문의하세요.");
                unlink($upload_dir."/".$pi_filename);
            }
        } catch(Exception $e) {
            alert($e->getMessage());
            unlink($upload_dir."/".$pi_filename);
        }
    }
    function uploadFile($src, $dest, $quality) {
        $info = getimagesize($src);
        $is_rotate = false;
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($src);
            if(function_exists('exif_read_data')) {
                $exif = exif_read_data($src);
                if(!empty($exif) && isset($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 8:
                            $image = imagerotate($image,90,0);
                            $is_rotate = true;
                        break;
                        case 3:
                            $image = imagerotate($image,180,0);
                        break;
                        case 6:
                            $image = imagerotate($image,-90,0);
                            $is_rotate = true;
                        break;
                        default:
                        break;
                    }
                }
            }
        }
        elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($src);
        }
        elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($src);
        }

        // Image Resize (기준보다 클 경우)
        $width = 1920;
        $height = 1080;
        if ($is_rotate) {
            $img_width = $info[1];
            $img_height = $info[0];
        } else {
            $img_width = $info[0];
            $img_height = $info[1];
        }
        if ($img_width > $width || $img_height > $height) {
            $img_ratio = $img_width / $img_height;
            $new_width = (int)($height * $img_ratio);
            $new_height = $height;
            $tmp = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height);
            imagedestroy($image);
            $image = $tmp;
        }
        if (imagejpeg($image, $dest, $quality)) {
            return $dest;
        } else {
            return null;
        }
    }

    public function deletePlayinfo($pi_seq) {
        $sql = "UPDATE pr_playinfo SET pi_enable = 0 WHERE pi_seq = ?";
        $res = $this->db->query($sql, array($pi_seq));
        if ($res) {
            alert("성공적으로 삭제되었습니다.", "/account/myplay");
        } else {
            alert("오류가 발생하였습니다.\r\n관리자에게 문의해주세요.");
        }

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
    public function getXScore($perfect, $great, $good, $bad, $miss, $break) {
        $break_val = ($break == "OFF") ? 50 : 0;
        $xscore = ($perfect*2) + $great - $bad - ($miss*2) - $break_val;
        return $xscore;
    }
}
?>