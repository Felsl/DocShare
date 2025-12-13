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
        }

        $this->repo = new AdminDAO();

        // Kiểm tra quyền admin
        if (!($_SESSION["is_admin"] ?? false)) {
            http_response_code(403);
            echo "Bạn không có quyền truy cập trang quản trị.";
            exit;
        }
    }

    /**
     * ⚡ FIX: Router gọi mặc định admin/index → phải có action này
     */
    public function index()
    {
        // Chuyển hướng về dashboard
        header("Location: index.php?c=admin&a=dashboard");
        exit;
    }

    /**
     * Trang Dashboard Admin
     */
    public function dashboard()
    {
        $stats = $this->repo->getStatistics();
        $pendingList = $this->repo->getPendingList();

        // Map thống kê sang biến đơn
        $totalUsers = $stats['total_users'] ?? 0;
        $totalDocs = $stats['total_docs'] ?? 0;
        $pendingDocs = $stats['pending_docs'] ?? 0;
        $totalComments = $stats['total_comments'] ?? 0;

        // Chuyển từng row DB -> Document object
        foreach ($pendingList as &$row) {
            if (is_array($row)) {
                $row = new Document(
                    $row['id'] ?? null,
                    $row['title'] ?? '',
                    $row['slug'] ?? '',
                    $row['description'] ?? '',
                    $row['filename'] ?? '',
                    $row['file_type'] ?? '',
                    (int) ($row['filesize'] ?? 0),
                    (int) ($row['category_id'] ?? 0),
                    (int) ($row['uploader_id'] ?? 0),
                    (int) ($row['downloads'] ?? 0),
                    $row['status'] ?? 'pending',
                    (int) ($row['is_featured'] ?? 0),
                    $row['created_at'] ?? null
                );
            }
        }

        require __DIR__ . "/../view/admin/dashboard.php";
    }


    /**
     * Lấy thông tin admin theo ID
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
            $data['password'], // đã hash trước khi truyền
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
}
