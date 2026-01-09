<?php

namespace App\DB;

use Exception;

final class QueryBuilder
{
    private Database $db;
    private string $table;
    private array $wheres = [];
    private array $bindings = [];
    private ?string $orderBy = null;
    private ?int $limit = null;

    public function __construct(Database $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function where(string $column, string $operator, $value): self
    {
        $placeholder = ':' . str_replace('.', '_', $column) . count($this->bindings);
        $this->wheres[] = "$column $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "$column $direction";
        return $this;
    }


    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function get(array $columns = ['*']): array
    {
        $sql = "SELECT " . implode(', ', $columns) . " FROM {$this->table}";
        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $this->db->select($sql, $this->bindings);
    }

    public function first(array $columns = ['*']): ?array
    {
        $this->limit(1);
        $results = $this->get($columns);
        return $results[0] ?? null;
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $bindings = [];
        foreach ($data as $col => $val) {
            $bindings[':' . $col] = $val;
        }

        return $this->db->insert($sql, $bindings);
    }

    public function update(array $data): int
    {
        if (!$this->wheres) {
            throw new Exception("Update without WHERE is not allowed!");
        }

        $set = [];
        $bindings = $this->bindings;

        foreach ($data as $col => $val) {
            $placeholder = ':' . $col . '_upd';
            $set[] = "$col = $placeholder";
            $bindings[$placeholder] = $val;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $this->wheres);
        return $this->db->update($sql, $bindings);
    }

    public function delete(): int
    {
        if (!$this->wheres) {
            throw new Exception("Delete without WHERE is not allowed!");
        }

        $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $this->wheres);
        return $this->db->delete($sql, $this->bindings);
    }

    public function reset(): self
    {
        $this->wheres = [];
        $this->bindings = [];
        $this->orderBy = null;
        $this->limit = null;
        return $this;
    }
}