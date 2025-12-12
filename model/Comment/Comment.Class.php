<?php
// model/comment/Comment.php
class Comment
{
    protected ?int $id;
    protected int $document_id;
    protected int $user_id;
    protected string $content;
    protected ?string $created_at;

    public function __construct(?int $id, int $document_id, int $user_id, string $content, ?string $created_at = null)
    {
        $this->id = $id;
        $this->document_id = $document_id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->created_at = $created_at;
    }

    public function getId(){ return $this->id; }
    public function getDocumentId(){ return $this->document_id; }
    public function getUserId(){ return $this->user_id; }
    public function getContent(){ return $this->content; }
    public function getCreatedAt(){ return $this->created_at; }
}
