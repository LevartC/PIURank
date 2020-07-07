<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    public function getWorkingLeague() {
        $sql = "SELECT * FROM al_info WHERE now() between li_starttime AND li_endtime LIMIT 1";
        $res = $this->db->query($sql);
        if ($row = $res->row_array()) {
            return $row;
        } else {
            return null;
        }
    }

    public function getTierData() {
        $sql = "SELECT * FROM al_tier";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function getChartData() {
        $sql = "SELECT * FROM al_chart";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function getLeagueUserData() {
        $sql = "SELECT u_id, u_nick, u_mmr, u_al_tier FROM pr_users WHERE u_al_tier IS NOT NULL";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function getTierPlayInfo($league_data) {
        $sql = "SELECT MAX(pi_xscore) AS pi_x, u_seq, pr_playinfo.* from pr_playinfo
            inner join pr_users on pi_u_seq = u_seq
            inner join pr_charts on pi_c_seq = c_seq
            inner join pr_songs on c_s_seq = s_seq
            INNER JOIN al_charts ON lc_c_seq = c_seq
            WHERE pi_status = 'Active' AND lc_li_season = 1 and lc_li_degree = 1 AND pi_createtime BETWEEN '2020-01-29' AND '2020-07-10'
            GROUP BY u_seq, pi_c_seq
            ORDER BY pi_u_seq ASC, pi_x DESC, pi_c_seq asc
            ";
    }

    public function getWorkingLeagueData() {
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