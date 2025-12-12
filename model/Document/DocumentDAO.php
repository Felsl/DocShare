<?php
require_once __DIR__ . "/Document.php";

class DocumentDAO
{
    protected PDO $pdo;

    public function __construct()
    {
        if (!isset($GLOBALS['pdo'])) {
            throw new RuntimeException("PDO connection not found.");
        }
        $this->pdo = $GLOBALS['pdo'];
    }

    /* -------------------- CREATE -------------------- */
    public function create(Document $d): int
    {
        $sql = "INSERT INTO documents 
                (title, slug, description, filename, file_type, filesize, 
                 category_id, uploader_id, downloads, status, is_featured, created_at)
                VALUES (:title, :slug, :desc, :fn, :ft, :fs, :cid, :uid, :dl, :st, :feat, :dt)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "title" => $d->getTitle(),
            "slug" => $d->getSlug(),
            "desc" => $d->getDescription(),
            "fn" => $d->getFilename(),
            "ft" => $d->getFileType(),
            "fs" => $d->getFilesize(),
            "cid" => $d->getCategoryId(),
            "uid" => $d->getUploaderId(),
            "dl" => $d->getDownloads(),
            "st" => $d->getStatus(),
            "feat" => $d->isFeatured() ? 1 : 0,
            "dt" => date("Y-m-d H:i:s")
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /* -------------------- FIND BY ID -------------------- */
    public function find(int $id): ?Document
    {
        $stmt = $this->pdo->prepare("SELECT * FROM documents WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $this->map($r) : null;
    }

    /* -------------------- LIST APPROVED -------------------- */
    public function listApproved(): array
    {
        $stmt = $this->pdo->query("
            SELECT * FROM documents 
            WHERE status = 'approved'
            ORDER BY created_at DESC
        ");

        $docs = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $docs[] = $this->map($r);
        }
        return $docs;
    }

    /* -------------------- LIST BY UPLOADER -------------------- */
    public function listByUploader(int $uid): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM documents 
            WHERE uploader_id = :uid
            ORDER BY created_at DESC
        ");
        $stmt->execute(["uid" => $uid]);

        $docs = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $docs[] = $this->map($r);
        }
        return $docs;
    }

    /* -------------------- LIST PENDING FOR ADMIN -------------------- */
    public function listPending(): array
    {
        $stmt = $this->pdo->query("
            SELECT * FROM documents 
            WHERE status = 'pending'
            ORDER BY created_at ASC
        ");

        $docs = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $docs[] = $this->map($r);
        }
        return $docs;
    }

    /* -------------------- SEARCH -------------------- */
    public function search(string $keyword): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM documents 
            WHERE status='approved' AND (title LIKE :kw OR description LIKE :kw)
            ORDER BY created_at DESC
        ");
        $stmt->execute(["kw" => "%$keyword%"]);

        $docs = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $docs[] = $this->map($r);
        }
        return $docs;
    }

    /* -------------------- UPDATE -------------------- */
    public function update(Document $d): bool
    {
        $sql = "UPDATE documents SET 
                title=:title, slug=:slug, description=:desc, filename=:fn,
                file_type=:ft, filesize=:fs, category_id=:cid, 
                status=:st, is_featured=:feat
                WHERE id=:id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "title" => $d->getTitle(),
            "slug" => $d->getSlug(),
            "desc" => $d->getDescription(),
            "fn" => $d->getFilename(),
            "ft" => $d->getFileType(),
            "fs" => $d->getFilesize(),
            "cid" => $d->getCategoryId(),
            "st" => $d->getStatus(),
            "feat" => $d->isFeatured() ? 1 : 0,
            "id" => $d->getId()
        ]);
    }

    /* -------------------- DELETE -------------------- */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM documents WHERE id = :id");
        return $stmt->execute(["id" => $id]);
    }

    /* -------------------- MAP ROW → DOCUMENT -------------------- */
    private function map(array $r): Document
    {
        return new Document(
            (int) $r["id"],
            $r["title"],
            $r["slug"],
            $r["description"],
            $r["filename"],
            $r["file_type"],
            (int) $r["filesize"],
            (int) $r["category_id"],
            (int) $r["uploader_id"],
            (int) $r["downloads"],
            $r["status"],
            (int) $r["is_featured"],
            $r["created_at"]
        );
    }

    public function approve(int $id): bool
    {
        $stmt = $this->pdo->prepare("
        UPDATE documents SET status = 'approved'
        WHERE id = :id
    ");
        return $stmt->execute(['id' => $id]);
    }

    public function reject(int $id): bool
    {
        $stmt = $this->pdo->prepare("
        UPDATE documents SET status = 'rejected'
        WHERE id = :id
    ");
        return $stmt->execute(['id' => $id]);
    }

}
