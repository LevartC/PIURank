<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    public function getLeagueInfo($season = 0, $degree = 0) {
        if ($season && $degree) {
            $sql = "SELECT * FROM al_info WHERE li_season = ? and li_degree = ?";
            $bind_array = array($season, $degree);
            $res = $this->db->query($sql, $bind_array);
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
            return $data;
        } else {
            $sql = "SELECT * FROM al_info";
            $res = $this->db->query($sql);
            $data = null;
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getWorkingLeague() {
        $sql = "SELECT * FROM al_info WHERE sysdate() between li_starttime AND li_endtime ORDER BY li_endtime DESC LIMIT 1";
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

    public function getLeagueUserData($league_data = "", $tier_name = "", $attend = 1) {
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
        if ($attend) {
            $where_tier = "  AND ls_attend = ?";
            $sql .= $where_tier;
            $bind_array[] = $attend;
        }
        $sql .= " ORDER BY ls_mmr DESC";
        $res = $this->db->query($sql, $bind_array);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[$row['u_nick']] = $row;
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

    public function getLeaguePlayInfo($league_data = "", $tier_charts = null, $tier_name = "", $tier_userdata) {
        if (!$league_data) {
            $league_data = $this->getWorkingLeague();
        }
        $sql = "SELECT MAX(pi_xscore) AS pi_x, u_nick, ppi.* from pr_playinfo as ppi
        inner join pr_users on pi_u_seq = u_seq
        inner join pr_charts on pi_c_seq = c_seq
        inner join pr_songs on c_s_seq = s_seq
        INNER JOIN al_charts ON lc_c_seq = c_seq
        WHERE pi_status = 'Active' AND lc_li_season = ? AND lc_li_degree = ? AND pi_createtime BETWEEN ? AND ? AND pi_enable = 1";
        if ($tier_name) {
            $sql .= " AND pi_u_seq IN (SELECT ls_u_seq FROM al_mmr WHERE ls_li_season = lc_li_season AND lc_li_degree = lc_li_degree AND ls_tier = ?)";
        }
        if ($tier_charts) {
            $where_chart = " AND (";
            $where_array = array();
            foreach ($tier_charts as $tc_array) {
                foreach ($tc_array as $tc_row) {
                    if ($tc_row['lc_c_seq']) {
                        $where_array[] = "(c_seq = '{$tc_row['lc_c_seq']}'" . ($tc_row['use_hj'] ? " AND pi_judge = 'HJ')" : ")");
                    }
                }
            }
            $where_array = implode(" OR ", $where_array);
            $where_chart .= $where_array . ")";
        }
        $sql .= $where_chart . " GROUP BY pi_u_seq, pi_c_seq ORDER BY pi_c_seq ASC, pi_x DESC, u_nick ASC";
        $bind_array = array($league_data['li_season'], $league_data['li_degree'], $league_data['li_starttime'], $league_data['li_endtime']);
        if ($tier_name) {
            array_push($bind_array, $tier_name);
        }
        $res = $this->db->query($sql, $bind_array);
        $data = null;
        $xscore = null;
        $user_cnt = count($tier_userdata);
        foreach($res->result_array() as $row) {
            $data[$row['pi_c_seq']][$row['u_nick']] = $row;
            $xscore[$row['pi_c_seq']][$row['u_nick']] = $row['pi_x'];
        }
        // 포인트 계산
        foreach($xscore as $c_seq => $c_array) {
            $i = 0;
            $tie_score = 0;
            $tie_point = 0;
            foreach($c_array as $u_nick => $x_value) {
                if ($tie_score) {
                    $data[$c_seq][$u_nick]['point'] = $tie_point;
                } else {
                    $tie_score = count(array_keys($c_array, $x_value));
                    if ($tie_score === 1) {
                        $point = $user_cnt - $i;
                        $data[$c_seq][$u_nick]['point'] = $point;
                    } else {
                        $point = $user_cnt - $i;
                        // 평균 계산
                        $tie_point = 0;
                        for ($x = 0; $x < $tie_score; ++$x) {
                            $tie_point += $point - $x;
                        }
                        $tie_point = $tie_point / $tie_score;
                        $data[$c_seq][$u_nick]['point'] = $tie_point;
                    }
                }
                $tie_score--;
                $i++;
            }
        }
        return $data;
    }

    public function setNextLeagueMMR($prev_league_data, $next_league_data, $mmr_result, $point_result, $avoider_mmr, $avoider_tier_name) {
        $sql = "REPLACE INTO al_mmr(ls_li_season, ls_li_degree, ls_u_seq, ls_mmr, ls_point) VALUES";
        $values_array = null;
        $bind_array = null;
        foreach($mmr_result as $mmr_u_seq => $mmr_value) {
            $values_array[] = "(?,?,?,?,?)";
            $bind_array[] = $next_league_data['li_season'];
            $bind_array[] = $next_league_data['li_degree'];
            $bind_array[] = $mmr_u_seq;
            $bind_array[] = $mmr_value;
            $bind_array[] = $point_result[$mmr_u_seq];
        }
        $values_str = implode(",", $values_array);
        $sql .= $values_str;
        if ($this->db->query($sql, $bind_array)) {
            $avoider_mmr = $avoider_mmr < 0 ? -$avoider_mmr : 0;
            $sql = "REPLACE INTO al_mmr(ls_li_season, ls_li_degree, ls_u_seq, ls_mmr)
            SELECT ?, ?, ls_u_seq, (ls_mmr-{$avoider_mmr}) as ls_mmr FROM al_mmr
            WHERE ls_li_season = ? AND ls_li_degree = ? AND ls_tier = ? AND ls_attend = 0";
            $bind_array = array($next_league_data['li_season'], $next_league_data['li_degree'], $prev_league_data['li_season'], $prev_league_data['li_degree'], $avoider_tier_name);
            if ($this->db->query($sql, $bind_array)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setTierMMR($prev_league_data, $next_league_data) {
        $sql = "UPDATE al_mmr, al_tier SET ls_tier = t_name WHERE ls_mmr > t_min_mmr AND ls_mmr <= t_max_mmr";
        if ($this->db->query($sql)) {
            $sql = "UPDATE al_mmr AS a, (SELECT ls_u_seq, ls_point FROM al_mmr WHERE ls_li_season = ? AND ls_li_degree = ?) AS b SET a.ls_point = b.ls_point WHERE a.ls_li_season = ? AND a.ls_li_degree = ? AND a.ls_u_seq = b.ls_u_seq";
            $bind_array = array($next_league_data['li_season'], $next_league_data['li_degree'], $prev_league_data['li_season'], $prev_league_data['li_degree']);
            if ($this->db->query($sql, $bind_array)) {
                $sql = "UPDATE al_mmr SET ls_point = 0 WHERE ls_li_season = ? AND ls_li_degree = ?";
                $bind_array = array($next_league_data['li_season'], $next_league_data['li_degree']);
                if ($this->db->query($sql, $bind_array)) {
                    return true;
                }
            }
        }
        return false;
    }

}
?>