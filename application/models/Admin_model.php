<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }

    function setPlayinfo($pi_data) {
        if ($pi_data['pi_status'] === PI_STATUS_ACTIVE) { // 승인, 수정
            if ($pi_data['pi_update']) { // 수정
                $pi_skillp = $this->playinfo_model->getSkillPoint($pi_data['pi_level'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_grade'], $pi_data['pi_break']);
                $sql = "UPDATE pr_playinfo SET pi_status = ?, pi_updatetime = now(), pi_skillp = ?, pi_grade = ?, pi_break = ?, pi_judge = ?, pi_score = ?, pi_perfect = ?, pi_great = ?, pi_good = ?, pi_bad = ?, pi_miss = ?, pi_maxcom = ?, pi_comment = ?, pi_cfrm_u_id = ? WHERE pi_seq = ?";
                $res = $this->db->query($sql, array(PI_STATUS_ACTIVE, $pi_skillp, $pi_data['pi_grade'], $pi_data['pi_break'], $pi_data['pi_judge'], $pi_data['pi_score'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_maxcom'], $pi_data['pi_comment'], $pi_data['pi_cfrm_u_id'], $pi_data['pi_seq']));
            } else { // 승인
                $sql = "UPDATE pr_playinfo SET pi_status = ?, pi_updatetime = now(), pi_comment = ?, pi_cfrm_u_id = ? WHERE pi_seq = ?";
                $res = $this->db->query($sql, array(PI_STATUS_ACTIVE, $pi_data['pi_comment'], $pi_data['pi_cfrm_u_id'], $pi_data['pi_seq']));
            }
            if ($res) {
                $this->updateSkillPoint($pi_data['u_seq']);
                return true;
            }
        } else { // 거부
            $sql = "UPDATE pr_playinfo SET pi_status = ?, pi_updatetime = now(), pi_comment = ?, pi_cfrm_u_id = ? WHERE pi_seq = ?";
            $res = $this->db->query($sql, array(PI_STATUS_DENIED, $pi_data['pi_comment'], $pi_data['pi_cfrm_u_id'], $pi_data['pi_seq']));
            if ($res) {
                return true;
            }
        }
        return false;
    }

    public function updateSkillPoint($u_seq) {
        if ($u_seq) {
            $sql = "update pr_users set u_skillp = truncate((SELECT SUM(sp) AS pi_skillp FROM (SELECT MAX(pi_skillp) AS sp from pr_playinfo where pi_u_seq = ? AND pi_status = 2 GROUP BY pi_c_seq ORDER BY pi_skillp DESC LIMIT 25) a), 2) WHERE u_seq = ?";
            if ($this->db->query($sql, array($u_seq, $u_seq))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getConfig($cf_name, $cf_key = 0) {
        $data = null;
        if ($cf_key) {
            $sql = "SELECT * from pr_config WHERE cf_name = ? and cf_key = ?";
            $res = $this->db->query($sql, array($cf_name, $cf_key));
            if ($row = $res->row_array()) {
                $data = $row['cf_value'];
            }
        } else {
            $sql = "SELECT * from pr_config WHERE cf_name = ?";
            $res = $this->db->query($sql, array($cf_name));
            foreach ($res->result_array() as $row) {
                $data[$row['cf_key']] = $row['cf_value'];
            }
        }
        return $data;
    }
    public function getConfigData($config_str) { // 지금은 안씀.
        $sql = "SELECT cf_key, cf_value FROM pr_config WHERE cf_name = ?";
        if ($res = $this->db->query($sql, array($config_str))) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

}