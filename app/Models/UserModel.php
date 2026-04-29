<?php

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function findByUsername(string $username): ?array
    {
        return $this->findBy('username', $username);
    }

    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_ARGON2ID);
        $data['points']   = 0;
        $data['credibility_score'] = 0;
        $data['is_verified'] = 0;
        return $this->create($data);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function getTopReviewers(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, COUNT(r.id) as review_count 
             FROM users u 
             LEFT JOIN reviews r ON r.user_id = u.id 
             WHERE u.role = 'reviewer'
             GROUP BY u.id 
             ORDER BY u.credibility_score DESC 
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function addPoints(int $userId, int $points): void
    {
        $this->db->prepare(
            "UPDATE users SET points = points + ?, updated_at = NOW() WHERE id = ?"
        )->execute([$points, $userId]);
    }

    public function updateCredibilityScore(int $userId): void
    {
        // Score = weighted avg of upvote ratio across all reviews
        $stmt = $this->db->prepare(
            "SELECT 
                COUNT(r.id) as total_reviews,
                COALESCE(SUM(r.upvotes), 0) as total_up,
                COALESCE(SUM(r.downvotes), 0) as total_down
             FROM reviews r WHERE r.user_id = ? AND r.status = 'published'"
        );
        $stmt->execute([$userId]);
        $stats = $stmt->fetch();

        if ($stats['total_reviews'] > 0) {
            $total_votes = $stats['total_up'] + $stats['total_down'];
            $score = $total_votes > 0
                ? round(($stats['total_up'] / $total_votes) * 100)
                : 50;
        } else {
            $score = 0;
        }

        $this->update($userId, ['credibility_score' => $score]);
    }
}
