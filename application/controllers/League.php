<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League extends CI_Controller {
	public $userdata;

	public function index()
	{
	}
	public function aevileague()
	{
		$this->load->view('league/aevileague');
    }
}
?>