<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container py-4">

    <h2 class="fw-bold text-warning mb-4">🛠 Admin Dashboard</h2>

    <!-- ===== STAT CARDS ===== -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="text-warning fw-bold"><?= $totalUsers ?></h3>
                    <div class="text-secondary small">Người dùng</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="text-warning fw-bold"><?= $totalDocs ?></h3>
                    <div class="text-secondary small">Tài liệu</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="text-warning fw-bold"><?= $pendingDocs ?></h3>
                    <div class="text-secondary small">Chờ duyệt</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="text-warning fw-bold"><?= $totalComments ?></h3>
                    <div class="text-secondary small">Bình luận</div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===== CATEGORY ===== -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold text-warning">📂 Quản lý danh mục</h4>
        <a href="index.php?c=category&a=admin" class="btn btn-warning btn-sm">
            Quản lý danh mục
        </a>
    </div>

    <!-- ===== PENDING DOCUMENTS ===== -->
    <h4 class="fw-semibold text-warning mt-4">⏳ Tài liệu chờ duyệt</h4>

    <div class="table-responsive mt-3">
        <table class="table table-dark table-bordered table-hover align-middle">
            <thead class="table-secondary text-dark">
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Người đăng</th>
                    <th>Ngày đăng</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $userDAO = new UserDAO();
            foreach ($pendingList as $d):
                $uploader = $userDAO->find($d->getUploaderId());
            ?>
                <tr>
                    <td><?= $d->getId() ?></td>
                    <td><?= htmlspecialchars($d->getTitle()) ?></td>
                    <td><?= htmlspecialchars($uploader->getName() ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($d->getCreatedAt()) ?></td>
                    <td class="text-center">
                        <a href="index.php?c=document&a=approve&id=<?= $d->getId() ?>"
                           class="btn btn-sm btn-success me-1">
                            ✔ Duyệt
                        </a>
                        <a href="index.php?c=document&a=reject&id=<?= $d->getId() ?>"
                           class="btn btn-sm btn-danger">
                            ✖ Từ chối
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
