<?php

class ReviewModel extends Model
{
    protected string $table = 'reviews';

    public function getWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, 
                    u.username, u.avatar, u.credibility_score as reviewer_score, u.is_verified,
                    p.name as product_name, p.slug as product_slug, p.image as product_image,
                    b.name as brand_name, b.slug as brand_slug,
                    c.name as category_name
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             JOIN products p ON r.product_id = p.id
             JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE r.id = ? AND r.status = 'published'"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getFeedPaginated(int $page = 1, ?int $categoryId = null, string $sort = 'latest'): array
    {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;
        $params  = [];

        $where = "r.status = 'published'";
        if ($categoryId) {
            $where  .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $order = match($sort) {
            'trending' => 'r.views DESC, r.upvotes DESC',
            'top'      => 'r.upvotes DESC',
            default    => 'r.created_at DESC',
        };

        // Count
        $countSql = "SELECT COUNT(*) FROM reviews r 
                     JOIN products p ON r.product_id = p.id 
                     WHERE {$where}";
        $cStmt = $this->db->prepare($countSql);
        $cStmt->execute($params);
        $total = (int)$cStmt->fetchColumn();

        // Data
        $sql = "SELECT r.*, 
                       u.username, u.avatar, u.credibility_score as reviewer_score,
                       p.name as product_name, p.slug as product_slug, p.image as product_image,
                       b.name as brand_name, b.slug as brand_slug
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                JOIN brands b ON p.brand_id = b.id
                WHERE {$where}
                ORDER BY {$order}
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / $perPage),
        ];
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, p.name as product_name, p.image as product_image,
                    b.name as brand_name
             FROM reviews r
             JOIN products p ON r.product_id = p.id
             JOIN brands b ON p.brand_id = b.id
             WHERE r.user_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getByProduct(int $productId, int $page = 1): array
    {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT r.*, u.username, u.avatar, u.credibility_score as reviewer_score, u.is_verified
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.product_id = ? AND r.status = 'published'
             ORDER BY r.upvotes DESC, r.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function incrementViews(int $id): void
    {
        $this->db->prepare("UPDATE reviews SET views = views + 1 WHERE id = ?")
                 ->execute([$id]);
    }

    public function vote(int $reviewId, int $userId, string $type): array
    {
        // Check existing vote
        $stmt = $this->db->prepare(
            "SELECT * FROM review_votes WHERE review_id = ? AND user_id = ?"
        );
        $stmt->execute([$reviewId, $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ($existing['type'] === $type) {
                // Remove vote (toggle off)
                $this->db->prepare("DELETE FROM review_votes WHERE id = ?")
                         ->execute([$existing['id']]);
                $col = $type === 'up' ? 'upvotes' : 'downvotes';
                $this->db->prepare("UPDATE reviews SET {$col} = GREATEST({$col} - 1, 0) WHERE id = ?")
                         ->execute([$reviewId]);
                return ['action' => 'removed', 'type' => $type];
            } else {
                // Switch vote
                $this->db->prepare("UPDATE review_votes SET type = ? WHERE id = ?")
                         ->execute([$type, $existing['id']]);
                $addCol    = $type === 'up' ? 'upvotes' : 'downvotes';
                $removeCol = $type === 'up' ? 'downvotes' : 'upvotes';
                $this->db->prepare(
                    "UPDATE reviews SET {$addCol} = {$addCol} + 1, 
                     {$removeCol} = GREATEST({$removeCol} - 1, 0) WHERE id = ?"
                )->execute([$reviewId]);
                return ['action' => 'switched', 'type' => $type];
            }
        }

        // New vote
        $this->db->prepare(
            "INSERT INTO review_votes (review_id, user_id, type, created_at) VALUES (?, ?, ?, NOW())"
        )->execute([$reviewId, $userId, $type]);
        $col = $type === 'up' ? 'upvotes' : 'downvotes';
        $this->db->prepare("UPDATE reviews SET {$col} = {$col} + 1 WHERE id = ?")
                 ->execute([$reviewId]);
        return ['action' => 'added', 'type' => $type];
    }

    public function getUserVote(int $reviewId, int $userId): ?string
    {
        $stmt = $this->db->prepare(
            "SELECT type FROM review_votes WHERE review_id = ? AND user_id = ?"
        );
        $stmt->execute([$reviewId, $userId]);
        $result = $stmt->fetch();
        return $result ? $result['type'] : null;
    }

    public function search(string $query, int $page = 1): array
    {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;
        $search  = '%' . $query . '%';

        $stmt = $this->db->prepare(
            "SELECT r.*, u.username, u.avatar,
                    p.name as product_name, p.slug as product_slug,
                    b.name as brand_name
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             JOIN products p ON r.product_id = p.id
             JOIN brands b ON p.brand_id = b.id
             WHERE r.status = 'published'
               AND (r.title LIKE ? OR r.description LIKE ? OR p.name LIKE ? OR b.name LIKE ?)
             ORDER BY r.upvotes DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute([$search, $search, $search, $search]);
        return $stmt->fetchAll();
    }
}
