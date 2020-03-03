<?php
try {
    require_once __dir__ ."/class/DBConn.php";
    
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
                session_start();
                $_SESSION['u_seq'] = $row['u_seq'];
                $_SESSION['u_id'] = $row['u_id'];
                $_SESSION['u_nick'] = $row['u_nick'];
                $_SESSION['u_email'] = $row['u_email'];
                $_SESSION['u_mmr'] = $row['u_mmr'];
                $_SESSION['u_skillp'] = $row['u_skillp'];
                $_SESSION['u_tier'] = $row['u_tier'];
                echo "<script>location.href = ('index.php');</script>";
            }
        } else {
            echo '<script>alert("비밀번호가 일치하지 않습니다.");history.back();</script>';
        }
    } else {
        echo "<script>alert('오류가 발생하였습니다.\r\n관리자에게 문의하세요.'); history.back();</script>";
    }
} catch(Exception $e) {
    echo "<script>alert('".$e->getMessage()."'); history.back();</script>";
}
?>