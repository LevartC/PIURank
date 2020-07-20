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
				$league_playdata = $this->league_model->getLeaguePlayInfo($league_data, $league_chartdata, $current_tier);
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