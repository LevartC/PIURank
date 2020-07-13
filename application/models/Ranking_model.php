<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ranking_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getRankingInfo($c_seq = null, $is_skill = false) {
        if ($c_seq) {
            $sql = "SELECT pr_users.u_nick, pr_playinfo.*, pr_charts.*, pr_songs.* FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq inner join pr_charts on pi_c_seq = c_seq inner join pr_songs on c_s_seq = pr_songs.s_seq WHERE c_seq = ?";
        } else {
            $sql = "SELECT u_nick, u_skillp FROM pr_users LIMIT 20";
        }
    }

    public function searchFile($c_title) {
        $search_str = "%".$c_title."%";
        $sql = "SELECT c_seq, s_title, s_title_kr, c_type+0 as c_type, c_level FROM pr_charts as a, pr_songs as b WHERE a.s_seq = b.s_seq AND a.c_level >= 12 AND (b.s_title LIKE ? OR b.s_title_kr LIKE ?)";
        if ($res = $this->db->query($sql, array($search_str, $search_str))) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
    }

}
?>