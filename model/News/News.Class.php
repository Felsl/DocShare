<?php
// model/news/News.php
class News
{
    protected ?int $id;
    protected string $title;
    protected ?string $img;
    protected ?string $short_content;
    protected string $content;
    protected int $is_hot;
    protected ?string $created_at;

    public function __construct(?int $id, string $title, ?string $img, ?string $short_content, string $content, int $is_hot = 0, ?string $created_at = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->img = $img;
        $this->short_content = $short_content;
        $this->content = $content;
        $this->is_hot = $is_hot;
        $this->created_at = $created_at;
    }

    // getters
    public function getId(){ return $this->id; }
    public function getTitle(){ return $this->title; }
    public function getImg(){ return $this->img; }
    public function getShortContent(){ return $this->short_content; }
    public function getContent(){ return $this->content; }
    public function isHot(){ return (bool)$this->is_hot; }
    public function getCreatedAt(){ return $this->created_at; }

    // setters
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setTitle(string $t): self { $this->title = $t; return $this; }
    public function setImg(?string $i): self { $this->img = $i; return $this; }
    public function setShortContent(?string $s): self { $this->short_content = $s; return $this; }
    public function setContent(string $c): self { $this->content = $c; return $this; }
    public function setIsHot(int $h): self { $this->is_hot = $h; return $this; }
    public function setCreatedAt(?string $t): self { $this->created_at = $t; return $this; }
}
