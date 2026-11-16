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
    public function all(array $criteria = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereClauses = [];

        // If the model has a default schoolId set, include it if not overridden
        if ($this->schoolId !== null && !array_key_exists('school_id', $criteria)) {
            $criteria['school_id'] = $this->schoolId;
        }

        // Build WHERE clauses dynamically from the criteria array
        if (!empty($criteria)) {
            foreach ($criteria as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
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
    public function count(array $criteria = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        $whereClauses = [];

        if ($this->schoolId !== null && !array_key_exists('school_id', $criteria)) {
            $criteria['school_id'] = $this->schoolId;
        }

        // Build WHERE clauses dynamically from the criteria array
        if (!empty($criteria)) {
            foreach ($criteria as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
