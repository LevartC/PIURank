<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }
    
    function setPlayinfo($pi_data) {
        if ($pi_data['pi_status'] == 1) { // 승인, 수정
            if ($pi_data['pi_update']) { // 수정
                $pi_skillp = $this->account_model->getSkillPoint($pi_data['pi_level'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_grade'], $pi_data['pi_break']);
                $sql = "UPDATE pr_playinfo SET pi_status = 1, pi_updatetime = now(), pi_skillp = ?, pi_grade = ?, pi_break = ?, pi_judge = ?, pi_score = ?, pi_perfect = ?, pi_great = ?, pi_good = ?, pi_bad = ?, pi_miss = ?, pi_maxcom = ?, pi_comment = ? WHERE pi_seq = ?";
                $res = $this->db->query($sql, array($pi_skillp, $pi_data['pi_grade'], $pi_data['pi_break'], $pi_data['pi_judge'], $pi_data['pi_score'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_maxcom'], $pi_data['pi_comment'], $pi_data['pi_seq']));
                if ($res) {
                    return true;
                }
            } else { // 승인
                $sql = "UPDATE pr_playinfo SET pi_status = 1, pi_updatetime = now(), pi_comment = ? WHERE pi_seq = ?";
                $res = $this->db->query($sql, array($pi_data['pi_comment'], $pi_data['pi_seq']));
                if ($res) {
                    return true;
                }
            }

        } else { // 거부
            $sql = "UPDATE pr_playinfo SET pi_status = -1, pi_updatetime = now(), pi_comment = ? WHERE pi_seq = ?";
            $res = $this->db->query($sql, array($pi_data['pi_comment'], $pi_data['pi_seq']));
            if ($res) {
                return true;
            }
        }
        return false;
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
}