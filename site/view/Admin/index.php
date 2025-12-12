<h1>Admin Dashboard</h1>

<h2>Thống kê</h2>
<ul>
    <li>Tổng tài liệu: <?= $total_docs ?></li>
    <li>Pending: <?= $pending_docs ?></li>
    <li>Approved: <?= $approved_docs ?></li>
    <li>Rejected: <?= $rejected_docs ?></li>
    <li>Tổng người dùng: <?= $total_users ?></li>
    <li>Uploader: <?= $uploaders ?></li>
    <li>Admin: <?= $total_admins ?></li>
</ul>

<h2>Tài liệu chờ duyệt</h2>
<?php foreach ($pendingList as $d): ?>
    <div>
        <b><?= htmlspecialchars($d['title']) ?></b><br>
        Người đăng: <?= $d['uploader_name'] ?> |
        Danh mục: <?= $d['category_name'] ?> |
        <a href="approve.php?id=<?= $d['id'] ?>">Duyệt</a> |
        <a href="reject.php?id=<?= $d['id'] ?>">Từ chối</a>
    </div>
<?php endforeach; ?>