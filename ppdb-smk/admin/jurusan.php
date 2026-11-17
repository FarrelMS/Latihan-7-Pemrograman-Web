<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($aksi === 'simpan') {
        $kode = strtoupper(trim($_POST['kode'] ?? '')); $nama = trim($_POST['nama'] ?? ''); $kuota = (int)($_POST['kuota'] ?? 0);
        if ($kode === '' || $nama === '' || $kuota < 0) { flash('Data jurusan tidak valid.', 'danger'); redirect('admin/jurusan.php'); }
        if ($id) $ok = db_exec('UPDATE jurusan SET kode=?,nama=?,kuota=? WHERE id=?', 'ssii', [$kode, $nama, $kuota, $id]);
        else $ok = db_exec('INSERT INTO jurusan(kode,nama,kuota) VALUES(?,?,?)', 'ssi', [$kode, $nama, $kuota]);
        flash($ok === false ? 'Gagal menyimpan (kode mungkin sudah dipakai).' : 'Jurusan disimpan.', $ok === false ? 'danger' : 'success');
    } elseif ($aksi === 'toggle') {
        db_exec('UPDATE jurusan SET aktif=1-aktif WHERE id=?', 'i', [$id]); flash('Status jurusan diubah.');
    } elseif ($aksi === 'hapus') {
        if (db_one('SELECT id FROM pendaftaran WHERE jurusan_id=? LIMIT 1', 'i', [$id])) flash('Jurusan sudah punya pendaftar, nonaktifkan saja.', 'danger');
        else { db_exec('DELETE FROM jurusan WHERE id=?', 'i', [$id]); flash('Jurusan dihapus.'); }
    }
    redirect('admin/jurusan.php');
}
$edit = isset($_GET['edit']) ? db_one('SELECT * FROM jurusan WHERE id=?', 'i', [(int)$_GET['edit']]) : null;
$rows = db_all('SELECT j.*, (SELECT COUNT(*) FROM pendaftaran p WHERE p.jurusan_id=j.id AND p.hasil_seleksi="Diterima") diterima FROM jurusan j ORDER BY j.nama');
$judul = 'Kelola Jurusan';
include __DIR__ . '/../includes/header.php';
?>
<h4>Kelola Jurusan &amp; Kuota</h4>
<div class="card mb-4"><div class="card-body">
  <form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
    <div class="col-md-2"><input name="kode" class="form-control" placeholder="Kode" value="<?= e($edit['kode'] ?? '') ?>" required></div>
    <div class="col-md-5"><input name="nama" class="form-control" placeholder="Nama jurusan" value="<?= e($edit['nama'] ?? '') ?>" required></div>
    <div class="col-md-2"><input type="number" min="0" name="kuota" class="form-control" placeholder="Kuota" value="<?= e($edit['kuota'] ?? '') ?>" required></div>
    <div class="col-md-3"><button class="btn btn-primary"><?= $edit ? 'Update' : 'Tambah' ?></button> <?php if ($edit): ?><a href="jurusan.php" class="btn btn-outline-secondary">Batal</a><?php endif; ?></div>
  </form>
</div></div>
<table class="table table-striped bg-white align-middle">
  <thead><tr><th>Kode</th><th>Nama</th><th>Kuota</th><th>Diterima</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['kode']) ?></td><td><?= e($r['nama']) ?></td><td><?= $r['kuota'] ?></td><td><?= $r['diterima'] ?></td>
    <td><?= $r['aktif'] ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>' ?></td>
    <td class="d-flex gap-1">
      <a href="?edit=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="aksi" value="toggle" class="btn btn-sm btn-outline-warning"><?= $r['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
      <form method="post" data-confirm="Hapus jurusan ini?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="aksi" value="hapus" class="btn btn-sm btn-outline-danger">Hapus</button></form>
    </td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
