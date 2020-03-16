<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playinfo_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }

    public function getConfigData($config_str) {
        $sql = "SELECT cf_key, cf_value FROM pr_config WHERE cf_name = ?";
        if ($res = $this->db->query($sql, array($config_str))) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function searchFile($c_title) {
        $search_str = "%".$c_title."%";
        $sql = "SELECT c_seq, s_title, s_title_kr, c_type+0 as c_type, c_level FROM pr_charts as a, pr_songs as b WHERE a.s_seq = b.s_seq AND a.c_level >= 12 AND (b.s_title LIKE ? OR b.s_title_kr LIKE ?)";
        if ($res = $this->db->query($sql, array($search_str, $search_str))) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
    }
    public function writeAction($pi_file, $pi_data) {
        require_once __dir__ ."/class/SkillPoint.php";

        $upload_dir = __dir__ ."/pi_images";
        $allowed_ext = array("jpg", "jpeg", "png", "gif");

        $error = $pi_file["error"];
        $fm = explode(".", $pi_file["name"]);
        $ext = $fm[count($fm)-1];
        $pi_filename = date("Ymdhis") . "_" . $pi_data['u_id']. "." . $ext;

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
        // 파일 이동
        move_uploaded_file($_FILES["pi_file"]["tmp_name"], $upload_dir."/".$pi_filename);

        try {
            $pi_skillp = $this->account_model->getSkillPoint($pi_data['pi_level'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_grade']);
            
            $sql = "INSERT INTO pr_playinfo(c_seq, u_seq, pi_grade, pi_break, pi_judge, pi_perfect, pi_great, pi_good, pi_bad, pi_miss, pi_maxcom, pi_score, pi_skillp, pi_filename) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $res = $this->db->query($sql, array($pi_data['c_seq'], $pi_data['u_seq'], $pi_data['pi_grade'], $pi_data['pi_break'], $pi_data['pi_judge'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_maxcom'], $pi_data['pi_score'], $pi_data['pi_skillp'], $pi_data['pi_filename']));
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
}
?>