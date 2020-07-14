<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public $piData;

	function __construct() {
		parent::__construct();
        $this->load->model('admin_model');
        $this->load->model('account_model');
        $this->load->model('playinfo_model');
        $pi_data = null;
    }

    public function playinfo_super() {
        if ($this->check_super()) {
            $this->piData = $this->playinfo_model->getPlayinfo();
            $this->load->view('admin/admin_playinfo');
        } else {
            alert("권한이 없습니다.");
        }
    }

    public function playinfo() {
        if ($this->check_admin()) {
            $this->piData = $this->playinfo_model->getPlayinfo(PI_STATUS_WAITING);
            $this->load->view('admin/admin_playinfo');
        }
    }
    public function aevileague() {
        if ($this->check_super()) {
            $this->load->view('admin/admin_aevileague');
        } else {
            alert("준비중입니다.");
        }
    }
    public function appr_id($u_id = "") {
        if ($this->check_super() && $u_id) {
            $sql = "UPDATE pr_users SET u_status = 'ACTIVE' where u_id = ?";
            $res = $this->db->query($sql, array($u_id));
            if ($res) {
                alert("성공");
            }
        } else {
            alert("권한이 없습니다.");
        }
    }
    public function deny_id($u_id = "") {
        if ($this->check_super() && $u_id) {
            $sql = "UPDATE pr_users SET pi_status = 'DISABLED' where u_id = ?";
            $res = $this->db->query($sql, array($u_id));
            if ($res) {
                alert("성공");
            }
        } else {
            alert("권한이 없습니다.");
        }
    }

    public function pi_approve() {
        if ($this->check_admin()) {
            $u_id = $this->session->u_id ? $this->session->u_id : "UNKNOWN";
            $pi_data = array(
                'pi_status' => PI_STATUS_ACTIVE,
                'pi_update' => false,
                'u_seq' => $this->input->post('u_seq'),
                'pi_seq' => $this->input->post('pi_seq'),
                'pi_comment' => $this->input->post('pi_comment'),
                'pi_cfrm_u_id' => $u_id,
            );
            if ($this->admin_model->setPlayinfo($pi_data)) {
                alert("승인을 완료하였습니다.");
            }
        }
    }
    public function pi_update() {
        if ($this->check_admin()) {
            $u_id = $this->session->u_id ? $this->session->u_id : "UNKNOWN";
            $pi_data = array(
                'pi_status' => PI_STATUS_ACTIVE,
                'pi_update' => true,
                'u_seq' => $this->input->post('u_seq'),
                'pi_seq' => $this->input->post('pi_seq'),
                'pi_level' => $this->input->post('pi_level'),
                'pi_grade' => $this->input->post('pi_grade'),
                'pi_break' => $this->input->post('pi_break'),
                'pi_judge' => $this->input->post('pi_judge'),
                'pi_perfect' => $this->input->post('pi_perfect'),
                'pi_great' => $this->input->post('pi_great'),
                'pi_good' => $this->input->post('pi_good'),
                'pi_bad' => $this->input->post('pi_bad'),
                'pi_miss' => $this->input->post('pi_miss'),
                'pi_maxcom' => $this->input->post('pi_maxcom'),
                'pi_score' => $this->input->post('pi_score'),
                'pi_comment' => $this->input->post('pi_comment'),
                'pi_cfrm_u_id' => $u_id,
            );
            if ($this->admin_model->setPlayinfo($pi_data)) {
                alert("수정 후 승인을 완료하였습니다.");
            }
        }
    }
    public function pi_reject() {
        if ($this->check_admin()) {
            $u_id = $this->session->u_id ? $this->session->u_id : "UNKNOWN";
            $pi_data = array(
                'pi_status' => PI_STATUS_DENIED,
                'pi_seq' => $this->input->post('pi_seq'),
                'pi_comment' => $this->input->post('pi_comment'),
                'pi_cfrm_u_id' => $u_id,
            );
            if ($this->admin_model->setPlayinfo($pi_data)) {
                alert("거부를 완료하였습니다.");
            }
        }
    }

    private function check_admin() {
        if (isset($this->session->u_class) && $this->session->u_class <= 2) {
            return true;
        } else {
            alert("권한이 없습니다.");
            return false;
        }
    }
    private function check_super() {
        if (isset($this->session->u_class) && $this->session->u_class == 1) {
            return true;
        } else {
            return false;
        }
    }
}
?>