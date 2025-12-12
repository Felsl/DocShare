<?php require './layout/header.php'; ?>

<h1>Thông báo</h1>
<?php if (empty($news)): ?>
    <div class="alert alert-info">Chưa có thông báo.</div>
<?php else:
    foreach ($news as $n): ?>
        <div class="card mb-3">
            <div class="row g-0">
                <?php if ($n->getImg()): ?>
                    <div class="col-md-3">
                        <img src="<?= htmlspecialchars($n->getImg()) ?>" class="img-fluid rounded-start" alt="">
                    </div>
                <?php endif; ?>
                <div class="col">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($n->getTitle()) ?>         <?php if ($n->isHot()): ?><span
                                    class="badge bg-danger">Hot</span><?php endif; ?></h5>
                        <p class="card-text"><?= htmlspecialchars($n->getShortContent()) ?></p>
                        <a href="/index.php?controller=news&action=show&id=<?= $n->getId() ?>"
                            class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>

<?php require './layout/footer.php'; ?>