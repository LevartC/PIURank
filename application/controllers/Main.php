<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('account_model');
    }

	public function index() {
		$user_data = null;
		if (isset($this->session->u_id)) {
		$user_data = $this->account_model->getUserData($this->session->u_id);
		}
		$playinfo_cnt = $this->account_model->getPlayinfoCount($this->session->u_id);
		
		$arr_data = array(
			'user_data' => $user_data,
			'playinfo_cnt' => $playinfo_cnt,
		);
		$this->load->view('main/main', $arr_data);
	}
}
?>