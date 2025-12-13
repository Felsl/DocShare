<!-- view/admin/dashboard.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2> Admin Dashboard</h2>
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
            <?php
            $userDAO = new UserDAO();
            $uploaders = [];
            foreach ($pendingList as $d):
                $uploaders[$d->getId()] = $userDAO->find($d->getUploaderId());
                ?>
                <tr>

                    <td><?= $d->getId() ?></td>
                    <td><?= htmlspecialchars($d->getTitle()) ?></td>
                    <td><?= htmlspecialchars($uploaders[$d->getId()]->getName()) ?></td>
                    <td><?= $d->getCreatedAt() ?></td>
                    <td>
                        <a class="btn btn-success btn-sm"
                            href="/index.php?c=document&a=approve&id=<?= $d->getId() ?>">Duyệt</a>
                        <a class="btn btn-danger btn-sm" href="/index.php?c=document&a=reject&id=<?= $d->getId() ?>">Từ
                            chối</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>