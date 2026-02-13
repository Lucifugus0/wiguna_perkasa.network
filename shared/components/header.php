<?php
// Get current user info from session
$user_name = $_SESSION['ses_nama'] ?? 'Guest';
$user_level = $_SESSION['ses_level'] ?? 'User';
?>

<header class="main-header">
    <!-- Logo -->
    <a href="<?= Router::url('dashboard') ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>W</b>PN</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Wiguna</b> Perkasa</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= Router::url('dist/img/user2-160x160.jpg') ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?= htmlspecialchars($user_name) ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= Router::url('dist/img/user2-160x160.jpg') ?>" class="img-circle" alt="User Image">
                            <p>
                                <?= htmlspecialchars($user_name) ?>
                                <small><?= htmlspecialchars($user_level) ?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= Router::url('profile') ?>" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= Router::url('logout.php') ?>" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
