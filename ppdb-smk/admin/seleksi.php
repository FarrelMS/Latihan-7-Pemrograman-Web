<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0); $hasil = $_POST['hasil'] ?? '';
    $p = db_one('SELECT p.*, j.kuota, j.kode FROM pendaftaran p JOIN jurusan j ON j.id=p.jurusan_id WHERE p.id=?', 'i', [$id]);
    if (!$p || !in_array($hasil, ['Diterima','Ditolak'], true)) flash('Permintaan tidak valid.', 'danger');
    elseif ($p['status_verifikasi'] !== 'Diverifikasi') flash('Seleksi hanya untuk pendaftaran berstatus Diverifikasi.', 'danger');
    else {
        if ($hasil === 'Diterima') {
            $c = db_one('SELECT COUNT(*) c FROM pendaftaran WHERE jurusan_id=? AND hasil_seleksi="Diterima" AND id<>?', 'ii', [$p['jurusan_id'], $id])['c'];
            if ($c >= $p['kuota']) { flash('Kuota jurusan ' . $p['kode'] . ' sudah penuh (' . $p['kuota'] . ').', 'danger'); redirect('admin/seleksi.php'); }
        }
        db_exec('UPDATE pendaftaran SET hasil_seleksi=? WHERE id=?', 'si', [$hasil, $id]);
        riwayat($id, $hasil, 'Hasil seleksi: ' . $hasil);
        flash('Hasil seleksi disimpan: ' . $hasil);
    }
    redirect('admin/seleksi.php');
}
$rows = db_all('SELECT p.*, s.nama_lengkap, s.asal_sekolah, j.kode FROM pendaftaran p JOIN siswa s ON s.id=p.siswa_id JOIN jurusan j ON j.id=p.jurusan_id
  WHERE p.status_verifikasi="Diverifikasi" ORDER BY p.hasil_seleksi, p.id');
$judul = 'Seleksi';
include __DIR__ . '/../includes/header.php';
?>
<h4>Seleksi Calon Siswa</h4>
<p class="text-muted">Hanya pendaftaran berstatus <b>Diverifikasi</b>. Menetapkan "Diterima" akan memeriksa kuota jurusan.</p>
<table class="table table-striped bg-white align-middle">
  <thead><tr><th>No. Daftar</th><th>Nama</th><th>Asal Sekolah</th><th>Jurusan</th><th>Hasil</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['no_pendaftaran']) ?></td><td><?= e($r['nama_lengkap']) ?></td><td><?= e($r['asal_sekolah']) ?></td><td><?= e($r['kode']) ?></td><td><?= badge($r['hasil_seleksi']) ?></td>
    <td><form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
      <button name="hasil" value="Diterima" class="btn btn-sm btn-success">Terima</button>
      <button name="hasil" value="Ditolak" class="btn btn-sm btn-danger">Tolak</button></form></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted">Belum ada pendaftaran terverifikasi.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
