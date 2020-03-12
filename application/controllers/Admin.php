<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public $pi_data;

	function __construct() {
		parent::__construct();
        $this->load->model('admin_model');
        $pi_data = null;
    }
    
    public function playinfo() {
        if (isset($this->session->u_class) && $this->session->u_class <= 2) {
            $this->pi_data = $this->admin_model->getWaitingPlayinfo();
            $this->load->view('admin/admin_playinfo');
        } else {
            alert("권한이 없습니다.");
        }
    }
    public function pi_approve() {
        $pi_data = array(
            'pi_status' => 1,
            'pi_seq' => $this->input->post('pi_seq'),
            'pi_comment' => $this->input->post('pi_comment'),
        );
        if ($this->admin_model->setPlayinfo($pi_data)) {
            alert("수정을 완료하였습니다.");
        }
    }
    public function pi_update() {
        $pi_data = array(
            'pi_status' => 1,
            'pi_update' => true,
            'pi_seq' => $this->input->post('pi_seq'),
            'pi_grade' => $this->input->post('pi_grade'),
            'pi_judge' => $this->input->post('pi_judge'),
            'pi_break' => $this->input->post('pi_break'),
            'pi_score' => $this->input->post('pi_score'),
            'pi_perfect' => $this->input->post('pi_perfect'),
            'pi_great' => $this->input->post('pi_great'),
            'pi_good' => $this->input->post('pi_good'),
            'pi_bad' => $this->input->post('pi_bad'),
            'pi_miss' => $this->input->post('pi_miss'),
            'pi_maxcom' => $this->input->post('pi_maxcom'),
            'pi_comment' => $this->input->post('pi_comment'),
        );
        if ($this->admin_model->setPlayinfo($pi_data)) {
            alert("수정을 완료하였습니다.");
        }
    }
    public function pi_reject() {
        $pi_data = array(
            'pi_status' => 0,
            'pi_seq' => $this->input->post('pi_seq'),
            'pi_comment' => $this->input->post('pi_comment'),
        );
        if ($this->admin_model->setPlayinfo($pi_data)) {
            alert("수정을 완료하였습니다.");
        }
    }
}
?>