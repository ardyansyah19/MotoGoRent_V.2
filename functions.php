<?php
/**
 * Fungsi-fungsi bantuan yang dipakai di seluruh halaman.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Redirect ke URL lain lalu hentikan eksekusi. */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/** Bersihkan string input dari tag HTML & spasi berlebih. */
function clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/** Simpan pesan flash (sekali tampil) ke session. */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Ambil & hapus pesan flash dari session. */
function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Buat / ambil token CSRF untuk form. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Validasi token CSRF yang dikirim dari form. */
function csrf_verify(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Format angka jadi Rupiah, mis. 45000 -> "Rp 45.000". */
function format_rupiah(int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
