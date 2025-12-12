<?php
// model/rating/Rating.php
class Rating
{
    protected ?int $id;
    protected int $document_id;
    protected int $user_id;
    protected int $stars;
    protected ?string $created_at;

    public function __construct(?int $id, int $document_id, int $user_id, int $stars, ?string $created_at = null)
    {
        $this->id = $id;
        $this->document_id = $document_id;
        $this->user_id = $user_id;
        $this->stars = $stars;
        $this->created_at = $created_at;
    }

    // getters
    public function getId(){ return $this->id; }
    public function getDocumentId(){ return $this->document_id; }
    public function getUserId(){ return $this->user_id; }
    public function getStars(){ return $this->stars; }
    public function getCreatedAt(){ return $this->created_at; }

    // setters (fluent)
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setDocumentId(int $docId): self { $this->document_id = $docId; return $this; }
    public function setUserId(int $userId): self { $this->user_id = $userId; return $this; }
    public function setStars(int $stars): self { $this->stars = $stars; return $this; }
    public function setCreatedAt(?string $t): self { $this->created_at = $t; return $this; }
}
