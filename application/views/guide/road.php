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
        <h1 class="h3 mb-0 text-gray-800">가이드 - 오시는 길</h1>
      </div>
      <!-- Content Row -->
      <div class="row">
        <div class="col-12 text-center border border-secondary p-0">
          <img style="width:100%" src="/img/guide/guide_1.jpg">
          <span class="text-black">내방역 4번출구에서 3~4분간 걸어오시면<br>건물 입구가 보입니다.<br></span>
        </div>
        <div class="col-12 text-center border border-secondary p-0">
          <img style="width:100%" src="/img/guide/guide_2.jpg">
          <img style="width:100%" src="/img/guide/guide_3.jpg">
          <span class="text-black">지하로 내려오시면 포스터가 붙은 문이 보이고,<br>들어오시면 스튜디오 입구가 보입니다.<br></span>
        </div>
        <div class="col-12 text-center border border-secondary p-0">
          <img style="width:100%" src="/img/guide/guide_4.jpg">
          <span class="text-black">도어락을 터치하여 비밀번호를 입력해주세요.<br>비밀번호는 대여 시작 10분 전에 문자로 안내하여 드립니다.<br>
          예약 시각에 따라 비밀번호 안내가 생략될 수 있으니 입장할 수 없을 경우 아래 연락처로 문의해주시기 바랍니다.<br>
          <b>문의전화 : <?=$this->config->item('profile_phone')?></b><br></span>
        </div>

      </div>

    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
