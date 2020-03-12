<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playinfo extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('playinfo_model');
		$this->load->model('account_model');
    }

	public function write() {
		$this->load->view('playinfo/write');
	}
	public function write_action() {
		if (isset($this->session->u_seq)) {
			$pi_file = $_FILES['pi_file'];
			$pi_data = array(
				'u_seq' => $this->session->u_seq,
				'c_seq' => $this->input->post('pi_c_seq'),
				'pi_level' => $this->input->post('pi_level'),
				'pi_level' => $this->input->post('pi_grade'),
				'pi_level' => $this->input->post('pi_break'),
				'pi_level' => $this->input->post('pi_judge'),
				'pi_level' => $this->input->post('pi_perfect'),
				'pi_level' => $this->input->post('pi_great'),
				'pi_level' => $this->input->post('pi_good'),
				'pi_level' => $this->input->post('pi_bad'),
				'pi_level' => $this->input->post('pi_miss'),
				'pi_level' => $this->input->post('pi_maxcom'),
				'pi_level' => $this->input->post('pi_score'),
			);
			$this->playinfo_model->writeAction($pi_file);
		} else {
			alert("로그인 정보가 존재하지 않습니다.");
		}
	}

	public function searchFile() {
		header("Content-Type: application/json");
		$c_title = $this->input->post('c_title');
		$this->playinfo_model->searchFile($c_title);
	}

}
?>