<?php
$common_dir = get_common_dir();
//$userdata = $this->userdata;
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<style>

.table-td-hover tbody td:hover {
  color: #858796;
  background-color: rgba(0, 0, 0, 0.075);
}

</style>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>
    <script>
    $("#nav_home").addClass("active");

    var year = <?=$year?>;
    var month = <?=$month?>;

    $(document).on("click", ".move_month", function(e) {
        e.preventDefault();
        month += parseInt($(this).attr("tabindex"));
        location.href = "ticket?y="+year+"&m="+month;
    });
    $(document).on("click", ".select_date", function(e) {
        e.preventDefault();
        var day = $(this).html().trim();
        location.href = "ticket/studio?y="+year+"&m="+month+"&d="+day;
    });
    </script>

      <!-- Begin Page Content -->
      <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">예약하기</h1>
      </div>

      <?php
        $date = "$year-$month-01"; // 현재 날짜
        $ts = strtotime($date); // 현재 날짜의 타임스탬프
        $start_week = date('w', $ts); // 1. 시작 요일
        $total_day = date('t', $ts); // 2. 현재 달의 총 날짜
        $total_week = ceil(($total_day + $start_week) / 7);  // 3. 현재 달의 총 주차
        $week_color = array('red', 'black', 'black', 'black', 'black', 'black', 'blue');
      ?>
      <div class="d-flex justify-content-center mx-auto mb-4">
        <a class="page-link move_month" href="" tabindex="-1"><</a>
        <h1 class="h3 mb-0 text-gray-800">&nbsp;<?=date("Y년 m월", $ts)?>&nbsp;</h1>
        <a class="page-link move_month" href="" tabindex="1">></a>
      </div>
      <table class="table table-td-hover text-center">
        <thead>
          <th style="color:red">일</th>
          <th style="color:black">월</th>
          <th style="color:black">화</th>
          <th style="color:black">수</th>
          <th style="color:black">목</th>
          <th style="color:black">금</th>
          <th style="color:blue">토</th>
        </thead>
        <tbody>
        <?php for ($n = 1, $i = 0; $i < $total_week; $i++): ?>
          <tr>
            <!-- 1일부터 7일 (한 주) -->
            <?php for ($k = 0; $k < 7; $k++): ?>
              <td>
                <?php if ( ($n > 1 || $k >= $start_week) && ($total_day >= $n) ): ?>
                  <div class="select_date" style="color:<?=$week_color[$k]?>">
                    <?= $n++ ?>
                  </div>
                <?php endif ?>
              </td>
            <?php endfor; ?>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>

    </div>
    <!-- /.container-fluid -->

    <?php require_once $common_dir . "/footer.php"; ?>
