<?php 
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<script>
$(document).on("click", ".pi_button", function(e) {
    console.log($(this).parent("form"));
    var pi_seq = ($(this).attr("index"));
    $("#pi_form"+pi_seq).attr("action", $(this).val());
    $("#pi_form"+pi_seq).submit();
});
function formCheck(frm) {
    switch($(frm).attr("action")) {
        case "pi_approve" :
            if (confirm("승인하시겠습니까?")) {
                return true;
            }
        break;
        case "pi_update" :
            if (frm.pi_score.value && frm.pi_perfect.value && frm.pi_great.value && frm.pi_good.value && frm.pi_bad.value && frm.pi_miss.value && frm.pi_maxcom.value ) {
                if (confirm("수정 후 승인하시겠습니까?")) {
                    return true;
                }
            } else {
                alert("정보가 누락되었습니다. 다시 확인해주세요.");
                return false;
            }
        break;
        case "pi_reject" :
            if (confirm("이 기록을 거부하시겠습니까?")) {
                return true;
            }
        break;
        default:
            return false;
        break;
    }
    return false;
}
</script>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php require_once $common_dir . "/body_sidebar.php"; ?>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php require_once $common_dir . "/body_topbar.php"; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">승인 대기중인 정보</h1>
          </div>

          
          <?php
          foreach($this->pi_data as $row) {
          ?>
          <form method="post" class="user" id="pi_form<?=$row['pi_seq']?>" name="pi_form<?=$row['pi_seq']?>" action="" onsubmit="return formCheck(this)">
          <!-- SONG TITLE / MODE / LEVEL -->
          <div class="row border border-secondary rounded mb-3">
            <div class="col-12 col-xl-4 pr_pi">
              <img alt="Playinfo Image" src="/pi_images/<?=$row['pi_filename']?>" />
            </div>
            <div class="col-12 col-xl-8 mt-2 mb-2">
              <div class="form-row">
                <input type="hidden" name="pi_seq" value="<?=$row['pi_seq']?>"/>
                <div class="form-group col-12 col-xl-8">
                  <input type="text" class="form-control" name="pi_title" value="<?=$row['s_title']?>(<?=$row['s_title_kr']?>)" required readonly/>
                </div>
                <div class="form-group col-6 col-xl-4">
                  <input type="text" class="form-control" name="pi_player" value="<?=$row['u_nick']?>" required readonly/>
                </div>
                <div class="form-group col-6 col-xl-3 mt-auto">
                  <input type="text" class="form-control" name="pi_modelevel" value="<?=$row['c_type']?> <?=$row['c_level']?>" readonly/>
                </div>
              <!-- GRADE / JUDGE / BREAK / SCORE -->
                <div class="form-group col col-xl-2">
                  <label for="pi_grade">그레이드</label>
                  <select class="form-control" name="pi_grade" value="<?=$row['pi_grade']?>">
                    <option>A</option>
                    <option>SSS</option>
                    <option>SS</option>
                    <option>S</option>
                    <option>B</option>
                    <option>C</option>
                    <option>D</option>
                    <option>F</option>
                  </select>
                </div>
                <div class="form-group col col-xl-2">
                  <label for="pi_judge">판정</label>
                  <select class="form-control" name="pi_judge" value="<?=$row['pi_judge']?>">
                    <option>NJ</option>
                    <option>HJ</option>
                    <option>VJ</option>
                  </select>
                </div>
                <div class="form-group col col-xl-2">
                  <label for="pi_break">Break</label>
                  <select class="form-control" name="pi_break" value="<?=$row['pi_break']?>"> 
                    <option>ON</option>
                    <option>OFF</option>
                  </select>
                </div>
                <div class="form-group col-4 col-xl-3">
                  <label for="pi_score">스코어</label>
                  <input type="text" class="form-control" name="pi_score" value="<?=$row['pi_score']?>" required=""/>
                </div>
              </div>
              <div class="form-row">
              <!-- PERFECT / GREAT / GOOD / BAD / MISS / MAXCOMBO -->
                <div class="form-group col-4 col-md-2 col-xl-2">
                  <label for="pi_perfect">퍼펙트</label>
                  <input type="text" class="form-control" name="pi_perfect" value="<?=$row['pi_perfect']?>" required=""/>
                </div>
                <div class="form-group col-4 col-md-2 col-xl-2">
                  <label for="pi_great">그레이트</label>
                  <input type="text" class="form-control" name="pi_great" value="<?=$row['pi_great']?>" required=""/>
                </div>
                <div class="form-group col-4 col-md-2 col-xl-2">
                  <label for="pi_good">굿</label>
                  <input type="text" class="form-control" name="pi_good" value="<?=$row['pi_good']?>" required=""/>
                </div>
                <div class="form-group col-4 col-md-2 col-xl-2">
                  <label for="pi_bad">배드</label>
                  <input type="text" class="form-control" name="pi_bad" value="<?=$row['pi_bad']?>" required=""/>
                </div>
                <div class="form-group col-4 col-md-2 col-xl-2">
                  <label for="pi_miss">미스</label>
                  <input type="text" class="form-control" name="pi_miss" value="<?=$row['pi_miss']?>" required=""/>
                </div>
                <div class="form-group col-4 col-md-2 col-xl-2">
                  <label for="pi_maxcom">맥스콤보</label>
                  <input type="text" class="form-control" name="pi_maxcom" value="<?=$row['pi_maxcom']?>" required=""/>
                </div>
                <div class="form-group col-12 col-xl-6">
                  <label for="pi_comment">코멘트</label>
                  <textarea class="form-control" name="pi_comment"></textarea>
                </div>
                <div class="form-group col-4 col-xl-2 m-auto">
                  <button type="button" value="pi_approve" index="<?=$row['pi_seq']?>"class="btn btn-primary btn-lg btn-block pi_button">승 인</button>
                </div>
                <div class="form-group col-4 col-xl-2 m-auto">
                  <button type="button" value="pi_update" index="<?=$row['pi_seq']?>" class="btn btn-success btn-lg btn-block pi_button">수 정</button>
                </div>
                <div class="form-group col-4 col-xl-2 m-auto">
                  <button type="button" value="pi_reject" index="<?=$row['pi_seq']?>" class="btn btn-danger btn-lg btn-block pi_button">거 부</button>
                </div>
              </div>
            </div>
          </div>
          </form>
          <?php
          }
          ?>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <?php require_once $common_dir . "/body_bottom.php"; ?>

    </div>
    <!-- End of Content Wrapper -->


  </div>
  <!-- End of Page Wrapper -->

</body>

</html>
