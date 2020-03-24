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
        <a class="dropdown-item" href="#">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            프로필
        </a>
        <a class="dropdown-item" href="#">
            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
            설정
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
    <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
    </li>
    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Dropdown
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">Something else here</a>
    </div>
    </li>
    <li class="nav-item">
    <a class="nav-link disabled" href="#">Disabled</a>
    </li>
</ul>
        <form>
            <div class="input-group">
                <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
                </div>
            </div>
        </form>
    </div>
</nav>
    
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

<!-- Sidebar Toggle (Topbar) -->
<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
<i class="fa fa-bars"></i>
</button>

<!-- Topbar Search -->
<form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
<div class="input-group">
    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
    <div class="input-group-append">
    <button class="btn btn-primary" type="button">
        <i class="fas fa-search fa-sm"></i>
    </button>
    </div>
</div>
</form>

<!-- Topbar Navbar -->
<ul class="navbar-nav ml-auto">

<!-- Nav Item - Search Dropdown (Visible Only XS) -->
<li class="nav-item dropdown no-arrow d-sm-none">
    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-search fa-fw"></i>
    </a>
    <!-- Dropdown - Messages -->
    <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
        <form class="form-inline mr-auto w-100 navbar-search">
            <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
            </div>
        </form>
        <div class="">
        <i class="fas fa-user-circle"></i>
        </div>
    </div>
</li>

<div class="topbar-divider d-none d-sm-block"></div>

<!-- Nav Item - User Information -->
<li class="nav-item dropdown no-arrow">
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="mr-2 d-inline text-gray-600"><?= $u_nick ? $u_nick : "로그인하세요." ?>&nbsp;&nbsp;</span>
    <i class="fas fa-user-circle fa-2x"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <?php
        if ($u_id) {
        ?>
        <!-- Dropdown - User Information -->
        <a class="dropdown-item" href="#">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            프로필
        </a>
        <a class="dropdown-item" href="#">
            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
            설정
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
</li>
</ul>
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
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="/account/logout">Logout</a>
        </div>
    </div>
</div>
</div>

<!-- End of Topbar -->
