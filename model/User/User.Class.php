<?php
class User
{
    protected ?int $id;
    protected string $email;
    protected string $passwordHash;
    protected string $name;
    protected ?string $address;
    protected ?string $phone;
    protected string $role;
    protected int $status;
    protected ?string $createdAt;

    public function __construct(
        ?int $id,
        string $email,
        string $passwordHash,
        string $name,
        ?string $address,
        ?string $phone,
        string $role,
        int $status,
        ?string $createdAt
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->name = $name;
        $this->address = $address;
        $this->phone = $phone;
        $this->role = $role;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getAddress()
    {
        return $this->address;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    // Setters
    public function setPasswordHash($hash)
    {
        $this->passwordHash = $hash;
    }
}
