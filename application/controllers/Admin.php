<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public $piData;

	function __construct() {
		parent::__construct();
        $this->load->model('admin_model');
        $this->load->model('account_model');
        $this->load->model('playinfo_model');
        $this->load->model('league_model');
        $this->load->model('ticket_model');
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
            } else {
                alert("승인에 실패하였습니다. 관리자에게 문의해주세요.");
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
            } else {
                alert("수정 후 승인에 실패하였습니다. 관리자에게 문의해주세요.");
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
        } else {
            alert("거부에 실패하였습니다. 관리자에게 문의해주세요.");
        }
    }

    public function ticket_all() {
        if ($this->check_studio()) {
            $ticket_data = $this->ticket_model->getTicketInfo(true);
            $view_data = array(
                "ticket_data" => $ticket_data,
            );
            $this->load->view('admin/admin_ticketinfo', $view_data);
        }
    }
    public function ticket() {
        if ($this->check_studio()) {
            $ticket_data = $this->ticket_model->getTicketInfo();
            $view_data = array(
                "ticket_data" => $ticket_data,
            );
            $this->load->view('admin/admin_ticketinfo', $view_data);
        }
    }
    public function sales() {
        if ($this->check_studio()) {
            $cnt = $this->ticket_model->getSaleData(0, 0);
            $page = $this->input->post_get('page');
            $page_rows = 10;
            if ($page < 1) {
                $page = 1;
            }
            $last_page = $cnt % $page_rows == 0 ? (int)($cnt / $page_rows) : (int)($cnt / $page_rows) + 1;
            if ($page > $last_page) {
                $page = $last_page;
            }
            $sales_data = $this->ticket_model->getSaleData($page);
            $product_data = $this->ticket_model->getProductData();
            $view_data = array(
                'page' => $page,
                'page_rows' => $page_rows,
                'last_page' => $last_page,
                'page_cnt' => $cnt,
                "sales_data" => $sales_data,
                "product_data" => $product_data,
            );
            $this->load->view('admin/admin_sales', $view_data);
        }
    }
    public function insertSales() {
        if ($this->check_studio()) {
            $dp_seq = $this->input->post_get('dp_seq');
            $ds_name = $this->input->post_get('ds_name') ?? null;
            $ds_price = $this->input->post_get('ds_price') ?? null;
            $ds_memo = $this->input->post_get('ds_memo') ?? null;
            if ($ds_name && $ds_price !== null) {
                $res = $this->ticket_model->insertSales($ds_name, $ds_price, $dp_seq, $ds_memo);
                if ($res) {
                    echo "Y";
                    return;
                }
            }
        }
        echo "N";
    }
	public function delTicket() {
        if ($this->check_studio()) {
            $tc_seq = $this->input->post_get('seq') ?? null;
            if ($tc_seq) {
                $del_res = $this->ticket_model->deleteTicket($tc_seq);
                if ($del_res) {
                    echo "Y";
                    return;
                }
            }
        }
        echo "N";
	}
	public function setDeposit() {
        if ($this->check_studio()) {
            $tc_seq = $this->input->post_get('seq') ?? null;
            if ($tc_seq) {
                $set_res = $this->ticket_model->setDeposit($tc_seq);
                if ($set_res) {
                    echo "Y";
                    return;
                }
            }
        }
        echo "N";
	}
	public function setSentSms() {
        if ($this->check_studio()) {
            $tc_seq = $this->input->post_get('seq') ?? null;
            if ($tc_seq) {
                $set_res = $this->ticket_model->setSentSms($tc_seq);
                if ($set_res) {
                    echo "Y";
                    return;
                }
            }
        }
        echo "N";
	}

	public function super_menu() {
		if ($this->check_super()) {
			$tier_data = $this->league_model->getTierData();
			$arr_data = array(
				"tier_data" => $tier_data,
			);
			$this->load->view('admin/super_menu', $arr_data);
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

	public function update_avoid($season, $degree) {
        if (!($season >= 0 || $degree >= 0)) {
            alert("시즌과 차수를 정확히 입력해주세요.");
            return false;
        }
        $tier_data = $this->league_model->getTierData();
        $league_data = $this->league_model->getLeagueInfo($season, $degree);
        foreach($tier_data as $tier_row) {
            $tier_chartdata = $this->league_model->getLeagueChartData($league_data, $tier_row['t_name']);
            // 불참인원 확인 및 제외
            $this->updateAvoidUser($league_data, $tier_chartdata, $tier_row['t_name']);
        }
    }
    // 매치 갱신 후 적용
	public function cleanup_match() {
		if ($this->check_super()) {
			$season = (int)$this->input->post('li_season');
            $degree = (int)$this->input->post('li_degree');
            if (!($season >= 0 || $degree >= 0)) {
                alert("시즌과 차수를 정확히 입력해주세요.");
                return false;
            }
			$tier_data = $this->league_model->getTierData();
			$league_data = $this->league_model->getLeagueInfo($season, $degree);
			foreach($tier_data as $tier_row) {
				$tier_chartdata = $this->league_model->getLeagueChartData($league_data, $tier_row['t_name']);
				// 불참인원 확인 및 제외
				$this->updateAvoidUser($league_data, $tier_chartdata, $tier_row['t_name']);
                $tier_userdata = $this->league_model->getLeagueUserData($league_data, $tier_row['t_name']);
                if ($tier_userdata) {
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
                    $rank_plus = 1;
                    $tie_cnt = 0;
                    foreach($rank_array as $rank_key => $rank_value) {
                        $rank_index["{$rank_value}"] = $rank_key + $rank_plus;
                        $tie_cnt = count(array_keys($total_points, $rank_value));
                        $rank_plus += $tie_cnt - 1;
                    }

                    // MMR 평균 계산
                    $mmr_avg = 0;
                    foreach($tier_userdata as $user_row) {
                        $mmr_avg += $user_row['ls_mmr'];
                    }
                    $lowest_rank = end($rank_index);	        // 꼴등
                    $user_cnt = count($tier_userdata);			// 총 참가자
                    $mmr_avg = $mmr_avg / $user_cnt;			// MMR 평균
                    $exp_con = (int)$tier_row['t_exp_con'];		// 팽창 상수
                    $deg_vc = (int)$tier_row['t_deg_vc'];		// 변동 계수
                    $mmr_result = null;
                    $point_result = null;
                    $match_mmr = 0;
                    $avoider_mmr = 0;
                    echo $tier_row['t_name'];
                    echo " : ";
                    echo "꼴등 {$lowest_rank}등 / ";
                    echo "총 참가자 {$user_cnt}명 / ";
                    echo "MMR 평균 {$mmr_avg} / ";
                    echo "팽창상수 {$exp_con} / ";
                    echo "변동계수 {$deg_vc} <br/>";
                    // 유저별 최종 MMR 계산
                    foreach($tier_userdata as $user_row) {
                        echo $user_row['u_nick'];
                        echo " : ";
                        $user_mmr = $user_row['ls_mmr'];		// 참가자 MMR
                        // 참가자 포인트 순위
                        $user_index = $total_points[$user_row['u_nick']] ?? null;
                        $user_rank = $user_index ? $rank_index["{$user_index}"] : $lowest_rank;
                        echo $total_points[$user_row['u_nick']] . "점 / " . $user_rank ."위 / ";
                        $match_mmr = (sqrt($user_cnt / 10)) * ($deg_vc * ($lowest_rank - (2 * ($user_rank - 0.5))) / $lowest_rank) + (($mmr_avg - $user_mmr) / 7.5) + $exp_con;
                        echo $match_mmr ." 변동 <br/>";
                        $user_seq = $this->account_model->getUserSeq($user_row['u_nick']);
                        $mmr_result[$user_seq] = ceil($user_mmr + $match_mmr);
                        $point_result[$user_seq] = $total_points[$user_row['u_nick']];
                        if ($total_points[$user_row['u_nick']] == end($rank_array)) {
                            $avoider_mmr = (int)$match_mmr;
                        }
                    }
                    echo "<br>";
                } else {
                    $mmr_result = null;
                    $point_result = null;
                    $avoider_mmr = 0;
                }
				$next_league_data = array('li_season' => $season, 'li_degree' => $degree+1);
				$this->league_model->setNextLeagueMMR($league_data, $next_league_data, $mmr_result, $point_result, $avoider_mmr, $tier_row['t_name']);
			}
			if ($this->league_model->setTierMMR($league_data, $next_league_data)) {
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
        $sql = "UPDATE al_mmr SET ls_attend = 0 WHERE ls_tier = ? AND ls_li_season = ? AND ls_li_degree = ? AND ls_u_seq NOT IN
			(SELECT distinct(u_seq) from pr_playinfo as ppi
			inner join pr_users on pi_u_seq = u_seq
			inner join pr_charts on pi_c_seq = c_seq
			INNER JOIN al_charts ON lc_c_seq = c_seq
            INNER JOIN al_info ON li_season = lc_li_season AND li_degree = lc_li_degree
			WHERE pi_status = 'Active' AND lc_li_season = ? AND lc_li_degree = ? AND pi_createtime BETWEEN li_starttime AND li_endtime AND pi_enable = 1";
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
        $bind_array = array($tier_name, $league_data['li_season'], $league_data['li_degree'], $league_data['li_season'], $league_data['li_degree']);
		$res = $this->db->query($sql, $bind_array);
		if ($res) {
			return true;
		} else {
			return false;
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
    private function check_studio() {
        if (isset($this->session->u_class) && ($this->session->u_class == 3 || $this->session->u_class == 1)) {
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