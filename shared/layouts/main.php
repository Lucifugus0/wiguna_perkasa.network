<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Dashboard' ?> - Wiguna Perkasa Network</title>

    <!-- Favicon -->
    <link rel="icon" href="<?= Router::url('dist/img/logo.png') ?>" type="image/png">

    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= Router::url('bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= Router::url('dist/css/AdminLTE.min.css') ?>">
    <link rel="stylesheet" href="<?= Router::url('dist/css/skins/_all-skins.min.css') ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= Router::url('plugins/select2/select2.min.css') ?>">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= Router::url('plugins/datatables/dataTables.bootstrap.css') ?>">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Modern Custom Styles -->
    <link rel="stylesheet" href="<?= Router::url('shared/assets/css/modern.css') ?>">

    <!-- Page-specific CSS -->
    <?php if (isset($pageCSS)): ?>
        <?php foreach ((array)$pageCSS as $css): ?>
            <link rel="stylesheet" href="<?= Router::url($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Header -->
        <?php require_once __DIR__ . '/../components/header.php'; ?>

        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?= $pageTitle ?? 'Dashboard' ?>
                    <?php if (isset($pageSubtitle)): ?>
                        <small><?= $pageSubtitle ?></small>
                    <?php endif; ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?= Router::url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <?php if (isset($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $item): ?>
                            <?php if (isset($item['url'])): ?>
                                <li><a href="<?= Router::url($item['url']) ?>"><?= $item['label'] ?></a></li>
                            <?php else: ?>
                                <li class="active"><?= $item['label'] ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <?php
                // Display content
                if (isset($content)) {
                    echo $content;
                } elseif (isset($viewFile)) {
                    require $viewFile;
                }
                ?>
            </section>
        </div>

        <!-- Footer -->
        <?php require_once __DIR__ . '/../components/footer.php'; ?>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- jQuery 2.2.3 -->
    <script src="<?= Router::url('plugins/jQuery/jquery-2.2.3.min.js') ?>"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="<?= Router::url('bootstrap/js/bootstrap.min.js') ?>"></script>
    <!-- Select2 -->
    <script src="<?= Router::url('plugins/select2/select2.full.min.js') ?>"></script>
    <!-- DataTables -->
    <script src="<?= Router::url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= Router::url('plugins/datatables/dataTables.bootstrap.min.js') ?>"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- AdminLTE App -->
    <script src="<?= Router::url('dist/js/app.min.js') ?>"></script>

    <!-- Modern Custom JS -->
    <script src="<?= Router::url('shared/assets/js/app.js') ?>"></script>

    <!-- Page-specific JS -->
    <?php if (isset($pageJS)): ?>
        <?php foreach ((array)$pageJS as $js): ?>
            <script src="<?= Router::url($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline Scripts -->
    <?php if (isset($inlineScript)): ?>
        <script>
            <?= $inlineScript ?>
        </script>
    <?php endif; ?>
</body>
</html>
