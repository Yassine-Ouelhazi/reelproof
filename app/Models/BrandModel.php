<?php

class BrandModel extends Model
{
    protected string $table = 'brands';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, u.email,
                    COUNT(DISTINCT p.id) as product_count,
                    COUNT(DISTINCT r.id) as review_count,
                    COALESCE(AVG(r.rating), 0) as avg_rating
             FROM brands b
             LEFT JOIN users u ON b.user_id = u.id
             LEFT JOIN products p ON p.brand_id = b.id
             LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 'published'
             WHERE b.slug = ?
             GROUP BY b.id"
        );
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getWithStats(int $page = 1): array
    {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT b.*,
                    COUNT(DISTINCT p.id) as product_count,
                    COUNT(DISTINCT r.id) as review_count,
                    COALESCE(AVG(r.rating), 0) as avg_rating
             FROM brands b
             LEFT JOIN products p ON p.brand_id = b.id AND p.status = 'active'
             LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 'published'
             GROUP BY b.id
             ORDER BY review_count DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }

    public function createWithSlug(array $data): int
    {
        $data['slug'] = slug($data['name']);
        $base = $data['slug'];
        $i    = 1;
        while ($this->findBy('slug', $data['slug'])) {
            $data['slug'] = $base . '-' . $i++;
        }
        return $this->create($data);
    }
}
