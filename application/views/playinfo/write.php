<?php
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Topbar -->
      <?php require_once $common_dir . "/body_topbar.php"; ?>

      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script src="/js/load-image.all.min.js"></script>
      <script>
      $("#coll_category").addClass("show");
      $("#nav_category").addClass("active");
      $("#nav_input_pi").addClass("active");

      function formCheck(fm) {
          if (!pi_u_seq_val) {
              alert("플레이어가 정상적으로 입력되지 않았습니다.");
              return false;
          } else {
              $("#pi_u_seq").val(pi_u_seq_val);
          }
          var pi_level = $('#pi_level').val();
          if (!pi_level) {
              alert("레벨이 입력되지 않았습니다.");
              return false;
          }
          if (!fm.pi_file.value) {
              alert("파일이 업로드되지 않았습니다.");
              return false;
          }
          if (confirm("기록을 입력하시겠습니까?")) {
              return true;
          } else {
              return false;
          }
      }

      $(document).on("change", "#pi_file", function(e) {
          if (this.value) {
              var pathstr = this.value;
          } else {
              alert("파일이 선택되지 않았습니다.");
              $("#submit_btn").attr("disabled", "");
              return false;
          }
          var pathpoint = pathstr.lastIndexOf('.');
          var file_name = pathstr.substring(pathstr.lastIndexOf('\\')+1);
          if (!file_name) {
              file_name = pathstr.substring(pathstr.lastIndexOf('/')+1);
          }
          var file_ext = pathstr.substring(pathstr.lastIndexOf('.')+1);
          switch(file_ext.toUpperCase()) {
            case "JPG" :
            case "PNG" :
            case "JPEG" :
                break;
            default:
                alert("지원하지 않는 확장자입니다.");
                $("#submit_btn").attr("disabled", "");
                return false;
          }
          $('#file_label').html(file_name);
          if (this.files && this.files[0]) {
            var files = e.target.files;
            var fileType = files[0].type;
            loadImage(files[0], function(img, data) {
              img.toBlob(function(blob) {
                var rotateFile = new File([blob], files[0].name, {type:fileType});
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#pi_img').removeClass("hiddenItem");
                    $('#pi_img').attr('src', e.target.result);
                    $("#submit_btn").removeAttr("disabled");
                }
                reader.readAsDataURL(rotateFile);
              }, fileType)}, {orientation:true} );
          }
      });

      $(function() {
        $("#pi_title").autocomplete({
          source : function( request, response ) {
            $.ajax({
              type: 'post',
              url: "searchFile",
              data: {"c_title" : $("#pi_title").val()},
              dataType: "json",
              //data: {"param":"param"},
              success: function(data) {
                //console.log(data);
                //서버에서 json 데이터 response 후 목록에 추가
                response(
                  $.map(data, function(item) {    //json[i] 번째 에 있는게 item 임.
                    switch(item["c_type"]) {
                      case "1":
                        mode = "S";
                        break;
                      case "2":
                        mode = "D";
                        break;
                      case "3":
                        mode = "SP";
                        break;
                      case "4":
                        mode = "DP";
                        break;
                      default:
                        break;
                    }
                    return {
                      label: mode + item["c_level"] + " : " + item["s_title"] + "(" + item["s_title_kr"] + ")",
                      value: item["s_title"],
                      type : item["c_type"],
                      level : item["c_level"],
                      c_seq : item["c_seq"]
                    }
                  })
                );
              }
            });
            },    // source 는 자동 완성 대상
          select : function(event, ui) {    //아이템 선택시
              $("#pi_mode option:eq("+ui.item.type+")").attr("selected","selected");
              $("#pi_level").val(ui.item.level);
              $("#pi_c_seq").val(ui.item.c_seq);
          },
          focus : function(event, ui) {    //포커스 가면
              return false;//한글 에러 잡기용도로 사용됨
          },
          minLength: 1,// 최소 글자수
          autoFocus: false, // 첫번째 항목 자동 포커스 기본값 false
          classes: {    //잘 모르겠음
              "ui-autocomplete": "highlight"
          },
          delay: 200,    //autocomplete 적용 딜레이(ms)
  //            disabled: true, //자동완성 기능 끄기
          position: { my : "left top" },    // 출력 위치
          close : function(event){    //자동완성창 닫아질때 호출
              //console.log(event);
          }
        });
      });
      var pi_u_seq_val = <?= isset($this->session->u_seq) ? "true" : "false" ?>;
      $(function() {
        $("#pi_u_nick").blur(function(e) {
            if (this.value != "") {
                $.ajax({
                    type : "POST",
                    url : "/account/check_nick",
                    data: { "reg_nick" : this.value },
                    success : function(data) {	//data : checkSignup에서 넘겨준 결과값
                        if($.trim(data)) {
                            $("#u_nick_label").html("등록 가능한 유저입니다.");
                            $("#u_nick_label").attr("style", "color:rgba(28, 200, 138, 0.9)");
                            $("#pi_u_nick").removeClass("is-invalid");
                            $("#pi_u_nick").addClass("is-valid");
                            pi_u_seq_val = data;
                        } else {
                            $("#u_nick_label").html("존재하지 않는 유저입니다.");
                            $("#u_nick_label").attr("style", "color:#e74a3b");
                            $("#u_nick_label").removeAttr("display");
                            $("#pi_u_nick").removeClass("is-valid");
                            $("#pi_u_nick").addClass("is-invalid");
                            $("#pi_u_seq").val("");
                            pi_u_seq_val = false;
                        }
                    }
                });
            } else {
                $("#u_nick_label").html("닉네임을 입력하세요.");
                $("#u_nick_label").attr("style", "color:#e74a3b");
                $("#u_nick_label").removeAttr("display");
                $("#pi_u_nick").removeClass("is-valid");
                $("#pi_u_nick").addClass("is-invalid");
                $("#pi_u_seq").val("");
                pi_u_seq_val = false;
            }
        });
      });
      </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-3">

        <div class="row">
          <div class="col">
            <form method="post" class="user" id="pi_form" name="pi_form" action="write_action" onsubmit="return formCheck(this)" enctype="multipart/form-data">
              <!-- IMAGE FILE -->
              <div class="form-row">
                <div class="col-lg-4 pr_pi">
                  <div class="form-group col-12 p-0">
                    <label class="form-control-label" for="pi_u_nick">플레이어</label>
                    <input type="text" class="form-control" id="pi_u_nick" name="pi_u_nick" value=<?= isset($this->session->u_nick) ? "'".$this->session->u_nick."' disabled" : "" ?>>
                    <label class="form-control-label" display="none" id="u_nick_label" for="pi_u_nick"></label>
                    <input type="hidden" id="pi_u_seq" name="pi_u_seq" value="<?= isset($this->session->u_seq) ? $this->session->u_seq : "" ?>">
                  </div>
                  <div class="form-group col-12">
                    <label class="form-control-label" for="pi_file">
                      리절트 사진
                    </label>
                    <input type="file" class="custom-file-input" id="pi_file" name="pi_file"/>
                    <label class="custom-file-label" id="file_label" for="pi_file">Choose File</label>
                    <p class="help-block">
                      .JPG / .JPEG / .PNG 파일만 가능합니다.
                    </p>
                  </div>
                </div>
                <div class="col-lg-8 pr_pi">
                  <img id="pi_img" class="hiddenItem" alt="Playinfo Image" src="" />
                </div>
              </div>
              <!-- SONG TITLE / MODE / LEVEL -->
              <div class="form-row">
                <div class="form-group col-md-6 col-xl-4">
                  <label class="form-control-label" for="pi_title">곡 제목</label>
                  <input type="text" class="form-control" id="pi_title" name="pi_title" placeholder="제목, 모드, 레벨을 검색하여 선택하세요." required=""/>
                  <input type="hidden" id="pi_c_seq" name="pi_c_seq"/>
                </div>
                <div class="form-group col-6 col-md-4 col-xl-2">
                  <label class="form-control-label" for="pi_mode">모드</label>
                  <select class="form-control" id="pi_mode" name="pi_mode" disabled="true">
                    <option></option>
                    <option>Single</option>
                    <option>Double</option>
                    <option>SingleP</option>
                    <option>DoubleP</option>
                    <option>CO-OP</option>
                  </select>
                </div>
                <div class="form-group col-3 col-md-2 col-xl-1">
                  <label class="form-control-label" for="pi_title">레벨</label>
                  <input type="text" class="form-control" id="pi_level" name="pi_level" required="" readonly/>
                </div>
              <!-- GRADE / JUDGE / BREAK / SCORE -->
                <div class="form-group col-3 col-md-2 col-xl-1">
                  <label class="form-control-label" for="pi_grade">그레이드</label>
                  <select class="form-control" id="pi_grade" name="pi_grade">
                    <option>A</option>
                    <option>SSS</option>
                    <option>SS</option>
                    <option>S</option>
                    <option>B</option>
                    <option>C</option>
                    <option>D</option>
                    <option>F</option>
                  </select>
                </div>
                <div class="form-group col-3 col-md-2 col-xl-1">
                  <label class="form-control-label" for="pi_judge">판정</label>
                  <select class="form-control" id="pi_judge" name="pi_judge">
                    <option>NJ</option>
                    <option>HJ</option>
                    <option>VJ</option>
                  </select>
                </div>
                <div class="form-group col-3 col-md-2 col-xl-1">
                  <label class="form-control-label" for="pi_break">Break</label>
                  <select class="form-control" id="pi_break" name="pi_break">
                    <option>ON</option>
                    <option>OFF</option>
                  </select>
                </div>
                <div class="form-group col-6 col-xl">
                  <label class="form-control-label" for="pi_score">스코어</label>
                  <input type="text" class="form-control" id="pi_score" name="pi_score" required=""/>
                </div>
              </div>
              <div class="form-row">
              <!-- PERFECT / GREAT / GOOD / BAD / MISS / MAXCOMBO -->
                <div class="form-group col-4 col-md-2">
                  <label class="form-control-label" for="pi_perfect">퍼펙트</label>
                  <input type="text" class="form-control" id="pi_perfect" name="pi_perfect" required=""/>
                </div>
                <div class="form-group col-4 col-md-2">
                  <label class="form-control-label" for="pi_great">그레이트</label>
                  <input type="text" class="form-control" id="pi_great" name="pi_great" required=""/>
                </div>
                <div class="form-group col-4 col-md-2">
                  <label class="form-control-label" for="pi_good">굿</label>
                  <input type="text" class="form-control" id="pi_good" name="pi_good" required=""/>
                </div>
                <div class="form-group col-4 col-md-2">
                  <label class="form-control-label" for="pi_bad">배드</label>
                  <input type="text" class="form-control" id="pi_bad" name="pi_bad" required=""/>
                </div>
                <div class="form-group col-4 col-md-2">
                  <label class="form-control-label" for="pi_miss">미스</label>
                  <input type="text" class="form-control" id="pi_miss" name="pi_miss" required=""/>
                </div>
                <div class="form-group col-4 col-md-2">
                  <label class="form-control-label" for="pi_maxcom">맥스콤보</label>
                  <input type="text" class="form-control" id="pi_maxcom" name="pi_maxcom" required=""/>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-6">
                  <button type="submit" id="submit_btn" class="btn btn-primary btn-block" disabled>
                    등 록
                  </button>
                </div>
                <div class="form-group col-6">
                  <button type="button" class="btn btn-secondary btn-block" onclick="history.back();">
                    뒤 로
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

    <!-- End of Content Wrapper -->
    </div>

    <?php require_once $common_dir . "/body_bottom.php"; ?>

  <!-- End of Page Wrapper -->
  </div>

</body>