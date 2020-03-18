<?php 
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<style>
  .recap_center div {
    margin:auto;
  }
</style>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
var chk_id = false;
var chk_nick = false;
var chk_pw = false;
$(function() {
    $("#reg_id").blur(function() {
        if (this.value != "") {
            $.ajax({
                type : "POST",
                url : "check_id",
                data: { "reg_id" : $('#reg_id').val() },
                success : function(data) {	//data : checkSignup에서 넘겨준 결과값
                    if($.trim(data) == "1") {
                        $("#reg_id_label").html("사용 가능한 아이디입니다.");
                        $("#reg_id_label").attr("style", "color:rgba(28, 200, 138, 0.9)");
                        $("#reg_id_label").removeAttr("display");
                        $("#reg_id").removeClass("is-invalid");
                        $("#reg_id").addClass("is-valid");
                        chk_id = true;
                    } else {
                        $("#reg_id_label").html("중복된 아이디입니다.");
                        $("#reg_id_label").attr("style", "color:#e74a3b");
                        $("#reg_id_label").removeAttr("display");
                        $("#reg_id").removeClass("is-valid");
                        $("#reg_id").addClass("is-invalid");
                        chk_id = false;
                    }
                }
            });
        } else {
            $("#reg_id_label").html("아이디를 입력하세요.");
            $("#reg_id_label").attr("style", "color:#e74a3b");
            $("#reg_id_label").removeAttr("display");
            $("#reg_id").removeClass("is-valid");
            $("#reg_id").addClass("is-invalid");
            chk_id = false;
        }
    });
    $("#reg_nick").blur(function() {
        if (this.value != "") {
            $.ajax({
                type : "POST",
                url : "check_nick",
                data: { "reg_nick" : this.value },
                success : function(data) {	//data : checkSignup에서 넘겨준 결과값
                    if($.trim(data) == "1") {
                        $("#reg_nick_label").html("사용 가능한 닉네임입니다.");
                        $("#reg_nick_label").attr("style", "color:rgba(28, 200, 138, 0.9)");
                        $("#reg_nick").removeClass("is-invalid");
                        $("#reg_nick").addClass("is-valid");
                        chk_nick = true;
                    } else {
                        $("#reg_nick_label").html("중복된 닉네임입니다.");
                        $("#reg_nick_label").attr("style", "color:#e74a3b");
                        $("#reg_nick_label").removeAttr("display");
                        $("#reg_nick").removeClass("is-valid");
                        $("#reg_nick").addClass("is-invalid");
                        chk_nick = false;
                    }
                }
            });
        } else {
            $("#reg_nick_label").html("닉네임을 입력하세요.");
            $("#reg_nick_label").attr("style", "color:#e74a3b");
            $("#reg_nick_label").removeAttr("display");
            $("#reg_nick").removeClass("is-valid");
            $("#reg_nick").addClass("is-invalid");
            chk_nick = false;
        }
    });
    $("#reg_pw, #reg_pw2").blur(function() {
        check_pw();
    });
});

// 숫자만 입력
$(document).on('keyup', '.number-only', function() { 
    this.value = this.value.replace(/[^0-9]/g,'');
});

// 한글만 입력
$(document).on('keyup', '.only-ko', function() {
    this.value = this.value.replace(/[a-z0-9]|[ \[\]{}()<>?|`~!@#$%^&*-_+=,.;:\"\\]/g,"");
});

// 한글만 입력
$(document).on('keyup', '.only-eng', function() {
    this.value = this.value.replace(/[^a-zA-Z-_0-9]/g,"");
});

// submit
$(document).on('submit', '#pi_form', function(event) {
    if (grecaptcha.getResponse() == "") {
        alert("Check the reCAPTCHA.");
        return false;
    }
    if (!chk_id) {
        alert("아이디를 다시 확인하세요.");
        $("#reg_id").focus();
        return false;
    }
    if (!chk_nick) {
        alert("닉네임을 다시 확인하세요.");
        $("#reg_nick").focus();
        return false;
    }
    if (!check_pw) {
        alert("패스워드를 다시 확인하세요.");
        $("#reg_pw").focus();
        return false;
    }
    if(confirm("등록하시겠습니까?") == false) {
        return false;
    }
});
function check_pw() {
    if ($("#reg_pw").val() && $("#reg_pw2").val()) {
        if ($("#reg_pw").val().length >= 6) {
            if ($("#reg_pw").val() == $("#reg_pw2").val()) {
                $("#reg_pw_label").html("패스워드가 일치합니다.");
                $("#reg_pw_label").attr("style", "color:rgba(28, 200, 138, 0.9)");
                $("#reg_pw_label").removeAttr("display");
                $("#reg_pw2").removeClass("is-invalid");
                $("#reg_pw2").addClass("is-valid");
                chk_pw = true;
            } else {
                $("#reg_pw_label").html("패스워드가 일치하지 않습니다.");
                $("#reg_pw_label").attr("style", "color:#e74a3b");
                $("#reg_pw_label").removeAttr("display");
                $("#reg_pw2").removeClass("is-valid");
                $("#reg_pw2").addClass("is-invalid");
                chk_pw = false;
            }
        } else {
            $("#reg_pw_label").html("패스워드는 6자 이상이어야 합니다.");
            $("#reg_pw_label").attr("style", "color:#e74a3b");
            $("#reg_pw_label").removeAttr("display");
            $("#reg_pw2").removeClass("is-valid");
            $("#reg_pw2").addClass("is-invalid");
            chk_pw = false;
        }
    } else {
        chk_pw = false;
    }
    return chk_pw;
}
</script>


<!-- Page Wrapper -->
<div id="wrapper">
  <!-- Sidebar -->
  <?php require_once $common_dir . "/body_sidebar.php"; ?>
  
  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
      
      <div class="container" style="max-width:750px">

        <div class="card o-hidden border-0 shadow-lg my-5" style="max-width:100%; margin:auto;">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">계정 등록하기</h1>
                  </div>
                  <form class="user" id="pi_form" name="pi_form" method="post" action="registerAction">
                    <div class="form-group row text-center">
                    &nbsp;&nbsp;※ 아이디와 닉네임은 영문과 숫자, -, _만 입력 가능합니다.
                    </div>
                    <div class="form-group row text-center">
                      <div class="col-md-6 mb-3 mb-sm-0">
                        <input type="text" class="form-control form-control-user only-eng" id="reg_id" name="reg_id" placeholder="아이디" required="">
                        <label class="form-control-label" display="none" id="reg_id_label" for="reg_id"></label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" class="form-control form-control-user only-eng" id="reg_nick" name="reg_nick" placeholder="닉네임" required="">
                      <label class="form-control-label" display="none" id="reg_nick_label" for="reg_nick"></label>
                      </div>
                    </div>
                    <div class="form-group row text-center">
                      <div class="col-md-6 mb-3 mb-sm-0">
                        <input type="password" class="form-control form-control-user" id="reg_pw" name="reg_pw" placeholder="비밀번호 입력" required="">
                      </div>
                      <div class="col-md-6">
                        <input type="password" class="form-control form-control-user" id="reg_pw2" name="reg_pw2" placeholder="비밀번호 확인" required="">
                        <label class="form-control-label" display="none" id="reg_pw_label" for="reg_pw2"></label>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="reg_email" name="reg_email" placeholder="이메일 주소" required="">
                    </div>
                    <div class="g-recaptcha recap_center mb-3" style="margin:auto" data-sitekey="6LftetsUAAAAAIO_nEX8DMF10PU80bkXz2Yd0Rdx"></div>
                    <div class="row">
                      <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                          계정 등록
                        </button>
                      </div>
                      <div class="col-6">
                        <button type="button" class="btn btn-secondary btn-user btn-block" onclick="history.back();">
                          뒤 로
                        </button>
                      </div>
                    </div>
                    <!--
                    <a href="index.html" class="btn btn-google btn-user btn-block">
                      <i class="fab fa-google fa-fw"></i> Register with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-user btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
                    </a>
                    -->
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="forgot_password">비밀번호 재설정</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="login">Already have an account? Login!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <!-- End of Main Content -->
      </div>

    <!-- End of Content Wrapper -->
    </div>

  <!-- End of Page Wrapper -->
  </div>
  
</body>

</html>
