<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
$label = ['kk'=>'Kartu Keluarga','akta'=>'Akta Kelahiran','rapor'=>'Rapor','foto'=>'Pas Foto'];
$rows = db_all('SELECT p.id, p.no_pendaftaran, s.nama_lengkap FROM pendaftaran p JOIN siswa s ON s.id=p.siswa_id ORDER BY p.id DESC');
$berkas = [];
foreach (db_all('SELECT * FROM berkas') as $b) $berkas[$b['pendaftaran_id']][$b['jenis']] = $b;
$judul = 'Berkas Pendaftar';
include __DIR__ . '/../includes/header.php';
?>
<h4>Berkas Pendaftar</h4>
<div class="table-responsive"><table class="table table-striped bg-white align-middle">
  <thead><tr><th>No. Daftar</th><th>Nama</th><?php foreach ($label as $l): ?><th><?= $l ?></th><?php endforeach; ?></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['no_pendaftaran']) ?></td><td><?= e($r['nama_lengkap']) ?></td>
    <?php foreach ($label as $k => $l): $b = $berkas[$r['id']][$k] ?? null; ?>
      <td><?= $b ? '<a target="_blank" href="' . BASE_URL . '/unduh.php?id=' . $b['id'] . '">Lihat</a> <small class="text-muted">(' . round($b['ukuran'] / 1024) . ' KB)</small>' : '<span class="text-muted">-</span>' ?></td>
    <?php endforeach; ?></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
