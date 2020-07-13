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

?>