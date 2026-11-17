<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
$tot = db_one('SELECT COUNT(*) total,
  SUM(status_verifikasi="Menunggu") menunggu, SUM(status_verifikasi="Diverifikasi") diverifikasi,
  SUM(status_verifikasi="Ditolak") ditolak, SUM(hasil_seleksi="Diterima") diterima FROM pendaftaran');
$perJurusan = db_all('SELECT j.kode,j.nama,j.kuota,COUNT(p.id) pendaftar, COALESCE(SUM(p.hasil_seleksi="Diterima"),0) diterima
  FROM jurusan j LEFT JOIN pendaftaran p ON p.jurusan_id=j.id GROUP BY j.id ORDER BY j.nama');
$judul = 'Dashboard Admin';
include __DIR__ . '/../includes/header.php';
$kartu = ['Total Pendaftar'=>$tot['total'],'Menunggu'=>$tot['menunggu'],'Diverifikasi'=>$tot['diverifikasi'],'Ditolak'=>$tot['ditolak'],'Diterima'=>$tot['diterima']];
?>
<h3 class="mb-3">Dashboard Admin</h3>
<div class="row g-3 mb-4">
<?php foreach ($kartu as $l => $v): ?>
  <div class="col-6 col-md"><div class="card text-center"><div class="card-body"><div class="text-muted small"><?= $l ?></div><div class="fs-2 fw-bold"><?= (int)$v ?></div></div></div></div>
<?php endforeach; ?>
</div>
<h5>Pendaftar per Jurusan</h5>
<table class="table table-striped bg-white">
  <thead><tr><th>Kode</th><th>Jurusan</th><th>Kuota</th><th>Pendaftar</th><th>Diterima</th><th>Sisa Kuota</th></tr></thead>
  <tbody>
  <?php foreach ($perJurusan as $r): ?>
    <tr><td><?= e($r['kode']) ?></td><td><?= e($r['nama']) ?></td><td><?= $r['kuota'] ?></td><td><?= $r['pendaftar'] ?></td><td><?= $r['diterima'] ?></td><td><?= max(0, $r['kuota'] - $r['diterima']) ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
