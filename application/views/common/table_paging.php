<script>
    $(document).on("click", ".page_link", function(e){
        e.preventDefault();
        var page_val = parseInt($(this).html());
        var page = $("#page").val() == "" ? 1 : parseInt($("#page").val());
        if (page_val > 0) {
            $("#page").val(page_val);
        } else {
            $("#page").val(page + parseInt($(this).attr("tabindex")));
        }
        $(".page_form").submit();
    });
</script>

<?php
if ($page > 0) {
?>
<!-- Paging -->
<ul class="pagination justify-content-center">
  <?php
  $page_min = ($page - 4) >= 1 ? $page - 4 : 1;
  $last_page = $page_cnt % $page_rows == 0 ? (int)($page_cnt / $page_rows) : (int)($page_cnt / $page_rows) + 1;
  $page_max = ($page + 4) <= $last_page ? $page + 4 : $last_page;
  $prev_disabled = $page > 1 ? "" : "disabled";
  $next_disabled = $page < $last_page ? "" : "disabled";
  ?>
  <?php
  if ($page_min != 1) {
  ?>
  <li class="page-item">
    <a class="page-link page_link" href="">1</a>
  </li>
  <li class="page-item disabled">
    <a class="page-link">...</a>
  </li>
  <?php
  }
  ?>
  <li class="page-item <?=$prev_disabled?>">
    <a class="page-link page_link" href="" tabindex="-1">Prev</a>
  </li>
  <?php
  for ($i = $page_min; $i <= $page_max; ++$i) {
  ?>
  <li class="page-item <?= ($i == $page) ? "active" : "" ?>">
    <a class="page-link page_link" href=""><?= $i ?></a>
  </li>
  <?php
  }
  ?>
  <li class="page-item <?=$next_disabled?>">
    <a class="page-link page_link" href="" tabindex="1">Next</a>
  </li>
  <?php
  if ($page_max != $last_page) {
  ?>
  <li class="page-item disabled">
    <a class="page-link">...</a>
  </li>
  <li class="page-item">
    <a class="page-link page_link" href=""><?= $last_page ?></a>
  </li>
  <?php
  }
  ?>
</ul>
<?php
}
?>