<?php
// site/view/layout/header.php
if (session_status() === PHP_SESSION_NONE)
    session_start();

// compute base path so links work both on local and when deployed to subfolder
// e.g. if script is /DocShare/site/index.php -> $base = /DocShare/site
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

?><!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>DocShare - Chia sẻ tài liệu</title>

    <!-- Prefer CDN for reliability. If you want local, change to "$base/assets/..." -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Your custom CSS (use $base so it works in subfolder) -->
    <link rel="stylesheet" href="<?= $base ?>/assets/css/home.css">

    <style>
        .doc-card {
            margin-bottom: 1rem;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <div class="container">
            <a class="navbar-brand" href="<?= $base ?>/index.php">DocShare</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php?c=document&a=index">Tài
                            liệu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php?c=category&a=index">Danh
                            mục</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php?c=news&a=index">Thông báo</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/profile.php">Xin chào,
                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php?c=user&a=logout">Đăng xuất</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php?c=user&a=login">Đăng
                                nhập</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php?c=user&a=register">Đăng
                                ký</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- main container start - views should NOT close body/html -->
    <div class="container">