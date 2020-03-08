<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public $pi_data;

	function __construct() {
		parent::__construct();
        $this->load->model('admin_model');
        $pi_data = null;
    }
    
    function playinfo() {
        if (isset($this->session->u_class) && $this->session->u_class <= 2) {
            $this->pi_data = $this->admin_model->getWaitingPlayinfo();
            $this->load->view('admin/playinfo_admin');
        } else {
            alert("권한이 없습니다.");
        }
    }
}
?>