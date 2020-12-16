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
    $(document).on("click", ".ticket_btn", function(e) {
        e.preventDefault();
        var grp = $(this).attr("group");
        var start_idx = $(this).attr("index");
        var end_idx = (start_idx + 12) < 36 ? start_idx : 36;
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
                is_selecting = 1;
            break;
            case 1:
                for (var i=start_idx+1; i<end_idx; ++i) {
                    var obj_str = "#btn_"+grp+""+i;
                    $(obj_str).attr("disabled", "true");
                }
                $("#ticket_submit").removeAttr("disabled");
                is_selecting = 2;
            break;
            case 2:
        }
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
        <h1 class="h3 mb-0 text-gray-800">예약하기</h1>
      </div>
      <?php
        $yesterday = date("Ymd", strtotime("{$year}-{$month}-{$n} -1 days"));
        $today = date("Ymd", strtotime("{$year}-{$month}-{$n}"));
        $tomorrow = date("Ymd", strtotime("{$year}-{$month}-{$n} +1 days"));
      ?>
                  <div data-toggle="modal" data-target="#tkModal<?=$n?>"><span style="color:<?=$week_color[$k]?>"><?= $n ?></span></div>
                  <!-- Ticket Modal -->
                  <div class="modal fade" id="tkModal<?= $n ?>" tabindex="-1" role="dialog" aria-labelledby="tkModalLabel<?= $n ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="tkModalLabel<?= $n ?>"><?= $month ?>월 <?= $n ?>일 예약</h5>
                          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span>
                          </button>
                        </div>
                        <div class="modal-body" style="font-size:0.8rem;">
                          <div class="card" style="width:100%;">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-12">
                                  <h3 class="select_ment">시작 시각을 선택해주세요.</h3>
                                </div>
                                <div class="col-3">
                                  <span style="font-size:1rem; color:black;"><?= date("n월 j일", strtotime($yesterday))?></span><br>
                                  <div class="btn-group-vertical">
                                    <?php for ($q = 12; $q < 24; ++$q) :
                                      $chk_disabled = $resv_data["{$yesterday}{$q}"] ?? "";
                                    ?>
                                    <button type="button" id="btn_<?=$today?><?=$q-24?>" class="btn btn-warning ticket_btn grp_<?=$today?>" group="<?=$today?>" index="<?=$q-24?>" <?=$chk_disabled?>><?=$q?>:00
                                    <?php endfor; ?>
                                  </div>
                                </div>
                                <div class="col-6">
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
                                <div class="col-3">
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
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
                        </div>
                      </div>
                    </div>
                  </div>
    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
