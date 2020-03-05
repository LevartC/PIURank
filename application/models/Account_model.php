<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }
    
    function getUserData($u_seq) {
        if ($u_seq) {
            $sql = "SELECT u_id, u_nick, u_email, u_mmr, u_skillp, u_tier FROM pr_users WHERE u_seq = ?";
            $res = $this->db->query($sql, array((int)$u_seq));
            if ($row = $res->row_array()) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function login_action($login_id, $login_pw) {
        if ($login_id && $login_pw) {
            $sql = "SELECT u_seq, u_id, u_pw, u_nick, u_class FROM pr_users WHERE u_id = ?";
            $res = $this->db->query($sql, array($login_id));
            if ($login_data = $res->row_array()) {
                if(!password_verify($login_pw, $row['u_pw'])) {
                    $_SESSION['u_seq'] = $login_data['u_seq'];
                    $_SESSION['u_id'] = $login_data['u_id'];
                    $_SESSION['u_nick'] = $login_data['u_nick'];
                    $_SESSION['u_class'] = $login_data['u_class'];
                    return true;
                } else {
                    alert("패스워드가 일치하지 않습니다.");
                    return false;
                }
            } else {
                alert("아이디가 존재하지 않습니다.");
            return false;
            }
        } else {
            alert("아이디 또는 패스워드가 입력되지 않았습니다.");
            return false;
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
}
