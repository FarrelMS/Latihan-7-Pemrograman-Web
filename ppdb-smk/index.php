<?php
require_once __DIR__ . '/config/app.php';
$jurusan = db_all('SELECT j.*, (SELECT COUNT(*) FROM pendaftaran p WHERE p.jurusan_id=j.id AND p.hasil_seleksi="Diterima") AS diterima
                   FROM jurusan j WHERE j.aktif=1 ORDER BY j.nama');
$judul = 'Beranda';
include __DIR__ . '/includes/header.php';
?>
<div class="hero p-5 mb-4 text-center">
  <h1 class="fw-bold">Penerimaan Peserta Didik Baru</h1>
  <p class="lead">SMK Nusantara - Tahun Ajaran <?= date('Y') ?>/<?= date('Y') + 1 ?></p>
  <a href="<?= BASE_URL ?>/auth/register.php" class="btn btn-light btn-lg">Mulai Pendaftaran</a>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <h4>Program Keahlian &amp; Kuota</h4>
    <div class="row g-3">
      <?php foreach ($jurusan as $j): ?>
        <div class="col-md-6"><div class="card h-100"><div class="card-body">
          <h5><?= e($j['kode']) ?> <small class="text-muted">- <?= e($j['nama']) ?></small></h5>
          <p class="mb-0">Kuota: <b><?= (int)$j['kuota'] ?></b> | Sisa: <b><?= max(0, $j['kuota'] - $j['diterima']) ?></b></p>
        </div></div></div>
      <?php endforeach; ?>
      <?php if (!$jurusan): ?><p class="text-muted">Belum ada jurusan aktif.</p><?php endif; ?>
    </div>
  </div>
  <div class="col-lg-4">
    <h4>Persyaratan</h4>
    <ul class="list-group">
      <li class="list-group-item">Kartu Keluarga</li>
      <li class="list-group-item">Akta Kelahiran</li>
      <li class="list-group-item">Rapor</li>
      <li class="list-group-item">Pas Foto</li>
      <li class="list-group-item text-muted small">Format JPG/JPEG/PNG/PDF, maks. 2 MB per file</li>
    </ul>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
