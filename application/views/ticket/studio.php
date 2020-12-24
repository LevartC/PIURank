<?php
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
    $("#nav_home").addClass("active");

    <?php
        $yesterday_time = strtotime("{$year}-{$month}-{$day} -1 days");
        $today_time = strtotime("{$year}-{$month}-{$day}");
        $tomorrow_time = strtotime("{$year}-{$month}-{$day} +1 days");
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
        $("#loading").hide();
        $("#step_1").show();
    });

    $(document).on("click", "#step1_next", function(e) {
        sel_machine = [];
        $("input[name='machines[]']:checked").each(function() {
            sel_machine.push($(this).val());
        });
        $("#step_1").hide();
        $("#loading").show();
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
                  $("#loading").hide();
                  $("#step_2").show();

              }
          }
        });
    });

    var start_btn = "";
    var end_btn = "";
    $(document).on("click", ".ticket_btn", function(e) {
        e.preventDefault();
        var max_ticket_hour = 12;
        switch(is_selecting) {
            case 0:
                start_btn = $(this);
                var start_idx = parseInt(start_btn.attr("index"));
                var end_idx = (start_idx + max_ticket_hour) < 36 ? (start_idx + max_ticket_hour) : 36;
                $(".ticket_btn").attr("disabled", "true");
                for (var i=start_idx; i<end_idx; ++i) {
                    var obj_str = ".ticket_btn[index='"+i+"']";
                    if ($(obj_str).val() == "1") {
                        break;
                    } else {
                        $(obj_str).removeAttr("disabled");
                    }
                }
                $("#select_ment").html("종료 시각을 선택해주세요.");
                is_selecting = 1;
            break;
            case 1:
                end_btn = $(this);
                is_selecting = 2;
                var ticket_time = start_btn.attr("year") + "년" + start_btn.attr("month") + "월" + start_btn.attr("day") + "일" + start_btn.html();

                $("#ticket_time").html();
                $("#step_2").hide();
                $("#step_3").show();
            break;
            default:
            break;
        }
                $("#ticket_submit").removeAttr("disabled");
    });

    $(document).on("click", ".move_month", function(e) {
        e.preventDefault();
        month += parseInt($(this).attr("tabindex"));
        location.href = "ticket?y="+year+"&m="+month;
    });
    </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?=$year?>년 <?=$month?>월 <?=$day?>일 예약</h1>
      </div>
      <div id="loading" class="text-center align-items-center mb-4">
        <img src="/img/loading.gif"></img>
      </div>
      <!-- Page Heading -->
      <div id="step_1" class="row align-items-center text-center mb-5" style="display:none;">
        <div class="col-12 mb-2">
          <h3>기체를 선택해주세요.</h3>
        </div>
        <div class="col">
          <input type="checkbox" name="machines[]" value="W" class="form-control"><span class="text-primary">LX-W</span></input>
        </div>
        <div class="col">
          <input type="checkbox" name="machines[]" value="G" class="form-control text-success"><span class="text-success">LX-G</span></input>
        </div>
        <div class="col">
          <input type="checkbox" name="machines[]" value="F" class="form-control text-info"><span class="text-info">FX</span></input>
        </div>
        <div class="col-12 my-3">
          <button id="step1_next" type="button" class="btn btn-secondary btn-block">다음</button>
        </div>
      </div>
      <div id="step_2" class="row align-items-center text-center" style="display:none;">
        <div class="col-12 mb-2">
          <h3 id="select_ment">시작 시각을 선택해주세요.</h3>
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
            <button type="button" id="btn_<?=$yesterday?><?=$q?>" class="btn btn-warning ticket_btn" year="<?=date("Y", $yesterday_time)?>" month="<?=date("n", $yesterday_time)?>" day="<?=date("j", $yesterday_time)?>" value="0" index="<?=$q-24?>"><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
        <div class="col-6 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn" year="<?=date("Y", $today_time)?>" month="<?=date("n", $today_time)?>" day="<?=date("j", $today_time)?>" value="0" index="<?=$q?>"><?=$q?>시
            <?php endfor; ?>
          </div>
          <div class="btn-group-vertical">
            <?php for ($q = 12; $q < 24; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn" year="<?=date("Y", $today_time)?>" month="<?=date("n", $today_time)?>" day="<?=date("j", $today_time)?>" value="0" index="<?=$q?>"><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
        <div class="col-3 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$tomorrow}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$tomorrow?><?=$q?>" class="btn btn-success ticket_btn" year="<?=date("Y", $tomorrow_time)?>" month="<?=date("n", $tomorrow_time)?>" day="<?=date("j", $tomorrow_time)?>" value="0" index="<?=$q+24?>"><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
      </div>
      <div id="step_3" class="row align-items-center text-center" style="display:none;">
        <div class="col-12 mb-2">
          <h3 id="select_ment">정보를 입력해주세요.</h3>
        </div>
        <div class="col-4">
          예약시각
        </div>
        <div class="col-8" id="ticket_time">
          16:00 ~ 18:00
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
