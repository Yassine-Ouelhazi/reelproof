<?php

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Generic Finders ───────────────────────────────────────────────────────

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1"
        );
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAllBy(string $column, mixed $value, string $orderBy = 'created_at DESC'): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} = ? ORDER BY {$orderBy}"
        );
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function all(string $orderBy = 'created_at DESC'): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy}"
        );
        return $stmt->fetchAll();
    }

    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE, string $where = '', array $params = []): array
    {
        $offset = ($page - 1) * $perPage;
        $whereClause = $where ? "WHERE {$where}" : '';

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} {$whereClause}"
        );
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} {$whereClause} 
             ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / $perPage),
        ];
    }

    // ── Write Operations ──────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sets = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }

    public function count(string $where = '', array $params = []): int
    {
        $whereClause = $where ? "WHERE {$where}" : '';
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} {$whereClause}"
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
