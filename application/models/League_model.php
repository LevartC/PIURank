<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    public function getWorkingLeague() {
        $sql = "SELECT * FROM al_info WHERE sysdate() between li_starttime AND li_endtime LIMIT 1";
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
        $sql = "SELECT u_id, u_nick, u_mmr, u_al_tier FROM pr_users WHERE u_al_tier IS NOT NULL ORDER BY u_al_tier";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function getLeagueChartData($league_data = "") {
        if (!$league_data) {
            $league_data = $this->getWorkingLeague();
        }
        $sql = "SELECT *, c_type+0 as charttype FROM al_charts AS lc inner join pr_charts as c on lc_c_seq = c_seq inner join pr_songs as s on c_s_seq = s_seq WHERE lc_li_season = '{$league_data['li_season']}' AND lc_li_degree = '{$league_data['li_degree']}'";
        $res = $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[$row['lc_t_name']][] = $row;
        }
        return $data;
    }

    public function getLeaguePlayInfo($league_data = "") {
        if (!$league_data) {
            $league_data = $this->getWorkingLeague();
        }
        $sql = "SELECT MAX(pi_xscore) AS pi_x, u_nick, a.* from pr_playinfo as a
        inner join pr_users on pi_u_seq = u_seq
        inner join pr_charts on pi_c_seq = c_seq
        inner join pr_songs on c_s_seq = s_seq
        INNER JOIN al_charts ON lc_c_seq = c_seq
        WHERE pi_status = 'Active' AND lc_li_season = '{$league_data['li_season']}' AND lc_li_degree = '{$league_data['li_degree']}' AND pi_createtime BETWEEN '{$league_data['li_starttime']}' AND '{$league_data['li_endtime']}'
        GROUP BY u_seq, pi_c_seq
        ORDER BY pi_u_seq ASC, pi_x DESC, pi_c_seq asc
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