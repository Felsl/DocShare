<?php
// model/user/UserRepository.php
// Yêu cầu: trước khi tạo UserRepository, file tạo PDO phải chạy và gán $pdo (global).
// Ví dụ bạn có file connectDB.php chứa đoạn PDO của bạn và include nó trước khi dùng repository.

require_once __DIR__ . '/User.Class.php'; // điều chỉnh đường dẫn nếu cần

class UserDAO
{
    /** @var PDO */
    protected $pdo;

    public function __construct()
    {
        error_log("PDO CHECK: " . print_r($GLOBALS['pdo'], true));
        // Dùng global $pdo (theo style project hiện tại)
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $this->pdo = $GLOBALS['pdo'];
        } else {
            throw new RuntimeException('Global $pdo không tồn tại. Include file kết nối DB trước khi khởi tạo repository.');
        }
    }

    /**
     * Lấy user theo id
     * @param int $id
     * @return User|null
     */
    public function find(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT id, email, password, name, address, phone, role, status, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToUser($row) : null;
    }

    /**
     * Lấy user theo email (dùng login)
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        $stmt = $this->pdo->prepare("SELECT id, email, password, name, address, phone, role, status, created_at FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToUser($row) : null;
    }

    /**
     * Tạo user mới (đăng ký)
     * $user->getPasswordHash() phải là hash (password_hash)
     * @param User $user
     * @return int inserted id
     */
    public function save(User $user): int
    {
        $sql = "INSERT INTO users (email, password, name, address, phone, role, status, created_at)
                VALUES (:email, :password, :name, :address, :phone, :role, :status, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $now = date('Y-m-d H:i:s');

        $stmt->execute([
            'email' => $user->getEmail(),
            'password' => $user->getPasswordHash(),
            'name' => $user->getName(),
            'address' => $user->getAddress(),
            'phone' => $user->getPhone(),
            'role' => $user->getRole() ?? 'user',
            'status' => $user->getStatus() ?? 1,
            'created_at' => $user->getCreatedAt() ?? $now
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật profile (không đổi mật khẩu ở đây)
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        if ($user->getId() === null) {
            throw new InvalidArgumentException('User id required for update.');
        }

        $sql = "UPDATE users SET
                    email = :email,
                    name = :name,
                    address = :address,
                    phone = :phone,
                    role = :role,
                    status = :status
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'address' => $user->getAddress(),
            'phone' => $user->getPhone(),
            'role' => $user->getRole(),
            'status' => $user->getStatus(),
            'id' => $user->getId()
        ]);
    }

    /**
     * Update mật khẩu
     * @param int $id
     * @param string $newHash
     * @return bool
     */
    public function updatePasswordById(int $id, string $newHash): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :pwd WHERE id = :id");
        return $stmt->execute(['pwd' => $newHash, 'id' => $id]);
    }

    /**
     * Xóa user (admin)
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy danh sách user (dành cho admin) với limit/offset tuỳ chọn
     * @param int|null $limit
     * @param int|null $offset
     * @return User[]
     */
    public function getAll(?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT id, email, password, name, address, phone, role, status, created_at FROM users ORDER BY created_at DESC";
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($limit !== null) {
            $sql .= " LIMIT :limit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->pdo->query($sql);
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->mapRowToUser($row);
        }
        return $users;
    }

    /**
     * Kiểm tra tồn tại email (dùng validate khi đăng ký)
     * @param string $email
     * @return bool
     */
    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Map array row -> User object
     * @param array $row
     * @return User
     */
    protected function mapRowToUser(array $row)
    {
        // User::__construct(?int $id, string $email, string $passwordHash, string $name, ?string $address, ?string $phone, string $role, int $status, ?string $createdAt)
        return new User(
            (int) $row['id'],
            $row['email'],
            $row['password'],
            $row['name'],
            $row['address'] ?? null,
            $row['phone'] ?? null,
            $row['role'] ?? 'user',
            isset($row['status']) ? (int) $row['status'] : 1,
            $row['created_at'] ?? null
        );
    }
}
