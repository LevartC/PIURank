<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	function __construct() {
		parent::__construct();
//		$this->load->model('admin_model');
    }
    
    function playinfo() {
        if (isset($this->session->u_class) && $this->session->u_class <= 2) {
            $this->load->view('admin/playinfo_admin');
        } else {
            alert("권한이 없습니다.");
        }
    }
}
?>