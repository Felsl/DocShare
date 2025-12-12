<?php
// model/uploads_log/UploadLog.php
class UploadLog
{
    protected ?int $id;
    protected int $document_id;
    protected string $action;
    protected ?int $admin_id;
    protected ?string $note;
    protected ?string $created_at;

    public function __construct(?int $id, int $document_id, string $action, ?int $admin_id = null, ?string $note = null, ?string $created_at = null)
    {
        $this->id = $id;
        $this->document_id = $document_id;
        $this->action = $action;
        $this->admin_id = $admin_id;
        $this->note = $note;
        $this->created_at = $created_at;
    }

    // getters
    public function getId(){ return $this->id; }
    public function getDocumentId(){ return $this->document_id; }
    public function getAction(){ return $this->action; }
    public function getAdminId(){ return $this->admin_id; }
    public function getNote(){ return $this->note; }
    public function getCreatedAt(){ return $this->created_at; }

    // setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setDocumentId(int $d): self { $this->document_id = $d; return $this; }
    public function setAction(string $a): self { $this->action = $a; return $this; }
    public function setAdminId(?int $id): self { $this->admin_id = $id; return $this; }
    public function setNote(?string $n): self { $this->note = $n; return $this; }
    public function setCreatedAt(?string $t): self { $this->created_at = $t; return $this; }
}
