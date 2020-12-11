<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket extends CI_Controller {
	function __construct() {
		parent::__construct();
        $this->load->model('account_model');
        $this->load->model('ticket_model');
    }

	public function index()
	{
		$year = $this->input->get_post('y') ?? date('Y');
		$month = $this->input->get_post('m') ?? date('m');
		$view_data = array(
			'year' => $year,
			'month' => $month,
		);
		$this->load->view('ticket/studio', $view_data);
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