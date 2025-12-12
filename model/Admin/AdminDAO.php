<?php
// model/admin/AdminRepository.php
require_once __DIR__ . '/Admin.php';

class AdminDAO
{
    protected PDO $pdo;

    public function __construct()
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $this->pdo = $GLOBALS['pdo'];
        } else {
            throw new RuntimeException('Global $pdo not found or not a PDO instance.');
        }
    }

    /**
     * Lấy tất cả admin
     * @return Admin[]
     */
    public function all(): array
    {
        $sql = "SELECT * FROM admins ORDER BY id ASC";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $r) {
            $result[] = $this->mapRowToAdmin($r);
        }
        return $result;
    }

    /**
     * Tìm admin theo id
     * @param int $id
     * @return Admin|null
     */
    public function find(int $id): ?Admin
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToAdmin($row) : null;
    }

    /**
     * Tìm admin theo username
     * @param string $username
     * @return Admin|null
     */
    public function findByUsername(string $username): ?Admin
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToAdmin($row) : null;
    }

    /**
     * Tạo mới admin
     * @param Admin $admin
     * @return int inserted id
     * @throws Exception on failure
     */
    public function create(Admin $admin): int
    {
        $sql = "INSERT INTO admins (username, password, full_name, email, phone, created_at)
                VALUES (:username, :password, :full_name, :email, :phone, :created_at)";
        $stmt = $this->pdo->prepare($sql);

        $now = $admin->getCreatedAt() ?? date('Y-m-d H:i:s');

        $ok = $stmt->execute([
            'username' => $admin->getUsername(),
            'password' => $admin->getPassword(),
            'full_name' => $admin->getFullName(),
            'email' => $admin->getEmail(),
            'phone' => $admin->getPhone(),
            'created_at' => $now
        ]);

        if (!$ok) {
            $error = $stmt->errorInfo();
            throw new RuntimeException("Failed to create admin: " . ($error[2] ?? 'unknown'));
        }

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật admin (không đổi username)
     * @param Admin $admin
     * @return bool
     */
    public function update(Admin $admin): bool
    {
        if (!$admin->getId()) {
            throw new InvalidArgumentException('Admin id is required for update.');
        }

        $sql = "UPDATE admins SET 
                    password = :password,
                    full_name = :full_name,
                    email = :email,
                    phone = :phone
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'password' => $admin->getPassword(),
            'full_name' => $admin->getFullName(),
            'email' => $admin->getEmail(),
            'phone' => $admin->getPhone(),
            'id' => $admin->getId()
        ]);
    }

    /**
     * Xóa admin
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM admins WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Kiểm tra đăng nhập (username + plain password).
     * Lưu ý: repository giả định password trong DB là hash.
     * Bạn có thể dùng password_verify nếu hash là bcrypt/argon2; 
     * nếu hệ bạn đang dùng MD5 (không khuyến nghị) thì phải kiểm tra khác.
     *
     * @param string $username
     * @param string $plainPassword
     * @return Admin|null
     */
    public function authenticate(string $username, string $plainPassword): ?Admin
    {
        $admin = $this->findByUsername($username);
        if (!$admin)
            return null;

        $hash = $admin->getPassword();

        // Nếu bạn dùng password_hash() (bcrypt/argon2)
        if (password_get_info($hash)['algo'] !== 0) {
            if (password_verify($plainPassword, $hash)) {
                return $admin;
            }
            return null;
        }

        // Fallback: nếu DB chứa MD5 (legacy) - KHÔNG KHUYẾN NGHỊ dùng MD5 trên production
        if (md5($plainPassword) === $hash) {
            return $admin;
        }

        return null;
    }

    /**
     * Map DB row -> Admin object
     * @param array $r
     * @return Admin
     */
    protected function mapRowToAdmin(array $r): Admin
    {
        return new Admin(
            isset($r['id']) ? (int) $r['id'] : null,
            $r['username'],
            $r['password'],
            $r['full_name'],
            $r['email'] ?? null,
            $r['phone'] ?? null,
            $r['created_at'] ?? null
        );
    }
    public function getStatistics(): array
    {
        $stats = [];

        // Tổng tài liệu
        $stats['total_docs'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM documents
        ")->fetchColumn();

        // Pending
        $stats['pending_docs'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM documents WHERE status = 'pending'
        ")->fetchColumn();

        // Approved
        $stats['approved_docs'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM documents WHERE status = 'approved'
        ")->fetchColumn();

        // Rejected
        $stats['rejected_docs'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM documents WHERE status = 'rejected'
        ")->fetchColumn();

        // Tổng user
        $stats['total_users'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM users
        ")->fetchColumn();

        // Tổng uploader
        $stats['uploaders'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM users WHERE role = 'uploader'
        ")->fetchColumn();

        // Tổng admin
        $stats['total_admins'] = (int) $this->pdo->query("
            SELECT COUNT(*) FROM admins
        ")->fetchColumn();

        return $stats;
    }

    /**
     * Lấy danh sách tài liệu đang chờ duyệt
     */
    public function getPendingList(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT d.*, u.name AS uploader_name, c.name AS category_name
            FROM documents d
            JOIN users u ON d.uploader_id = u.id
            JOIN categories c ON d.category_id = c.id
            WHERE d.status = 'pending'
            ORDER BY d.created_at ASC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
