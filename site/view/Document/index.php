<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Danh sách tài liệu</h1>
    <a href="/index.php?controller=document&action=upload" class="btn btn-primary">📤 Upload tài liệu</a>
</div>

<div class="row">
    <?php if (empty($docs)): ?>
        <div class="col-12">
            <div class="alert alert-info">Chưa có tài liệu nào.</div>
        </div>
    <?php else:
        foreach ($docs as $doc): ?>
            <div class="col-md-4">
                <div class="card doc-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($doc->getTitle()) ?></h5>
                        <p class="card-text truncate"><?= htmlspecialchars($doc->getDescription()) ?></p>
                        <p class="small text-muted">Loại: <?= htmlspecialchars($doc->getFileType()) ?> • Kích thước:
                            <?= number_format($doc->getFilesize() / 1024) ?> KB</p>
                        <a href="/index.php?controller=document&action=detail&id=<?= $doc->getId() ?>"
                            class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                        <a href="/<?= $doc->getFilename() ?>" class="btn btn-sm btn-success" download>⬇ Tải về</a>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>