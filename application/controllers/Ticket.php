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
		$this->load->view('ticket/calendar', $view_data);
	}
	public function studio()
	{
		$year = $this->input->get_post('y') ?? null;
		$month = $this->input->get_post('m') ?? null;
		$day = $this->input->get_post('d') ?? null;

		$date = date("Y-m-d", strtotime("{$year}-{$month}-{$day}"));
		if (!($year && $month && $day)) {
			alert("날짜를 정확히 입력해주세요.");
		}
		$resv_data = $this->ticket_model->getReservationInfo($year, $month, $day, $machines);
		$view_data = array(
			'year' => $year,
			'month' => $month,
			'day' => $day,
			'resv_data' => $resv_data,
		);
		$this->load->view('ticket/studio', $view_data);
	}
	public function getReservation() {
		$machines = $this->input->post_get('machines');
		$year = $this->input->post_get('year') ?? null;
		$month = $this->input->post_get('month') ?? null;
		$day = $this->input->post_get('day') ?? null;
		$resv_data = null;
		if ($machines) {
			$resv_data = $this->ticket_model->getReservationInfo($year, $month, $day, $machines);
		}
		echo json_encode($resv_data);
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