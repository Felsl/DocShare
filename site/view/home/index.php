<?php require './layout/header.php'; ?>

<link rel="stylesheet" href="assets/css/home.css">

<?php
// xác định có đang search hay không
$isSearch = isset($_GET['q']) && trim($_GET['q']) !== '';
?>

<!-- HERO chỉ hiển thị khi KHÔNG search -->
<?php if (!$isSearch): ?>
    <div class="home-hero">
        <div class="container hero-inner">
            <div class="hero-left">
                <h1 class="hero-title">DocShare — Tài liệu học tập</h1>
                <p class="hero-sub">Chia sẻ tài liệu, học tập cùng cộng đồng. Tìm nhanh — Tải ngay — Chia sẻ kiến thức.</p>
                <div class="hero-cta">
                    <a href="<?= $base ?>/index.php?c=document&a=upload" class="btn btn-gold">📤 Upload</a>
                    <a href="<?= $base ?>/index.php?c=document&a=index" class="btn btn-outline-light ms-2">📚 Tất cả tài
                        liệu</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container mt-5">

    <!-- TIÊU ĐỀ -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="section-title">
            <?php if ($isSearch): ?>
                🔍 Kết quả tìm kiếm cho:
                <span class="text-warning">“<?= htmlspecialchars($_GET['q']) ?>”</span>
            <?php else: ?>
                📚 Tài liệu mới cập nhật
            <?php endif; ?>
        </h3>

        <?php if (!$isSearch): ?>
            <a href="<?= $base ?>/index.php?c=document&a=index" class="small text-muted">
                Xem tất cả →
            </a>
        <?php endif; ?>
    </div>

    <!-- DANH SÁCH -->
    <div class="row g-4">

        <?php if (empty($latest)): ?>

            <!-- KHÔNG CÓ KẾT QUẢ -->
            <div class="col-12">
                <div class="alert alert-warning">
                    ❌ Không tìm thấy tài liệu phù hợp.
                </div>
            </div>

        <?php else: ?>

            <?php
            $userDAO = new UserDAO();
            foreach ($latest as $item):
                $user = $userDAO->find($item->getUploaderId());
                ?>

                <div class="col-12 col-md-6">
                    <div class="card dark-card">
                        <div class="card-body">
                            <h5 class="card-title mb-1">
                                <?= htmlspecialchars($item->getTitle()) ?>
                            </h5>

                            <p class="card-text text-muted small truncate-2">
                                <?= htmlspecialchars($item->getDescription()) ?>
                            </p>

                            <div class="meta d-flex justify-content-between align-items-center mt-3">
                                <div class="small text-light">
                                    <i class="bi bi-person"></i>
                                    <?= htmlspecialchars($user ? $user->getName() : 'Unknown') ?>
                                </div>

                                <div>
                                    <a href="<?= $base ?>/index.php?c=document&a=detail&id=<?= urlencode($item->getId()) ?>"
                                        class="btn btn-sm btn-gold">
                                        Xem
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>

<?php require './layout/footer.php'; ?>