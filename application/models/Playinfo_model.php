<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playinfo_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }

    public function getConfigData($config_str) {
        $sql = "SELECT cf_key, cf_value FROM pr_config WHERE cf_name = ?";
        if ($res = $this->db->query($sql, array($config_str))) {
            foreach($res->result_array() as $row) {
                $data[] = $row;
            }
        }
        return $data;
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