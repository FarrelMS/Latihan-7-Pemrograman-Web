<?php
require_once __DIR__ . '/../config/app.php';
require_login('siswa');
$siswa = siswa_login();
$p = $siswa ? pendaftaran_siswa($siswa['id']) : null;
$jml = $p ? db_one('SELECT COUNT(*) c FROM berkas WHERE pendaftaran_id=?', 'i', [$p['id']])['c'] : 0;
$judul = 'Dashboard Siswa';
include __DIR__ . '/../includes/header.php';
?>
<h3>Selamat datang, <?= e($_SESSION['user']['nama']) ?></h3>
<div class="row g-3 mt-1">
  <div class="col-md-4"><div class="card"><div class="card-body">
    <h6 class="text-muted">1. Biodata</h6>
    <?= $siswa ? '<span class="badge text-bg-success">Sudah diisi</span>' : '<span class="badge text-bg-warning">Belum diisi</span>' ?>
    <div class="mt-2"><a href="biodata.php" class="btn btn-sm btn-outline-primary">Isi / ubah biodata</a></div>
  </div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body">
    <h6 class="text-muted">2. Pendaftaran</h6>
    <?php if ($p): ?>No: <b><?= e($p['no_pendaftaran']) ?></b><br>Jurusan: <?= e($p['jurusan']) ?>
    <?php else: ?><span class="badge text-bg-warning">Belum mendaftar</span><div class="mt-2"><a href="pendaftaran.php" class="btn btn-sm btn-outline-primary">Daftar sekarang</a></div><?php endif; ?>
  </div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body">
    <h6 class="text-muted">3. Berkas &amp; Status</h6>
    <?php if ($p): ?>Berkas: <b><?= $jml ?>/4</b> | Verifikasi: <?= badge($p['status_verifikasi']) ?> | Hasil: <?= badge($p['hasil_seleksi']) ?>
    <div class="mt-2"><a href="upload_berkas.php" class="btn btn-sm btn-outline-primary">Upload</a> <a href="status.php" class="btn btn-sm btn-outline-secondary">Status</a></div>
    <?php else: ?><span class="text-muted">Tersedia setelah mendaftar.</span><?php endif; ?>
  </div></div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
