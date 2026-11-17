<?php
require_once __DIR__ . '/../config/app.php';
require_login('siswa');
$siswa = siswa_login();
$p = $siswa ? pendaftaran_siswa($siswa['id']) : null;
$riwayat = $p ? db_all('SELECT * FROM riwayat_status WHERE pendaftaran_id=? ORDER BY id DESC', 'i', [$p['id']]) : [];
$judul = 'Status Pendaftaran';
include __DIR__ . '/../includes/header.php';
?>
<h4>Status Pendaftaran &amp; Pengumuman</h4>
<?php if (!$p): ?>
  <div class="alert alert-warning">Anda belum melakukan pendaftaran. <a href="pendaftaran.php">Daftar sekarang</a></div>
<?php else: ?>
  <div class="card mb-3"><div class="card-body">
    <p class="mb-1">No. Pendaftaran: <b><?= e($p['no_pendaftaran']) ?></b> | Jurusan: <b><?= e($p['jurusan']) ?></b></p>
    <p class="mb-2">Verifikasi: <?= badge($p['status_verifikasi']) ?></p>
    <?php if ($p['hasil_seleksi'] === 'Diterima'): ?>
      <div class="alert alert-success mb-0"><b>Selamat!</b> Anda dinyatakan <b>DITERIMA</b> di jurusan <?= e($p['jurusan']) ?>.</div>
    <?php elseif ($p['hasil_seleksi'] === 'Ditolak'): ?>
      <div class="alert alert-danger mb-0">Mohon maaf, Anda dinyatakan <b>TIDAK DITERIMA</b>.</div>
    <?php else: ?>
      <div class="alert alert-secondary mb-0">Hasil seleksi belum diumumkan.</div>
    <?php endif; ?>
  </div></div>
  <h5>Riwayat Perubahan Status</h5>
  <table class="table table-sm table-striped bg-white">
    <thead><tr><th>Waktu</th><th>Status</th><th>Keterangan</th></tr></thead>
    <tbody>
    <?php foreach ($riwayat as $r): ?>
      <tr><td><?= e($r['created_at']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['keterangan']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
