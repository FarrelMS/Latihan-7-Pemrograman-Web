<?php
require_once __DIR__ . '/../config/app.php';
if (!empty($_SESSION['user'])) redirect($_SESSION['user']['role'] === 'admin' ? 'admin/dashboard.php' : 'siswa/dashboard.php');
$err = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? ''); $pw = $_POST['password'] ?? '';
    $u = db_one('SELECT * FROM users WHERE email=?', 's', [$email]);
    if ($u && password_verify($pw, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'nama' => $u['nama'], 'role' => $u['role']];
        redirect($u['role'] === 'admin' ? 'admin/dashboard.php' : 'siswa/dashboard.php');
    }
    $err = 'Email atau password salah.';
}
$judul = 'Login';
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card"><div class="card-body p-4">
  <h4 class="mb-3">Login</h4>
  <?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif; ?>
  <form method="post"><?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($email) ?>" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Masuk</button>
  </form>
  <p class="mt-3 mb-0 small">Belum punya akun? <a href="register.php">Daftar</a></p>
</div></div></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
