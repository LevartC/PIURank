<?php

function get_levelweight($level) {
    $ret = 0;
    for ($i=1; $i<=$level; ++$i) {
        $ret += $i;
    }
    return $ret;
}
function get_skillpoint($level, $perfect, $great, $good, $bad, $miss, $grade) {
    
    $judge_perfect = 1.0;
    $judge_great = 0.5;
    $judge_good = 0;
    $judge_bad = -0.5;
    $judge_miss = -1.0;

    $grade_val['SSS'] = 0.95;
    $grade_val['SS'] = 0.9;
    $grade_val['S'] = 0.85;
    $grade_val['A'] = 0.8;
    $grade_val['B'] = 0.7;
    $grade_val['C'] = 0.55;
    $grade_val['D'] = 0.35;
    $grade_val['F'] = 0.15;

    $fc_bonus = 0.05;
    $break_off = -0.15;

    $judge_point_val = 0.25;

    $total_notes = $perfect+$great+$good+$bad+$miss;
    $judge_notes =
        $judge_perfect  * $perfect +
        $judge_great    * $great +
        $judge_good     * $good +
        $judge_bad      * $bad +
        $judge_miss     * $miss ;
    $level_weight = get_levelweight($level);
    $judge_point = ($judge_notes / $total_notes) * $level_weight;
    $grade_point = $grade_val[$grade] * $level_weight * $judge_point_val;

    $skill_point = $judge_point + $grade_point;
    return $skill_point;

}


?>