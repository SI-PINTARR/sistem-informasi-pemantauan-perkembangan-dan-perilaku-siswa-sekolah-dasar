<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

function keluar($kode, $pesan) {
  http_response_code($kode);
  echo json_encode(['error' => $pesan]);
  exit;
}

try {
  $db = new PDO(
    'mysql:host=localhost;dbname=db_pemantauan_siswa;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
  );
} catch (PDOException $e) {
  keluar(500, 'Koneksi database gagal. Cek MySQL di XAMPP.');
}

function wajib_login() {
  if (empty($_SESSION['id_pengguna'])) keluar(401, 'Belum login.');
}

function input() {
  return json_decode(file_get_contents('php://input'), true) ?: [];
}

// Siswa satu kelas, lengkap dengan penanda "perlu perhatian"
function daftar_siswa($db) {
  $st = $db->prepare("SELECT s.id_siswa, s.nis, s.nama_siswa,
    (SELECT ROUND(AVG(n.hasil_semester)) FROM nilai n WHERE n.id_siswa = s.id_siswa) AS rata,
    (SELECT COUNT(*) FROM kehadiran k WHERE k.id_siswa = s.id_siswa AND k.status_kehadiran = 'Alpa') AS alpa,
    (SELECT COUNT(*) FROM notifikasi t WHERE t.id_siswa = s.id_siswa AND t.id_pengguna = ? AND t.status_baca = 0) AS notif
    FROM siswa s WHERE s.id_kelas = ? ORDER BY s.nama_siswa");
  $st->execute([$_SESSION['id_pengguna'], $_SESSION['id_kelas']]);
  $out = [];
  foreach ($st->fetchAll() as $r) {
    $al = [];
    if ($r['rata'] !== null && (int)$r['rata'] < 70) $al[] = 'Nilai rata-rata ' . $r['rata'];
    if ((int)$r['alpa'] >= 3) $al[] = 'Alpa ' . $r['alpa'] . ' kali';
    if ((int)$r['notif'] > 0) $al[] = 'Ada peringatan baru';
    $r['alasan'] = implode(', ', $al);
    $r['perhatian'] = count($al) > 0;
    $out[] = $r;
  }
  return $out;
}
