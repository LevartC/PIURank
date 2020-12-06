<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ranking_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getRankingInfo($u_id = null, $page = 0, $page_rows = 10) {
        $bind_array = array();
        if (!($page || $page_rows)) {
            $sql = "SELECT count(*) as cnt FROM pr_users";
        } else {
            $sql = "SELECT u_nick, u_skillp FROM pr_users";
        }
        if ($u_id) {
//            $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq inner join pr_charts on pi_c_seq = c_seq inner join pr_songs on c_s_seq = pr_songs.s_seq WHERE pi_enable = 1 AND c_seq = ?";
            $sql .= " WHERE u_id = ?";
            array_push($bind_array, $u_id);
        }
        $sql .= " ORDER BY u_skillp DESC";
        if ($page && $page_rows) {
            $lim_start = ($page - 1) * $page_rows;
            $lim_end = $page_rows;
            $sql .= " LIMIT {$lim_start}, {$lim_end}";
        }
        $res = count($bind_array) ? $this->db->query($sql, $bind_array) : $this->db->query($sql);
        $data = null;
        foreach($res->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function searchFile($c_title) {
        $search_str = $this->db->escape_like_str($c_title);
        $sql = "SELECT c_seq, s_title, s_title_kr, c_type+0 as c_type, c_level FROM pr_charts as a, pr_songs as b WHERE a.s_seq = b.s_seq AND a.c_level >= 12 AND (b.s_title LIKE '%{$search_str}%' OR b.s_title_kr LIKE '%{$search_str}%')";
        if ($res = $this->db->query($sql)) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
    }

}
?>