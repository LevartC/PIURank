<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="naver-site-verification" content="2c36c9ba3be57572e0f303d2f046c4456f02b5cf" />


  <title><?=$head_title ?? "PIURANK"?></title>

  <!-- Custom fonts for this template-->
  <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="/css/sb-admin-2.css?20201225" rel="stylesheet">
  <link href="/css/pr_custom.css?20201225" rel="stylesheet">

  <!-- Bootstrap core JavaScript-->
  <script src="/vendor/jquery/jquery.min.js"></script>
  <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>
  <!-- Custom scripts for all pages-->
  <script src="/js/sb-admin-2.js"></script>
  <script>
    // 숫자만 입력
    $(document).on("keyup", ".only_num", function(e) {
        hp_val = $(this).val().replace(/[^0-9]/gi,'');
        $(this).val(hp_val);
    });

    // 휴대폰번호 체크
    $(document).on("keyup", ".mb_hp", function(e) {
        // 기존 번호에서 -를 삭제
        var hp_val = check_hp($(this).val());
        if (!hp_val) {
            hp_val = $(this).val().replace(/-|[^0-9]/gi , '');
        }
        $(this).val(hp_val);
    });
    function check_hp(trans_num) {
        trans_num = trans_num.replace(/-|[^0-9]/gi , '');
        // 입력값이 있을때만 실행
        if(trans_num != null && trans_num != '') {
            if(trans_num.length == 11 || trans_num.length == 10) {
                // 유효성 체크
                var regExp_ctn = /^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})([0-9]{3,4})([0-9]{4})$/;
                if(regExp_ctn.test(trans_num)) {
                    // 유효성 체크에 성공하면 하이픈을 넣고 값을 바꿔줌
                    trans_num = trans_num.replace(/^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?([0-9]{3,4})-?([0-9]{4})$/,"$1-$2-$3");
                    return trans_num;
                }
            }
        }
        return null;
    }
  </script>
</head>
