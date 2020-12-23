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

    var year = <?=$year?>;
    var month = <?=$month?>;
    var day = <?=$day?>;

    var is_selecting = 0;
    var sel_machine = null;

    $(document).on("click", "#step1_next", function(e) {
        sel_machine = [];
        $("input[name='machines[]']:checked").each(function() {
            sel_machine.push($(this).val());
        });
        $("#step_1").addClass("d-none");
        $("#step_2").removeClass("d-none");
    });

    $(document).on("click", ".ticket_btn", function(e) {
        e.preventDefault();
        var max_ticket_hour = 12;
        var grp = $(this).attr("group");
        var start_idx = parseInt($(this).attr("index"));
        var end_idx = (start_idx + max_ticket_hour) < 36 ? (start_idx + max_ticket_hour) : 36;
        switch(is_selecting) {
            case 0:
                $(".grp_"+grp).attr("disabled", "true");
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
                          
                      }
                  }
                });
                for (var i=start_idx; i<end_idx; ++i) {
                    var obj_str = "#btn_"+grp+""+i;
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
                for (var i=start_idx+1; i<end_idx; ++i) {
                    var obj_str = "#btn_"+grp+""+i;
                    $(obj_str).attr("disabled", "true");
                }
                is_selecting = 2;
                $("#step_2").addClass("d-none");
                $("#step_3").removeClass("d-none");
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

      <!-- Page Heading -->
      <div id="step_1" class="row align-items-center text-center mb-5">
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
      <?php
        $yesterday = date("Ymd", strtotime("{$year}-{$month}-{$day} -1 days"));
        $today = date("Ymd", strtotime("{$year}-{$month}-{$day}"));
        $tomorrow = date("Ymd", strtotime("{$year}-{$month}-{$day} +1 days"));
      ?>
      <div id="step_2" class="row align-items-center text-center d-none">
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
            <button type="button" id="btn_<?=$today?><?=$q-24?>" class="btn btn-warning ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q-24?>" <?=$chk_disabled?>><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
        <div class="col-6 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q?>" value="<?=$chk_disabled ? 1 : 0?>" <?=$chk_disabled?>><?=$q?>시
            <?php endfor; ?>
          </div>
          <div class="btn-group-vertical">
            <?php for ($q = 12; $q < 24; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q?>" value="<?=$chk_disabled ? 1 : 0?>" <?=$chk_disabled?>><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
        <div class="col-3 m-0 p-0">
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$tomorrow}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q+24?>" class="btn btn-success ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q+24?>" value="<?=$chk_disabled ? 1 : 0?>" <?=$chk_disabled?>><?=$q?>시
            <?php endfor; ?>
          </div>
        </div>
      </div>
      <div id="step_3" class="row align-items-center text-center d-none">
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
