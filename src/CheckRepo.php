<?php

namespace Hexlet\Code;

use Carbon\Carbon;

class CheckRepo
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getEntities(): array
    {
        $checks = [];
        $sql = "SELECT * FROM url_checks";
        $stmt = $this->conn->query($sql);

        while ($row = $stmt->fetch()) {
            $check = Check::fromArray([$row['url_id'], $row['created_at']]);
            $check->setId($row['id']);
            $checks[] = $check;
        }

        return $checks;
    }

    public function findByUrlId(int $urlId): array
    {
        $checks = [];
        $sql = "SELECT * FROM url_checks WHERE url_id = ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$urlId]);

        while ($row = $stmt->fetch()) {
            $check = Check::fromArray([$row['url_id']]);
            $check->setId($row['id']);
            $check->setCreatedAt($row['created_at']);
            $check->setStatusCode($row['status_code']);
            $check->setH1($row['h1']);
            $check->setTitle($row['title']);
            $check->setDescription($row['description']);
            $checks[] = $check;
        }

        return $checks;
    }

    public function save(Check $check): void
    {
        $date = Carbon::now();
        $dateFormated = $date->format('Y-m-d H:i:s');

        $sql = "INSERT INTO url_checks (url_id, created_at, status_code, h1, title, description)
                    VALUES (:url_id, :created_at, :status_code, :h1, :title, :description)";
        $stmt = $this->conn->prepare($sql);
        $urlId = $check->getUrlId();
        $stmt->bindParam(':url_id', $urlId);
        $stmt->bindParam(':created_at', $dateFormated);
        $statusCode = $check->getStatusCode();
        $stmt->bindParam(':status_code', $statusCode);
        $h1 = $check->getH1();
        $stmt->bindParam(':h1', $h1);
        $title = $check->getTitle();
        $stmt->bindParam(':title', $title);
        $description = $check->getDescription();
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $check->setId($id);
    }
}
