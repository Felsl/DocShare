<?php
// model/category/Category.php
class Category
{
    protected ?int $id;
    protected string $code;
    protected string $name;
    protected ?string $description;

    public function __construct(
        ?int $id,
        string $code,
        string $name,
        ?string $description = null
    ) {
        $this->id          = $id;
        $this->code        = $code;
        $this->name        = $name;
        $this->description = $description;
    }

    // ----------- Getter -----------
    public function getId()         { return $this->id; }
    public function getCode()       { return $this->code; }
    public function getName()       { return $this->name; }
    public function getDescription(){ return $this->description; }

    // ----------- Setter -----------
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
