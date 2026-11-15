<?php

namespace App\Core;

use PDO;

class Model
{
    protected PDO $db;
    protected string $table;
    protected ?int $schoolId;

    public function __construct(?int $schoolId = null)
    {
        $this->db = Database::connect();
        $this->schoolId = $schoolId;
    }

    // Create a record.
    public function create(array $data): string
    {

        if ($this->schoolId !== null) {
            $data['school_id'] = $this->schoolId;
        }

        $columns = array_keys($data);
        $fields = implode(', ', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    // Find a single record by ID.
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";

        if ($this->schoolId !== null) {
            $sql .= " AND school_id = :school_id";
        }

        $stmt = $this->db->prepare($sql);

        $params = ['id' => $id];

        if ($this->schoolId !== null) {
            $params['school_id'] = $this->schoolId;
        }

        $stmt->execute($params);

        return $stmt->fetch();
    }

    // Retrieve all records.
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        $params = [];

        if ($this->schoolId !== null) {
            $sql .= " WHERE school_id = :school_id";
            $params['school_id'] = $this->schoolId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Update a record by ID.
    public function update(int $id, array $data): bool
    {

        if (empty($data)) {
            return false;
        }

        $fields = [];

        foreach ($data as $column => $value) {
            $fields[] = "$column = :$column";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";

        if ($this->schoolId !== null) {
            $sql .= " AND school_id = :school_id";
        }

        $data['id'] = $id;

        if ($this->schoolId !== null) {
            $data['school_id'] = $this->schoolId;
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    // Delete a record by ID.
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        $params = ['id' => $id];

        if ($this->schoolId !== null) {
            $sql .= " AND school_id = :school_id";
            $params['school_id'] = $this->schoolId;
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    // Count records.
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        $params = [];

        if ($this->schoolId !== null) {
            $sql .= " WHERE school_id = :school_id";
            $params['school_id'] = $this->schoolId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
