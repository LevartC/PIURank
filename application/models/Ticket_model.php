<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_model extends CI_Model
{
	function __construct() {
        parent::__construct();
    }

    function getReservationInfo($year, $month) {
        $tc_start = date("Y-m-d H:i:s", strtotime("{$year}-{$month}-01 -12 hours"));
        $tc_end = date("Y-m-d H:i:s", strtotime("{$year}-{$month}-01 12:00:00 +1 months"));
        $sql = "SELECT tc_type, tc_start, tc_end FROM dv_ticket WHERE tc_start >= ? AND tc_end <= ?";
        $bind_array = array($tc_start, $tc_end);
        $res = $this->db->query($sql, $bind_array);

        $data = null;
        if ($res) {
            foreach($res->result_array() as $row) {
                $start_time = strtotime($row['tc_start']);
                $end_time = strtotime($row['tc_end']);
                for ($t = $start_time; $t < $end_time; $t = strtotime("+1 hours", $t)) {
                    $idx = date("YmdG", $t);
                    $data[$row['tc_type']][$idx] = "disabled";
                }
            }
        }
        return $data;
    }

}
?>