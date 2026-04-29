<?php

class CategoryModel extends Model
{
    protected string $table = 'categories';

    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }

    public function getAllWithCount(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, COUNT(p.id) as product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
             GROUP BY c.id
             ORDER BY product_count DESC"
        );
        return $stmt->fetchAll();
    }
}
