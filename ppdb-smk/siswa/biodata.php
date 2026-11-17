<?php
require_once __DIR__ . '/../config/app.php';
require_login('siswa');
$s = siswa_login() ?: ['nisn'=>'','nama_lengkap'=>$_SESSION['user']['nama'],'jenis_kelamin'=>'','tempat_lahir'=>'','tanggal_lahir'=>'','alamat'=>'','asal_sekolah'=>'','no_hp'=>''];
$ada = (bool) siswa_login();
$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['nisn','nama_lengkap','jenis_kelamin','tempat_lahir','tanggal_lahir','alamat','asal_sekolah','no_hp'] as $k) $s[$k] = trim($_POST[$k] ?? '');
    if (!preg_match('/^\d{10}$/', $s['nisn'])) $err[] = 'NISN harus 10 digit angka.';
    if ($s['nama_lengkap'] === '') $err[] = 'Nama lengkap wajib diisi.';
    if (!in_array($s['jenis_kelamin'], ['L','P'], true)) $err[] = 'Pilih jenis kelamin.';
    if ($s['tempat_lahir'] === '') $err[] = 'Tempat lahir wajib diisi.';
    $d = DateTime::createFromFormat('Y-m-d', $s['tanggal_lahir']);
    if (!$d || $d->format('Y-m-d') !== $s['tanggal_lahir'] || $d > new DateTime()) $err[] = 'Tanggal lahir tidak valid.';
    if ($s['alamat'] === '') $err[] = 'Alamat wajib diisi.';
    if ($s['asal_sekolah'] === '') $err[] = 'Asal sekolah wajib diisi.';
    if (!preg_match('/^[0-9+\-]{8,20}$/', $s['no_hp'])) $err[] = 'Nomor HP tidak valid.';
    if (!$err) {
        $v = [$s['nisn'],$s['nama_lengkap'],$s['jenis_kelamin'],$s['tempat_lahir'],$s['tanggal_lahir'],$s['alamat'],$s['asal_sekolah'],$s['no_hp'],$_SESSION['user']['id']];
        if ($ada) db_exec('UPDATE siswa SET nisn=?,nama_lengkap=?,jenis_kelamin=?,tempat_lahir=?,tanggal_lahir=?,alamat=?,asal_sekolah=?,no_hp=? WHERE user_id=?', 'ssssssssi', $v);
        else db_exec('INSERT INTO siswa(nisn,nama_lengkap,jenis_kelamin,tempat_lahir,tanggal_lahir,alamat,asal_sekolah,no_hp,user_id) VALUES(?,?,?,?,?,?,?,?,?)', 'ssssssssi', $v);
        flash('Biodata berhasil disimpan.');
        redirect('siswa/biodata.php');
    }
}
$judul = 'Biodata';
include __DIR__ . '/../includes/header.php';
?>
<div class="card"><div class="card-body p-4">
  <h4 class="mb-3">Biodata Calon Siswa</h4>
  <?php foreach ($err as $x): ?><div class="alert alert-danger py-2"><?= e($x) ?></div><?php endforeach; ?>
  <form method="post" class="row g-3"><?= csrf_field() ?>
    <div class="col-md-6"><label class="form-label">NISN</label><input name="nisn" maxlength="10" class="form-control" value="<?= e($s['nisn']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input name="nama_lengkap" class="form-control" value="<?= e($s['nama_lengkap']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Jenis Kelamin</label>
      <select name="jenis_kelamin" class="form-select" required>
        <option value="">-- pilih --</option>
        <option value="L" <?= $s['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
        <option value="P" <?= $s['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
      </select></div>
    <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input name="tempat_lahir" class="form-control" value="<?= e($s['tempat_lahir']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control" value="<?= e($s['tanggal_lahir']) ?>" required></div>
    <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" rows="2" class="form-control" required><?= e($s['alamat']) ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Asal Sekolah</label><input name="asal_sekolah" class="form-control" value="<?= e($s['asal_sekolah']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">No. HP</label><input name="no_hp" class="form-control" value="<?= e($s['no_hp']) ?>" required></div>
    <div class="col-12"><button class="btn btn-primary">Simpan Biodata</button></div>
  </form>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
