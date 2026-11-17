<?php
require_once __DIR__ . '/../config/app.php';
$err = []; $nama = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? ''); $email = trim($_POST['email'] ?? '');
    $pw = $_POST['password'] ?? ''; $pw2 = $_POST['password2'] ?? '';
    if ($nama === '') $err[] = 'Nama lengkap wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err[] = 'Format email tidak valid.';
    if (strlen($pw) < 6) $err[] = 'Password minimal 6 karakter.';
    if ($pw !== $pw2) $err[] = 'Konfirmasi password tidak sama.';
    if (!$err && db_one('SELECT id FROM users WHERE email=?', 's', [$email])) $err[] = 'Email sudah digunakan.';
    if (!$err) {
        db_exec('INSERT INTO users(nama,email,password,role) VALUES(?,?,?,"siswa")', 'sss', [$nama, $email, password_hash($pw, PASSWORD_DEFAULT)]);
        flash('Registrasi berhasil, silakan login.');
        redirect('auth/login.php');
    }
}
$judul = 'Registrasi';
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card"><div class="card-body p-4">
  <h4 class="mb-3">Registrasi Calon Siswa</h4>
  <?php foreach ($err as $x): ?><div class="alert alert-danger py-2"><?= e($x) ?></div><?php endforeach; ?>
  <form method="post"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input name="nama" class="form-control" value="<?= e($nama) ?>" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($email) ?>" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Konfirmasi Password</label><input type="password" name="password2" class="form-control" required></div>
    <button class="btn btn-primary w-100">Daftar</button>
  </form>
  <p class="mt-3 mb-0 small">Sudah punya akun? <a href="login.php">Login</a></p>
</div></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
