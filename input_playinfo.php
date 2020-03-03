<!-- Page Header -->
<?php require_once "include_header.php"; ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<body id="page-top">
    
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php require_once "body_sidebar.php"; ?>
    <script>
    $("#coll_category").addClass("show");
    $("#nav_category").addClass("active");
    $("#nav_input_pi").addClass("active");
    
    function formCheck(fm) {
        console.log(fm.pi_file.value);
        if (!fm.pi_file.value) {
            alert("파일이 업로드되지 않았습니다.");
            return false;
        }
        return true;
    }
    
    $(document).on("change", "#pi_file", function(e) {
        var pathstr = this.value;
        var pathpoint = pathstr.lastIndexOf('.');
        var file_ext = pathstr.substring(pathstr.lastIndexOf('.')+1);
        switch(file_ext) {
          case "jpg" :
          case "JPG" :
          case "gif" :
          case "GIF" :
          case "png" :
          case "PNG" :
          case "jpeg" :
          case "JPEG" :
            console.log(file_ext);
            break;
        }
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#pi_img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
      
    $(function() {
        $("#pi_title").autocomplete({
            source : function( request, response ) {
                $.ajax({
                    type: 'post',
                    url: "searchfile.php",
                    data: {"c_title" : $("#pi_title").val()},
                    dataType: "json",
                    //data: {"param":"param"},
                    success: function(data) {
                        console.log(data);
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
                                    label: mode + item["c_level"] + " : " + item["s_title"],
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
                $("#pi_seq").val(ui.item.c_seq);
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
                console.log(event);
            }
        });
    });
    </script>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      
      <!-- Topbar -->
      <?php require_once "body_topbar.php"; ?>

      
      <!-- Begin Page Content -->
      <div class="container-fluid">
              
        <div class="row">
          <div class="col">
            <form role="form" method="post" id="pi_form" name="pi_form" action="input_pi_action.php" onsubmit="return formCheck(this)" enctype="multipart/form-data">
              <!-- IMAGE FILE -->
              <div class="form-row">
                <div class="form-group col-md-4 pr_pi">
                  <label for="pi_file">
                    리절트 사진
                  </label>
                  <input type="file" class="custom-file-input" id="pi_file" name="pi_file"/>
                  <label class="custom-file-label" for="pi_file">Choose File</label>
                  <p class="help-block">
                    .JPG / .JPEG / .PNG 파일만 가능합니다.
                  </p>
                </div>
                <div class="col-md-8 pr_pi">
                  <img id="pi_img" alt="Playinfo Image" src="" class="rounded" />
                </div>
              </div>
              <!-- SONG TITLE / MODE / LEVEL -->
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="pi_title">곡 제목</label>
                  <input type="text" class="form-control" id="pi_title" name="pi_title" placeholder="제목, 모드, 레벨을 검색하여 선택하세요." required=""/>
                  <input type="hidden" id="pi_seq" name="pi_seq"/>
                </div>
                <div class="form-group col-md-4">
                  <label for="pi_mode">모드</label>
                  <select class="form-control" id="pi_mode" name="pi_mode" disabled="true">
                    <option></option>
                    <option>Single</option>
                    <option>Double</option>
                    <option>SingleP</option>
                    <option>DoubleP</option>
                    <option>CO-OP</option>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label for="pi_title">레벨</label>
                  <input type="text" class="form-control" id="pi_level" name="pi_level" required="" disabled="true"/>
                </div>
              </div>
              <!-- GRADE / JUDGE / BREAK / SCORE -->
              <div class="form-row">
                <div class="form-group col-md-2">
                  <label for="pi_grade">그레이드</label>
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
                <div class="form-group col-md-2">
                  <label for="pi_judge">판정</label>
                  <select class="form-control" id="pi_judge" name="pi_judge">
                    <option>NJ</option>
                    <option>HJ</option>
                    <option>VJ</option>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label for="pi_break">Break</label>
                  <select class="form-control" id="pi_break" name="pi_break">
                    <option>ON</option>
                    <option>OFF</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label for="pi_score">스코어</label>
                  <input type="text" class="form-control" id="pi_score" name="pi_score" required=""/>
                </div>
              </div>
              <!-- PERFECT / GREAT / GOOD -->
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="pi_perfect">퍼펙트</label>
                  <input type="text" class="form-control" id="pi_perfect" name="pi_perfect" required=""/>
                </div>
                <div class="form-group col-md-4">
                  <label for="pi_great">그레이트</label>
                  <input type="text" class="form-control" id="pi_great" name="pi_great" required=""/>
                </div>
                <div class="form-group col-md-4">
                  <label for="pi_good">굿</label>
                  <input type="text" class="form-control" id="pi_good" name="pi_good" required=""/>
                </div>
              </div>
              <!-- BAD / MISS / MAXCOMBO -->
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="pi_bad">배드</label>
                  <input type="text" class="form-control" id="pi_bad" name="pi_bad" required=""/>
                </div>
                <div class="form-group col-md-4">
                  <label for="pi_miss">미스</label>
                  <input type="text" class="form-control" id="pi_miss" name="pi_miss" required=""/>
                </div>
                <div class="form-group col-md-4">
                  <label for="pi_maxcom">맥스콤보</label>
                  <input type="text" class="form-control" id="pi_maxcom" name="pi_maxcom" required=""/>
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary btn-block">
                등 록
              </button>
            </form>
          </div>
        </div>
        
    <!-- End of Content Wrapper -->
    </div>

    <?php require_once "body_bottom.php"; ?>

  <!-- End of Page Wrapper -->
  </div>
  
</body>