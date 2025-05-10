<?php

namespace Hexlet\Code;

use Carbon\Carbon;

class checkRepo
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

    public function findByUrl_id(int $url_id): array
    {
        $checks = [];
        $sql = "SELECT * FROM url_checks WHERE url_id = ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$url_id]);

        while ($row = $stmt->fetch()) {
            $check = Check::fromArray([$row['url_id']]);
            $check->setId($row['id']);
            $check->setCreated_at($row['created_at']);
            $check->setStatusCode($row['status_code']) ?? '';
            $checks[] = $check;
        }

        return $checks;
    }

    public function save(Check $check): void
    {
        $date = Carbon::now();
        $dateFormated = $date->format('Y-m-d H:i:s');

        $sql = "INSERT INTO url_checks (url_id, created_at, status_code) VALUES (:url_id, :created_at, :status_code)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':url_id', $check->getUrl_id());
        $stmt->bindParam(':created_at', $dateFormated);
        $stmt->bindParam(':status_code', $check->getStatusCode());
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $check->setId($id);
    }
}