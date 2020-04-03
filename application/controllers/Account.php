<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
    public $userData;
	function __construct() {
		parent::__construct();
		$this->load->model('account_model');
    }
    
    public function register() {
        if ($this->check_login()) {
            alert("이미 로그인되어 있습니다.");
        } else {
            $this->load->view('account/register');
        }
    }

    public function profile() {
        if ($this->check_login()) {
            $this->userData = $this->account_model->getUserData($this->session->u_seq);
            $this->load->view('account/profile');
        } else {
            alert("로그인 정보가 없습니다.");
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect("http://".$_SERVER['SERVER_NAME']);
    }

    public function forgot_password() {
        $this->load->view('account/forgot_password');
    }

    private function check_login() {
        if ($this->session->u_seq) {
            return true;
        } else {
            return false;
        }
    }
    public function check_id() {
        $reg_id = $this->input->post('reg_id');
        $this->account_model->check_id($reg_id);
        
    }
    public function check_nick() {
        $reg_nick = $this->input->post('reg_nick');
        $this->account_model->check_nick($reg_nick);
    }
    public function login_action() {
        $login_id = $this->input->post('login_id');
        $login_pw = $this->input->post('login_pw');
        if ($this->account_model->login_action($login_id, $login_pw)) {
            redirect("http://".$_SERVER['SERVER_NAME']);
        }
    }
    public function register_action() {
        $reg_id = $this->input->post('reg_id');
        $reg_nick = $this->input->post('reg_nick');
        $reg_pw = $this->input->post('reg_pw');
        $reg_email = $this->input->post('reg_email');
        if ($reg_id && $reg_nick && $reg_pw && $reg_email) {
            if ($this->account_model->register_action($reg_id, $reg_nick, $reg_pw, $reg_email)) {
                alert('회원 가입에 성공하였습니다.\\r\\n이제 해당 아이디로 로그인이 가능합니다.', "http://".$_SERVER['SERVER_NAME']."/main");
            } else {
                alert('회원 가입에 실패하였습니다.\\r\\n관리자에게 문의하세요.');
            }
        }
    }
}
?>