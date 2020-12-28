<?php
$head_title = "기록 등록하기";
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<script>
  pi_status = <?= $this->piStatus?>;
$(document).ready(function(e) {
$("#pi_status").val(pi_status).attr("selected", "selected");

});
$(document).on("change", "#pi_status", function(e){
    $("#page").val("1");
    $("#status_form").submit();
});
$(document).on("click", "#btn_pi_del", function(e){
    if(confirm("이 기록을 삭제하시겠습니까?\n삭제한 기록은 복구할 수 없습니다.")) {
        var num = $(this).val();
        $.ajax({
            type : "GET",
            url : "/account/pi_delete",
            data: { "num" : num },
            success : function(data) {
                if($.trim(data) == "Y") {
                    alert("삭제가 완료되었습니다.");
                    location.reload();
                } else {
                    alert("삭제에 실패하였습니다.\n관리자에게 문의해주세요.");
                }
            }
        });
    }
});
</script>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Topbar -->
        <?php require_once $common_dir . "/body_topbar.php"; ?>

        <!-- Begin Page Content -->
        <div class="container-fhd mt-3">

          <!-- Page Heading -->
          <div class="d-flex justify-content-between mb-4">
            <div>
              <h1 class="h3 mb-0 text-gray-800">내 기록</h1>
            </div>
            <div>
              <form method="post" id="status_form" name="status_form" class="user page_form" action="myplay">
              <input type="hidden" id="page" name="page" value=""></input>
              <select class="form-control" id="pi_status" name="pi_status">
                <option value="<?=PI_STATUS_ALL?>">전체</option>
                <option value="<?=PI_STATUS_WAITING?>">대기</option>
                <option value="<?=PI_STATUS_ACTIVE?>">활성</option>
                <option value="<?=PI_STATUS_DENIED?>">거부</option>
              </select>
              </form>
            </div>
          </div>


          <?php
          if ($this->piData === null || !count($this->piData)) {
          ?>
          <div class="border border-secondary rounded p-3">
              정보가 존재하지 않습니다.
          </div>
          <?php
          } else {
          ?>
          <div class="row mb-2 py-2 text-center">
            <table class="table table-hover border border-light">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width:70px;min-width:70px;">날짜</th>
                  <th scope="col">제목</th>
                  <th scope="col" style="width:60px;min-width:60px;">상태</th>
                </tr>
              </thead>
              <tbody>
          <?php
              foreach($this->piData as $row) {
                  switch($row['pi_status']) {
                      case "Active":
                          $status_type = "success";
                          $status_str = "활성";
                      break;
                      case "Waiting":
                          $status_type = "secondary";
                          $status_str = "대기";
                      break;
                      case "Denied":
                          $status_type = "danger";
                          $status_str = "거부";
                      break;
                      default:
                          $status_type = "";
                          $status_str = "";
                      break;
                  }
                  if (!$row['pi_comment']) {
                      $row['pi_comment'] = "코멘트 없음";
                  }
          ?>
          <!-- DATE / TITLE / STATUS -->
            <tr class="table-<?= $status_type?> text-dark" href="#" data-toggle="modal" data-target="#piModal<?=$row['pi_seq']?>">
              <th scope="row"> <?= date("m-d", strtotime($row['pi_createtime'])) ?></th>
              <td><?=$row['s_title_kr']?></td>
              <td><?=$status_str?></td>
            </tr>
          <!-- Playinfo Modal -->
          <div class="modal fade" id="piModal<?=$row['pi_seq']?>" tabindex="-1" role="dialog" aria-labelledby="piModalLabel<?= $row['pi_seq']?>" aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="piModalLabel<?=$row['pi_seq']?>">플레이 정보</h5>
                          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span>
                          </button>
                      </div>
                      <div class="modal-body" style="font-size:0.8rem;">
                        <div class="card" style="width:100%;">
                          <img class="card-img-top" src="<?=PI_IMAGE_PATH?>/<?=$row['pi_filename']?>" alt="Playinfo Image">
                          <div class="card-body">
                            <h5 class="card-title text-dark d-flex justify-content-between text-center border border-dark rounded p-2">
                              <div class="col-8"><?=$row['s_title_kr']?></div>
                              <div class="col-4">SKILL<br><?=get_sp_floor($row['pi_skillp'])?></div>
                            </h5>
                            <div class="text-center border border-dark rounded p-2 m-0 mb-2">
                              <pre class="text-<?= $status_type?>"><?= $row['pi_comment']?></pre>
                            </div>
                            <div class="row text-center text-dark" style="font-size:0.8rem;">
                              <div class="col-3 border border-secondary font-weight-bold text-primary">퍼펙트</div>
                              <div class="col-4 border border-secondary"><?= $row['pi_perfect']?></div>
                              <div class="col-3 border border-secondary font-weight-bold" >난이도</div>
                              <div class="col-2 border border-secondary"><?= get_type_index($row['charttype'])?><?=$row['c_level']?></div>
                              <div class="col-3 border border-secondary font-weight-bold text-success">그레이트</div>
                              <div class="col-4 border border-secondary"><?= $row['pi_great']?></div>
                              <div class="col-3 border border-secondary font-weight-bold">BREAK</div>
                              <div class="col-2 border border-secondary"><?= $row['pi_break']?></div>
                              <div class="col-3 border border-secondary font-weight-bold text-warning">굿</div>
                              <div class="col-4 border border-secondary"><?= $row['pi_good']?></div>
                              <div class="col-3 border border-secondary font-weight-bold">JUDGE</div>
                              <div class="col-2 border border-secondary"><?= $row['pi_judge']?></div>
                              <div class="col-3 border border-secondary font-weight-bold" style="color:purple">배드</div>
                              <div class="col-4 border border-secondary"><?= $row['pi_bad']?></div>
                              <div class="col-3 border border-secondary font-weight-bold">그레이드</div>
                              <div class="col-2 border border-secondary"><?= $row['pi_grade']?></div>
                              <div class="col-3 border border-secondary font-weight-bold text-danger">미스</div>
                              <div class="col-4 border border-secondary"><?= $row['pi_miss']?></div>
                              <div class="col-3 border border-secondary font-weight-bold">영상</div>
                              <div class="col-2 border border-secondary"><?= isset($row['pi_vodaddr']) ? "<a href='".$row['pi_vodaddr']."'>링크</a>" : "없음"?></div>
                              <div class="col-3 border border-secondary font-weight-bold">스코어</div>
                              <div class="col-4 border border-secondary"><?= $row['pi_score']?></div>
                              <div class="col-3 border border-secondary font-weight-bold">맥스콤보</div>
                              <div class="col-2 border border-secondary"><?= $row['pi_maxcom']?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                          <button id="btn_pi_del" class="btn btn-danger mr-auto" type="button" value="<?=$row['pi_seq']?>">삭제</button>
                          <button class="btn btn-secondary ml-auto" type="button" data-dismiss="modal">닫기</button>
                      </div>
                  </div>
              </div>
          </div>
          <?php
              }
          ?>
              </tbody>
            </table>
          </div>
          <?php
          }
          ?>
          <?php include $common_dir . "/table_paging.php"; ?>
        </div>
        <!-- /.container-fluid -->

      <?php require_once $common_dir . "/body_bottom.php"; ?>

    </div>
    <!-- End of Content Wrapper -->


  </div>
  <!-- End of Page Wrapper -->

</body>

</html>
