<?php require './layout/header.php'; ?>

<link rel="stylesheet" href="assets/css/home.css">

<div class="home-hero">
    <div class="container hero-inner">
        <div class="hero-left">
            <h1 class="hero-title">DocShare — Tài liệu & Truyện học tập</h1>
            <p class="hero-sub">Chia sẻ tài liệu, học tập cùng cộng đồng. Tìm nhanh — Tải ngay — Chia sẻ kiến thức.</p>
            <div class="hero-cta">
                <a href="<?= $base ?>/index.php?c=document&a=upload" class="btn btn-gold">📤 Upload</a>
                <a href="<?= $base ?>/index.php?c=document&a=index" class="btn btn-outline-light ms-2">📚 Tất cả tài
                    liệu</a>
            </div>
        </div>

        <div class="hero-right d-none d-md-block">
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="section-title">Truyện/Tài liệu mới cập nhật</h3>
        <a href="/index.php?c=document&a=index" class="small text-muted">Xem tất cả →</a>
    </div>

    <div class="row g-4">
        <?php
        // $latest: array of objects (or fallback sample)
        if (empty($latest)) {
            // fallback sample to avoid empty view
            $latest = [
                (object) ['id' => 0, 'title' => 'Tài liệu mẫu 1', 'description' => 'Mô tả ngắn', 'thumbnail' => '/assets/img/placeholder.png', 'downloads' => 123, 'uploader' => 'Admin'],
                (object) ['id' => 1, 'title' => 'Tài liệu mẫu 2', 'description' => 'Mô tả ngắn', 'thumbnail' => '/assets/img/placeholder.png', 'downloads' => 98, 'uploader' => 'UserA'],
            ];
        }
        foreach ($latest as $item): ?>
            <div class="col-12 col-md-6">
                <div class="card dark-card">
                    <div class="row g-0">
                        <div class="col-auto">
                        </div>
                        <div class="col">
                            <div class="card-body">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($item->getTitle()) ?></h5>
                                <p class="card-text text-muted small truncate-2">
                                    <?= htmlspecialchars($item->description ?? '') ?>
                                </p>

                                <div class="meta d-flex justify-content-between align-items-center mt-3">
                                    <div class="small text-muted">
                                        <i class="bi bi-person"></i> <?= htmlspecialchars($item->uploader ?? 'Unknown') ?>
                                        &nbsp;•&nbsp;
                                        <i class="bi bi-download"></i> <?= intval($item->downloads ?? 0) ?> lượt tải
                                    </div>
                                    <div>
                                        <a href="index.php?c=document&a=detail&id=<?= urlencode($item->getId()) ?>"
                                            class="btn btn-sm btn-gold ms-2">Xem</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Section: Featured (carousel-like / grid) -->
    <div class="mt-5">
        <h4 class="section-title">⭐ Nổi bật</h4>
        <div class="row g-3">
            <?php
            if (empty($featured)) {
                $featured = array_slice($latest, 0, 4);
            }
            foreach ($featured as $f):
                // an toàn: lấy title
                if (is_object($f)) {
                    $title = method_exists($f, 'getTitle') ? $f->getTitle() :
                        (property_exists($f, 'title') ? $f->title : 'Untitled');
                    // thumbnail: có thể tên getter khác, thử một vài khả năng
                    if (method_exists($f, 'getThumbnail')) {
                        $thumbnail = $f->getThumbnail();
                    } elseif (method_exists($f, 'getFilename')) {
                        $thumbnail = $f->getFilename();
                    } else {
                        $thumbnail = property_exists($f, 'thumbnail') ? $f->thumbnail : '/assets/img/placeholder.png';
                    }
                    // downloads
                    $downloads = method_exists($f, 'getDownloads') ? $f->getDownloads() : (property_exists($f, 'downloads') ? $f->downloads : 0);
                } else {
                    // fallback nếu $f là mảng
                    $title = $f['title'] ?? ($f->title ?? 'Untitled');
                    $thumbnail = $f['thumbnail'] ?? $f['file'] ?? '/assets/img/placeholder.png';
                    $downloads = $f['downloads'] ?? 0;
                }
                ?>
                <div class="col-6 col-md-3">
                    <div class="card card-feature">
                        <div class="card-body p-2">
                            <h6 class="mb-1"><?= htmlspecialchars($title) ?></h6>
                            <div class="small text-muted"><?= intval($downloads) ?> lượt tải</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

</div>

<?php require './layout/footer.php'; ?>