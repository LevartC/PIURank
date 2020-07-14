<?php
function get_sp_floor($skillp) {
    return (floor($skillp * 100) / 100);
}

function get_type_index($index) {
    $chart_type = array(
        1 => 'S',
        2 => 'D',
        3 => 'SP',
        4 => 'DP',
        5 => 'COOP',
        6 => 'ETC',
    );
    return $chart_type[$index];
}

function saveLog($log_str) {
    $logPathDir = __DIR__ . "/../../../Logs/piurank";

    $log_file = fopen($logPathDir."/".date("Ymd").".log", "a");
    if ($log_file) {
        $log_str = "[".date("Y-m-d H:i:s")."] ".$log_str;
        fwrite($log_file, $log_str."\r\n");
        fclose($log_file);
        return true;
    } else {
        return false;
    }
}

?>