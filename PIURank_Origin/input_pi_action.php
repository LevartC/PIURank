<?php
require_once __dir__ ."/class/DBConn.php";
require_once __dir__ ."/class/SkillPoint.php";

session_start();
if (isset($_SESSION["u_seq"])) {
    $u_seq = $_SESSION["u_seq"];
}

$upload_dir = __dir__ ."/pi_images";
$allowed_ext = array("jpg", "jpeg", "png", "gif");

$pi_file = $_FILES["pi_file"];
$error = $pi_file["error"];
$fm = explode(".", $pi_file["name"]);
$ext = $fm[count($fm)-1];
$pi_filename = date("Ymdhis") . "_" . $_SESSION['u_id']. "." . $ext;

if( $error != UPLOAD_ERR_OK ) {
	switch( $error ) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			echo "<script>alert('파일이 너무 큽니다. ($error)'); history.back();";
			break;
		case UPLOAD_ERR_NO_FILE:
			echo "<script>alert('파일이 첨부되지 않았습니다. ($error)'); history.back();";
			break;
		default:
            echo "<script>alert('파일이 제대로 업로드되지 않았습니다. ($error)'); history.back();";
            break;
	}
}
// 확장자 확인
if( !in_array($ext, $allowed_ext) ) {
    echo "<script>alert('허용되지 않는 확장자입니다.'); history.back();";
    exit();
}
// 파일 이동
move_uploaded_file($_FILES["pi_file"]["tmp_name"], $upload_dir."/".$pi_filename);

try {
    $db = new DBConn;
    $db->connect_default();
    // u_seq는 상단에 이미 입력됨.
    $c_seq = $_POST["pi_seq"];
    $pi_level = $_POST["pi_level"];
    $pi_grade = $_POST["pi_grade"];
    $pi_break = $_POST["pi_break"];
    $pi_judge = $_POST["pi_judge"];
    $pi_perfect = $_POST["pi_perfect"];
    $pi_great = $_POST["pi_great"];
    $pi_good = $_POST["pi_good"];
    $pi_bad = $_POST["pi_bad"];
    $pi_miss = $_POST["pi_miss"];
    $pi_maxcom = $_POST["pi_maxcom"];
    $pi_score = $_POST["pi_score"];
    $pi_skillp = get_skillpoint($pi_level, $pi_perfect, $pi_great, $pi_good, $pi_bad, $pi_miss, $pi_grade);
    
    $sql = "INSERT INTO pr_playinfo(c_seq, u_seq, pi_grade, pi_break, pi_judge, pi_perfect, pi_great, pi_good, pi_bad, pi_miss, pi_maxcom, pi_score, pi_skillp, pi_filename) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iisssiiiiiiids", $c_seq, $u_seq, $pi_grade, $pi_break, $pi_judge, $pi_perfect, $pi_great, $pi_good, $pi_bad, $pi_miss, $pi_maxcom, $pi_score, $pi_skillp, $pi_filename);
    if ($stmt->execute()) {
        echo "<script>alert('성공적으로 입력되었습니다.'); history.back();</script>";
    } else {
        echo "<script>alert('오류가 발생하였습니다.\r\n관리자에게 문의하세요.'); history.back();</script>";
    }
} catch(Exception $e) {
    echo "<script>alert('".$e->getMessage()."'); history.back();</script>";
}
?>