<?php
// controller/admin/AdminController.php
require_once __DIR__ . '/../../model/admin/AdminDAO.php';

class AdminController
{
    protected AdminDAO $repo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->repo = new AdminDAO();
        }
        if (!($_SESSION["is_admin"] ?? false)) {
            http_response_code(403);
            echo "Bạn không có quyền truy cập trang quản trị.";
            exit;
        }

        // Repository tự xử lý $GLOBALS['pdo']

    }


    /**
     *Lấy thông tin admin theo ID
     */
    public function getAdmin(int $id): ?Admin
    {
        return $this->repo->find($id);
    }

    /**
     * Lấy admin theo username
     */
    public function getByUsername(string $username): ?Admin
    {
        return $this->repo->findByUsername($username);
    }

    /**
     * Tạo admin mới
     */
    public function create(array $data): int
    {
        $admin = new Admin(
            null,
            $data['username'],
            $data['password'], // đã hash trước khi truyền vào controller
            $data['full_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            null
        );

        return $this->repo->create($admin);
    }

    /**
     * Cập nhật admin
     */
    public function update(int $id, array $data): bool
    {
        $admin = $this->repo->find($id);
        if (!$admin)
            return false;

        if (isset($data['password'])) {
            $admin->setPassword($data['password']);
        }
        if (isset($data['full_name'])) {
            $admin->setFullName($data['full_name']);
        }
        if (isset($data['email'])) {
            $admin->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $admin->setPhone($data['phone']);
        }

        return $this->repo->update($admin);
    }

    /**
     * Xóa admin
     */
    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
    public function dashboard()
    {
        $stats = $this->repo->getStatistics();
        $pendingList = $this->repo->getPendingList();
        // có thể thêm phân trang/ lọc
        require __DIR__ . "/../../view/admin/dashboard.php";
    }

}
