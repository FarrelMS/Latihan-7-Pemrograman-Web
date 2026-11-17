<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0); $st = $_POST['status'] ?? ''; $ket = trim($_POST['keterangan'] ?? '');
    if (db_one('SELECT id FROM pendaftaran WHERE id=?', 'i', [$id]) && in_array($st, ['Menunggu','Diverifikasi','Ditolak'], true)) {
        // jika verifikasi dibatalkan/ditolak, hasil seleksi dikembalikan ke "Belum"
        db_exec('UPDATE pendaftaran SET status_verifikasi=?, hasil_seleksi=IF(?="Diverifikasi", hasil_seleksi, "Belum") WHERE id=?', 'ssi', [$st, $st, $id]);
        riwayat($id, $st, $ket !== '' ? $ket : 'Status verifikasi diperbarui admin');
        flash('Status verifikasi diperbarui.');
    }
    redirect('admin/verifikasi.php');
}
$rows = db_all('SELECT p.*, s.nama_lengkap, s.nisn, j.kode,
  (SELECT COUNT(*) FROM berkas b WHERE b.pendaftaran_id=p.id) jml_berkas
  FROM pendaftaran p JOIN siswa s ON s.id=p.siswa_id JOIN jurusan j ON j.id=p.jurusan_id ORDER BY FIELD(p.status_verifikasi,"Menunggu","Diverifikasi","Ditolak"), p.id DESC');
$judul = 'Verifikasi Pendaftaran';
include __DIR__ . '/../includes/header.php';
?>
<h4>Verifikasi Pendaftaran</h4>
<div class="table-responsive"><table class="table table-striped bg-white align-middle">
  <thead><tr><th>No. Daftar</th><th>Nama / NISN</th><th>Jurusan</th><th>Berkas</th><th>Status</th><th>Ubah Status</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['no_pendaftaran']) ?></td><td><?= e($r['nama_lengkap']) ?><br><small class="text-muted"><?= e($r['nisn']) ?></small></td><td><?= e($r['kode']) ?></td><td><?= $r['jml_berkas'] ?>/4</td><td><?= badge($r['status_verifikasi']) ?></td>
    <td><form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
      <select name="status" class="form-select form-select-sm w-auto">
        <?php foreach (['Menunggu','Diverifikasi','Ditolak'] as $s): ?><option <?= $s === $r['status_verifikasi'] ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
      </select>
      <input name="keterangan" class="form-control form-control-sm" placeholder="Keterangan (opsional)">
      <button class="btn btn-sm btn-primary">Simpan</button></form></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted">Belum ada pendaftaran.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
