<?php
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>

    <!-- Begin Page Content -->
    <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">AEVILEAGUE</h1>
      </div>

      <?php
      if ($league_chartdata) {
      ?>
      <div class="card border-primary mb-3" style="max-width:100%">
        <div class="card-header text-dark bg-white"><h3 class="m-0 p-0">Master Tier : Songs List</h2></div>
        <div class="card-body p-2">
          <?php
          $lc_cnt = count($league_chartdata['Master']);
          $div_num = 2;
          $div_cnt = ($lc_cnt / $div_num) + 1;
          $div_col = 12 / $div_num;
          for ($i = 0, $num = 0; $i < $div_cnt; ++$i) {
          ?>
          <div class="row p-0 m-0">
              <?php
              for ($j = 0; $num < $lc_cnt; ++$j, ++$num) {
                  $chart_row = $league_chartdata['Master'][$num];
              ?>
              <div class="col-<?=$div_col?> border border-dark p-1 m-0">
              <img style="width:100%" src="/titles/<?=$chart_row['s_seq']?>_min.jpg">
              <div class="border border-dark p-1 m-0 text-dark"><?=$chart_row['s_artist_kr']?> - <?=$chart_row['s_title_kr']?> <?=get_type_index($chart_row['charttype'])?><?=$chart_row['c_level']?> <?=$chart_row['use_hj'] ? "[HJ]" : ""?></div>
              </div>
              <?php
              }
              ?>
          </div>
          <?php
          }
          ?>
        </div>
      </div>
      <?php
      } else {
      ?>
      <div class="border border-secondary rounded p-3 mb-3">
        진행중인 리그가 존재하지 않습니다.
      </div>
      <?php
      }
      ?>

    </div>
    <!-- /.container-fhd -->

    <?php require_once $common_dir . "/footer.php"; ?>