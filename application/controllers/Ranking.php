<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ranking extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('playinfo_model');
		$this->load->model('account_model');
    }

	public function index()
	{
	}
	public function total()
	{
		$this->load->view('ranking/total');
	}
	public function song()
	{
		
		$this->load->view('ranking/song');
	}
}
?>