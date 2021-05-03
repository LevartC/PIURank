<?php

$db_con = mysqli_connect('localhost', 'piurank', 'fodzld', 'piurank');

if (mysqli_errno($db_con)) {
    echo 'Failed to DB Connect.';
    exit;
}

$nowtime = date('Y-m-d H:i:s');
$sql = "SELECT * FROM dv_sms_manage WHERE sm_time < '{$nowtime}'";
$res = mysqli_query($db_con, $sql);
if ($row = mysqli_fetch_array($res)) {
    if (mb_strwidth($row['sm_content']) > 90) {
        sendLMS($row['sm_dest_num'], $row['sm_title'], $row['sm_content']);
    }
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

?>