<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("league_model");
		$this->load->model("account_model");
    }

	public function index()
	{
	}
	public function aevileague()
	{
		$current_league = explode('-', $this->input->get_post('league'));
		$current_season = $current_league[0] ?? null;
		$current_degree = $current_league[1] ?? null;
		$current_tier = $this->input->get_post('tier');

		$tier_data = $this->league_model->getTierData();
		$all_league_data = $this->league_model->getAllLeague();
		if ($current_season && $current_degree) {
			$league_data = $this->league_model->getLeagueInfo($current_season, $current_degree);
		} else {
			$league_data = $this->league_model->getWorkingLeague();
		}
		$prev_league_data = null;
		$league_userdata = null;
		$prev_userdata = null;
		$league_chartdata = null;
		$league_playdata = null;
		if ($league_data) {
			if ($current_tier && $current_tier != 'Overview') {
				$league_userdata = $this->league_model->getLeagueUserData($league_data, $current_tier);
				$league_chartdata = $this->league_model->getLeagueChartData($league_data, $current_tier);
				if ($league_userdata && $league_chartdata) {
					$league_playdata = $this->league_model->getLeaguePlayInfo($league_data, $league_chartdata, $current_tier, $league_userdata);
				} else {
					alert("유저 및 차트 정보가 존재하지 않습니다.");
				}
			} else {
				$league_userdata = $this->league_model->getLeagueUserData($league_data);
				if ($league_data['li_degree'] > '1') {
					$prev_league_data = $this->league_model->getLeagueInfo($league_data['li_season'], ($league_data['li_degree']-1));
					$prev_userdata = $this->league_model->getLeagueUserData($prev_league_data, 0, 0);
				}
			}
		}

		$arr_data = array(
			"current_tier" => $current_tier,
			"tier_data" => $tier_data,
			"league_data" => $league_data,
			"prev_league_data" => $prev_league_data,
			"league_userdata" => $league_userdata,
			"prev_userdata" => $prev_userdata,
			"league_chartdata" => $league_chartdata,
			"league_playdata" => $league_playdata,
			"all_league_data" => $all_league_data,
		);
		$this->load->view('league/aevileague', $arr_data);
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