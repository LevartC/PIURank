<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }

    public function writeData() {
        if ($this->input->post('wr_chk4') === null) {
            $cs_market_chk = 0;
        } else {
            $cs_market_chk = 1;
        }

        $data = array(
            'cs_name' => $this->input->post('wr_name'),
            'cs_pw' => password_hash($this->input->post('wr_pw'), PASSWORD_DEFAULT),
            'cs_tel' => $this->input->post('wr_tel'),
            'cs_email' => $this->input->post('wr_mail'),
            'cs_content' => $this->input->post('wr_content'),
            'cs_uptime' => date('Y-m-d H:i:s'),
            'cs_market_chk' => $cs_market_chk
        );

        return $this->db->insert('barunai_cs', $data);
    }

    public function check_id($reg_id) {
        if ($reg_id) {
            $sql = "SELECT u_seq FROM pr_users WHERE u_id = ?";
        
            $res = $this->db->query($sql, array($reg_id));
            if ($res->num_rows() == 0) {
                echo "1";
            }
        }
    }
    
    public function check_nick($reg_nick) {
        if ($reg_nick) {
            $sql = "SELECT u_seq FROM pr_users WHERE u_nick = ?";
        
            $res = $this->db->query($sql, array($reg_nick));
            if ($res->num_rows() == 0) {
                echo "1";
            }
        }
    }
    public function register_action($reg_id, $reg_nick, $reg_pw, $reg_email) {
        $reg_hash = password_hash($reg_pw, PASSWORD_DEFAULT);
        $sql = "INSERT INTO pr_users(u_id, u_pw, u_nick, u_email) VALUES(?,?,?,?)";
        if ($this->db->query($sql, array($reg_id, $reg_hash, $reg_nick, $reg_email))) {
            return true;
        } else {
            return false;
        }
    }
}
