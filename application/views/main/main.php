<?php
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>
    <script>
    $("#nav_home").addClass("active");

    $(document).on("click", "#ticket_link", function(e) {
        location.href = "/ticket";
    });
    </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">STATUS</h1>
      </div>
      <!-- Content Row -->
      <div class="row">

        <div class="col-12 mb-4">
          <div class="card shadow h-100 py-2" style="border-left:0.25rem solid blueviolet !important" id="ticket_link">
            <div class="card-body">
              <div class="row no-gutters text-center align-items-center">
                <div class="col mr-2">
                  <div class="font-weight-bold text-blueviolet text-uppercase mb-1">DIVISION STUDIO OPEN!</div>
                  <div class="h5 mb-0 text-blueviolet font-weight-bold text-gray-800">지금 예약하기 (클릭)</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-ticket-alt fa-3x text-blueviolet"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="font-weight-bold text-primary text-uppercase mb-1">Your skill points</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $user_data['u_skillp'] ?? "로그인하세요." ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-star-of-david fa-3x text-primary"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="font-weight-bold text-success text-uppercase mb-1">Your MMR Points</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $user_data ? $user_data['u_mmr'] : "로그인하세요." ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-registered fa-3x text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="font-weight-bold text-info text-uppercase mb-1">Registered Charts</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $playinfo_cnt ?? 0 ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clipboard-list fa-3x text-info"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="font-weight-bold text-warning text-uppercase mb-1">YOUR LEAGUE TIER</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800 text-uppercase"><?= $user_data ? ($user_data['u_tier'] ? $user_data['u_tier'] : "없음") : "로그인하세요." ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-comments fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
