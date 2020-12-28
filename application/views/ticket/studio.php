<?php
$head_title = "DIVISION STUDIO 예약";
$common_dir = get_common_dir();
//$userdata = $this->userdata;
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>
    <script>
    $("#nav_ticket").addClass("active");

    <?php
        $yesterday_time = strtotime($date . " -1 days");
        $today_time = $time;
        $tomorrow_time = strtotime($date . " +1 days");
        $yesterday = date("Ymd", $yesterday_time);
        $today = date("Ymd", $today_time);
        $tomorrow = date("Ymd", $tomorrow_time);
    ?>
    var year = <?=$year?>;
    var month = <?=$month?>;
    var day = <?=$day?>;
    var today = "<?=$today?>";
    var yesterday = "<?=$yesterday?>";
    var tomorrow = "<?=$tomorrow?>";

    var is_selecting = 0;
    var sel_machine = null;

    $(window).on("load", function(e) {
        loadComplete();
        $("#step_1").show();
    });

    $(document).on("submit", "#ticket_form", function(e) {
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
        var tc_email = $("#tc_email").val();
        if (!check_hp(tc_tel)) {
            alert("이메일을 입력해주세요.");
            return;
        }
        var tc_person = $("#tc_person").val();
        if (tc_person == "") {
            alert("인원을 입력해주세요.");
            return;
        }
        $.ajax({
            type : "POST",
            url : "setTicket",
            data: {
                "machines" : sel_machine,
                "year" : year,
                "month" : month,
                "day" : day,
                "tc_name" : tc_name,
                "tc_tel" : tc_tel,
                "tc_email" : tc_email,
                "tc_person" : tc_person,
                "start_idx" : start_btn.attr("index"),
                "end_idx" : end_btn.attr("index"),
            },
            error : function(data) {
                console.log(data);
                alert("예약에 실패하였습니다. 관리자에게 문의해주세요.");
                location.reload();
            },
            success : function(data) {
                loadComplete();
                var rtn = data[data.length-1];
                if (rtn == "Y") {
                    alert("예약이 완료되었습니다.");
                  location.href = "/ticket";
                } else if (rtn == "N") {
                    alert("예약에 실패하였습니다. 관리자에게 문의해주세요.");
                  location.reload();
                } else {
                    alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                  location.reload();
                }
            }
        });
    });

    $(document).on("click", "#step1_next", function(e) {
        sel_machine = [];
        $("input[name='machines[]']:checked").each(function() {
            sel_machine.push($(this).val());
        });
        if (sel_machine.length > 0) {
            $("#step_1").hide();
            loading();
            $.ajax({
              type : "POST",
              url : "getReservation",
              data: { "machines" : sel_machine, "year" : year, "month" : month, "day" : day },
              dataType: "JSON",
              error : function(data) {
                  console.log(data);
                  alert("예약 정보 불러오기에 실패했습니다. 다시 시도해주세요.");
                  location.reload();
              },
              success : function(data) {
                  console.log(data);
                  if (data) {
                      for(var idx in data) {
                          for (var t_idx in data[idx]) {
                              var btn_id = "#btn_" + t_idx;
                              $(btn_id).val("1");
                              $(btn_id).attr("disabled", "true");
                          }
                      }
                  }
                  loadComplete();
                  $("#step_2").show();
              }
            });
        } else {
            alert("기체를 선택해주세요.");
        }
    });
    function loading() {
      $("#loading").show();
    }
    function loadComplete() {
      $("#loading").hide();
    }

    var start_btn = "";
    var end_btn = "";
    $(document).on("click", ".ticket_btn", function(e) {
        e.preventDefault();
        switch(is_selecting) {
            case 0:
                start_btn = $(this);
                var max_ticket_hour = 12;
                var start_idx = parseInt(start_btn.attr("index"));
                var end_idx = (start_idx + max_ticket_hour) < 36 ? (start_idx + max_ticket_hour) : 36;
                $(".ticket_btn").attr("disabled", "true");
                for (var i=start_idx; i<end_idx; ++i) {
                    var obj_str = ".ticket_btn[index='"+i+"']";
                    $(obj_str).removeAttr("disabled");
                    if ($(obj_str).val() == "1") {
                        break;
                    }
                }
                $("#select_ment").html("종료 시각을 선택해주세요.");
                is_selecting = 1;
            break;
            case 1:
                end_btn = $(this);
                if (start_btn.attr("index") == end_btn.attr("index")) {
                    alert("시작 시각과 종료 시각이 같을 수 없습니다.");
                    break;
                }
                is_selecting = 0;
                $("#step_2").hide();
                loading();
                var str_machine = [];
                $("input[name='machines[]']:checked").each(function() {
                    str_machine.push($(this).attr("str"));
                });
                $("#str_machine").html(str_machine.join(" / "));
                var ticket_time = start_btn.attr("str") + " 부터<br>" + end_btn.attr("str") + " 까지";
                $("#ticket_time").html(ticket_time);
                $.ajax({
                    type : "POST",
                    url : "getPrice",
                    data: { "machines" : sel_machine, "year" : year, "month" : month, "day" : day, "start_idx" : start_btn.attr("index"), "end_idx" : end_btn.attr("index") },
                    dataType: "JSON",
                    error : function(request,status,error) {
                        console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                        alert("가격 정보 불러오기에 실패했습니다. 다시 시도해주세요.");
                        location.reload();
                    },
                    success : function(data) {
                        console.log(data);
                        loadComplete();
                        $("#tc_price").html(data["total"].toLocaleString());
                        $("#step_3").show();
                    }
                });
                break;
            default:
            break;
        }
    });

    </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-blueviolet text-bold"><i class="fas fa-fw fa-ticket-alt"></i> <?=$year?>년 <?=$month?>월 <?=$day?>일 예약</h1>
      </div>
      <div id="loading" class="text-center align-items-center mb-4">
        <img src="/img/loading.gif"></img>
      </div>
      <!-- Page Heading -->
      <div id="step_1" class="row align-items-center text-center my-2" style="display:none;">
        <div class="col-12 my-2 text-black">
          <h3>기체를 선택해주세요.</h3>
        </div>
        <div class="col">
          <input type="checkbox" name="machines[]" value="W" str="LX-W" class="form-control "><span class="text-primary text-bold">LX-W</span></input>
        </div>
        <div class="col">
          <input type="checkbox" name="machines[]" value="G" str="LX-G" class="form-control"><span class="text-success text-bold">LX-G</span></input>
        </div>
        <div class="col">
          <input type="checkbox" name="machines[]" value="F" str="FX-정인" class="form-control"><span class="text-info text-bold">FX-정인</span></input>
        </div>
        <div class="col-12 my-3">
          <button id="step1_next" type="button" class="btn btn-primary btn-block">다 음</button>
        </div>
      </div>
      <div id="step_2" class="row align-items-center text-center my-2" style="display:none;">
        <div class="col-12 my-2">
          <h3 id="select_ment" class="text-black">시작 시각을 선택해주세요.</h3>
        </div>
        <div class="col-3 m-0">
          <span style="font-size:1rem; color:black;"><?= date("n/j", strtotime($yesterday))?></span><br>
        </div>
        <div class="col-6 m-0">
          <span style="font-size:1rem; color:black;"><?= date("n/j", strtotime($today))?></span><br>
        </div>
        <div class="col-3 m-0">
          <span style="font-size:1rem; color:black;"><?= date("n/j", strtotime($tomorrow))?></span><br>
        </div>
        <div class="col-3 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 12; $q < 24; ++$q) :
              $chk_disabled = $resv_data["{$yesterday}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$yesterday?><?=$q?>" class="btn btn-warning ticket_btn" str="<?=date("Y년 n월 j일", $yesterday_time)?> <?=$q?>시" value="0" index="<?=$q-24?>"><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
        <div class="col-6 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn" str="<?=date("Y년 n월 j일", $today_time)?> <?=$q?>시" value="0" index="<?=$q?>"><?=$q?>시
            <?php endfor; ?>
          </div>
          <div class="btn-group-vertical">
            <?php for ($q = 12; $q < 24; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn" str="<?=date("Y년 n월 j일", $today_time)?> <?=$q?>시" value="0" index="<?=$q?>"><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
        <div class="col-3 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$tomorrow}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$tomorrow?><?=$q?>" class="btn btn-success ticket_btn" str="<?=date("Y년 n월 j일", $tomorrow_time)?> <?=$q?>시" value="0" index="<?=$q+24?>"><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
      </div>
      <form method="post" id="ticket_form">
      <div id="step_3" class="row align-items-center text-center my-2" style="display:none;">
        <div class="col-12 my-2">
          <h3 id="select_ment" class="text-black">예약정보를 입력해주세요.</h3>
        </div>
        <div class="col-4 my-2">
          선택기체
        </div>
        <div class="col-8 my-2" id="str_machine">
        </div>
        <div class="col-4 my-2">
          예약시각
        </div>
        <div class="col-8 my-2" id="ticket_time">
        </div>
        <div class="col-4 my-2 align-items-center justify-content-center">
          이름<br>(입금자명)
        </div>
        <div class="col-8 my-2 d-block">
          <input class="form-control form-control-user ticket_form" type="text" id="tc_name" name="tc_name" required=""> </input>
        </div>
        <div class="col-4 my-2 align-items-center justify-content-center">
          연락처
        </div>
        <div class="col-8 my-2 d-block">
          <input class="form-control form-control-user ticket_form mb_hp" type="text" id="tc_tel" name="tc_tel" maxlength="13" required=""> </input>
        </div>
        <div class="col-4 my-2 align-items-center justify-content-center">
          이메일
        </div>
        <div class="col-8 my-2 d-block">
          <input class="form-control form-control-user ticket_form" type="email" id="tc_email" name="tc_email" placeholder="이메일로 예약내역이 발송됩니다." required=""> </input>
        </div>
        <div class="col-4 my-2 align-items-center justify-content-center">
          인 원
        </div>
        <div class="col-8 my-2 d-flex align-items-center justify-content-center">
          <input class="form-control form-control-user ticket_form only_num" type="text" id="tc_person" name="tc_person" style="width:50px;" maxlength="2" required=""></input>&nbsp;명
        </div>
        <div class="col-4 my-2 align-items-center justify-content-center">
          가 격
        </div>
        <div class="col-8 my-2 align-items-center justify-content-center">
          <span id="tc_price" class="text-primary"></span>&nbsp;원
        </div>
        <div class="col-4 my-2 text-black">
          입금계좌
        </div>
        <div class="col-8 my-2 text-black">
          우리은행 1002-954-983411<br>
          예금주 : 박소담
        </div>
        <div class="col-12 my-2">
          <button class="btn btn-block btn-primary" type="button" data-toggle="modal" data-target="#notice_modal">예약하기</button>
          <div class="modal fade" id="notice_modal" tabindex="-1" role="dialog" aria-labelledby="notice_modal" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="">반드시 확인해주세요!</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body text-left" style="font-size:1rem;">
                  - <span class="text-primary text-bold">예약시각에 맞춰 대여가 시작</span>됩니다. 늦지 않게 도착해주세요.<br>
                  - <span class="text-danger text-bold">예약 당일 취소는 불가능</span>하며, 취소 요청은 개별 문의 바랍니다.<br>
                  - 다음 예약자를 위해 예약 종료 <span class="text-info text-bold">10분 전부터 퇴실 준비</span>를 해주세요.<br>
                  - 예약한 기체 외에 <span class="text-orange text-bold">다른 기체나 방에 접근하지 말아주세요.</span> (예: LX기체 이용시 FX방 접근 금지)<br>
                  - LX 기체를 1대만 대여할 시 나머지 1대를 다른 팀에서 예약하여 <span class="text-success text-bold">같은 공간에서 이용</span>하게 될 수 있습니다.<br>
                  - 개인 장비로 방송하실 때는 <span class="text-purple text-bold">설치 및 철거 시간을 고려</span>하여 예약해주세요.<br>
                  - 스튜디오 안에서 <span class="text-danger text-bold">음주, 흡연을 하지 말아주세요.</span><br>
                  - 발판의 <span class="text-primary text-bold">위치를 임의로</span> 움직이지 말아주시고, 발판에 <span class="text-info text-bold">눕거나 앉지</span> 말아주세요.<br>
                  - 스튜디오의 벽이나 물건에 <span class="text-black text-bold">낙서</span>를 하지 말아주세요.<br>
                  - 퇴실시 놓고 가시는 물건은 없으신지 확인해주세요. <span class="text-orange text-bold">디비전 스튜디오는 개인 분실물에 대하여 책임을 지지 않습니다.</span><br>
                  - 스튜디오에 비치된 공용 물품을 소중히 사용해주세요. <span class="text-danger text-bold">물품 도난 및 파손시 민/형사 책임</span>을 물을 수 있습니다.<br>
                  <br>
                  &nbsp;&nbsp;&nbsp;<span class="text-black text-bold text-large">위 내용에 모두 동의하십니까?</span><br>
                </div>
                <div class="modal-footer">
                    <button id="open_ticket" class="btn btn-info mr-auto" type="submit">동의 후 예약하기</button>
                    <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      </form>
    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
