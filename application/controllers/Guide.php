<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guide extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('account_model');
    }

	public function road() {
		$this->load->view('guide/road');
	}
}
?>