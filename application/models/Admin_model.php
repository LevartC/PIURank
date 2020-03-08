<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }
    
    function getUserData($u_seq) {
        if ($u_seq) {
            $sql = "SELECT u_id, u_nick, u_email, u_mmr, u_skillp, u_tier FROM pr_users WHERE u_seq = ?";
            $res = $this->db->query($sql, array((int)$u_seq));
            if ($row = $res->row_array()) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getWaitingPlayinfo() {
        $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pr_playinfo.u_seq = pr_users.u_seq inner join pr_charts on pr_playinfo.c_seq = pr_charts.c_seq inner join pr_songs on pr_charts.s_seq = pr_songs.s_seq WHERE pi_active is null";
        $res = $this->db->query($sql);
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }
}