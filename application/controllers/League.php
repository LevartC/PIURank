<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("league_model");
		$this->load->model("account_model");
    }

	public function index()
	{
	}
	public function aevileague()
	{
		$current_league = explode('-', $this->input->get_post('league'));
		$current_season = $current_league[0] ?? null;
		$current_degree = $current_league[1] ?? null;
		$current_tier = $this->input->get_post('tier');

		$tier_data = $this->league_model->getTierData();
		$all_league_data = $this->league_model->getAllLeague();
		if ($current_season && $current_degree) {
			$league_data = $this->league_model->getLeagueInfo($current_season, $current_degree);
		} else {
			$league_data = $this->league_model->getWorkingLeague();
		}
		$prev_league_data = null;
		$league_userdata = null;
		$prev_userdata = null;
		$league_chartdata = null;
		$league_playdata = null;
		if ($league_data) {
			if ($current_tier && $current_tier != 'Overview') {
				$league_userdata = $this->league_model->getLeagueUserData($league_data, $current_tier);
				$league_chartdata = $this->league_model->getLeagueChartData($league_data, $current_tier);
				if ($league_userdata && $league_chartdata) {
					$league_playdata = $this->league_model->getLeaguePlayInfo($league_data, $league_chartdata, $current_tier, $league_userdata);
				} else {
					alert("유저 및 차트 정보가 존재하지 않습니다.");
				}
			} else {
				$league_userdata = $this->league_model->getLeagueUserData($league_data);
				if ($league_data['li_degree'] > '1') {
					$prev_league_data = $this->league_model->getLeagueInfo($league_data['li_season'], ($league_data['li_degree']-1));
					$prev_userdata = $this->league_model->getLeagueUserData($prev_league_data, 0, 0);
				}
			}
		}

		$arr_data = array(
			"current_tier" => $current_tier,
			"tier_data" => $tier_data,
			"league_data" => $league_data,
			"prev_league_data" => $prev_league_data,
			"league_userdata" => $league_userdata,
			"prev_userdata" => $prev_userdata,
			"league_chartdata" => $league_chartdata,
			"league_playdata" => $league_playdata,
			"all_league_data" => $all_league_data,
		);
		$this->load->view('league/aevileague', $arr_data);
	}

	public function super_menu() {
		if ($this->check_super()) {
			$tier_data = $this->league_model->getTierData();
			$arr_data = array(
				"tier_data" => $tier_data,
			);
			$this->load->view('league/super_menu', $arr_data);
		} else {
			alert("권한이 없습니다.");
		}
	}

	public function add_chart() {
		if ($this->check_super()) {
			$season = $this->input->post('al_li_season');
			$degree = $this->input->post('al_li_degree');
			$tier = $this->input->post('tier');
			$c_seq = $this->input->post('al_c_seq');
			$use_hj = $this->input->post('al_usehj') ?? 0;
			if ($season && $degree && $tier && $c_seq) {
				if ($this->league_model->setLeagueChart($season, $degree, $tier, $c_seq, $use_hj)) {
					alert("차트 입력에 성공하였습니다.");
				} else {
					alert("차트 입력에 실패하였습니다.\n관리자에게 문의하세요.");
				}
			} else {
				alert("입력값이 정확하지 않습니다.");
			}
		} else {
			alert("권한이 없습니다.");
		}
	}

	public function cleanup_match() {
		if ($this->check_super()) {
			$season = $this->input->post('li_season') ? (int)$this->input->post('li_season') : 1;
			$degree = $this->input->post('li_degree') ? (int)$this->input->post('li_degree') : 2;
			$tier_data = $this->league_model->getTierData();
			$league_data = $this->league_model->getLeagueInfo($season, $degree);
			foreach($tier_data as $tier_row) {
				$tier_chartdata = $this->league_model->getLeagueChartData($league_data, $tier_row['t_name']);
				// 불참인원 확인 및 제외
				$this->updateAvoidUser($league_data, $tier_chartdata, $tier_row['t_name']);
				$tier_userdata = $this->league_model->getLeagueUserData($league_data, $tier_row['t_name']);
				$tier_playdata = $this->league_model->getLeaguePlayInfo($league_data, $tier_chartdata, $tier_row['t_name'], $tier_userdata);
				$total_points = null;
				// 포인트 집계
				foreach($tier_playdata as $c_array) {
					foreach($c_array as $u_nick => $pi_row) {
						if (isset($total_points[$u_nick])) {
							$total_points[$u_nick] += $pi_row['point'];
						} else {
							$total_points[$u_nick] = $pi_row['point'];
						}
					}
				}
			// MMR = sqrt(총참가자/10) * ( (변동계수 * (꼴찌 - (2 * (참가순위 - 0.5))) / 꼴찌 ) + ((MMR평균 - 참가MMR) / 7.5) + 팽창 상수
				// 포인트 랭킹 인덱스 생성
				$rank_array = array_unique(array_values($total_points));
				rsort($rank_array);
				$rank_index = null;
				foreach($rank_array as $rank_key => $rank_value) {
					$rank_index["{$rank_value}"] = $rank_key+1;
				}

				// MMR 평균 계산
				$mmr_avg = 0;
				foreach($tier_userdata as $user_row) {
					$mmr_avg += $user_row['ls_mmr'];
				}
				$lowest_rank = count($rank_array);			// 꼴등
				$user_cnt = count($tier_userdata);			// 총 참가자
				$mmr_avg = $mmr_avg / $user_cnt;			// MMR 평균
				$exp_con = $tier_row['t_exp_con'];			// 팽창 상수
				$deg_vc = $tier_row['t_deg_vc'];			// 변동 계수
				$mmr_result = null;
				$point_result = null;
				$match_mmr = 0;
				$avoider_mmr = 0;
				// 유저별 최종 MMR 계산
				foreach($tier_userdata as $user_row) {
					$user_mmr = $user_row['ls_mmr'];		// 참가자 MMR
					// 참가자 포인트 순위
					$user_index = $total_points[$user_row['u_nick']] ?? null;
					$user_rank = $user_index ? $rank_index["{$user_index}"] : $lowest_rank;
					$match_mmr = (sqrt($user_cnt / 10)) * ($deg_vc * ($lowest_rank - (2 * ($user_rank - 0.5))) / $lowest_rank) + (($mmr_avg - $user_mmr) / 7.5) + $exp_con;
					$user_seq = $this->account_model->getUserSeq($user_row['u_nick']);
					$mmr_result[$user_seq] = ceil($user_mmr + $match_mmr);
					$point_result[$user_seq] = $total_points[$user_row['u_nick']];
					if ($rank_array[$lowest_rank-1] == $total_points[$user_row['u_nick']]) {
						$avoider_mmr = (int)$match_mmr;
					}
				}
				$next_league_data = array('li_season' => $season, 'li_degree' => $degree+1);
				$this->league_model->setNextLeagueMMR($league_data, $next_league_data, $mmr_result, $point_result, $avoider_mmr, $tier_row['t_name']);
			}
			if ($this->league_model->setTierMMR($league_data, $next_league_data)) {
				alert("성공");
				return true;
			} else {
				return false;
			}
		} else {
			alert("권한이 없습니다.");
		}
	}

	// 불참인원 갱신 쿼리
	private function updateAvoidUser($league_data, $tier_charts, $tier_name) {
        $sql = "UPDATE al_mmr SET ls_attend = 0 WHERE ls_tier = ? and ls_u_seq NOT IN
			(SELECT distinct(u_seq) from pr_playinfo as ppi
			inner join pr_users on pi_u_seq = u_seq
			inner join pr_charts on pi_c_seq = c_seq
			INNER JOIN al_charts ON lc_c_seq = c_seq
			WHERE pi_status = 'Active' AND lc_li_season = ? AND lc_li_degree = ? AND pi_createtime BETWEEN ? AND ? AND pi_enable = 1";
		$where_chart = " AND (";
		$where_array = array();
		foreach ($tier_charts as $tc_array) {
			foreach ($tc_array as $tc_row) {
				if ($tc_row['lc_c_seq']) {
					$where_array[] = "(c_seq = '{$tc_row['lc_c_seq']}'" . ($tc_row['use_hj'] ? " AND pi_judge = 'HJ')" : ")");
				}
			}
		}
		$where_array = implode(" OR ", $where_array);
		$where_chart .= $where_array . "))";
        $sql .= $where_chart;
        $bind_array = array($tier_name, $league_data['li_season'], $league_data['li_degree'], $league_data['li_starttime'], $league_data['li_endtime']);
		$res = $this->db->query($sql, $bind_array);
		if ($res) {
			return true;
		} else {
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
	private function check_admin() {
		if (isset($this->session->u_class) && $this->session->u_class <= 2) {
			return true;
		} else {
			return false;
		}
	}
}
?>