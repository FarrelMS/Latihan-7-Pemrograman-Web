<?php
require_once __DIR__ . '/../config/app.php';
$u = $_SESSION['user'] ?? null;
$menu = [];
if ($u && $u['role'] === 'admin') {
    $menu = ['admin/dashboard.php'=>'Dashboard','admin/siswa.php'=>'Siswa','admin/jurusan.php'=>'Jurusan',
             'admin/verifikasi.php'=>'Verifikasi','admin/seleksi.php'=>'Seleksi','admin/berkas.php'=>'Berkas','admin/laporan.php'=>'Laporan'];
} elseif ($u) {
    $menu = ['siswa/dashboard.php'=>'Dashboard','siswa/biodata.php'=>'Biodata','siswa/pendaftaran.php'=>'Pendaftaran',
             'siswa/upload_berkas.php'=>'Upload Berkas','siswa/status.php'=>'Status'];
}
$self = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul ?? 'PPDB') ?> - PPDB SMK Nusantara</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">PPDB SMK Nusantara</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <?php foreach ($menu as $href => $label): ?>
          <li class="nav-item"><a class="nav-link <?= basename($href) === $self ? 'active' : '' ?>" href="<?= BASE_URL . '/' . $href ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if ($u): ?>
          <li class="nav-item"><span class="nav-link"><?= e($u['nama']) ?> (<?= e($u['role']) ?>)</span></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/register.php">Daftar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($f = flash()): ?>
  <div class="alert alert-<?= e($f[1]) ?> alert-dismissible fade show"><?= e($f[0]) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
