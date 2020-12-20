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

    $(document).on("click", "#step1_next", function(e) {
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
                $("#step_3").css("display", "flex");
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
      <div class="row align-items-center text-center mb-5">
        <div class="col-12 mb-2">
          <h3>기체를 선택해주세요.</h3>
        </div>
        <div class="col">
          <button type="button" class="btn btn-primary">LX - W</button>
        </div>
        <div class="col">
          <button type="button" class="btn btn-success">LX - G</button>
        </div>
        <div class="col">
          <button type="button" class="btn btn-info">FX</button>
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
        <div class="col-12 m-0">
          <h3 id="select_ment">시작 시각을 선택해주세요.</h3>
        </div>
        <div class="col m-0 p-0">
          <span style="font-size:1rem; color:black;"><?= date("n월 j일", strtotime($yesterday))?></span><br>
          <div class="btn-group-vertical">
            <?php for ($q = 12; $q < 24; ++$q) :
              $chk_disabled = $resv_data["{$yesterday}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q-24?>" class="btn btn-warning ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q-24?>" <?=$chk_disabled?>><?=$q?>:00
            <?php endfor; ?>
          </div>
        </div>
        <div class="col m-0 p-0">
          <span style="font-size:1rem; color:black;"><?= date("n월 j일", strtotime($today))?></span><br>
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q?>" value="<?=$chk_disabled ? 1 : 0?>" <?=$chk_disabled?>><?=$q?>:00
            <?php endfor; ?>
          </div>
          <div class="btn-group-vertical">
            <?php for ($q = 12; $q < 24; ++$q) :
              $chk_disabled = $resv_data["{$today}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q?>" class="btn btn-primary ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q?>" value="<?=$chk_disabled ? 1 : 0?>" <?=$chk_disabled?>><?=$q?>:00
            <?php endfor; ?>
          </div>
        </div>
        <div class="col m-0 p-0">
          <span style="font-size:1rem; color:black;"><?= date("n월 j일", strtotime($tomorrow))?></span><br>
          <div class="btn-group-vertical">
            <?php for ($q = 0; $q < 12; ++$q) :
              $chk_disabled = $resv_data["{$tomorrow}{$q}"] ?? "";
            ?>
            <button type="button" id="btn_<?=$today?><?=$q+24?>" class="btn btn-success ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q+24?>" value="<?=$chk_disabled ? 1 : 0?>" <?=$chk_disabled?>><?=$q?>:00
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
