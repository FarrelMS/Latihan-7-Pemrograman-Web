<?php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
$status = $_GET['status'] ?? ''; $hasil = $_GET['hasil'] ?? ''; $jid = (int)($_GET['jurusan'] ?? 0);
$w = []; $t = ''; $p = [];
if (in_array($status, ['Menunggu','Diverifikasi','Ditolak'], true)) { $w[] = 'p.status_verifikasi=?'; $t .= 's'; $p[] = $status; } else $status = '';
if (in_array($hasil, ['Belum','Diterima','Ditolak'], true)) { $w[] = 'p.hasil_seleksi=?'; $t .= 's'; $p[] = $hasil; } else $hasil = '';
if ($jid) { $w[] = 'p.jurusan_id=?'; $t .= 'i'; $p[] = $jid; }
$rows = db_all('SELECT p.no_pendaftaran,s.nisn,s.nama_lengkap,s.asal_sekolah,s.no_hp,j.nama jurusan,p.status_verifikasi,p.hasil_seleksi,p.created_at
  FROM pendaftaran p JOIN siswa s ON s.id=p.siswa_id JOIN jurusan j ON j.id=p.jurusan_id' . ($w ? ' WHERE ' . implode(' AND ', $w) : '') . ' ORDER BY p.created_at DESC', $t, $p);

if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan_ppdb_' . date('Ymd_His') . '.csv"');
    $o = fopen('php://output', 'w');
    fwrite($o, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
    fputcsv($o, ['No Pendaftaran','NISN','Nama','Asal Sekolah','No HP','Jurusan','Status Verifikasi','Hasil Seleksi','Tanggal Daftar']);
    foreach ($rows as $r) fputcsv($o, array_values($r));
    fclose($o); exit;
}
$jurusan = db_all('SELECT id,nama FROM jurusan ORDER BY nama');
$qs = http_build_query(['status'=>$status,'hasil'=>$hasil,'jurusan'=>$jid,'export'=>1]);
$judul = 'Laporan';
include __DIR__ . '/../includes/header.php';
?>
<h4>Laporan Pendaftaran</h4>
<form class="row g-2 mb-3">
  <div class="col-md-3"><select name="status" class="form-select"><option value="">Semua verifikasi</option>
    <?php foreach (['Menunggu','Diverifikasi','Ditolak'] as $s): ?><option <?= $s === $status ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3"><select name="hasil" class="form-select"><option value="">Semua hasil</option>
    <?php foreach (['Belum','Diterima','Ditolak'] as $s): ?><option <?= $s === $hasil ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3"><select name="jurusan" class="form-select"><option value="0">Semua jurusan</option>
    <?php foreach ($jurusan as $j): ?><option value="<?= $j['id'] ?>" <?= $j['id'] == $jid ? 'selected' : '' ?>><?= e($j['nama']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-3"><button class="btn btn-primary">Filter</button> <a class="btn btn-success" href="?<?= e($qs) ?>">Ekspor CSV</a></div>
</form>
<p class="text-muted">Jumlah data: <b><?= count($rows) ?></b></p>
<div class="table-responsive"><table class="table table-striped bg-white">
  <thead><tr><th>No. Daftar</th><th>NISN</th><th>Nama</th><th>Asal Sekolah</th><th>HP</th><th>Jurusan</th><th>Verifikasi</th><th>Hasil</th><th>Tanggal</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e($r['no_pendaftaran']) ?></td><td><?= e($r['nisn']) ?></td><td><?= e($r['nama_lengkap']) ?></td><td><?= e($r['asal_sekolah']) ?></td><td><?= e($r['no_hp']) ?></td>
    <td><?= e($r['jurusan']) ?></td><td><?= badge($r['status_verifikasi']) ?></td><td><?= badge($r['hasil_seleksi']) ?></td><td><?= e($r['created_at']) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="9" class="text-center text-muted">Tidak ada data.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
