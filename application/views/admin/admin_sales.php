<?php
$head_title = "매출 정보";
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>
<script>
$(document).on("click", "#sales_btn", function(e) {
    var name = $("#ds_name").val();
    if (name == "") {
        alert("품목명을 입력해주세요.");
        return false;
    }
    if (confirm("매출을 등록하시겠습니까?")) {
        var dp_seq = $("#dp_seq").val();
        var price = $("#ds_price").val();
        var memo = $("#ds_memo").val();
        $.ajax({
            type : "POST",
            url : "insertSales",
            data: {
                "dp_seq" : dp_seq,
                "ds_name" : name,
                "ds_price" : price,
                "ds_memo" : memo,
            },
            error : function(data) {
                console.log(data);
                alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                location.reload();
            },
            success : function(data) {
                var rtn = data[data.length-1];
                if (rtn == "Y") {
                    alert("매출 등록이 완료되었습니다.");
                } else if (rtn == "N") {
                    console.log(data);
                    alert("매출 등록에 실패하였습니다. 관리자에게 문의해주세요.");
                } else {
                    console.log(data);
                    alert("알 수 없는 오류가 발생했습니다. 관리자에게 문의해주세요.");
                }
                location.reload();
            }
        });
    }
});
$(document).on("change", "#dp_seq", function(e) {
    var seq = $(this).val();
    if (seq == '0') {
        $("#ds_name").removeAttr("readonly");
        $("#ds_price").val("");
    } else {
        $("#ds_name").val($("#dp_seq option:selected").attr("text"));
        $("#ds_name").attr("readonly", "true");
        $("#ds_price").val($("#dp_seq option:selected").attr("price"));
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
        <div class="container-fhd mt-3 p-0">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 mb-0 text-gray-800">DIVISION STUDIO 매출정보</h1>
          </div>
          <div class="card border-success">
            <div class="card-header pl-4 bg-primary text-white"><h4>매출 등록</h4></div>
            <div class="card-body p-3">
              <div class="row justify-content-center align-items-center text-right">
                <div class="col-3 my-1">
                  품목명
                </div>
                <div class="col-9 my-1">
                  <select class="form-control form-control-user" id="dp_seq" name="dp_seq">
                    <option value="0" selected>직접 입력</option>
                    <?php
                        foreach($product_data as $p_row) {
                    ?>
                    <option value="<?=$p_row['dp_seq']?>" text="<?=$p_row['dp_name']?>" price="<?=$p_row['dp_price']?>"><?=$p_row['dp_name']?> (재고:<?=$p_row['dp_count']?>)</option>
                    <?php
                        }
                    ?>
                  </select>
                </div>
                <div class="col-3 my-1">
                  품목명
                </div>
                <div class="col-9 my-1">
                  <input class="form-control form-control-user" type="text" id="ds_name" name="ds_name"></input>
                </div>
                <div class="col-3 my-1">
                  가격
                </div>
                <div class="col-9 my-1">
                  <input class="form-control form-control-user" type="text" id="ds_price" name="ds_price"></input>
                </div>
                <div class="col-3 my-1">
                  메모
                </div>
                <div class="col-9 my-1">
                  <input class="form-control form-control-user" type="text" id="ds_memo" name="ds_memo"></input>
                </div>
                <div class="col-12 my-1">
                  <button class="btn btn-block btn-primary" type="button" id="sales_btn">등록하기</button>
                </div>
              </div>
            </div>
          </div>
          <table class="table table-bordered text-center my-2 text-black" style="font-size:0.8rem;">
            <thead>
              <th scope="col" style="min-width:100px; width:100px">매출시각</th>
              <th scope="col" style="min-width:120px">품목명<br>메모</th>
              <th scope="col" style="min-width:100px">가격</th>
            </thead>
            <tbody>
              <?php
              if ($sales_data) {
                foreach($sales_data as $sales_row) {
              ?>
              <tr>
                <td>
                  <?=$sales_row['ds_datetime']?>
                </td>
                <td>
                  <?=$sales_row['ds_name']?><br>
                  (<?=$sales_row['ds_memo']?>)
                </td>
                <td>
                  <?=number_format($sales_row['ds_price'])?> 원
                </td>
              </tr>
              <?php
                }
              } else {
              ?>
              <tr><td colspan="3">매출 정보가 없습니다.</td></tr>
              <?php
              }
              ?>
            </tbody>
          </table>
          <?php include $common_dir . "/table_paging.php"; ?>

        </div>
        <!-- /.container-fluid -->
      <form method="post" id="pageform" name="pageform" class="user page_form">
      <input type="hidden" id="page" name="page" value=""></input>
      </form>
      <?php require_once $common_dir . "/body_bottom.php"; ?>

    </div>
    <!-- End of Content Wrapper -->


  </div>
  <!-- End of Page Wrapper -->

</body>

</html>
