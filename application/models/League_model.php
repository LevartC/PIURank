<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class League_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getWorkingLeague() {
        $sql = "SELECT * FROM al_info WHERE now() between li_starttime AND li_endtime LIMIT 1";
        $res = $this->db->query($sql);
        if ($row = $res->row_array()) {
            return $row;
        } else {
            return null;
        }
    }

}
?>