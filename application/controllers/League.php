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
		$arr_data = array(
			"tier_data" => $this->league_model->getTierData(),
			"league_data" => $this->league_model->getWorkingLeagueData(),
		);
		$this->load->view('league/aevileague', $arr_data);
    }
}
?>