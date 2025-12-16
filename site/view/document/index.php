<?php
// site/view/document/index.php
// Biến bắt buộc từ controller:
// - $documents : array<Document>
// - (optional) $category : Category
// - (optional) $_GET['q']

require './layout/header.php';
?>

<h3 class="mb-3">
    <?php if (isset($category)): ?>
        📂 Danh mục: <?= htmlspecialchars($category->getName()) ?>
    <?php elseif (!empty($_GET['q'])): ?>
        🔍 Kết quả tìm kiếm cho:
        <span class="text-warning">“<?= htmlspecialchars($_GET['q']) ?>”</span>
    <?php else: ?>
        📚 Tài liệu mới cập nhật
    <?php endif; ?>
</h3>

<?php if (empty($documents)): ?>
    <div class="alert alert-warning">
        ❌ Không có tài liệu nào.
    </div>
<?php else: ?>

    <div class="row">
        <?php foreach ($documents as $doc): ?>
            <?php
            // Document object – dùng getter trực tiếp
            $docId = $doc->getId();
            $title = $doc->getTitle();
            $downloads = $doc->getDownloads();
            ?>

            <div class="col-md-6 doc-card mb-3">
                <div class="card bg-dark text-light h-100">
                    <div class="card-body">

                        <h5 class="card-title">
                            <?= htmlspecialchars($title) ?>
                        </h5>

                        <a href="<?= $base ?>/index.php?c=document&a=detail&id=<?= urlencode($docId) ?>"
                            class="btn btn-sm btn-outline-warning mt-2">
                            Xem
                        </a>

                        <div class="small text-muted mt-2">
                            <?= (int) $downloads ?> lượt tải
                        </div>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?php require './layout/footer.php'; ?>