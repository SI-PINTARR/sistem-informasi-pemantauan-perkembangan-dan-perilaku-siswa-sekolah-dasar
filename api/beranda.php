<?php
require 'koneksi.php';
wajib_login();
$k = $_SESSION['id_kelas'];
$u = $_SESSION['id_pengguna'];
$kelas = null; $kh = ['tanggal' => null, 'hadir' => 0, 'total' => 0];
$rata = null; $tugas = null; $akt = []; $siswa = [];

if ($k) {
  $s = $db->prepare('SELECT nama_kelas, tahun_ajaran FROM kelas WHERE id_kelas = ?');
  $s->execute([$k]); $kelas = $s->fetch();

  $s = $db->prepare('SELECT MAX(h.tanggal) FROM kehadiran h JOIN siswa s ON s.id_siswa = h.id_siswa WHERE s.id_kelas = ?');
  $s->execute([$k]); $tgl = $s->fetchColumn();
  if ($tgl) {
    $s = $db->prepare("SELECT SUM(h.status_kehadiran = 'Hadir') AS hadir, COUNT(*) AS total
      FROM kehadiran h JOIN siswa s ON s.id_siswa = h.id_siswa WHERE s.id_kelas = ? AND h.tanggal = ?");
    $s->execute([$k, $tgl]); $kh = $s->fetch(); $kh['tanggal'] = $tgl;
  }

  $s = $db->prepare('SELECT AVG(n.hasil_semester) FROM nilai n JOIN siswa s ON s.id_siswa = n.id_siswa WHERE s.id_kelas = ?');
  $s->execute([$k]); $rata = $s->fetchColumn();

  $s = $db->prepare('SELECT t.judul_tugas FROM tugas t JOIN siswa s ON s.id_siswa = t.id_siswa WHERE s.id_kelas = ? ORDER BY t.tanggal DESC LIMIT 1');
  $s->execute([$k]); $tugas = $s->fetchColumn();

  $s = $db->prepare('SELECT DISTINCT t.judul_tugas, t.mata_pelajaran, t.tanggal FROM tugas t JOIN siswa s ON s.id_siswa = t.id_siswa
    WHERE s.id_kelas = ? AND t.tanggal >= CURDATE() ORDER BY t.tanggal LIMIT 5');
  $s->execute([$k]); $akt = $s->fetchAll();

  $siswa = daftar_siswa($db);
}

$s = $db->prepare('SELECT t.jenis_peringatan, t.pesan, t.tanggal, t.status_baca, s.nama_siswa
  FROM notifikasi t JOIN siswa s ON s.id_siswa = t.id_siswa WHERE t.id_pengguna = ? ORDER BY t.tanggal DESC LIMIT 10');
$s->execute([$u]); $notif = $s->fetchAll();
$s = $db->prepare('SELECT COUNT(*) FROM notifikasi WHERE id_pengguna = ? AND status_baca = 0');
$s->execute([$u]); $belum = (int)$s->fetchColumn();

echo json_encode([
  'guru' => ['nama' => $_SESSION['nama']],
  'kelas' => $kelas,
  'jumlah_siswa' => count($siswa),
  'kehadiran' => ['tanggal' => $kh['tanggal'], 'hadir' => (int)$kh['hadir'], 'total' => (int)$kh['total']],
  'rata_kelas' => $rata === false || $rata === null ? null : (int)round($rata),
  'tugas_terakhir' => $tugas ?: null,
  'perhatian' => array_values(array_filter($siswa, fn($x) => $x['perhatian'])),
  'aktivitas' => $akt,
  'notifikasi' => $notif,
  'notif' => $belum
]);
