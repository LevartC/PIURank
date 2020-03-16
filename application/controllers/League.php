<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League extends CI_Controller {
	public $userdata;

	public function index()
	{
		$this->load->model('account_model');
		if (isset($this->session->u_seq)) {
			$this->userdata = $this->account_model->getUserData($this->session->u_seq);
		}
		$this->load->view('main/main');
	}
	public function aevileague()
	{
		$this->load->view('league/aevileague');
    }
}
?>