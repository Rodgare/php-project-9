<?php

namespace Hexlet\Code;

class Check
{
    private ?int $id = null;
    private ?int $url_id = null;
    private ?string $created_at = null;
    private ?string $status_code = null;
    private ?string $h1 = null;
    private ?string $title = null;
    private ?string $description = null;

    public static function fromArray(array $checkData): Check
    {
        [$url_id] = $checkData;
        $check = new Check();
        $check->setUrl_id($url_id);
        
        return $check;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl_id(): ?string
    {
        return $this->url_id;
    }

    public function getCreated_at(): ?string
    {
        return $this->created_at;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCreated_at(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUrl_id(string $url_id): void
    {  
        $this->url_id = $url_id;
    }

    public function exists(): bool
    {
        return !is_null($this->getId());
    }
}
