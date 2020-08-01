<?php
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function(e) {
    });
    
    $(document).on("submit", "#frm_league", function(e) {
        if (confirm("정말로 시즌을 갱신하시겠습니까?")) {
            return true;
        } else {
            return false;
        }
    });
    $(document).on("submit", "#frm_chart", function(e) {
        if (confirm("해당 티어에 차트를 입력하시겠습니까?")) {
            return true;
        } else {
            return false;
        }
    });
    $(function() {
      $("#al_title").autocomplete({
        source : function( request, response ) {
          $.ajax({
            type: 'post',
            url: "/playinfo/searchFile",
            data: {"c_title" : $("#al_title").val()},
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
            $("#al_c_seq").val(ui.item.c_seq);
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
</script>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>

    <!-- Begin Page Content -->
    <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">AEVILEAGUE 관리자</h1>
      </div>
      <form class="user" id="frm_league" method="post" action="cleanup_match">
        <div class="form-row">
          <div class="col-4">
              <label for="li_season">시즌</label>
              <input type="text" class="form-control" id="li_season" name="li_season"></input>
          </div>
          <div class="col-4">
              <label for="li_degree">차수</label>
              <input type="text" class="form-control" id="li_degree" name="li_degree"></input>
          </div>
        </div>
        <div class="form-row m-2">
              <button class="btn btn-primary" type="submit">시즌 갱신</button>
        </div>
      </form>
      <form class="user" id="frm_chart" method="post" action="add_chart">
        <div class="form-row">
          <div class="col-12">
              <label for="al_title">곡 선택</label>
              <input type="text" class="form-control" id="al_title" name="al_title" placeholder="제목, 모드, 레벨을 검색하여 선택하세요."></input>
              <input type="checkbox" id="al_usehj" name="al_usehj" value="1"></input>
              <label for="al_usehj">HJ</label>
              <input type="hidden" id="al_c_seq" name="al_c_seq"/>
          </div>
          <div class="col-4">
              <label for="al_li_season">시즌</label>
              <input type="text" class="form-control" id="al_li_season" name="al_li_season"></input>
          </div>
          <div class="col-4">
              <label for="al_li_degree">차수</label>
              <input type="text" class="form-control" id="al_li_degree" name="al_li_degree"></input>
          </div>
          <div class="col-4">
              <label for="tier">티어</label>
              <select class="form-control league_select" id="tier" name="tier">
                <?php
                foreach($tier_data as $tier_row) {
                ?>
                <option><?=$tier_row['t_name']?></option>
                <?php
                }
                ?>
              </select>
          </div>
        </div>
        <div class="form-row m-2">
              <button class="btn btn-primary" type="submit">추가</button>
        </div>
      </form>
    </div>
    <!-- /.container-fhd -->

    <?php require_once $common_dir . "/footer.php"; ?>