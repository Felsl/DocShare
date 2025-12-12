<?php
// model/comment/CommentRepository.php
require_once __DIR__ . '/Comment.php';

class CommentDAO
{
    protected $pdo;

    public function __construct()
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $this->pdo = $GLOBALS['pdo'];
        } else {
            throw new RuntimeException('Global $pdo not found. Include DB connection first.');
        }
    }

    // Create
    public function create(Comment $c): int
    {
        $sql = "INSERT INTO comments (document_id, user_id, content, created_at) VALUES (:d, :u, :c, :t)";
        $stmt = $this->pdo->prepare($sql);
        $now = $c->getCreatedAt() ?? date('Y-m-d H:i:s');
        $stmt->execute([
            'd' => $c->getDocumentId(),
            'u' => $c->getUserId(),
            'c' => $c->getContent(),
            't' => $now
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    // Read single
    public function find(int $id): ?Comment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToComment($r) : null;
    }

    // List by document
    public function listByDocument(int $documentId, int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM comments WHERE document_id = :d ORDER BY created_at DESC LIMIT :off, :lim";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':d', $documentId, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r)
            $out[] = $this->mapRowToComment($r);
        return $out;
    }

    // Update
    public function update(Comment $c): bool
    {
        if ($c->getId() === null)
            throw new InvalidArgumentException('Comment id required');
        $sql = "UPDATE comments SET content = :c WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['c' => $c->getContent(), 'id' => $c->getId()]);
    }

    // Delete
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Count for document
    public function countByDocument(int $documentId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM comments WHERE document_id = :d");
        $stmt->execute(['d' => $documentId]);
        return (int) $stmt->fetchColumn();
    }

    protected function mapRowToComment(array $r): Comment
    {
        return new Comment(
            isset($r['id']) ? (int) $r['id'] : null,
            (int) $r['document_id'],
            (int) $r['user_id'],
            $r['content'],
            $r['created_at'] ?? null
        );
    }
    public function getByDocument(int $docId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE document_id = :d ORDER BY created_at DESC");
        $stmt->execute(['d' => $docId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];

        foreach ($rows as $r) {
            $out[] = new Comment(
                $r['id'],
                $r['document_id'],
                $r['user_id'],
                $r['content'],
                $r['created_at']
            );
        }

        return $out;
    }
}
