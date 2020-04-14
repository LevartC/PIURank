<?php
    $u_id = isset($_SESSION['u_id']) ? $_SESSION['u_id'] : null;
    $u_nick = isset($_SESSION['u_nick']) ? $_SESSION['u_nick'] : null;
?>

<!-- Plug Bootstrap Nav Bar code here -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow">

    <!-- Nav Item - User Information -->
    <div class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle align-items-center d-flex" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-inline text-gray-600" ><?= $u_nick ? $u_nick : "로그인하세요." ?>&nbsp;&nbsp;</span>
            <i class="fas fa-user-circle fa-2x" style="color:#d1d3e2"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <?php
            if ($u_id) {
            ?>
            <!-- Dropdown - User Information -->
            <a class="dropdown-item" href="/account/profile">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                프로필
            </a>
            <a class="dropdown-item" href="/account/myplay">
                <i class="fas fa-list-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                내 기록
            </a>
            <!-- Activity Log 
            <a class="dropdown-item" href="#">
                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                Activity Log
            </a>
            -->
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                로그아웃
            </a>
            <?php
            } else {
            ?>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#loginModal">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                로그인
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/account/register">
                <i class="fas fa-sign-in-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                회원 가입
            </a>
            <?php
            }
            ?>
        </div>
    </div>
        
    <div class="topbar-divider d-none d-lg-block"></div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item" id="nav_home">
                <a class="nav-link" href="/">
                <i class="fas fa-home"></i>
                <span>HOME</span></a>
            </li>
            <div class="topbar-divider d-none d-lg-block"></div>
            <li class="nav-item" id="nav_input_pi">
                <a class="nav-link" href="/playinfo/write">
                    <i class="fas fa-fw fa-table"></i>
                    <span>기록 입력</span>
                </a>
            </li>
            <div class="topbar-divider d-none d-lg-block"></div>
            <li class="nav-item" id="nav_aevileague">
                <a class="nav-link" href="/playinfo/write">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Aevileague</span>
                </a>
            </li>
<?php
if (isset($_SESSION['u_class']) && $_SESSION['u_class'] <= '2') {
?>
            <div class="topbar-divider d-none d-lg-block"></div>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    관리자
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="/admin/aevileague">Aevileague 관리자</a>
                    <a class="dropdown-item" href="/admin/playinfo">기록 관리자</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
<?php
}
?>
        </ul>
        <form>
            <div class="input-group">
                <input type="text" disabled class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
                </div>
            </div>
        </form>
    </div>
</nav>
    
<!-- Login Modal-->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
            <form class="user" method="post" id="login_form" name="login_form" action="/account/login_action">
                <div class="form-group">
                    <input type="text" class="form-control form-control-user" name="login_id" id="login_id" placeholder="ID를 입력하세요." required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control form-control-user" name="login_pw" id="login_pw" placeholder="Password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">취소</button>
                <button type="submit" class="btn btn-primary">로그인</a>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">로그아웃</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">정말로 로그아웃하시겠습니까?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">취소</button>
                <a class="btn btn-primary" href="/account/logout">로그아웃</a>
            </div>
        </div>
    </div>
</div>

<!-- End of Topbar -->
