<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Upload tài liệu</h1>

<form method="post" enctype="multipart/form-data" action="">
    <div class="mb-3">
        <label class="form-label">Tiêu đề</label>
        <input name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Danh mục</label>
        <select name="category_id" class="form-select" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c->getId() ?>"><?= htmlspecialchars($c->getName()) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Chọn file</label>
        <input type="file" name="file" class="form-control" accept=".pdf,.docx,.pptx,.zip" required>
    </div>
    <button class="btn btn-success">Upload</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>