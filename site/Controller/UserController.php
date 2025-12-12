<?php

class UserController
{
    protected $userDAO;

    public function __construct()
    {
        // Load UserDAO
        if (!class_exists("UserDAO")) {
            require_once __DIR__ . "/../../model/User/UserDAO.php";
        }

        $this->userDAO = new UserDAO();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hiển thị form login
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $error = null;
            require __DIR__ . "/../view/user/login.php";
            return;
        }

        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");

        // ===============================
        // 1) USER LOGIN (bcrypt)
        // ===============================
        $user = $this->userDAO->findByEmail($email);

        if ($user) {

            // USER → bcrypt
            if (!password_verify($password, $user->getPasswordHash())) {
                $error = "Sai mật khẩu.";
                require __DIR__ . "/../view/user/login.php";
                return;
            }

            $_SESSION["user_id"] = $user->getId();
            $_SESSION["is_admin"] = false;

            header("Location: index.php?c=home&a=index");
            exit;
        }

        // ===============================
        // 2) ADMIN LOGIN (MD5)
        // ===============================
        require_once dirname(__DIR__, 2) . "/model/Admin/AdminDAO.php";
        $adminDAO = new AdminDAO();
        $admin = $adminDAO->findByEmail($email);

        if ($admin) {

            // ADMIN → md5
            if (md5($password) !== $admin->getPassword()) {
                $error = "Sai mật khẩu.";
                require __DIR__ . "/../view/user/login.php";
                return;
            }

            $_SESSION["admin_id"] = $admin->getId();
            $_SESSION["is_admin"] = true;

            header("Location: index.php?c=admin&a=index");
            exit;
        }

        $error = "Email không tồn tại.";
        require __DIR__ . "/../view/user/login.php";
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php?c=user&a=login");
        exit;
    }
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $error = null;
            require __DIR__ . '/../view/user/register.php';
            return;
        }

        // POST data
        $name = trim($_POST["fullname"] ?? '');
        $email = trim($_POST["email"] ?? '');
        $password = trim($_POST["password"] ?? '');
        $confirm = trim($_POST["confirm"] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $error = "Vui lòng nhập đầy đủ thông tin.";
            require __DIR__ . '/../view/user/register.php';
            return;
        }

        if ($password !== $confirm) {
            $error = "Mật khẩu nhập lại không khớp.";
            require __DIR__ . '/../view/user/register.php';
            return;
        }

        // Kiểm tra email tồn tại
        $exist = $this->userDAO->findByEmail($email);
        if ($exist) {
            $error = "Email đã được đăng ký.";
            require __DIR__ . '/../view/user/register.php';
            return;
        }

        // Hash password (BCRYPT)
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Tạo User object
        $user = new User(
            null,
            $email,
            $hash,     // sẽ gán qua setter -> dùng getPasswordHash()
            $name,
            "",        // nếu chưa có input ở view -> default
            "",
            "user",
            1,
            date("Y-m-d H:i:s")
        );

        // Ghi DB
        $id = $this->userDAO->save($user);

        if ($id <= 0) {
            $error = "Không thể tạo tài khoản. Vui lòng thử lại.";
            require __DIR__ . '/../view/user/register.php';
            return;
        }

        // Thành công → chuyển sang login
        header("Location: index.php?c=user&a=login");
        exit;
    }


}
