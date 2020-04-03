<?php 
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<script>
var orig_nick = "<?= $this->session->u_nick ?>";
var chk_nick = true;
var chk_pw = true;
$(function() {
    $("#u_nick").blur(function() {
        if (this.value != orig_nick) {
            if (this.value != "") {
                nick_input = $(this);
                nick_label = $("#u_nick_label");
                $.ajax({
                    type : "POST",
                    url : "/account/check_nick",
                    data: { "reg_nick" : this.value },
                    success : function(data) {	//data : checkSignup에서 넘겨준 결과값
                        if($.trim(data) == "1") {
                            nick_label.html("사용 가능한 닉네임입니다.");
                            nick_label.attr("style", "color:rgba(28, 200, 138, 0.9)");
                            nick_input.removeClass("is-invalid");
                            nick_input.addClass("is-valid");
                            chk_nick = true;
                        } else {
                            nick_label.html("중복된 닉네임입니다.");
                            nick_label.attr("style", "color:#e74a3b");
                            nick_label.removeAttr("display");
                            nick_input.removeClass("is-valid");
                            nick_input.addClass("is-invalid");
                            chk_nick = false;
                        }
                    }
                });
            } else {
                nick_label.html("닉네임을 입력하세요.");
                nick_label.attr("style", "color:#e74a3b");
                nick_label.removeAttr("display");
                nick_input.removeClass("is-valid");
                nick_input.addClass("is-invalid");
                chk_nick = false;
            }
        }
    });
    $("#u_pw, #u_pw2").blur(function() {
        check_pw();
    });
});
function check_pw() {
    pw_input = $("#u_pw");
    pw2_input = $("#u_pw2");
    pw_label = $("#u_pw_label");
    var pw_chk;
    if (pw_input.val() && pw2_input.val()) {
        if (pw_input.val().length >= 6) {
            if (pw_input.val() == pw2_input.val()) {
                pw_label.html("패스워드가 일치합니다.");
                pw_label.attr("style", "color:rgba(28, 200, 138, 0.9)");
                pw_label.removeAttr("display");
                pw2_input.removeClass("is-invalid");
                pw2_input.addClass("is-valid");
                chk_pw = true;
            } else {
                pw_label.html("패스워드가 일치하지 않습니다.");
                pw_label.attr("style", "color:#e74a3b");
                pw_label.removeAttr("display");
                pw2_input.removeClass("is-valid");
                pw2_input.addClass("is-invalid");
                chk_pw = false;
            }
        } else {
            pw_label.html("패스워드는 6자 이상이어야 합니다.");
            pw_label.attr("style", "color:#e74a3b");
            pw_label.removeAttr("display");
            pw2_input.removeClass("is-valid");
            pw2_input.addClass("is-invalid");
            chk_pw = false;
        }
    } else {
        chk_pw = true;
    }
    return chk_pw;
}
</script>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Topbar -->
      <?php require_once $common_dir . "/body_topbar.php"; ?>

        <!-- Begin Page Content -->
      <div class="container mt-3" style="max-width:750px">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">프로필 정보</h1>
          </div>

          <div class="card o-hidden border-0 shadow-lg my-2" style="max-width:100%; margin:auto;">
            <div class="card-body p-4">
              <div class="form-row">
                <input type="hidden" name="u_seq" value="<?=$this->session->u_seq?>"/>
                <div class="form-group col-md-6">
                  <label class="form-control-label" for="u_id">아이디</label>
                  <input type="text" class="form-control only-eng" id="u_id" name="u_id" value="<?=$this->userData['u_id']?>" required="" disabled/>
                </div>
                <div class="form-group col-md-6">
                  <label class="form-control-label" for="u_nick">닉네임 (영문과 숫자, -, _만 입력 가능)</label>
                  <input type="text" class="form-control only-eng" id="u_nick" name="u_nick" value="<?=$this->userData['u_nick']?>" required=""/>
                  <label class="form-control-label" display="none" id="u_nick_label" for="u_nick"></label>
                </div>
                <div class="form-group col-md-6">
                  <label class="form-control-label" for="u_pw">패스워드</label>
                  <input type="password" class="form-control" id="u_pw" name="u_pw" required=""/>
                </div>
                <div class="form-group col-md-6">
                  <label class="form-control-label" for="u_pw">패스워드 확인</label>
                  <input type="password" class="form-control" id="u_pw2" name="u_pw2" required=""/>
                  <label class="form-control-label" display="none" id="u_pw_label" for="u_pw2"></label>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      <?php require_once $common_dir . "/body_bottom.php"; ?>

    </div>
    <!-- End of Content Wrapper -->


  </div>
  <!-- End of Page Wrapper -->

</body>

</html>
