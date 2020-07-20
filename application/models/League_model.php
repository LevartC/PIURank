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
        $sql = "SELECT * FROM al_tier ORDER BY t_min_mmr ASC";
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

    public function getLeagueUserData($league_data = "", $tier_name = "") {
        if (!$league_data) {
            $league_data = $this->getWorkingLeague();
        }
        $bind_array = array($league_data['li_season'], $league_data['li_degree']);
        $sql = "SELECT u_nick, ls_mmr, ls_tier, t_color FROM pr_users inner join al_mmr ON u_seq = ls_u_seq inner join al_tier on ls_tier = t_name WHERE ls_li_season = ? AND ls_li_degree = ?";
        // 티어이름 없을 시 전체 검색
        if ($tier_name) {
            $where_tier = " AND ls_tier = ?";
            $sql .= $where_tier;
            $bind_array[] = $tier_name;
        }
        $sql .= " ORDER BY ls_mmr DESC";
        $res = $this->db->query($sql, $bind_array);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function getLeagueChartData($league_data = "", $tier_name = "") {
        // 리그데이터 없을 시 현재 리그 데이터 불러오기
        if (!$league_data) {
            $league_data = $this->getWorkingLeague();
        }
        $sql = "SELECT *, c_type+0 as charttype FROM al_charts AS lc inner join pr_charts as c on lc_c_seq = c_seq inner join pr_songs as s on c_s_seq = s_seq WHERE lc_li_season = ? AND lc_li_degree = ?";
        $bind_array = array($league_data['li_season'], $league_data['li_degree']);
        // 티어이름 없을 시 전체 검색
        if ($tier_name) {
            $where_tier = " AND lc_t_name = ?";
            $sql .= $where_tier;
            $bind_array[] = $tier_name;
        }
        $sql .= " ORDER BY lc_seq ASC";
        $res = $this->db->query($sql, $bind_array);
        $data = null;

        foreach($res->result_array() as $row) {
            $data[$row['lc_t_name']][] = $row;
        }
        return $data;
    }

    public function getLeaguePlayInfo($league_data = "", $tier_chart = null, $tier_name = "") {
        if (!$league_data) {
            $league_data = $this->getWorkingLeague();
        }
        $sql = "SELECT MAX(pi_xscore) AS pi_x, u_nick, ppi.* from pr_playinfo as ppi
        inner join pr_users on pi_u_seq = u_seq
        inner join pr_charts on pi_c_seq = c_seq
        inner join pr_songs on c_s_seq = s_seq
        INNER JOIN al_charts ON lc_c_seq = c_seq
        WHERE pi_status = 'Active' AND lc_li_season = ? AND lc_li_degree = ? AND pi_createtime BETWEEN ? AND ? AND pi_enable = 1";
        if ($tier_chart) {
            $where_chart = " AND (";
            $where_array = array();
            foreach ($tier_chart as $tc_array) {
                foreach ($tc_array as $tc_row) {
                    if ($tc_row['lc_c_seq']) {
                        $where_array[] = "(c_seq = '{$tc_row['lc_c_seq']}'" . ($tc_row['use_hj'] ? " AND pi_judge = 'HJ')" : ")");
                    }
                }
            }
            $where_array = implode(" OR ", $where_array);
            $where_chart .= $where_array . ")";
        }
        $sql .= $where_chart . " GROUP BY pi_u_seq, pi_c_seq ORDER BY u_mmr DESC, pi_c_seq ASC";
        $bind_array = array($league_data['li_season'], $league_data['li_degree'], $league_data['li_starttime'], $league_data['li_endtime']);
        $res = $this->db->query($sql, $bind_array);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[$row['u_nick']][$row['pi_c_seq']] = $row;
        }
        return $data;
    }
}
?>