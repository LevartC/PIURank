<?php 
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
    $("#status_form").submit();
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
              <form method="post" id="status_form" name="status_form" class="user" action="myplay">
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
                  <th scope="col">날짜</th>
                  <th scope="col">제목</th>
                  <th scope="col">상태</th>
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
              <th scope="row"> <?= date("Y-m-d", strtotime($row['pi_createtime'])) ?></th>
              <td><?=$row['s_title']?><br>(<?=$row['s_title_kr']?>)</td>
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
                      <div class="modal-body">
                        <div class="card" style="width: 100%;">
                        <img class="card-img-top" src="/pi_images/<?=$row['pi_filename']?>" alt="Playinfo Image">
                        <div class="card-body">
                          <h5 class="card-title text-dark d-flex justify-content-between text-center border border-dark rounded p-2">
                            <div><?=$row['s_title']?><br>(<?=$row['s_title_kr']?>)</div>
                            <div>스킬포인트<br><?=get_sp_floor($row['pi_skillp'])?></div>
                          </h5>
                          <div class="text-center border border-dark rounded p-2 mb-2">
                            <pre class="text-<?= $status_type?>"><?= $row['pi_comment']?></pre>
                          </div>
                          <div class="row text-center text-dark">
                            <div class="col-3 border border-secondary">BREAK</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_break']?></div>
                            <div class="col-3 border border-secondary">JUDGE</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_judge']?></div>
                            <div class="col-3 border border-secondary">그레이드</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_grade']?></div>
                            <div class="col-3 border border-secondary">스코어</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_score']?></div>
                            <div class="col-3 border border-secondary text-primary">퍼펙트</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_perfect']?></div>
                            <div class="col-3 border border-secondary text-success">그레이트</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_great']?></div>
                            <div class="col-3 border border-secondary text-warning">굿</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_good']?></div>
                            <div class="col-3 border border-secondary" style="color:purple">배드</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_bad']?></div>
                            <div class="col-3 border border-secondary text-danger">미스</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_miss']?></div>
                            <div class="col-3 border border-secondary">맥스콤보</div>
                            <div class="col-3 border border-secondary"><?= $row['pi_maxcom']?></div>
                          </div>
                        </div>
                      </div></div>
                      <div class="modal-footer">
                          <button class="btn btn-secondary" type="button" data-dismiss="modal">닫기</button>
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

        </div>
        <!-- /.container-fluid -->

      <?php require_once $common_dir . "/body_bottom.php"; ?>

    </div>
    <!-- End of Content Wrapper -->


  </div>
  <!-- End of Page Wrapper -->

</body>

</html>
