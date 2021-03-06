<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
    public $userData;
    public $piStatus;
    public $piData;
	function __construct() {
		parent::__construct();
		$this->load->model('account_model');
		$this->load->model('playinfo_model');
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
            $this->userData = $this->account_model->getUserData($this->session->u_id);
            $this->load->view('account/profile');
        } else {
            alert("로그인 정보가 없습니다.");
        }
    }

    public function myplay() {
        if ($this->check_login()) {
            $this->piStatus = PI_STATUS_ALL;
            if ($this->input->post('pi_status') !== null) {
                $this->piStatus = (int)$this->input->post('pi_status');
            }
            $cnt = $this->playinfo_model->getPlayinfoCount($this->piStatus, $this->session->u_id);
            $page = $this->input->post_get('page');
            $page_rows = 10;
            if ($page < 1) {
                $page = 1;
            }
            $last_page = $cnt % $page_rows == 0 ? (int)($cnt / $page_rows) : (int)($cnt / $page_rows) + 1;
            if ($page > $last_page) {
                $page = $last_page;
            }
            $this->piData = $this->playinfo_model->getPlayinfo($this->piStatus, $this->session->u_id, $page, $page_rows);
            $view_data = array(
                'page' => $page,
                'page_rows' => $page_rows,
                'last_page' => $last_page,
                'page_cnt' => $cnt,
            );
            $this->load->view('account/myplay', $view_data);
        } else {
            alert("로그인 정보가 없습니다.");
        }
    }
    public function get_playinfo() {
        if ($this->check_login()) {
            $page = (int)$this->input->get_post("page");
            $rows = (int)$this->input->get_post("rows");
            $cnt = $this->playinfo_model->getPlayinfoCount($this->piStatus, $this->session->u_id);
            if ($page < 1) {
                $page = 1;
            }
            $last_page = $cnt % $rows == 0 ? (int)($cnt / $rows) : (int)($cnt / $rows) + 1;
            if ($page > $last_page) {
                $page = $last_page;
            }
            $pi_data = $this->playinfo_model->getPlayinfo($this->piStatus, $this->session->u_id, $page, $rows);
            echo json_encode($pi_data);
        } else {
            echo "error : 로그인 정보가 없습니다.";
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect("http://".$_SERVER['SERVER_NAME']);
    }

    public function forgot_password() {
        alert("준비중입니다.");
        //$this->load->view('account/forgot_password');
    }

    public function prof_update() {
        if ($this->check_login()) {
            $updateUserData = array(
                'u_id' => $this->input->post('u_id'),
                'u_nick' => $this->input->post('u_nick'),
                'u_pw' => $this->input->post('u_pw'),
                'u_email' => $this->input->post('u_email'),
            );
            if ($this->session->u_id != $this->input->post('u_id')) {
                alert("현재 로그인 정보가 일치하지 않습니다.");
            } else {
                if ($this->account_model->setUserData($updateUserData)) {
                    alert("프로필 수정이 성공적으로 완료되었습니다.");
                    $this->load->view('account/profile');
                } else {
                    alert("프로필 수정에 실패하였습니다. 관리자에게 문의해주세요.");
                }
            }
        } else {
            alert("로그인 정보가 없습니다.");
        }
    }

    private function check_login() {
        if ($this->session->u_id) {
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
                alert('회원 가입에 성공하였습니다.\\r\\n관리자에게 가입 승인을 요청해주세요.', "http://".$_SERVER['SERVER_NAME']."/main");
            } else {
                alert('회원 가입에 실패하였습니다.\\r\\n관리자에게 문의해주세요.');
            }
        } else {
            alert('필수 항목이 입력되지 않았습니다.');
        }
    }
    public function pi_delete() {
        if ($this->check_login()) {
            $pi_seq = $this->input->get_post('num');
            $u_id = $this->session->u_id;
            if ($this->playinfo_model->deletePlayinfo($pi_seq, $u_id)) {
                echo "Y";
            } else {
                echo "N";
            }
        } else {
            echo "N";
        }
    }
}
?>