<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="/assets/css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh">
        <div class="card shadow p-4" style="max-width: 420px; width: 100%;">
            <h3 class="text-center mb-4">📝 Đăng ký tài khoản</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="../../index.php?c=user&a=register">
                <div class="mb-3">
                    <label>Họ và tên</label>
                    <input name="fullname" type="text" class="form-control" required placeholder="Nhập họ tên">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input name="email" type="email" class="form-control" required placeholder="Nhập email">
                </div>

                <div class="mb-3">
                    <label>Mật khẩu</label>
                    <input name="password" type="password" class="form-control" required placeholder="Mật khẩu">
                </div>

                <div class="mb-3">
                    <label>Nhập lại mật khẩu</label>
                    <input name="confirm" type="password" class="form-control" required placeholder="Nhập lại mật khẩu">
                </div>

                <button type="submit" class="btn btn-success w-100">Đăng ký</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>