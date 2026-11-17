<?php
// Menampilkan berkas dari storage/berkas (hanya admin atau pemilik berkas)
require_once __DIR__ . '/config/app.php';
if (empty($_SESSION['user'])) redirect('auth/login.php');
$b = db_one('SELECT b.*, s.user_id FROM berkas b JOIN pendaftaran p ON p.id=b.pendaftaran_id JOIN siswa s ON s.id=p.siswa_id WHERE b.id=?', 'i', [(int)($_GET['id'] ?? 0)]);
if (!$b || ($_SESSION['user']['role'] !== 'admin' && (int)$b['user_id'] !== (int)$_SESSION['user']['id'])) { http_response_code(403); die('Akses ditolak.'); }
$path = UPLOAD_DIR . $b['nama_file'];
if (!is_file($path)) { http_response_code(404); die('File tidak ditemukan.'); }
header('Content-Type: ' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path));
header('Content-Disposition: inline; filename="' . basename($b['nama_asli']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
