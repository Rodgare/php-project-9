<?php

namespace Hexlet\Code;

use Carbon\Carbon;

class UrlRepo
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getEntities(): array
    {
        $urls = [];
        $sql = "SELECT
                    u.id AS url_id,
                    u.name AS url_name,
                    u.created_at AS url_created_at,
                    c.status_code AS status_code,
                    c.created_at AS check_created_at
                FROM
                    urls u
                LEFT JOIN LATERAL (
                    SELECT *
                    FROM url_checks
                    WHERE url_id = u.id
                    ORDER BY created_at DESC
                    LIMIT 1
                ) c ON true
                ORDER BY
                    u.id;
                ";
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch()) {
            $url = Url::fromArray([$row['url_name']]);
            $url->setCreatedAt($row['url_created_at']);
            if ($row['check_created_at']) {
                $url->setLastCheckDate($row['check_created_at']);
            }
            if ($row['status_code']) {
                $url->setLastCheckCode($row['status_code']);
            }
            $url->setId($row['url_id']);
            $urls[] = $url;
        }

        return $urls;
    }

    public function find(int $id): ?Url
    {
        $sql = "SELECT * FROM urls WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $url = Url::fromArray([$row['name']]);
            $url->setId($row['id']);
            $url->setCreatedAt($row['created_at']);
            return $url;
        }

        return null;
    }

    public function save(Url $url): void
    {
        if ($url->exists()) {
            $this->update($url);
        } else {
            $this->create($url);
        }
    }

    private function update(Url $url): void
    {
        $sql = "UPDATE urls SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $id = $url->getId();
        $name = $url->getName();
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    private function create(Url $url): void
    {
        $date = Carbon::now();
        $dateFormated = $date->format('Y-m-d H:i:s');

        $sql = "INSERT INTO urls (name, created_at) VALUES (:name, :created_at)";
        $stmt = $this->conn->prepare($sql);
        $name = $url->getName();
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':created_at', $dateFormated);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $url->setId($id);
    }

    public function isNameExists(Url $url): bool
    {
        $sql = "SELECT * FROM urls WHERE name = :name";
        $stmt = $this->conn->prepare($sql);
        $name = $url->getName();
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $urls = $stmt->fetch();

        if ($urls) {
            $url->setId($urls['id']);
            return true;
        }

        return false;
    }
}
