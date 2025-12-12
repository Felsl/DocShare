<?php require './layout/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <h2><?= htmlspecialchars($doc->getTitle()) ?></h2>
        <p class="text-muted">Danh mục: <?= htmlspecialchars($category->getName() ?? 'N/A') ?> • Người upload:
            <?= htmlspecialchars($uploader->getName() ?? 'N/A') ?>
        </p>
        <p><?= nl2br(htmlspecialchars($doc->getDescription())) ?></p>

        <div class="mb-3">
            <a class="btn btn-primary" href="/<?= $doc->getFilename() ?>" download>⬇ Tải về</a>
            <span class="ms-3">Lượt tải: <?= $doc->getDownloads() ?></span>
        </div>

        <hr>
        <h5>Bình luận</h5>
        <?php if (empty($comments)): ?>
            <p class="text-muted">Chưa có bình luận.</p>
        <?php else:
            foreach ($comments as $c): ?>
                <div class="border rounded p-2 mb-2">
                    <div class="small text-muted"><?= htmlspecialchars($c->getCreatedAt()) ?></div>
                    <div><?= nl2br(htmlspecialchars($c->getContent())) ?></div>
                </div>
            <?php endforeach; endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="post" action="/index.php?controller=comment&action=store">
                <input type="hidden" name="document_id" value="<?= $doc->getId() ?>">
                <div class="mb-2">
                    <textarea name="content" class="form-control" rows="3" placeholder="Viết bình luận..."
                        required></textarea>
                </div>
                <button class="btn btn-sm btn-primary">Gửi bình luận</button>
            </form>
        <?php else: ?>
            <p><a href="/login.php">Đăng nhập</a> để bình luận.</p>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h6>Thông tin tài liệu</h6>
            <ul class="list-unstyled">
                <li>Loại: <?= htmlspecialchars($doc->getFileType()) ?></li>
                <li>Kích thước: <?= number_format($doc->getFilesize() / 1024) ?> KB</li>
                <li>Trạng thái: <?= htmlspecialchars($doc->getStatus()) ?></li>
                <li>Ngày tạo: <?= htmlspecialchars($doc->getCreatedAt()) ?></li>
            </ul>
        </div>
    </div>
</div>

<?php require './layout/footer.php'; ?>