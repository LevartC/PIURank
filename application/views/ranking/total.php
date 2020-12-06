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
    $("#nav_ranking").addClass("active");
    </script>

    <!-- Begin Page Content -->
    <div class="container-fhd mt-4 pb-3">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">TOTAL SKILL RANKING</h1>
      </div>
      <form method="post" id="seatch_form" class="user page_form">
        <input type="hidden" id="page" name="page" value=""></input>
      </form>

<?php
foreach($rank_info as $rank_key => $rank_row) {
    $ord_lib = array('TH','ST','ND','RD','TH','TH','TH','TH','TH','TH');
    $cur_rank = ($page-1) * $page_rows + 1 + $rank_key;
    $rank_color = "";
    $rank_label = "";
    switch($cur_rank) {
      case 1:
?>
      <!-- Content Row -->
      <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-lg-6 col-md-8 col-sm-12 mx-auto my-2">
          <div class="card border-left-warning shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-3">
                    <i class="fas fa-crown fa-3x text-warning"></i>
                </div>
                <div class="col">
                  <div class="h3 font-weight-bold text-warning text-uppercase">1ST GRADE</div>
                  <div class="h4 font-weight-bold text-gray-800"><?=$rank_row['u_nick']?></div>
                  <div class="h4 mb-0 font-weight-bold text-dark"><?=$rank_row['u_skillp']?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
      break;
      case 2:
?>
      <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12 mx-auto my-2">
          <div class="card border-left-silver shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-3">
                    <i class="fas fa-medal fa-3x text-silver"></i>
                </div>
                <div class="col">
                  <div class="h3 font-weight-bold text-silver text-uppercase">2ND GRADE</div>
                  <div class="h4 font-weight-bold text-gray-800"><?=$rank_row['u_nick']?></div>
                  <div class="h4 mb-0 font-weight-bold text-dark"><?=$rank_row['u_skillp']?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
<?php
      break;
      case 3:
?>
        <div class="col-lg-5 col-md-8 col-sm-12 mx-auto my-2">
          <div class="card border-left-bronze shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-3">
                    <i class="fas fa-award fa-3x text-bronze"></i>
                </div>
                <div class="col">
                  <div class="h3 font-weight-bold text-bronze text-uppercase">3RD GRADE</div>
                  <div class="h4 font-weight-bold text-gray-800"><?=$rank_row['u_nick']?></div>
                  <div class="h4 mb-0 font-weight-bold text-dark"><?=$rank_row['u_skillp']?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
      break;
      default:
?>
      <div class="row">
        <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
          <div class="card shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-2 h3 font-weight-bold"><?=$cur_rank?></div>
                <div class="col-5 h4 font-weight-bold"><?=$rank_row['u_nick']?></div>
                <div class="col-5 h4 font-weight-bold"><?=$rank_row['u_skillp']?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
      break;
    }
}
?>
    </div>
<?php include $common_dir . "/table_paging.php"; ?>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
