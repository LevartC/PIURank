<?php
require_once __dir__ ."/class/DBConn.php";
$db = new DBConn;
$db->connect_default();

if (isset($_POST["reg_nick"])) {
    $reg_nick = $_POST["reg_nick"];
} else {
    $reg_nick = null;
}
if ($reg_nick) {
    $sql = "SELECT u_seq FROM pr_users WHERE u_nick = ?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $reg_nick);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            echo "1";
        }
    }
}
?>