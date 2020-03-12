<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }
    

    function getWaitingPlayinfo() {
        $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pr_playinfo.u_seq = pr_users.u_seq inner join pr_charts on pr_playinfo.c_seq = pr_charts.c_seq inner join pr_songs on pr_charts.s_seq = pr_songs.s_seq WHERE pi_status is null";
        $res = $this->db->query($sql);
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }
    
    function setPlayinfo($pi_data) {
        if ($pi_data['pi_status'] == 1) { // 승인, 수정
            if ($pi_data['pi_update']) { // 수정
                $sql = "UPDATE pr_playinfo SET pi_status = 1, pi_updatetime = now(), pi_grade = ?, pi_break = ?, pi_judge = ?, pi_score = ?, pi_perfect = ?, pi_great = ?, pi_good = ?, pi_bad = ?, pi_miss = ?, pi_maxcom = ?, pi_comment = ? WHERE pi_seq = ?";
                $res = $this->db->query($sql, array($pi_data['pi_grade'], $pi_data['pi_break'], $pi_data['pi_judge'], $pi_data['pi_score'], $pi_data['pi_perfect'], $pi_data['pi_great'], $pi_data['pi_good'], $pi_data['pi_bad'], $pi_data['pi_miss'], $pi_data['pi_maxcom'], $pi_data['pi_comment'], $pi_data['pi_seq']));
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
            $sql = "UPDATE pr_playinfo SET pi_status = 0, pi_updatetime = now(), pi_comment = ? WHERE pi_seq = ?";
            $res = $this->db->query($sql, array($pi_data['pi_comment'], $pi_data['pi_seq']));
            if ($res) {
                return true;
            }
        }
        return false;
    }
}