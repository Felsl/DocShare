<?php require './layout/header.php' ?>

<h3>✏️ Sửa danh mục</h3>

<form method="post"
      action="index.php?c=category&a=update&id=<?= $category->getId() ?>"
      class="col-md-6">
    

    <div class="mb-2">
        <label>Mã</label>
        <input class="form-control" name="code" value="<?= htmlspecialchars($category->getCode()) ?>" required>
    </div>

    <div class="mb-2">
        <label>Tên</label>
        <input class="form-control" name="name" value="<?= htmlspecialchars($category->getName()) ?>" required>
    </div>

    <div class="mb-2">
        <label>Mô tả</label>
        <textarea class="form-control"
            name="description"><?= htmlspecialchars($category->getDescription()) ?></textarea>
    </div>

    <button class="btn btn-primary">💾 Lưu</button>
    <a href="index.php?c=category&a=index" class="btn btn-secondary">⬅️ Quay lại</a>
</form>

<?php require './layout/footer.php' ?>