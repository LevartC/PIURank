<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ranking extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('playinfo_model');
		$this->load->model('account_model');
		$this->load->model('ranking_model');
    }

	public function index()
	{
	}
	public function total()
	{
		$page = $this->input->post_get('page');
		$page_rows = 10;
		if ($page < 1) {
			$page = 1;
		}
		$cnt = $this->ranking_model->getRankingInfo(null, 0, 0)[0]['cnt'];
		$last_page = $cnt % $page_rows == 0 ? (int)($cnt / $page_rows) : (int)($cnt / $page_rows) + 1;
		if ($page > $last_page) {
			$page = $last_page;
		}
		$rank_info = $this->ranking_model->getRankingInfo(null, $page, $page_rows);
		$view_data = array(
			'page' => $page,
			'page_rows' => $page_rows,
			'last_page' => $last_page,
			'page_cnt' => $cnt,
			'rank_info' => $rank_info,
		);
		$this->load->view('ranking/total', $view_data);
	}
	public function song()
	{
		$this->load->view('ranking/song');
	}
}
?>