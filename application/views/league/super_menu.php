<?php
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>

<script>
    $(document).ready(function(e) {
    });
    $(document).on("change", "#al_tier", function(e) {
        $("#tier_select_form").submit();
    });
</script>
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
      <form class="user" method="post" action="/aevileague/cleanup_match">
      <div class="row">
        <div class="col-4">
            <label for="li_season">시즌</label>
            <input class="form-control" id="li_season" name="li_season"></input>
        </div>
        <div class="col-4">
            <label for="li_degree">차수</label>
            <input class="form-control" id="li_degree" name="li_degree"></input>
        </div>
        <div class="col-4">
            <label for="li_chartcnt">차트개수</label>
            <input class="form-control" id="li_chartcnt" name="li_chartcnt"></input>
        </div>
      </div>
      <div class="row m-2">
            <button class="btn btn-primary" type="submit">생성</button>
      </div>
      </form>
    </div>
    <!-- /.container-fhd -->

    <?php require_once $common_dir . "/footer.php"; ?>