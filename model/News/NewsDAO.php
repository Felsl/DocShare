<?php
// model/news/NewsRepository.php
require_once __DIR__ . '/News.php';

class NewsDAO
{
    protected $pdo;
    public function __construct()
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO)
            $this->pdo = $GLOBALS['pdo'];
        else
            throw new RuntimeException('Global $pdo not found.');
    }

    public function create(News $n): int
    {
        $sql = "INSERT INTO news (title, img, short_content, content, is_hot, created_at) VALUES (:t, :i, :s, :c, :h, :dt)";
        $stmt = $this->pdo->prepare($sql);
        $now = $n->getCreatedAt() ?? date('Y-m-d H:i:s');
        $stmt->execute([
            't' => $n->getTitle(),
            'i' => $n->getImg(),
            's' => $n->getShortContent(),
            'c' => $n->getContent(),
            'h' => $n->isHot() ? 1 : 0,
            'dt' => $now
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function find(int $id): ?News
    {
        $stmt = $this->pdo->prepare("SELECT * FROM news WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToNews($r) : null;
    }

    public function update(News $n): bool
    {
        if ($n->getId() === null)
            throw new InvalidArgumentException('News id required');
        $sql = "UPDATE news SET title=:t, img=:i, short_content=:s, content=:c, is_hot=:h WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            't' => $n->getTitle(),
            'i' => $n->getImg(),
            's' => $n->getShortContent(),
            'c' => $n->getContent(),
            'h' => $n->isHot() ? 1 : 0,
            'id' => $n->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM news WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function listHot(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM news WHERE is_hot = 1 ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r)
            $out[] = $this->mapRowToNews($r);
        return $out;
    }

    public function listAll(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT :off, :lim");
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r)
            $out[] = $this->mapRowToNews($r);
        return $out;
    }

    protected function mapRowToNews(array $r): News
    {
        return new News(
            isset($r['id']) ? (int) $r['id'] : null,
            $r['title'],
            $r['img'] ?? null,
            $r['short_content'] ?? null,
            $r['content'],
            isset($r['is_hot']) ? (int) $r['is_hot'] : 0,
            $r['created_at'] ?? null
        );
    }
}
