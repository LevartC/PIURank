<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket extends CI_Controller {

	function __construct() {
		parent::__construct();
        $this->load->model('account_model');
        $this->load->model('ticket_model');
    }

	public function index()
	{
		$year = $this->input->get_post('y') ?? date('Y');
		$month = $this->input->get_post('m') ?? date('m');
		$view_data = array(
			'year' => $year,
			'month' => $month,
			'is_admin' => $this->ticket_model->check_studio(),
		);
		$this->load->view('ticket/calendar', $view_data);
	}
	public function studio()
	{
		$year = $this->input->get_post('y') ?? null;
		$month = $this->input->get_post('m') ?? null;
		$day = $this->input->get_post('d') ?? null;
		$is_admin = $this->ticket_model->check_studio();

		$time = strtotime("{$year}-{$month}-{$day}");
		if (!$is_admin) {
			if ($time < strtotime("-1 days")) {
				alert("과거 시간대 예약은 할 수 없습니다.");
			}
			if ($time > strtotime("+14 days")) {
				alert("예약은 2주 후까지 가능합니다.");
			}
		}
		$year = date("Y", $time);
		$month = date("n", $time);
		$day = date("j", $time);
		$date = date("Y-m-d", $time);
		if (!($year && $month && $day)) {
			alert("날짜를 정확히 입력해주세요.");
		}
		$view_data = array(
			'year' => $year,
			'month' => $month,
			'day' => $day,
			'date' => $date,
			'time' => $time,
			'is_admin' => $is_admin,
		);
		$this->load->view('ticket/studio', $view_data);
	}

	public function getReservation()
	{
		$machines = $this->input->post_get('machines');
		$year = $this->input->post_get('year') ?? null;
		$month = $this->input->post_get('month') ?? null;
		$day = $this->input->post_get('day') ?? null;
		$resv_data = null;
		if ($machines && $year && $month && $day) {
			$resv_data = $this->ticket_model->getReservationInfo($year, $month, $day, $machines);
		}
		echo json_encode($resv_data);
	}

	public function getPrice()
	{
		$machines = $this->input->post_get('machines') ?? null;
		$year = $this->input->post_get('year') ?? null;
		$month = $this->input->post_get('month') ?? null;
		$day = $this->input->post_get('day') ?? null;
		$start_idx = $this->input->post_get('start_idx') ?? null;
		$end_idx = $this->input->post_get('end_idx') ?? null;
		if ($machines) {
			$date = date("Y-m-d", strtotime("{$year}-{$month}-{$day}"));
			$price_data = $this->ticket_model->getPrice($machines, $date, $start_idx, $end_idx);
			echo json_encode($price_data);
		} else {
			echo json_encode(null);
		}
	}
	public function setTicket()
	{
		$machines = $this->input->post_get('machines') ?? null;
		$year = $this->input->post_get('year') ?? null;
		$month = $this->input->post_get('month') ?? null;
		$day = $this->input->post_get('day') ?? null;
		$tc_name = $this->input->post_get('tc_name') ?? null;
		$tc_tel = $this->input->post_get('tc_tel') ?? null;
		$tc_email = $this->input->post_get('tc_email') ?? null;
		$tc_person = $this->input->post_get('tc_person') ?? null;
		$start_idx = $this->input->post_get('start_idx') ?? null;
		$end_idx = $this->input->post_get('end_idx') ?? null;
		if ($machines && $year && $month && $day && $tc_name && $tc_tel && $tc_email && $tc_person && $start_idx !== null && $end_idx !== null) {
			$u_id = $this->session->u_id ?? null;
			$date = date("Y-m-d", strtotime("{$year}-{$month}-{$day}"));
			$price_data = $this->ticket_model->getPrice($machines, $date, $start_idx, $end_idx);
			$tc_res = $this->ticket_model->insertTicket($machines, $u_id, $date, $start_idx, $end_idx, $tc_name, $tc_tel, $tc_email, $tc_person, $price_data);
			if ($tc_res) {
				$this->ticket_model->sendEmail($machines, $date, $start_idx, $end_idx, $tc_name, $tc_tel, $tc_email, $tc_person, $price_data);
				echo "Y";
			} else {
				echo "N";
			}
		} else {
			echo "필수 정보가 누락되었습니다.";
		}

	}

	public function searchTicket() {
		$tc_name = $this->input->post_get('tc_name') ?? null;
		$tc_tel = $this->input->post_get('tc_tel') ?? null;

		if ($tc_name && $tc_tel) {
			$ticket_str = $this->ticket_model->searchTicket($tc_name, $tc_tel);
			$res_str = null;
			if ($ticket_str) {
				foreach($ticket_str as $tc_row) {
					$res_str[] = $tc_row['mc_name'] . " ({$tc_row['tc_version']})<br>" . date("Y년 n월 j일 G시 부터", strtotime($tc_row['tc_starttime'])) . "<br>" . date("Y년 n월 j일 G시 까지", strtotime($tc_row['tc_endtime'])) . "<br>가격 : {$tc_row['tc_price']} 원 <span class=" . ($tc_row['tc_deposit'] ? "'text-primary'>(입금완료)" : "'text-secondary'>(입금대기)") . "</span>";
				}
			}
			echo json_encode($res_str);
		} else {
			echo "이름과 연락처를 올바르게 입력해주세요.";
		}
	}

	public function mailtest() {
		$this->load->library('PHPMailer_Lib');
		$mail = $this->phpmailer_lib->load();
		try {
			// 기본 설정
			$mail->SMTPDebug = 0;
			$mail->isSMTP();
			$mail->Host = "smtp.piurank.com";
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->item('mailer_id');
			$mail->Password = $this->config->item('mailer_pw');
			$mail->SMTPSecure = "ssl";
			$mail->Port = 465;
			$mail->CharSet = "utf-8";

			// 관리자에게 전송
			$mail->setFrom("ticket@piurank.com", "DIVISION STUDIO 관리자");
			$mail->addAddress("curicou@naver.com", "유저");
			$mail->addAddress("eodmalt@piurank.com", "WINDFORCE");
			$mail->isHTML(false); // HTML 태그 사용 여부
			$mail->Subject = "네이버 메일테스트";
			$mail->Body = "테스트입니다.";
			// 메일 전송
			$mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error : ", $mail->ErrorInfo;
        }
    }

}
?>