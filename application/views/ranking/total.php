<?php 
$common_dir = get_common_dir();
//$userdata = $this->userdata;
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>

    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>
    <script>
    $("#nav_home").addClass("active");
    </script>

    <!-- Begin Page Content -->
    <div class="container-fhd mt-5">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">TOTAL SKILL RANKING</h1>
      </div>

      <!-- Content Row -->
      <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-lg-6 mx-auto my-2 col-sm-12">
          <div class="card border-left-warning shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col-auto">
                    <i class="fas fa-crown fa-3x text-warning"></i>
                </div>
                <div class="col ml-4">
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
        <div class="col-lg-5 mx-auto my-2 col-md-12">
          <div class="card border-left-silver shadow h-100 text-center">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col-lg-2 col-3 text-center">
                    <i class="fas fa-medal fa-3x text-silver"></i>
                </div>
                <div class="col ml-4">
                  <div class="h3 font-weight-bold text-silver text-uppercase">2ND GRADE</div>
                  <div class="h4 font-weight-bold text-gray-800">RSS</div>
                  <div class="h4 mb-0 font-weight-bold text-dark">10151.812</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 mx-auto my-2 col-sm-12">
          <div class="card border-left-bronze shadow h-100 text-center">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col-lg-2 col-3">
                    <i class="fas fa-award fa-3x text-bronze"></i>
                </div>
                <div class="col-auto">
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
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="font-weight-bold text-info text-uppercase">Registered Playinfo</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 3 ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clipboard-list fa-3x text-info"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Requests</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-comments fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
