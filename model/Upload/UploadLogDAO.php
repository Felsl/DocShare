<?php
// model/uploads_log/UploadLogRepository.php
require_once __DIR__ . '/UploadLog.Class.php';

class UploadLogDAO
{
    protected $pdo;
    public function __construct()
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO)
            $this->pdo = $GLOBALS['pdo'];
        else
            throw new RuntimeException('Global $pdo not found.');
    }

    public function create(UploadLog $l): int
    {
        $sql = "INSERT INTO uploads_log (document_id, action, admin_id, note, created_at) VALUES (:d, :a, :adm, :n, :t)";
        $stmt = $this->pdo->prepare($sql);
        $now = $l->getCreatedAt() ?? date('Y-m-d H:i:s');
        $stmt->execute([
            'd' => $l->getDocumentId(),
            'a' => $l->getAction(),
            'adm' => $l->getAdminId(),
            'n' => $l->getNote(),
            't' => $now
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function listByDocument(int $docId): array
    {
        $stmt = $this->pdo->prepare("SELECT ul.*, a.username as admin_username FROM uploads_log ul LEFT JOIN admins a ON ul.admin_id = a.id WHERE ul.document_id = :d ORDER BY ul.created_at DESC");
        $stmt->execute(['d' => $docId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM uploads_log WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
