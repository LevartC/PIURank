<?php

$db_con = mysqli_connect('localhost', 'piurank', 'fodzld', 'piurank');

if (mysqli_errno($db_con)) {
    echo 'Failed to DB Connect.';
    exit;
}

$nowtime = date('Y-m-d H:i:s');
$sql = "SELECT * FROM dv_send_list INNER JOIN dv_ticket ON ml_tc_seq = tc_seq WHERE ml_time < '{$nowtime}' AND ml_send_stack <= 0";
$res = mysqli_query($db_con, $sql);
if ($row = mysqli_fetch_array($res)) {
    // 90바이트 초과시 LMS, 이하시 SMS
    if (mb_strwidth($row['sm_content']) > 90) {
        $msg_res = sendLMS($row['tc_tel'], $row['ml_msg_title'], $row['ml_msg_content']);
    } else {
        $msg_res = sendSMS($row['tc_tel'], $row['ml_msg_content']);
    }
    // 이메일 전송
    $mail_res = sendEmail($row['tc_email'], $row['ml_mail_title'], $row['ml_mail_content']);
    if ($msg_res && $mail_res) {
        saveMsgLog($db_con, $row['tc_tel'], $row['ml_seq']);
        saveMailLog($db_con, $row['tc_tel'], $row['ml_seq']);
        deleteSendList($db_con, $row['ml_seq']);
    }
}

function saveMsgLog($db_con, $dest_num, $ml_seq) {
    $sql = "INSERT INTO dv_msg_log(ml_seq, ml_tc_seq, ml_datetime, ml_dest_num, ml_msg_title, ml_msg_content, ml_sent_time, ml_status) SELECT ml_seq, ml_tc_seq, ml_datetime, '{$dest_num}' as ml_dest_num, ml_msg_title, ml_msg_content FROM dv_send_list WHERE ml_seq = '{$ml_seq}'";
    $res = mysqli_query($db_con, $sql);
    return $res;
}

function saveMailLog($db_con, $dest_addr, $ml_seq) {
    $sql = "INSERT INTO dv_mail_log(ml_seq, ml_tc_seq, ml_datetime, ml_dest_addr, ml_mail_title, ml_mail_content, ml_sent_time, ml_status) SELECT ml_seq, ml_tc_seq, ml_datetime, '{$dest_addr}' as ml_dest_addr, ml_mail_title, ml_mail_content FROM dv_send_list WHERE ml_seq = '{$ml_seq}'";
    $res = mysqli_query($db_con, $sql);
    return $res;
}
function deleteSendList($db_con, $ml_seq) {
    $sql = "DELETE FROM dv_send_list WHERE $ml_seq = '{$ml_seq}'";
    $res = mysqli_query($db_con, $sql);
    return $res;
}

function sendSMS($dest_num, $content = '') {
    if (!$content) {
        return false;
    }
    $url = "https://api-sms.cloud.toast.com/sms/v2.4/appKeys/hWOhWAXiVIAkuGUL/sender/sms";

    $data = array(
        "body" => $content,
        "sendNo" => "01085076643",
        "recipientList" => null,
        "userId" => "test",
    );
    $data["recipientList"][] = array("recipientNo" => $dest_num);
    $json_data = json_encode($data);
    $header = array(
        'Content-Type: application/json;charset=UTF-8',
    );
    $ch = curl_init();                                 	//curl 초기화
    curl_setopt($ch, CURLOPT_URL, $url);               	//URL 지정하기
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    	//요청 결과를 문자열로 반환
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      	//connection timeout 10초
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   	//원격 서버의 인증서가 유효한지 검사 안함
    curl_setopt($ch, CURLOPT_POST, true);              	//true시 post 전송
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  		//POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $curl_res = curl_exec($ch);

    $res_dec = json_decode($curl_res);

    if ($res_dec['isSuccessful'] == true || $res_dec['isSuccessful'] == 'true') {
        return true;
    } else {
        return false;
    }
}


function sendLMS($dest_num, $title = '', $content = '') {
    if (!$content) {
        return false;
    }
    $url = "https://api-sms.cloud.toast.com/sms/v2.4/appKeys/hWOhWAXiVIAkuGUL/sender/mms";

    $data = array(
        "title" => $title,
        "body" => $content,
        "sendNo" => "01085076643",
        "recipientList" => null,
        "userId" => "test",
    );
    $data["recipientList"][] = array("recipientNo" => $dest_num);
    $json_data = json_encode($data);
    $header = array(
        'Content-Type: application/json;charset=UTF-8',
    );
    $ch = curl_init();                                 	//curl 초기화
    curl_setopt($ch, CURLOPT_URL, $url);               	//URL 지정하기
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    	//요청 결과를 문자열로 반환
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      	//connection timeout 10초
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   	//원격 서버의 인증서가 유효한지 검사 안함
    curl_setopt($ch, CURLOPT_POST, true);              	//true시 post 전송
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  		//POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $curl_res = curl_exec($ch);

    $res_dec = json_decode($curl_res);

    if ($res_dec['isSuccessful'] == true || $res_dec['isSuccessful'] == 'true') {
        return true;
    } else {
        return false;
    }
}


require_once __DIR__.'/application/libraries/PHPMailer/PHPMailerAutoload.php';
function sendEmail($dest_data, $title, $content) {
    $mail = new PHPMailer(true);
    try {
        // 기본 설정
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.piurank.com";
        $mail->SMTPAuth = true;
        $mail->Username = 'ticket';
        $mail->Password = 'xlzptaoslwj';
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        $mail->CharSet = "utf-8";
        $mail->isHTML(false); // HTML 태그 사용 여부
        $mail->Subject = $title;
        $mail->Body = $content;
        $mail->setFrom("ticket@piurank.com", "DIVISION STUDIO 관리자");
        foreach($dest_data as $dest_row) {
            $mail->addAddress($dest_row['addr'], $dest_row['name']);
        }
        // 메일 전송
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error : ", $mail->ErrorInfo;
        return false;
    }
    return true;
}

?>