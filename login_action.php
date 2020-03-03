<?php
try {
    $db = new DBConn;
    $db->connect_default();
    $login_id = $_POST["login_id"];
    $login_pw = $_POST["login_pw"];

    $sql = "select * from pr_users where u_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $login_id);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($row = $db->fetch_array($res)) {
            if (password_verify($login_pw, $row['u_pw'])) {
                start_session();
                echo "<script>location.href = ('index.php');</script>";
            }
        } else {

        }
    } else {
        echo "<script>alert('오류가 발생하였습니다.\r\n관리자에게 문의하세요.'); history.back();</script>";
    }
} catch(Exception $e) {
    echo "<script>alert('".$e->getMessage()."'); history.back();</script>";
}
?>