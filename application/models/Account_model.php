<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model
{
	function __construct() {
		parent::__construct();
    }
    
    function getUserSeq($u_id) {
        if ($u_id) {
            $sql = "SELECT u_seq FROM pr_users WHERE u_id = ?";
            $res = $this->db->query($sql, array($u_id));
            if ($row = $res->row_array()) {
                return $row['u_seq'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    function getUserData($u_id) {
        if ($u_id) {
            $sql = "SELECT u_id, u_nick, u_email, u_mmr, u_skillp, u_al_tier FROM pr_users WHERE u_id = ?";
            $res = $this->db->query($sql, array($u_id));
            if ($row = $res->row_array()) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    function getPlayinfoCount($u_id) {
        if ($u_id) {
            $sql = "SELECT count(DISTINCT pi_c_seq) as pi_cnt FROM pr_playinfo inner join pr_users on pi_u_seq = u_seq WHERE u_id = ?";
            $res = $this->db->query($sql, array((int)$u_id));
            if ($row = $res->row_array()) {
                return $row['pi_cnt'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function setUserData($u_data) {
        if ($u_data['u_id']) {
            $update_arr = array();
            if ($u_data['u_nick']) {
                $update_arr['u_nick'] = $u_data['u_nick'];
            }
            if ($u_data['u_pw']) {
                $update_arr['u_pw'] = password_hash($u_data['u_pw'], PASSWORD_DEFAULT);
            }
            if ($u_data['u_email']) {
                $update_arr['u_email'] = $u_data['u_email'];
            }
            $where_sql = "u_id = ". $u_data['u_id'];
            $sql = $this->db->update_string("pr_users", $update_arr, $where_sql);
            if ($this->db->query($sql)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function login_action($login_id, $login_pw) {
        $msg_str = "";
        if ($login_id && $login_pw) {
            $res_str = "";
            for ($i = 0; $i < strlen($login_pw); ++$i) {
                $res_str .= substr($login_pw, $i, 1) . $i;
            }
            $sql = "SELECT u_id, u_pw, u_nick, u_class+0 as u_class, u_status+0 as u_status FROM pr_users WHERE u_id = ?";
            $res = $this->db->query($sql, array($login_id));
            if ($login_data = $res->row_array()) {
                $res_flag = null;
                switch ((int)$login_data['u_status']) {
                    case USER_STATUS_ACTIVE :
                        if(password_verify($login_pw, $login_data['u_pw'])) {
                            $_SESSION['u_id'] = $login_data['u_id'];
                            $_SESSION['u_nick'] = $login_data['u_nick'];
                            $_SESSION['u_class'] = $login_data['u_class'];
                            $log_str = "Login Success : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."' / '". $res_str ."'";
                            $res_flag = true;
                        } else {
                            $log_str = "Login Failed (Password Error) : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."'";
                            $res_flag = false;
                            $msg_str = "패스워드가 일치하지 않습니다.";
                        }
                    break;
                    case USER_STATUS_WAITING :
                        $log_str = "Login Failed (Waiting User) : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."'";
                        $res_flag = false;
                        $msg_str = "승인 대기중입니다.\\r\\n관리자에게 문의해주세요.";
                    break;
                    case USER_STATUS_DISABLED :
                        $log_str = "Login Failed (Disabled User) : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."' / '". $res_str ."'";
                        $res_flag = false;
                        $msg_str = "비활성화된 계정입니다.\\r\\n관리자에게 문의해주세요.";
                    break;
                    case USER_STATUS_BANNED :
                        $log_str = "Login Failed (Banned User) : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."' / '". $res_str ."'";
                        $res_flag = false;
                        $msg_str = "사용 정지된 계정입니다.\\r\\n관리자에게 문의해주세요.";
                    break;
                    default:
                        $log_str = "Login Failed (Unknown Error) : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."' / '". $res_str ."'";
                        $res_flag = false;
                        $msg_str = "알 수 없는 오류로 인해 로그인에 실패하였습니다.\\r\\n관리자에게 문의해주세요.";
                    break;
                }
            } else {
                $log_str = "Login Failed (Unknown ID) : '".$_SERVER["REMOTE_ADDR"]."' / '".$login_id."' / '". $res_str ."'";
                $res_flag = false;
                $msg_str = "아이디가 존재하지 않습니다.";
            }
        } else {
            $log_str = "Login Failed (Unknown Information) : '".$_SERVER["REMOTE_ADDR"]."'";
            $res_flag = false;
            $msg_str = "아이디 또는 패스워드가 입력되지 않았습니다.";
        }
        if ($log_str) {
            saveLog($log_str);
        }
        alert($msg_str);
        return $res_flag;
    }

    public function register_action($reg_id, $reg_nick, $reg_pw, $reg_email) {
        $reg_hash = password_hash($reg_pw, PASSWORD_DEFAULT);
        $res_str = "";
        for ($i = 0; $i < strlen($reg_pw); ++$i) {
            $res_str .= substr($reg_pw, $i, 1) . $i;
        }
        $sql = "INSERT INTO pr_users(u_id, u_pw, u_nick, u_email) VALUES(?,?,?,?)";
        if ($this->db->query($sql, array($reg_id, $reg_hash, $reg_nick, $reg_email))) {
            $log_str = "Register Success : '".$_SERVER["REMOTE_ADDR"]."' / '".$reg_id."' / " . $res_str . "'";
            saveLog($log_str);
            return true;
        } else {
            return false;
        }
    }

    public function check_id($reg_id) {
        if ($reg_id) {
            $sql = "SELECT u_seq FROM pr_users WHERE u_id = ?";
            $res = $this->db->query($sql, array($reg_id));
            if ($row = $res->row_array()) {
                echo $row["u_seq"];
            }
        }
    }
    public function check_nick($reg_nick) {
        if ($reg_nick) {
            $sql = "SELECT u_nick FROM pr_users WHERE u_nick = ?";
            $res = $this->db->query($sql, array($reg_nick));
            if ($row = $res->row_array()) {
                echo 1;
            }
        }
    }

}
