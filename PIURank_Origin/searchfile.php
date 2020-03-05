<?php
header("Content-Type: application/json");
require_once __dir__ ."/class/DBConn.php";
$db = new DBConn;
$db->connect_default();

$c_title = $_POST["c_title"];
$sql = "SELECT c_seq, s_title, s_title_kr, c_type+0 as c_type, c_level FROM pr_charts as a, pr_songs as b WHERE (b.s_title LIKE '%".$c_title."%' OR b.s_title_kr LIKE '%".$c_title."%') AND a.s_seq = b.s_seq";
if ($res = $db->query($sql)) {
    $data = $db->fetch_array_all($res);
    echo json_encode($data);
}
?>