<?php
$common_dir = get_common_dir();
//<!-- Page Header -->
require_once $common_dir . "/header.php";
?>

<script>
    $(document).ready(function(e) {
    });
    $(document).on("change", ".league_select", function(e) {
        $("#league_select_form").submit();
    });
</script>
<!-- Body head -->
<?php require_once $common_dir . "/body_head.php"; ?>
    <!-- Topbar -->
    <?php require_once $common_dir . "/body_topbar.php"; ?>
    <script>
      $("#nav_aevileague").addClass("active");
    </script>
    <!-- Begin Page Content -->
    <div class="container-fhd mt-4">

      <!-- Page Heading -->
      <div class="align-items-center justify-content-between mb-4">
        <form class="user" method="get" id="league_select_form" name="league_select_form">
          <div class="row">
            <div class="col-12 col-sm-6 mb-2">
              <h1 class="h3 mb-0 text-gray-800">AEVILEAGUE</h1>
            </div>
            <?php 
            if (isset($all_league_data)) {
            ?>
            <div class="col-6 col-sm-3">
              <select class="form-control league_select" id="league" name="league">
                <?php
                foreach($all_league_data as $league_row) {
                ?>
                <option value="<?=$league_row['li_season']?>-<?=$league_row['li_degree']?>" <?=($league_row['li_season'] == $league_data['li_season'] && $league_row['li_degree'] == $league_data['li_degree']) ? "selected" : ""?>>시즌 <?=$league_row['li_season']?> - <?=$league_row['li_degree']?></option>
                <?php
                }
                ?>
              </select>
            </div>
            <?php
            }
            ?>
            <div class="col-6 col-sm-3">
              <select class="form-control league_select" id="tier" name="tier">
                <option>Overview</option>
                <?php
                foreach($tier_data as $tier_row) {
                ?>
                <option <?=$tier_row['t_name'] == $current_tier ? "selected" : ""?>><?=$tier_row['t_name']?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>

      <?php
      if ($league_data) {
          if ($league_chartdata) {
      ?>
      <div class="card border-primary mb-3" style="max-width:100%">
        <div class="card-header text-dark bg-white">
          <h3 class="m-0 p-0"><?=$current_tier?> Tier : Songs List</h2>
        </div>
        <div class="card-body p-2">
      <?php
              // Tier Songs List
              $lc_cnt = count($league_chartdata[$current_tier]);
              $div_num = 2;
              $div_cnt = ($lc_cnt / $div_num) + 1;
              $div_col = 12 / $div_num;
              for ($i = 0, $num = 0; $i < $div_cnt; ++$i) {
      ?>
          <div class="row p-0 m-0">
      <?php
                  for ($j = 0; $num < $lc_cnt; ++$j, ++$num) {
                      $chart_row = $league_chartdata[$current_tier][$num];
      ?>
              <div class="col-<?=$div_col?> border border-dark p-1 m-0">
              <img style="width:100%" src="/titles/<?=$chart_row['s_seq']?>_min.jpg">
              <div class="border border-dark p-1 m-0 text-dark"><?=$chart_row['s_artist_kr']?> - <?=$chart_row['s_title_kr']?> <?=get_type_index($chart_row['charttype'])?><?=$chart_row['c_level']?> <?=$chart_row['use_hj'] ? "[HJ]" : ""?></div>
              </div>
      <?php
                  }
      ?>
          </div>
      <?php
              }
      ?>
        </div>
      </div>
      <?php
          // 각 유저별 플레이 정보
          foreach($league_userdata as $u_nick => $user_row) {
              $total_point = 0.0;
              for($i = 0; $i < $lc_cnt; ++$i) {
                  $total_point += isset($league_playdata[$league_chartdata[$current_tier][$i]['c_seq']][$user_row['u_nick']]) ? $league_playdata[$league_chartdata[$current_tier][$i]['c_seq']][$user_row['u_nick']]['point'] : 0;
              }
      ?>
      <div class="card border-primary mb-3" style="max-width:100%">
        <div class="card-header text-dark bg-white">
          <h3 class="m-0 p-0"><?=$user_row['u_nick']?> (<?=$user_row['ls_mmr']?>) [<?=number_format($total_point, 1)?>]</h2>
        </div>
        <div class="card-body p-2">
          <div class="row p-0 m-0">
      <?php
              for($i = 0; $i < $lc_cnt; ++$i) {
                  $p_nick = $user_row['u_nick'];
                  $chart_row = $league_chartdata[$current_tier][$i];
                  $play_row = isset($league_playdata[$chart_row['c_seq']][$p_nick]) ? $league_playdata[$chart_row['c_seq']][$p_nick] : null;
      ?>
            <div class="col-md-6">
              <div class="row my-1 text-right" href="#" data-toggle="modal" data-target="#piModal<?=$play_row['pi_seq']?>">
                <div class="col-12 border border-dark text-center"><?=$chart_row['s_artist_kr']?> - <?=$chart_row['s_title_kr']?> <?=get_type_index($chart_row['charttype'])?><?=$chart_row['c_level']?> <?=$chart_row['use_hj'] ? "[HJ]" : ""?> [Point <?=$play_row['point'] ?? 0?>]</div>
                <div class="col-6 col-lg-4 border border-dark text-primary">PERFECT <?=$play_row ? $play_row['pi_perfect'] : 0?></div>
                <div class="col-6 col-lg-4 border border-dark text-success">GREAT <?=$play_row ? $play_row['pi_great'] : 0?></div>
                <div class="col-6 col-lg-4 border border-dark text-warning">GOOD <?=$play_row ? $play_row['pi_good'] : 0?></div>
                <div class="col-6 col-lg-4 border border-dark" style="color:purple">BAD <?=$play_row ? $play_row['pi_bad'] : 0?></div>
                <div class="col-6 col-lg-4 border border-dark text-danger">MISS <?=$play_row ? $play_row['pi_miss'] : 0?></div>
                <div class="col-6 col-lg-4 border border-dark" style="color:black">XSCORE <?=$play_row ? $play_row['pi_xscore'] : 0?></div>
              </div>
            </div>
      <?php
                  if ($play_row) {
      ?>
            <!-- Image Modal -->
            <div class="modal fade" id="piModal<?=$play_row['pi_seq']?>" tabindex="-1" role="dialog" aria-labelledby="piModalLabel<?= $play_row['pi_seq']?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="piModalLabel<?=$play_row['pi_seq']?>">플레이 이미지</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <img class="card-img-top" src="<?=PI_IMAGE_PATH?>/<?=$play_row['pi_filename']?>" alt="Image">
                  </div>
                </div>
              </div>
            </div>
      <?php
                  }
              }
      ?>
          </div>
        </div>
      </div>
      <?php
          }
      ?>
      <?php
          } else {
      ?>
        <div class="card border-primary mb-3" style="max-width:100%">
          <div class="card-header text-dark bg-white">
            <h3 class="m-0 p-0">시즌 <?=$league_data['li_season']?> - <?=$league_data['li_degree']?> : 선수 명단</h2>
          </div>
          <div class="card-body p-2">
            <table class="table table-hover border border-light">
              <thead class="table-light text-center">
                <tr>
                  <th scope="col">NAME</th>
                  <th scope="col">MMR</th>
                  <th scope="col">TIER</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($league_userdata) {
                    foreach($league_userdata as $user_row) {
                        $mmr_str = "";
                        $mmr_diff = "(0)";
                        if (isset($prev_userdata[$user_row['u_nick']])) {
                            $mmr_diff = $user_row['ls_mmr'] - $prev_userdata[$user_row['u_nick']]['ls_mmr'];
                            $mmr_diff = "<b><span style='" . ($mmr_diff > 0 ? "color:blue;'>(+" : "color:red;'>(") . $mmr_diff . ")</span></b>";
                            $mmr_str = $prev_userdata[$user_row['u_nick']]['ls_mmr'] . " → " . $user_row['ls_mmr'];
                        } else {
                            $mmr_str = $user_row['ls_mmr'];
                        }
                ?>
                <tr class="text-center">
                  <th scope="row" style="color:black;"><?=$user_row['u_nick']?></th>
                  <td><?= $mmr_str ?> <?= $mmr_diff ?></td>
                  <td style="color:<?=$user_row['t_color']?>;"><?=$user_row['ls_tier']?></td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                  <th class="text-center" colspan="3">유저 정보가 존재하지 않습니다.</th>
                </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php
          }
      } else {
      ?>
      <div class="border border-secondary rounded p-3 mb-3">
        현재 진행중인 리그가 없습니다.
      </div>
      <?php
      }
      ?>

    </div>
    <!-- /.container-fhd -->

    <?php require_once $common_dir . "/footer.php"; ?>