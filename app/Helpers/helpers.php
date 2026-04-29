<?php

// ── URL Helpers ───────────────────────────────────────────────────────────────

function url(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

// ── HTML Helpers ──────────────────────────────────────────────────────────────

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function csrf(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . Session::csrfToken() . '">';
}

function method(string $method): string
{
    return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
}

// ── Flash Messages ────────────────────────────────────────────────────────────

function flash(string $key): ?string
{
    return Session::getFlash($key);
}

function hasFlash(string $key): bool
{
    return Session::hasFlash($key);
}

// ── Auth Helpers ──────────────────────────────────────────────────────────────

function auth(): bool
{
    return Session::has('user_id');
}

function authUser(): ?array
{
    if (!auth()) return null;
    static $user = null;
    if ($user === null) {
        $model = new UserModel();
        $user = $model->find(Session::get('user_id'));
    }
    return $user;
}

function authRole(): ?string
{
    return Session::get('user_role');
}

function isReviewer(): bool
{
    return authRole() === 'reviewer';
}

function isBuyer(): bool
{
    return authRole() === 'buyer';
}

function isBrand(): bool
{
    return authRole() === 'brand';
}

// ── String Helpers ────────────────────────────────────────────────────────────

function slug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function truncate(string $text, int $limit = 100): string
{
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit) . '…';
}

function timeAgo(string $datetime): string
{
    $time = time() - strtotime($datetime);
    return match(true) {
        $time < 60      => 'just now',
        $time < 3600    => floor($time / 60) . 'm ago',
        $time < 86400   => floor($time / 3600) . 'h ago',
        $time < 604800  => floor($time / 86400) . 'd ago',
        $time < 2592000 => floor($time / 604800) . 'w ago',
        default         => date('M j, Y', strtotime($datetime)),
    };
}

function formatNumber(int $num): string
{
    return match(true) {
        $num >= 1000000 => round($num / 1000000, 1) . 'M',
        $num >= 1000    => round($num / 1000, 1) . 'K',
        default         => (string)$num,
    };
}

// ── Upload Helpers ────────────────────────────────────────────────────────────

function uploadFile(array $file, string $destination, array $allowedTypes, int $maxSize): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed. Please try again.'];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        return ['success' => false, 'error' => 'File type not allowed.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
    $fullPath = PUBLIC_PATH . '/' . $destination . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['success' => false, 'error' => 'Could not save file.'];
    }

    return ['success' => true, 'filename' => $filename, 'path' => $destination . '/' . $filename];
}

// ── Pagination ────────────────────────────────────────────────────────────────

function paginationLinks(array $pagination, string $baseUrl): string
{
    if ($pagination['last_page'] <= 1) return '';

    $current = $pagination['current_page'];
    $last    = $pagination['last_page'];
    $html    = '<nav class="pagination">';

    if ($current > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($current - 1) . '" class="page-btn">‹ Prev</a>';
    }

    for ($i = max(1, $current - 2); $i <= min($last, $current + 2); $i++) {
        $active = $i === $current ? ' active' : '';
        $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="page-btn' . $active . '">' . $i . '</a>';
    }

    if ($current < $last) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($current + 1) . '" class="page-btn">Next ›</a>';
    }

    $html .= '</nav>';
    return $html;
}
