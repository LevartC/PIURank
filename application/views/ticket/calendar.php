<?php
$head_title = "DIVISION STUDIO 예약";
$common_dir = get_common_dir();
//$userdata = $this->userdata;
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<style>

.table-td-hover tbody td:hover {
  color: #858796;
  background-color: rgba(0, 0, 0, 0.075);
}

</style>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>
    <script>
    $("#nav_ticket").addClass("active");

    var year = <?=$year?>;
    var month = <?=$month?>;
    var day = <?=date("d")?>;

    $(document).on("click", ".move_month", function(e) {
        e.preventDefault();
        month += parseInt($(this).attr("tabindex"));
        if (month > 12) {
            month -= 12;
            year++;
        }
        if (month < 1) {
            month += 12;
            year--;
        }
        location.href = "ticket?y="+year+"&m="+month;
    });
    $(document).on("click", ".select_date", function(e) {
        e.preventDefault();
        var day = $(this).html().trim();
        location.href = "/ticket/studio?y="+year+"&m="+month+"&d="+day;
    });

    $(document).on("click", "#search_ticket", function(e) {
        e.preventDefault();
        var tc_name = $("#tc_name").val();
        if (tc_name == "") {
            alert("이름을 입력해주세요.");
            return;
        }
        var tc_tel = $("#tc_tel").val();
        if (!check_hp(tc_tel)) {
            alert("올바른 연락처를 입력해주세요.");
            return;
        }
        $.ajax({
            type : "POST",
            url : "/ticket/searchTicket",
            data: {
                "tc_name" : tc_name,
                "tc_tel" : tc_tel,
            },
            dataType: "JSON",
            error : function(data) {
                console.log(data);
                alert("예약 정보 불러오기에 실패했습니다. 다시 시도해주세요.");
                location.reload();
            },
            success : function(data) {
                console.log(data);
                if (data) {
                    var tmp_txt = "";
                    for (var i in data) {
                        var num = parseInt(i) + 1;
                        tmp_txt += num + ". " + data[i] + "<br>";
                    }
                    tmp_txt = "<span style='color:blue;size:1rem;'>총 " + num + "건 예약중입니다.</span><br><br>" + tmp_txt;
                    $("#ticket_infotext").html(tmp_txt);
                } else {
                    $("#ticket_infotext").html("예약정보가 없습니다.");
                }
            }
        });
    });
    </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-blueviolet text-bold"><i class="fas fa-fw fa-ticket-alt"></i> DIVISION STUDIO 예약</h1>
      </div>

      <?php
        $date = "$year-$month-01"; // 현재 날짜
        $ts = strtotime($date); // 현재 날짜의 타임스탬프
        $start_week = date('w', $ts); // 1. 시작 요일
        $total_day = date('t', $ts); // 2. 현재 달의 총 날짜
        $total_week = ceil(($total_day + $start_week) / 7);  // 3. 현재 달의 총 주차
        $week_color = array('red', 'black', 'black', 'black', 'black', 'black', 'blue');
      ?>
      <div class="d-flex justify-content-center mx-auto my-2">
        <a class="page-link move_month" href="" tabindex="-1"><</a>
        <h1 class="h4 mb-0 text-gray-800">&nbsp;<?=date("Y년 m월", $ts)?>&nbsp;</h1>
        <a class="page-link move_month" href="" tabindex="1">></a>
      </div>
      <div class="d-flex justify-content-center mx-auto my-2 text-black">
        예약하실 날짜를 선택하세요.
      </div>
      <table class="table table-td-hover text-center my-2">
        <thead>
          <th style="color:red">일</th>
          <th style="color:black">월</th>
          <th style="color:black">화</th>
          <th style="color:black">수</th>
          <th style="color:black">목</th>
          <th style="color:black">금</th>
          <th style="color:blue">토</th>
        </thead>
        <tbody>
        <?php for ($n = 1, $i = 0; $i < $total_week; $i++): ?>
          <tr>
            <!-- 1일부터 7일 (한 주) -->
            <?php for ($k = 0; $k < 7; $k++): ?>
              <td>
                <?php if ( ($n > 1 || $k >= $start_week) && ($total_day >= $n) ): ?>
                  <div
                    <?php
                    $curdate = strtotime("{$year}-{$month}-{$n}");
                    echo $curdate >= strtotime("-1 days") && $curdate <= strtotime("+14 days") ? "class='select_date' style='color:{$week_color[$k]}'" : ""?>>
                    <?= $n++ ?>
                  </div>
                <?php endif ?>
              </td>
            <?php endfor; ?>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>
      <div class="my-2">
        <button class="btn btn-block btn-primary" type="button" data-toggle="modal" data-target="#map_modal">스튜디오 오시는 길</button>
        <div class="modal fade" id="map_modal" tabindex="-1" role="dialog" aria-labelledby="map_modal" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-black">스튜디오 오시는 길</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body text-left" style="font-size:1rem; color:black;">
                <img src="/img/studio_map.jpg" style="width:100%"></img>
                <br>
                <br>
                <img src="/img/studio_map2.jpg" style="width:100%"></img>
                <br>
                <br>
                주소 : 서울시 서초구 서초대로 72 서창빌딩 B1<br>
                - 내방역 4번 출구에서 직진, 도보 3분 거리<br>
                - 이수역 5번 출구에서 <span class="text-success"><i class="fas fa-bus"></i> 4319</span> <span style="color:lightgreen"><i class="fas fa-bus"></i>서초16</span> 이용,
                <br>&nbsp;&nbsp;2정거장 이동
                <br>
                - 인접 버스 : 
                <span class="text-primary"><i class="fas fa-bus"></i> 148</span>
                <span class="text-success"><i class="fas fa-bus"></i> 4319</span>
                <span style="color:lightgreen"><i class="fas fa-bus"></i>서초16</span>
                <br>
                - 물은 기본 제공됩니다. (생수 + 종이컵)
                <br>
                - 주차 가능합니다.
              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="my-2">
        <button class="btn btn-block btn-success" type="button" data-toggle="modal" data-target="#modal_checkTicket">예약 확인하기</button>
        <div class="modal fade" id="modal_checkTicket" tabindex="-1" role="dialog" aria-labelledby="modal_checkTicket" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-black">예약 확인하기</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body text-left" style="color:black;">
                <div class="row text-center align-items-center justify-content-center">
                  <div class="col-3 my-2 text-black">
                    입금계좌
                  </div>
                  <div class="col-9 my-2 text-black">
                    우리은행 1002-954-983411<br>
                    예금주 : 박소담
                  </div>
                  <div class="col-3 my-2">
                    이 름
                  </div>
                  <div class="col-9 my-2 d-block">
                    <input class="form-control form-control-user ticket_form" type="text" id="tc_name" name="tc_name" required=""> </input>
                  </div>
                  <div class="col-3 my-2">
                    연락처
                  </div>
                  <div class="col-9 my-2 d-block">
                    <input class="form-control form-control-user ticket_form mb_hp" type="text" id="tc_tel" name="tc_tel" maxlength="13" required=""> </input>
                  </div>
                  <div class="col-12 my-2">
                    <button id="search_ticket" type="button" class="btn btn-info btn-block">예약 확인</button>
                  </div>
                  <div class="col-12 my-2" id="ticket_infotext">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card border-secondary text-center">
        <div class="card-header text-black text-bold text-large">
          스튜디오 이용 요금표 (시간당)
        </div>
        <table class="table table-bordered border-secondary text-center my-2">
          <tr>
            <td colspan="2" class="text-black">평일 0시 ~ <span class="text-primary">주말</span>, <span class="text-danger">공휴일</span> 전날 17시까지</td>
          </tr>
          <tr>
            <th style="width:50%" class="text-black">
              <span class="text-primary">LX-W</span> / <span class="text-success">LX-G</span><br>
              8,000원
            </td>
            <th style="width:50%" class="text-black">
              <span class="text-info">FX-정인</span><br>
              7,000원
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-black"><span class="text-primary">주말</span>, <span class="text-danger">공휴일</span> 전날 17시 ~ 평일 0시까지</td>
          </tr>
          <tr>
            <th style="width:50%" class="text-black">
              <span class="text-primary">LX-W</span> / <span class="text-success">LX-G</span><br>
              9,000원
            </td>
            <th style="width:50%" class="text-black">
              <span class="text-info">FX-정인</span><br>
              8,000원
            </td>
          </tr>
        </table>
      </div>

    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
