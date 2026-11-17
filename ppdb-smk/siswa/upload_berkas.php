<?php
require_once __DIR__ . '/../config/app.php';
require_login('siswa');
$siswa = siswa_login();
$p = $siswa ? pendaftaran_siswa($siswa['id']) : null;
if (!$p) { flash('Lakukan pendaftaran terlebih dahulu.', 'warning'); redirect('siswa/pendaftaran.php'); }

$jenis = ['kk'=>'Kartu Keluarga','akta'=>'Akta Kelahiran','rapor'=>'Rapor','foto'=>'Pas Foto'];
$allowed = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','pdf'=>'application/pdf'];
$terkunci = $p['hasil_seleksi'] !== 'Belum';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$terkunci) {
    csrf_check();
    $ok = 0; $gagal = [];
    foreach ($jenis as $key => $label) {
        $f = $_FILES[$key] ?? null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) continue;
        if ($f['error'] !== UPLOAD_ERR_OK) { $gagal[] = "$label: upload gagal."; continue; }
        if ($f['size'] > 2 * 1024 * 1024) { $gagal[] = "$label: ukuran maksimal 2 MB."; continue; }
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $f['tmp_name']);
        if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) { $gagal[] = "$label: hanya JPG, JPEG, PNG, atau PDF."; continue; }
        $nama = $key . '_' . $p['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $nama)) { $gagal[] = "$label: gagal menyimpan file."; continue; }
        $lama = db_one('SELECT nama_file FROM berkas WHERE pendaftaran_id=? AND jenis=?', 'is', [$p['id'], $key]);
        if ($lama) {
            @unlink(UPLOAD_DIR . $lama['nama_file']);
            db_exec('UPDATE berkas SET nama_file=?,nama_asli=?,ukuran=?,uploaded_at=NOW() WHERE pendaftaran_id=? AND jenis=?', 'ssiis', [$nama, basename($f['name']), $f['size'], $p['id'], $key]);
        } else {
            db_exec('INSERT INTO berkas(pendaftaran_id,jenis,nama_file,nama_asli,ukuran) VALUES(?,?,?,?,?)', 'isssi', [$p['id'], $key, $nama, basename($f['name']), $f['size']]);
        }
        $ok++;
    }
    if ($ok) flash("$ok berkas berhasil diunggah.");
    if ($gagal) flash(implode(' ', $gagal), 'danger');
    redirect('siswa/upload_berkas.php');
}
$ada = [];
foreach (db_all('SELECT * FROM berkas WHERE pendaftaran_id=?', 'i', [$p['id']]) as $b) $ada[$b['jenis']] = $b;
$judul = 'Upload Berkas';
include __DIR__ . '/../includes/header.php';
?>
<div class="card"><div class="card-body p-4">
<h4 class="mb-1">Upload Dokumen Persyaratan</h4>
<p class="text-muted">Format JPG, JPEG, PNG, PDF. Maksimal 2 MB per file. Upload ulang akan mengganti file lama.</p>
<?php if ($terkunci): ?><div class="alert alert-info">Hasil seleksi sudah ditetapkan, berkas tidak dapat diubah.</div><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?>
  <?php foreach ($jenis as $key => $label): ?>
    <div class="row mb-3 align-items-center">
      <label class="col-md-3 col-form-label"><?= $label ?></label>
      <div class="col-md-5"><input type="file" name="<?= $key ?>" class="form-control" accept=".jpg,.jpeg,.png,.pdf" <?= $terkunci ? 'disabled' : '' ?>></div>
      <div class="col-md-4 small">
        <?php if (isset($ada[$key])): ?>
          <span class="badge text-bg-success">Terunggah</span>
          <a href="<?= BASE_URL ?>/unduh.php?id=<?= $ada[$key]['id'] ?>" target="_blank"><?= e($ada[$key]['nama_asli']) ?></a>
        <?php else: ?><span class="badge text-bg-secondary">Belum ada</span><?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$terkunci): ?><button class="btn btn-primary">Upload</button><?php endif; ?>
</form>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
