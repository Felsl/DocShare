<?php require './layout/header.php'; ?>

<h3>📂 Danh sách danh mục</h3>

<form method="post" action="index.php?c=category&a=store" class="row g-2 mb-4">
    <div class="col-md-3">
        <input class="form-control" name="code" placeholder="Mã danh mục" required>
    </div>
    <div class="col-md-3">
        <input class="form-control" name="name" placeholder="Tên danh mục" required>
    </div>
    <div class="col-md-4">
        <input class="form-control" name="description" placeholder="Mô tả">
    </div>
    <div class="col-md-2">
        <button class="btn btn-success w-100">➕ Thêm</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= $c->getId() ?></td>
                <td><?= htmlspecialchars($c->getCode()) ?></td>
                <td><?= htmlspecialchars($c->getName()) ?></td>
                <td><?= htmlspecialchars($c->getDescription()) ?></td>
                <td>
                    <a href="index.php?c=category&a=edit&id=<?= $c->getId() ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                    <a href="index.php?c=category&a=delete&id=<?= $c->getId() ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Xoá danh mục này?')">
                        🗑️ Xoá
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require './layout/footer.php'; ?>
