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
    
    public function playinfo() {
        if ($this->check_admin()) {
            $this->piData = $this->playinfo_model->getPlayinfo(null);
            $this->load->view('admin/admin_playinfo');
        }
    }
    public function aevileague() {
        if ($this->check_admin()) {
            $this->load->view('admin/admin_aevileague');
        }
    }
    public function pi_approve() {
        if ($this->check_admin()) {
            $pi_data = array(
                'pi_status' => 1,
                'pi_update' => false,
                'pi_seq' => $this->input->post('pi_seq'),
                'pi_comment' => $this->input->post('pi_comment'),
            );
            if ($this->admin_model->setPlayinfo($pi_data)) {
                alert("승인을 완료하였습니다.");
            }
        }
    }
    public function pi_update() {
        if ($this->check_admin()) {
            $pi_data = array(
                'pi_status' => 1,
                'pi_update' => true,
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
            );
            if ($this->admin_model->setPlayinfo($pi_data)) {
                alert("수정 후 승인을 완료하였습니다.");
            }
        }
    }
    public function pi_reject() {
        if ($this->check_admin()) {
            $pi_data = array(
                'pi_status' => 0,
                'pi_seq' => $this->input->post('pi_seq'),
                'pi_comment' => $this->input->post('pi_comment'),
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
}
?>