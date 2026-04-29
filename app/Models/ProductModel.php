<?php

class ProductModel extends Model
{
    protected string $table = 'products';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, b.name as brand_name, b.slug as brand_slug, b.logo as brand_logo,
                    c.name as category_name, c.slug as category_slug,
                    COUNT(r.id) as review_count,
                    COALESCE(AVG(r.rating), 0) as avg_rating
             FROM products p
             JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 'published'
             WHERE p.slug = ?
             GROUP BY p.id"
        );
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getWithStats(int $page = 1, ?int $categoryId = null): array
    {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;
        $params  = [];
        $where   = "p.status = 'active'";

        if ($categoryId) {
            $where   .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $stmt = $this->db->prepare(
            "SELECT p.*, b.name as brand_name, b.slug as brand_slug,
                    c.name as category_name,
                    COUNT(r.id) as review_count,
                    COALESCE(AVG(r.rating), 0) as avg_rating
             FROM products p
             JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 'published'
             WHERE {$where}
             GROUP BY p.id
             ORDER BY review_count DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByBrand(int $brandId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, COUNT(r.id) as review_count,
                    COALESCE(AVG(r.rating), 0) as avg_rating
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 'published'
             WHERE p.brand_id = ? AND p.status = 'active'
             GROUP BY p.id
             ORDER BY review_count DESC"
        );
        $stmt->execute([$brandId]);
        return $stmt->fetchAll();
    }

    public function createWithSlug(array $data): int
    {
        $data['slug'] = slug($data['name']);
        // Ensure slug uniqueness
        $base = $data['slug'];
        $i    = 1;
        while ($this->findBy('slug', $data['slug'])) {
            $data['slug'] = $base . '-' . $i++;
        }
        return $this->create($data);
    }

    public function search(string $query): array
    {
        $search = '%' . $query . '%';
        $stmt = $this->db->prepare(
            "SELECT p.*, b.name as brand_name, COUNT(r.id) as review_count
             FROM products p
             JOIN brands b ON p.brand_id = b.id
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
             GROUP BY p.id
             ORDER BY review_count DESC
             LIMIT 20"
        );
        $stmt->execute([$search, $search]);
        return $stmt->fetchAll();
    }
}
