<?php
require 'koneksi.php';
wajib_login();
$in = input();
$id = (int)($in['id_siswa'] ?? 0);
$st = $in['status'] ?? '';
$jenis = $in['jenis'] ?? 'Catatan';
$cat = trim($in['catatan'] ?? '');

if (!in_array($st, ['Hadir', 'Izin', 'Sakit', 'Alpa'], true)) keluar(400, 'Status kehadiran tidak valid.');
if (!in_array($jenis, ['Positif', 'Negatif', 'Catatan'], true)) keluar(400, 'Jenis catatan tidak valid.');

$c = $db->prepare('SELECT nama_siswa FROM siswa WHERE id_siswa = ? AND id_kelas = ?');
$c->execute([$id, $_SESSION['id_kelas']]);
$nama = $c->fetchColumn();
if (!$nama) keluar(403, 'Siswa bukan di kelas Anda.');

$db->prepare('INSERT INTO kehadiran (id_siswa, tanggal, status_kehadiran) VALUES (?, CURDATE(), ?)
  ON DUPLICATE KEY UPDATE status_kehadiran = VALUES(status_kehadiran)')->execute([$id, $st]);
if ($cat !== '') {
  $db->prepare('INSERT INTO perilaku (id_siswa, tanggal, jenis_perilaku, keterangan) VALUES (?, CURDATE(), ?, ?)')
     ->execute([$id, $jenis, mb_substr($cat, 0, 500)]);
}
echo json_encode(['pesan' => "Catatan $nama tersimpan ($st)"]);
