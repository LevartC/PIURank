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
		if ($this->check_admin()) {
			$tier_data = $this->league_model->getTierData();
			$league_data = $this->league_model->getWorkingLeague();
			$league_chartdata = $this->league_model->getLeagueChartData();
			$league_userdata = $this->league_model->getLeagueUserData();
			if ($league_data) {
				$league_playdata = $this->league_model->getLeaguePlayInfo($league_data);
			} else {
				$league_playdata = null;
			}

			$arr_data = array(
				"tier_data" => $tier_data,
				"league_data" => $league_data,
				"league_chartdata" => $league_chartdata,
				"league_userdata" => $league_userdata,
				"league_playdata" => $league_playdata,
			);
			$this->load->view('league/aevileague', $arr_data);
		}
	}
	
	private function check_admin() {
		if (isset($this->session->u_class) && $this->session->u_class <= 2) {
			return true;
		} else {
			alert("준비중입니다.");
			return false;
		}
	}
}
?>