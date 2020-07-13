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
      if ($league_data) {
      ?>
      <div class="card border-primary mb-3" style="max-width:100%">
        <div class="card-header">Header</div>
        <div class="card-body">
          <?php
          if (count($league_chartdata) <= 3) {
              foreach($league_chartdata as $chart_row) {
          ?>
              <img src="<?=$chart_row['lc_c_seq']?>">
          <?php
              }
          } else {
          ?>
          <img src="">
          <?php
          }
          ?>
          <h4 class="card-title">Light card title</h4>
          <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
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