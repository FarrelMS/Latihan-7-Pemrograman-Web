<?php
if (session_status() === PHP_SESSION_NONE) session_start();
mysqli_report(MYSQLI_REPORT_OFF);
require_once __DIR__ . '/koneksi.php';

define('BASE_URL', '/ppdb-smk');            // sesuaikan dengan nama folder di htdocs
define('UPLOAD_DIR', __DIR__ . '/../storage/berkas/');

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect($path) { header('Location: ' . BASE_URL . '/' . $path); exit; }

function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

/* ---------- CSRF ---------- */
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) die('Token CSRF tidak valid.');
    }
}

/* ---------- Auth ---------- */
function require_login($role) {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== $role) redirect('auth/login.php');
}

/* ---------- Helper query (prepared statement) ---------- */
function db_all($sql, $types = '', $params = []) {
    global $koneksi;
    $st = mysqli_prepare($koneksi, $sql);
    if (!$st) die('SQL error: ' . mysqli_error($koneksi));
    if ($types) mysqli_stmt_bind_param($st, $types, ...$params);
    mysqli_stmt_execute($st);
    $res = mysqli_stmt_get_result($st);
    $rows = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($st);
    return $rows;
}
function db_one($sql, $types = '', $params = []) { $r = db_all($sql, $types, $params); return $r[0] ?? null; }
function db_exec($sql, $types = '', $params = []) {
    global $koneksi;
    $st = mysqli_prepare($koneksi, $sql);
    if (!$st) die('SQL error: ' . mysqli_error($koneksi));
    if ($types) mysqli_stmt_bind_param($st, $types, ...$params);
    $ok = mysqli_stmt_execute($st);
    $n = $ok ? mysqli_stmt_affected_rows($st) : false;
    mysqli_stmt_close($st);
    return $n;
}
function insert_id() { global $koneksi; return mysqli_insert_id($koneksi); }

function riwayat($pendaftaran_id, $status, $ket = '') {
    db_exec('INSERT INTO riwayat_status(pendaftaran_id,status,keterangan) VALUES(?,?,?)', 'iss', [$pendaftaran_id, $status, $ket]);
}
function badge($s) {
    $c = ['Menunggu'=>'warning','Diverifikasi'=>'info','Ditolak'=>'danger','Diterima'=>'success','Belum'=>'secondary'];
    return '<span class="badge text-bg-' . ($c[$s] ?? 'secondary') . '">' . e($s) . '</span>';
}
function siswa_login() { return db_one('SELECT * FROM siswa WHERE user_id=?', 'i', [$_SESSION['user']['id']]); }
function pendaftaran_siswa($siswa_id) {
    return db_one('SELECT p.*, j.nama AS jurusan, j.kode FROM pendaftaran p JOIN jurusan j ON j.id=p.jurusan_id WHERE p.siswa_id=?', 'i', [$siswa_id]);
}
