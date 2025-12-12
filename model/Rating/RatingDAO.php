<?php
// model/rating/RatingRepository.php
require_once __DIR__ . '/Rating.php';

class RatingDAO
{
    protected $pdo;
    public function __construct()
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO)
            $this->pdo = $GLOBALS['pdo'];
        else
            throw new RuntimeException('Global $pdo not found.');
    }

    // find by id
    public function find(int $id): ?Rating
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToRating($r) : null;
    }

    // find by user+doc
    public function findByUserAndDoc(int $userId, int $docId): ?Rating
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE user_id = :u AND document_id = :d LIMIT 1");
        $stmt->execute(['u' => $userId, 'd' => $docId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToRating($r) : null;
    }

    // upsert (insert or update)
    public function upsert(int $userId, int $docId, int $stars): int
    {
        $existing = $this->findByUserAndDoc($userId, $docId);
        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE ratings SET stars = :s WHERE id = :id");
            $stmt->execute(['s' => $stars, 'id' => $existing->getId()]);
            return (int) $existing->getId();
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO ratings (document_id, user_id, stars, created_at) VALUES (:d, :u, :s, :t)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute(['d' => $docId, 'u' => $userId, 's' => $stars, 't' => $now]);
            return (int) $this->pdo->lastInsertId();
        }
    }

    // delete
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM ratings WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // average stars for document
    public function avgForDocument(int $docId): float
    {
        $stmt = $this->pdo->prepare("SELECT AVG(stars) AS avg_stars FROM ratings WHERE document_id = :d");
        $stmt->execute(['d' => $docId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r && $r['avg_stars'] !== null ? (float) $r['avg_stars'] : 0.0;
    }

    // count ratings for document
    public function countForDocument(int $docId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ratings WHERE document_id = :d");
        $stmt->execute(['d' => $docId]);
        return (int) $stmt->fetchColumn();
    }

    protected function mapRowToRating(array $r): Rating
    {
        return new Rating(
            isset($r['id']) ? (int) $r['id'] : null,
            (int) $r['document_id'],
            (int) $r['user_id'],
            (int) $r['stars'],
            $r['created_at'] ?? null
        );
    }
    public function createFromParams(int $userId, int $documentId, int $stars): int
    {
        $r = new Rating(null, $documentId, $userId, $stars, null);
        return $this->create($r);
    }

    public function create(Rating $r): int
    {
        $sql = "INSERT INTO ratings (document_id, user_id, stars, created_at) VALUES (:doc, :user, :stars, :dt)";
        $stmt = $this->pdo->prepare($sql);
        $now = date('Y-m-d H:i:s');
        $stmt->execute([
            'doc' => $r->getDocumentId(),
            'user' => $r->getUserId(),
            'stars' => $r->getStars(),
            'dt' => $now
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Rating $r): bool
    {
        if ($r->getId() === null) {
            throw new InvalidArgumentException('Rating id required for update');
        }
        $stmt = $this->pdo->prepare("UPDATE ratings SET stars = :stars WHERE id = :id");
        return $stmt->execute(['stars' => $r->getStars(), 'id' => $r->getId()]);
    }

    public function findByUserAndDocument(int $userId, int $documentId): ?Rating
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE user_id = :uid AND document_id = :did LIMIT 1");
        $stmt->execute(['uid' => $userId, 'did' => $documentId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToRating($r) : null;
    }


    public function getByDocument(int $documentId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE document_id = :doc ORDER BY created_at DESC");
        $stmt->execute(['doc' => $documentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r)
            $out[] = $this->mapRowToRating($r);
        return $out;
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE user_id = :user ORDER BY created_at DESC");
        $stmt->execute(['user' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r)
            $out[] = $this->mapRowToRating($r);
        return $out;
    }

    public function getAggregateForDocument(int $documentId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS cnt, AVG(stars) AS avg_stars FROM ratings WHERE document_id = :doc");
        $stmt->execute(['doc' => $documentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int) $row['cnt'] === 0) {
            return ['avg' => 0.0, 'count' => 0];
        }
        return ['avg' => round((float) $row['avg_stars'], 2), 'count' => (int) $row['cnt']];
    }
}
