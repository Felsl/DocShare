<?php
// view/layout/header.php
if (session_status() === PHP_SESSION_NONE)
    session_start();
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>DocShare - Chia sẻ tài liệu</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
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

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
        <div class="container">
            <a class="navbar-brand" href="/index.php">DocShare</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/index.php?controller=document&action=index">Tài
                            liệu</a></li>
                    <li class="nav-item"><a class="nav-link" href="/index.php?controller=category&action=index">Danh
                            mục</a></li>
                    <li class="nav-item"><a class="nav-link" href="/index.php?controller=news&action=index">Thông
                            báo</a></li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/profile.php">Xin chào,
                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="/logout.php">Đăng xuất</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/login.php">Đăng nhập</a></li>
                        <li class="nav-item"><a class="nav-link" href="/register.php">Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">