<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("league_model");
    }

	public function index()
	{
	}
	public function aevileague()
	{
		$current_tier = $this->input->get_post('al_tier');

		$tier_data = $this->league_model->getTierData();
		$league_data = $this->league_model->getWorkingLeague();
		$league_userdata = null;
		$league_chartdata = null;
		$league_playdata = null;
		if ($league_data) {
			if ($current_tier && $current_tier != 'Overview') {
				$league_userdata = $this->league_model->getLeagueUserData($league_data, $current_tier);
				$league_chartdata = $this->league_model->getLeagueChartData($league_data, $current_tier);
				$league_playdata = $this->league_model->getLeaguePlayInfo($league_data, $league_chartdata, $current_tier, $league_userdata);
			} else {
				$league_userdata = $this->league_model->getLeagueUserData($league_data);
			}
		}

		$arr_data = array(
			"current_tier" => $current_tier,
			"tier_data" => $tier_data,
			"league_data" => $league_data,
			"league_chartdata" => $league_chartdata,
			"league_userdata" => $league_userdata,
			"league_playdata" => $league_playdata,
		);
		$this->load->view('league/aevileague', $arr_data);
	}

	// 불참인원 갱신 쿼리
	private function updateAvoidUser($league_data, $tier_charts, $tier_name) {
        $sql = "UPDATE al_mmr SET ls_attend = 0 WHERE ls_tier = 'Gold' and ls_u_seq NOT IN
			(SELECT MAX(pi_xscore) AS pi_x, u_nick, ppi.* from pr_playinfo as ppi
			inner join pr_users on pi_u_seq = u_seq
			inner join pr_charts on pi_c_seq = c_seq
			inner join pr_songs on c_s_seq = s_seq
			INNER JOIN al_charts ON lc_c_seq = c_seq
			WHERE pi_status = 'Active' AND lc_li_season = ? AND lc_li_degree = ? AND pi_createtime BETWEEN ? AND ? AND pi_enable = 1";
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
		$where_chart .= $where_array . "))";
        $sql .= $where_chart;
        $bind_array = array($tier_name, $league_data['li_season'], $league_data['li_degree'], $league_data['li_starttime'], $league_data['li_endtime']);
		$res = $this->db->query($sql, $bind_array);
		if ($res) {
			return true;
		} else {
			return false;
		}
	}
	public function cleanup_match() {
		$tier_data = $this->league_model->getTierData();
		$league_data = $this->league_model->getWorkingLeague();
		foreach($tier_data as $tier_row) {
			$tier_chartdata = $this->league_model->getLeagueChartData($league_data, $tier_row['t_name']);
			// 불참인원 확인
			$this->updateAvoidUser($league_data, $tier_chartdata, $tier_row['t_name']);
			$tier_userdata = $this->league_model->getLeagueUserData($league_data, $tier_row['t_name']);
			$tier_playdata = $this->league_model->getLeaguePlayInfo($league_data, $tier_chartdata, $tier_row['t_name'], $tier_userdata);
			$total_points = null;
			foreach($tier_playdata as $c_seq => $c_array) {
				foreach($c_array as $u_nick => $pi_row) {
					if (isset($total_points[$u_nick])) {
						$total_points[$u_nick] += $pi_row['point'];
					} else {
						$total_points[$u_nick] = $pi_row['point'];
					}
				}
			}
		}
	}

	private function check_super() {
		if (isset($this->session->u_class) && $this->session->u_class == 1) {
			return true;
		} else {
			return false;
		}
	}
	private function check_admin() {
		if (isset($this->session->u_class) && $this->session->u_class <= 2) {
			return true;
		} else {
			return false;
		}
	}
}
?>