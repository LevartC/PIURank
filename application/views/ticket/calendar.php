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
        location.href = "/ticket?y="+year+"&m="+month;
    });
    $(document).on("click", ".select_date", function(e) {
        e.preventDefault();
        var url_addr = "/ticket/studio";
        var day = $(this).html().trim();
        location.href = url_addr + "?y="+year+"&m="+month+"&d="+day;
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
        $date = "{$year}-{$month}-01"; // 현재 날짜
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
      <div class="text-center justify-content-center mx-auto my-2 text-black">
        예약하실 날짜를 선택하세요.<br>
        ※ 당일 예약은 별도로 문의해주시기 바랍니다.<br>
        (영업시간 외 예약도 문의 받고 있습니다.)<br>
        문의전화 : 010-2942-2527<br>
        <a href="https://open.kakao.com/o/sll6X0Oc">카카오톡(DIVISION STUDIO) : (클릭)</a><br>
        <a href="https://open.kakao.com/me/wind4rce">카카오톡(WINDFORCE) : (클릭)</a>
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
                    if ($is_admin || ($curdate >= strtotime("-1 days") && $curdate <= strtotime("+14 days"))) {
                        echo "class='select_date' style='color:{$week_color[$k]}'";
                    }
                    ?>>
                    <?= $n++ ?>
                  </div>
                <?php endif ?>
              </td>
            <?php endfor; ?>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>
      <div class="my-2 d-flex">
        <button class="btn btn-primary" style="width:55%;" type="button" data-toggle="modal" data-target="#map_modal">스튜디오 오시는 길</button>
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
                <br>&nbsp;&nbsp;2정거장 이동 후 하차
                <br>
                - 인접 버스 : 
                <span class="text-primary"><i class="fas fa-bus"></i> 148</span>
                <span class="text-success"><i class="fas fa-bus"></i> 4319</span>
                <span style="color:lightgreen"><i class="fas fa-bus"></i>서초16</span>
                <br>
                - 주차 가능합니다.
              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
              </div>
            </div>
          </div>
        </div>
        &nbsp;
        <button class="btn btn-success" style="width:45%;" type="button" data-toggle="modal" data-target="#modal_checkTicket">예약 확인하기</button>
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
      <div class="my-2 d-flex">
        <button class="btn border-secondary" style="width:33%; background-color:white; color:black;" type="button" data-toggle="modal" data-target="#lx_w_info">LX-W<br>소개</button>
        <div class="modal fade" id="lx_w_info" tabindex="-1" role="dialog" aria-labelledby="lx_w_info" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-black">LX-W 기체 소개</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body text-left" style="font-size:0.8rem; color:black;">
                <img src="/img/lx_w.jpg" style="width:100%"></img><br><br>
                <span style="font-size:1rem; color:darkblue;">&nbsp;[ Version : XX, PRIME2 ]</span><br>
                &nbsp;WINDFORCE의 LX 기체입니다. (좌측에 위치)<br>
                &nbsp;호평이 자자하던 (故)강남 KONG과 같은 기종의 모니터이며,<br>
                &nbsp;(故)이수 짱오락실과 유사한 발판 컨디션으로 세팅하였습니다.<br>
                &nbsp;방문하신 많은 유저분들이 이 기체에서 좋은 성과를 많이 내고 계십니다.<br>
                ※ 방송 가능합니다.<br>
                <br><span style="font-size:1rem">
                [특징]<br>
                - 타격감
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" style="width:80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                - 민감도
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar" style="width:70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                - 발판 단차 (턱 높이)
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-info" role="progressbar" style="width:60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    무턱
                  </div>
                </div>
                </span>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
              </div>
            </div>
          </div>
        </div>
        &nbsp;
        <button class="btn border-secondary" style="width:33%; background-color:black; color:white;" type="button" data-toggle="modal" data-target="#lx_g_info">LX-G<br>소개</button>
        <div class="modal fade" id="lx_g_info" tabindex="-1" role="dialog" aria-labelledby="lx_g_info" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-black">LX-G 기체 소개</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body text-left" style="font-size:0.8rem; color:black;">
                <img src="/img/lx_g.jpg" style="width:100%"></img><br><br>
                <span style="font-size:1rem; color:darkblue;">&nbsp;[ Version : XX, PRIME2 ]</span><br>
                &nbsp;GIMGIMGI의 LX 기체입니다. (우측에 위치)<br>
                &nbsp;꾸준히 관리하여 기체의 상태가 매우 좋습니다.<br>
                &nbsp;발판 단차가 거의 없는 무턱으로 튜닝되어 있으며,<br>
                &nbsp;턱이 없는 발판을 원하시는 분들에게 제격입니다.<br>
                ※ 방송 가능합니다.<br>
                <br><span style="font-size:1rem">
                [특징]<br>
                - 타격감
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" style="width:60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                - 민감도
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar" style="width:70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                - 발판 단차 (턱 높이)
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-info" role="progressbar" style="width:85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    무턱
                  </div>
                </div>
                </span>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
              </div>
            </div>
          </div>
        </div>
        &nbsp;
        <button class="btn btn-info" style="width:33%;" type="button" data-toggle="modal" data-target="#fx_j_info">FX-정인<br>소개</button>
        <div class="modal fade" id="fx_j_info" tabindex="-1" role="dialog" aria-labelledby="fx_j_info" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-black">FX-정인 기체 소개</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body text-left" style="font-size:0.8rem; color:black;">
                <img src="/img/fx_j.jpg" style="width:100%"></img><br><br>
                <span style="font-size:1rem; color:darkblue;">&nbsp;[ Version : XX, PRIME2, FIESTA2, FIESTA EX, NXA, NX2 ]</span><br>
                &nbsp;WINDFORCE의 FX 기체입니다. (별실 설치)<br>
                &nbsp;(故)정인게임장의 고유 모니터 채용 등 기존의 단점을 없애고 장점만을 계승하였으며,
                &nbsp;독립된 공간에 설치하여 그 시절 그 느낌을 살려 보았습니다.<br>
                &nbsp;홈케이드로 사용하였기 때문에 발판 단차가 적고(무턱), 발판 소음이 많이 나지 않으며, 무릎 부하가 적은 것이 특징입니다.<br>
                &nbsp;심혈을 기울여 세팅한 WINDFORCE의 튜닝 노하우가 담겨 있는 발판을 경험하실 수 있습니다.<br>
                &nbsp;현재 구버전 (FIESTA2, FIESTA EX, NXA, NX2) 플레이가 가능합니다.<br>
                ※ 모든 버전 방송 가능합니다.<br>
                <br><span style="font-size:1rem">
                [특징]<br>
                - 소음
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" style="width:20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                - 민감도
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar" style="width:80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                - 턱 높이 (발판 단차)
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-info" role="progressbar" style="width:80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    무턱
                  </div>
                </div>
                - 무릎 부하
                <div class="row">
                  <div class="col-2 text-center">
                    낮음
                  </div>
                  <div class="col-8 my-auto">
                    <div class="progress">
                      <div class="progress-bar bg-warning" role="progressbar" style="width:10%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    높음
                  </div>
                </div>
                </span>
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
        탈의실 구비 / 플레이 전용 신발 구비<br>
        물 기본 제공 (생수 + 종이컵 제공)<br>
      </div>

    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
