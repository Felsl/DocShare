<?php
// model/category/CategoryRepository.php
require_once __DIR__ . '/Category.Class.php';

class CategoryDAO
{
    protected $pdo;
    public function __construct()
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO)
            $this->pdo = $GLOBALS['pdo'];
        else
            throw new RuntimeException('Global $pdo not found.');
    }

    public function find(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToCategory($r) : null;
    }

    public function findByCode(string $code)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE code = :c LIMIT 1");
        $stmt->execute(['c' => $code]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->mapRowToCategory($r) : null;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r)
            $out[] = $this->mapRowToCategory($r);
        return $out;
    }

    public function create(Category $c): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (code,name,description) VALUES (:code,:name,:desc)");
        $stmt->execute(['code' => $c->getCode(), 'name' => $c->getName(), 'desc' => $c->getDescription()]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Category $c): bool
    {
        if (!$c->getId())
            return false;
        $stmt = $this->pdo->prepare("UPDATE categories SET code=:code, name=:name, description=:desc WHERE id=:id");
        return $stmt->execute(['code' => $c->getCode(), 'name' => $c->getName(), 'desc' => $c->getDescription(), 'id' => $c->getId()]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    protected function mapRowToCategory(array $r)
    {
        return new Category((int) $r['id'], $r['code'], $r['name'], $r['description'] ?? null);
    }
}
