<?php
require_once __dir__ ."/class/DBConn.php";
$db = new DBConn;
$db->connect_default();

if (isset($_POST["reg_id"])) {
    $reg_id = $_POST["reg_id"];
} else {
    $reg_id = null;
}
if ($reg_id) {
    $sql = "SELECT u_seq FROM pr_users WHERE u_id = ?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $reg_id);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            echo "1";
        }
    }
} else {
    
}
?>