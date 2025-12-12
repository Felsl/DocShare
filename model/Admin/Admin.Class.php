<?php
// model/admin/Admin.php
class Admin
{
    protected ?int $id;
    protected string $username;
    protected string $password; // hashed
    protected string $full_name;
    protected ?string $email;
    protected ?string $phone;
    protected ?string $created_at;

    public function __construct(
        ?int $id,
        string $username,
        string $password,
        string $full_name,
        ?string $email = null,
        ?string $phone = null,
        ?string $created_at = null
    ) {
        $this->id         = $id;
        $this->username   = $username;
        $this->password   = $password;
        $this->full_name  = $full_name;
        $this->email      = $email;
        $this->phone      = $phone;
        $this->created_at = $created_at;
    }

    // ----------- Getter -----------
    public function getId()         { return $this->id; }
    public function getUsername()   { return $this->username; }
    public function getPassword()   { return $this->password; }
    public function getFullName()   { return $this->full_name; }
    public function getEmail()      { return $this->email; }
    public function getPhone()      { return $this->phone; }
    public function getCreatedAt()  { return $this->created_at; }

    // ----------- Setter (full version) -----------

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $hash): self
    {
        $this->password = $hash;
        return $this;
    }

    public function setFullName(string $fullName): self
    {
        $this->full_name = $fullName;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }
}
