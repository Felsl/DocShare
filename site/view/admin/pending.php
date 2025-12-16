<?php require __DIR__ . '/../layout/header.php'; ?>

<h1>Quản trị — Tài liệu chờ duyệt</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Uploader</th>
            <th>Ngày</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($docs)): ?>
            <tr>
                <td colspan="5">Không có tài liệu chờ duyệt.</td>
            </tr>
        <?php else:
            foreach ($docs as $d): ?>
                <tr>
                    <td><?= $d->getId() ?></td>
                    <td><?= htmlspecialchars($d->getTitle()) ?></td>
                    <td><?= $d->getUploaderId() ?></td>
                    <td><?= $d->getCreatedAt() ?></td>
                    <td>
                        <a class="btn btn-sm btn-success"
                            href="/admin.php?controller=document&action=approve&id=<?= $d->getId() ?>">Duyệt</a>
                        <a class="btn btn-sm btn-danger"
                            href="/admin.php?controller=document&action=reject&id=<?= $d->getId() ?>">Từ chối</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../layout/footer.php'; ?>