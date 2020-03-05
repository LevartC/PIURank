<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playinfo extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('playinfo_model');
    }

	public function write()
	{
		$this->load->view('playinfo/write');
	}

	public function search_file() {
		header("Content-Type: application/json");
		$c_title = $this->input->post('c_title');
		$this->playinfo_model->search_file($c_title);
	}
}
?>