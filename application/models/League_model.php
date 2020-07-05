<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getWorkingLeague() {
        $sql = "SELECT * FROM al_info WHERE now() between li_starttime AND li_endtime LIMIT 1";
        $res = $this->db->query($sql);
        if ($row = $res->row_array()) {
            return $row;
        } else {
            return null;
        }
    }
    
    function getTierData() {
        $sql = "SELECT * FROM al_tier";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }
    
    function getChartData() {
        $sql = "SELECT * FROM al_char";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }


    function getWorkingLeagueData() {
        $sql = "SELECT * FROM pr_playinfo
            inner join pr_users on pi_u_seq = u_seq
            inner join pr_charts on pi_c_seq = c_seq
            inner join pr_songs on c_s_seq = s_seq
            INNER JOIN al_tier ON u_al_tier = t_name
            INNER JOIN al_charts ON lc_c_seq = c_seq
            INNER JOIN al_info ON lc_li_season = li_season and lc_li_degree = li_degree
            WHERE pi_status = 'Active' AND pi_createtime BETWEEN li_starttime AND li_endtime
            ";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }
}
?>