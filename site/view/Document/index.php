<?php
// site/view/document/index.php
// $docs: array of Document objects or stdClass

require './layout/header.php'; ?>

<h3>Truyện/Tài liệu mới cập nhật</h3>

<?php if (empty($docs)): ?>
    <p>Không có tài liệu nào.</p>
<?php else: ?>
    <div class="row">
        <?php foreach ($docs as $doc):
            // Lấy tiêu đề an toàn (Document có thể có getter)
            if (is_object($doc)) {
                $title = method_exists($doc, 'getTitle') ? $doc->getTitle() :
                    (property_exists($doc, 'title') ? $doc->title : 'Untitled');
                $thumb = method_exists($doc, 'getThumbnail') ? $doc->getThumbnail() : ($doc->thumbnail ?? '/assets/img/placeholder.png');
            } else {
                $title = $doc['title'] ?? 'Untitled';
                $thumb = $doc['thumbnail'] ?? '/assets/img/placeholder.png';
            }
            ?>
            <?php
            // Lấy dữ liệu từ object Document bằng getter hoặc fallback
            $docId = method_exists($doc, 'getId') ? $doc->getId() : ($doc->id ?? '');
            $title = method_exists($doc, 'getTitle') ? $doc->getTitle() : ($doc->title ?? 'Untitled');
            $thumb = method_exists($doc, 'getThumbnail') ? $doc->getThumbnail() : ($doc->thumbnail ?? '/assets/img/placeholder.png');
            $downloads = method_exists($doc, 'getDownloads') ? $doc->getDownloads() : ($doc->downloads ?? 0);
            ?>

            <div class="col-md-6 doc-card">
                <div class="card bg-dark text-light">
                    <div class="row g-0">


                        <div class="col">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($title) ?></h5>

                                <a href="<?= $base ?>/index.php?c=document&a=detail&id=<?= urlencode($docId) ?>"
                                    class="btn btn-sm btn-outline-warning mt-2">
                                    Xem
                                </a>

                                <div class="small text-muted mt-2">
                                    <?= intval($downloads) ?> lượt tải
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require './layout/footer.php' ?>