<?php
require_once __DIR__ . '/../config/app.php';
require_login('siswa');
$siswa = siswa_login();
if (!$siswa) { flash('Lengkapi biodata terlebih dahulu.', 'warning'); redirect('siswa/biodata.php'); }
$p = pendaftaran_siswa($siswa['id']);
$jurusan = db_all('SELECT * FROM jurusan WHERE aktif=1 ORDER BY nama');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$p) {
    csrf_check();
    $jid = (int)($_POST['jurusan_id'] ?? 0);
    $j = db_one('SELECT id FROM jurusan WHERE id=? AND aktif=1', 'i', [$jid]);
    if (!$j) { flash('Pilih jurusan yang valid.', 'danger'); redirect('siswa/pendaftaran.php'); }
    $tmp = 'TMP' . bin2hex(random_bytes(6));
    db_exec('INSERT INTO pendaftaran(no_pendaftaran,siswa_id,jurusan_id) VALUES(?,?,?)', 'sii', [$tmp, $siswa['id'], $jid]);
    $id = insert_id();
    $no = 'PPDB-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    db_exec('UPDATE pendaftaran SET no_pendaftaran=? WHERE id=?', 'si', [$no, $id]);
    riwayat($id, 'Menunggu', 'Formulir pendaftaran dikirim');
    flash("Pendaftaran berhasil. Nomor pendaftaran Anda: $no");
    redirect('siswa/pendaftaran.php');
}
$judul = 'Pendaftaran';
include __DIR__ . '/../includes/header.php';
?>
<div class="card"><div class="card-body p-4">
<h4 class="mb-3">Formulir Pendaftaran</h4>
<?php if ($p): ?>
  <table class="table w-auto">
    <tr><th>No. Pendaftaran</th><td><?= e($p['no_pendaftaran']) ?></td></tr>
    <tr><th>Nama</th><td><?= e($siswa['nama_lengkap']) ?></td></tr>
    <tr><th>Jurusan</th><td><?= e($p['kode'] . ' - ' . $p['jurusan']) ?></td></tr>
    <tr><th>Tanggal</th><td><?= e($p['created_at']) ?></td></tr>
    <tr><th>Verifikasi</th><td><?= badge($p['status_verifikasi']) ?></td></tr>
  </table>
  <a href="upload_berkas.php" class="btn btn-primary">Lanjut upload berkas</a>
<?php else: ?>
  <form method="post" data-confirm="Kirim pendaftaran? Jurusan tidak dapat diubah setelah dikirim."><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Pilih Jurusan</label>
      <select name="jurusan_id" class="form-select" required>
        <option value="">-- pilih jurusan --</option>
        <?php foreach ($jurusan as $j): ?><option value="<?= $j['id'] ?>"><?= e($j['kode'] . ' - ' . $j['nama']) ?></option><?php endforeach; ?>
      </select></div>
    <button class="btn btn-primary">Kirim Pendaftaran</button>
  </form>
<?php endif; ?>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
