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
    </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">TOTAL SKILL RANKING</h1>
      </div>

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
                  <div class="h4 font-weight-bold text-gray-800">FEFEMZ*</div>
                  <div class="h4 mb-0 font-weight-bold text-dark">10302.423</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

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
                  <div class="h4 font-weight-bold text-gray-800">RSS</div>
                  <div class="h4 mb-0 font-weight-bold text-dark">10151.812</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 col-md-8 col-sm-12 mx-auto my-2">
          <div class="card border-left-bronze shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-3">
                    <i class="fas fa-award fa-3x text-bronze"></i>
                </div>
                <div class="col">
                  <div class="h3 font-weight-bold text-bronze text-uppercase">3RD GRADE</div>
                  <div class="h4 font-weight-bold text-gray-800">WIND4RCE</div>
                  <div class="h4 mb-0 font-weight-bold text-dark">9903.423</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
          <div class="card shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-2 h3 font-weight-bold">4TH</div>
                <div class="col-5 h4 font-weight-bold">PLAYER1</div>
                <div class="col-5 h4 font-weight-bold">9804.225</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
          <div class="card shadow h-100">
            <div class="card-body text-center">
              <div class="row no-gutters align-items-center">
                <div class="col-2 h3 font-weight-bold">5TH</div>
                <div class="col-5 h4 font-weight-bold">PLAYER2</div>
                <div class="col-5 h4 font-weight-bold">9801.998</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
