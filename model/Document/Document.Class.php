<?php
// model/document/Document.php
class Document
{
    protected ?int $id;
    protected string $title;
    protected string $slug;
    protected string $description;
    protected string $filename;
    protected string $file_type;
    protected int $filesize;
    protected int $category_id;
    protected int $uploader_id;
    protected int $downloads;
    protected string $status; // pending|approved|rejected
    protected int $is_featured;
    protected ?string $created_at;

    public function __construct(
        ?int $id,
        string $title,
        string $slug,
        string $description,
        string $filename,
        string $file_type,
        int $filesize,
        int $category_id,
        int $uploader_id,
        int $downloads = 0,
        string $status = 'pending',
        int $is_featured = 0,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->description = $description;
        $this->filename = $filename;
        $this->file_type = $file_type;
        $this->filesize = $filesize;
        $this->category_id = $category_id;
        $this->uploader_id = $uploader_id;
        $this->downloads = $downloads;
        $this->status = $status;
        $this->is_featured = $is_featured;
        $this->created_at = $created_at;
    }

    // getters
    public function getId(){ return $this->id; }
    public function getTitle(){ return $this->title; }
    public function getSlug(){ return $this->slug; }
    public function getDescription(){ return $this->description; }
    public function getFilename(){ return $this->filename; }
    public function getFileType(){ return $this->file_type; }
    public function getFilesize(){ return $this->filesize; }
    public function getCategoryId(){ return $this->category_id; }
    public function getUploaderId(){ return $this->uploader_id; }
    public function getDownloads(){ return $this->downloads; }
    public function getStatus(){ return $this->status; }
    public function isFeatured(){ return (bool)$this->is_featured; }
    public function getCreatedAt(){ return $this->created_at; }

    // setters (fluent)
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setTitle(string $t): self { $this->title = $t; return $this; }
    public function setSlug(string $s): self { $this->slug = $s; return $this; }
    public function setDescription(string $d): self { $this->description = $d; return $this; }
    public function setFilename(string $f): self { $this->filename = $f; return $this; }
    public function setFileType(string $ft): self { $this->file_type = $ft; return $this; }
    public function setFilesize(int $fs): self { $this->filesize = $fs; return $this; }
    public function setCategoryId(int $cid): self { $this->category_id = $cid; return $this; }
    public function setUploaderId(int $uid): self { $this->uploader_id = $uid; return $this; }
    public function setDownloads(int $d): self { $this->downloads = $d; return $this; }
    public function setStatus(string $s): self { $this->status = $s; return $this; }
    public function setIsFeatured(int $f): self { $this->is_featured = $f; return $this; }
    public function setCreatedAt(?string $t): self { $this->created_at = $t; return $this; }
}
