<?php
$head_title = "스튜디오 예약정보";
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<style>
.btn-xs {
    padding: 0.2rem;
    font-size: 0.7rem;
    line-height: 1;
    border-radius: 0.2rem;
}
</style>
<script>
$(document).on("click", ".tc_cancel", function(e) {
    var str = $(this).attr("str");
    if (confirm(str + " 예약을 취소하시겠습니까?")) {
        var seq = $(this).val();
        $.ajax({
            type : "POST",
            url : "delTicket",
            data: {
                "seq" : seq,
            },
            error : function(data) {
                console.log(data);
                alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                location.reload();
            },
            success : function(data) {
                var rtn = data[data.length-1];
                if (rtn == "Y") {
                    alert("취소가 완료되었습니다.");
                } else if (rtn == "N") {
                    console.log(data);
                    alert("취소에 실패하였습니다. 관리자에게 문의해주세요.");
                } else {
                    console.log(data);
                    alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                }
                location.reload();
            }
        });
    }
});
$(document).on("click", ".tc_deposit", function(e) {
    var str = $(this).attr("str");
    if (confirm(str + " 예약을 입금확인 처리하시겠습니까?")) {
        var seq = $(this).val();
        $.ajax({
            type : "POST",
            url : "setDeposit",
            data: {
                "seq" : seq,
            },
            error : function(data) {
                console.log(data);
                alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                location.reload();
            },
            success : function(data) {
                var rtn = data[data.length-1];
                if (rtn == "Y") {
                    alert("입금확인 처리가 완료되었습니다.");
                } else if (rtn == "N") {
                    console.log(data);
                    alert("입금확인 처리에 실패하였습니다. 관리자에게 문의해주세요.");
                } else {
                    console.log(data);
                    alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                }
                location.reload();
            }
        });
    }
});
$(document).on("click", ".tc_sentsms", function(e) {
    var str = $(this).attr("str");
    if (confirm(str + " 예약을 문자안내완료 처리하시겠습니까?")) {
        var seq = $(this).val();
        $.ajax({
            type : "POST",
            url : "setSentSms",
            data: {
                "seq" : seq,
            },
            error : function(data) {
                console.log(data);
                alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                location.reload();
            },
            success : function(data) {
                var rtn = data[data.length-1];
                if (rtn == "Y") {
                    alert("문자안내 처리가 완료되었습니다.");
                } else if (rtn == "N") {
                    console.log(data);
                    alert("문자안내 처리에 실패하였습니다. 관리자에게 문의해주세요.");
                } else {
                    console.log(data);
                    alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                }
                location.reload();
            }
        });
    }
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
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 mb-0 text-gray-800">DIVISION STUDIO 예약정보</h1>
          </div>
          <table class="table table-bordered text-center my-2 text-black" style="font-size:0.7rem;">
            <thead>
              <th scope="col">예약시각</th>
              <th scope="col">이름<br>연락처</th>
              <th scope="col">인원<br>가격</th>
            </thead>
            <tbody>
              <?php
              if ($ticket_data) {
                foreach($ticket_data as $ticket_row) {
              ?>
              <tr>
                <td>
                  <?=date("Y-m-d H시", strtotime($ticket_row['tc_starttime']))?> ~ <br>
                  <?=date("Y-m-d H시", strtotime($ticket_row['tc_endtime']))?><br>
                  <?php if (!$ticket_row['tc_sentsms']) { ?>
                    <button type="button" value="<?=$ticket_row['tc_seq']?>" str="<?=$ticket_row['tc_name']?>(<?=$ticket_row['mc_name']?>)" class="btn btn-xs btn-primary tc_sentsms">문자안내처리</button>
                  <?php } else { ?>
                    문자안내완료
                  <?php } ?>
                </td>
                <td>
                  <?=$ticket_row['tc_name']?> (<?=$ticket_row['mc_name']?>)<br>
                  <?=$ticket_row['tc_tel']?><br>
                  <?php if (!$ticket_row['tc_deposit']) { ?>
                    입금대기<button type="button" value="<?=$ticket_row['tc_seq']?>" str="<?=$ticket_row['tc_name']?>(<?=$ticket_row['mc_name']?>)" class="btn btn-xs btn-success tc_deposit">확인</button>
                  <?php } else { ?>
                    입금완료
                  <?php } ?>
                </td>
                <td>
                  <?=$ticket_row['tc_person']?>명&nbsp;<button type="button" value="<?=$ticket_row['tc_seq']?>" str="<?=$ticket_row['tc_name']?>(<?=$ticket_row['mc_name']?>)" class="btn btn-xs btn-danger tc_cancel">취소</button><br>
                  <?=number_format($ticket_row['tc_price'])?>원<br>
                </td>
              </tr>
              <?php
                }
              } else {
              ?>
              <tr><td colspan="3">예약 정보가 없습니다.</td></tr>
              <?php
              }
              ?>
            </tbody>
          </table>

        </div>
        <!-- /.container-fluid -->

      <?php require_once $common_dir . "/body_bottom.php"; ?>

    </div>
    <!-- End of Content Wrapper -->


  </div>
  <!-- End of Page Wrapper -->

</body>

</html>
