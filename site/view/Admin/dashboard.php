<!-- view/admin/dashboard.php -->

<h2>📊 Admin Dashboard</h2>

<div class="row mt-4">

    <div class="col-md-3">
        <div class="box">
            <h3><?= $totalUsers ?></h3>
            <p>Người dùng</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box">
            <h3><?= $totalDocs ?></h3>
            <p>Tài liệu</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box">
            <h3><?= $pendingDocs ?></h3>
            <p>Chờ duyệt</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box">
            <h3><?= $totalComments ?></h3>
            <p>Bình luận</p>
        </div>
    </div>

</div>

<hr>

<h3>⏳ Tài liệu chờ duyệt</h3>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Người đăng</th>
            <th>Ngày đăng</th>
            <th>Duyệt</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pendingList as $d): ?>
        <tr>
            <td><?= $d->getId() ?></td>
            <td><?= htmlspecialchars($d->getTitle()) ?></td>
            <td><?= htmlspecialchars($d->getUploaderName()) ?></td>
            <td><?= $d->getCreatedAt() ?></td>
            <td>
                <a class="btn btn-success btn-sm" href="/admin/approve.php?id=<?= $d->getId() ?>">Duyệt</a>
                <a class="btn btn-danger btn-sm" href="/admin/reject.php?id=<?= $d->getId() ?>">Từ chối</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
