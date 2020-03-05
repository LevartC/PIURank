<?php
require_once __dir__ ."/class/DBConn.php";
if (isset($_POST["reg_id"])) {
    $reg_id = $_POST["reg_id"];
}
if (isset($_POST["reg_nick"])) {
    $reg_nick = $_POST["reg_nick"];
}
if (isset($_POST["reg_pw"])) {
    $reg_pw = $_POST["reg_pw"];
}
if (isset($_POST["reg_email"])) {
    $reg_email = $_POST["reg_email"];
}
if ($reg_id && $reg_nick && $reg_pw && $reg_email) {
    try {
        $db = new DBConn;
        $db->connect_default();
        $reg_hash = password_hash($reg_pw, PASSWORD_DEFAULT);
        $sql = "INSERT INTO pr_users(u_id, u_pw, u_nick, u_email) VALUES(?,?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $reg_id, $reg_hash, $reg_nick, $reg_email);
        if ($stmt->execute()) {
            echo "<script>alert('회원 가입에 성공하였습니다.\\r\\n이제 해당 아이디로 로그인이 가능합니다.'); location.replace('index.php');</script>";
        } else {
            echo "<script>alert('오류가 발생하였습니다.\\r\\n관리자에게 문의하세요.'); history.back();</script>";
        }
    } catch(Exception $e) {
        echo "<script>alert('".$e->getMessage()."'); history.back();</script>";
    }
}
?>