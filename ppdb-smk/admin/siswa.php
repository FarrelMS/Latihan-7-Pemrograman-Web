<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$rows = db_all('SELECT s.*, u.email, p.no_pendaftaran, j.kode, p.status_verifikasi, p.hasil_seleksi
  FROM siswa s JOIN users u ON u.id=s.user_id
  LEFT JOIN pendaftaran p ON p.siswa_id=s.id LEFT JOIN jurusan j ON j.id=p.jurusan_id
  WHERE s.nama_lengkap LIKE ? OR s.nisn LIKE ? OR p.no_pendaftaran LIKE ? ORDER BY s.id DESC', 'sss', [$like, $like, $like]);
$judul = 'Data Siswa';
include __DIR__ . '/../includes/header.php';
?>
<h4>Data Siswa</h4>
<form class="row g-2 mb-3"><div class="col-md-4"><input name="q" value="<?= e($q) ?>" class="form-control" placeholder="Cari nama / NISN / no. pendaftaran"></div><div class="col"><button class="btn btn-primary">Cari</button></div></form>
<div class="table-responsive"><table class="table table-striped bg-white">
  <thead><tr><th>No. Daftar</th><th>NISN</th><th>Nama</th><th>L/P</th><th>TTL</th><th>Asal Sekolah</th><th>HP</th><th>Jurusan</th><th>Verifikasi</th><th>Hasil</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['no_pendaftaran'] ?? '-') ?></td><td><?= e($r['nisn']) ?></td><td><?= e($r['nama_lengkap']) ?><br><small class="text-muted"><?= e($r['email']) ?></small></td>
    <td><?= e($r['jenis_kelamin']) ?></td><td><?= e($r['tempat_lahir'] . ', ' . $r['tanggal_lahir']) ?></td><td><?= e($r['asal_sekolah']) ?></td><td><?= e($r['no_hp']) ?></td>
    <td><?= e($r['kode'] ?? '-') ?></td><td><?= $r['status_verifikasi'] ? badge($r['status_verifikasi']) : '-' ?></td><td><?= $r['hasil_seleksi'] ? badge($r['hasil_seleksi']) : '-' ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="10" class="text-center text-muted">Tidak ada data.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
