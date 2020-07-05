<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	public $userdata;

	function __construct() {
		parent::__construct();
		$this->load->model('account_model');
    }

	public function index() {
		if (isset($this->session->u_seq)) {
			$this->userdata = $this->account_model->getUserData($this->session->u_seq);
		}
		$this->load->view('main/main');
	}
}
?>