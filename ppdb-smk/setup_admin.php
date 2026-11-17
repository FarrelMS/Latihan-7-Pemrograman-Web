<?php
// Jalankan SEKALI lewat browser, lalu HAPUS file ini.
require_once __DIR__ . '/config/app.php';
$email = 'admin@ppdb.test';
if (db_one('SELECT id FROM users WHERE email=?', 's', [$email])) die('Admin sudah ada. Hapus file setup_admin.php.');
db_exec('INSERT INTO users(nama,email,password,role) VALUES(?,?,?,"admin")', 'sss',
        ['Administrator', $email, password_hash('admin123', PASSWORD_DEFAULT)]);
echo 'Admin dibuat. Email: admin@ppdb.test | Password: admin123. Segera hapus file ini.';
