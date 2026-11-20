<?php
require 'koneksi.php';
$in = input();
$u = trim($in['username'] ?? '');
$p = $in['password'] ?? '';

$st = $db->prepare("SELECT p.id_pengguna, p.nama, p.password, g.id_guru,
  (SELECT k.id_kelas FROM kelas k WHERE k.id_guru = g.id_guru ORDER BY k.tahun_ajaran DESC LIMIT 1) AS id_kelas
  FROM pengguna p JOIN guru g ON g.id_pengguna = p.id_pengguna
  WHERE (p.username = ? OR g.nip = ?) AND p.peran = 'Guru' AND p.status_akun = 1 LIMIT 1");
$st->execute([$u, $u]);
$r = $st->fetch();

if (!$r || !password_verify($p, $r['password'])) keluar(401, 'Nama pengguna atau kata sandi salah.');

session_regenerate_id(true);
$_SESSION['id_pengguna'] = $r['id_pengguna'];
$_SESSION['id_guru'] = $r['id_guru'];
$_SESSION['id_kelas'] = $r['id_kelas'];
$_SESSION['nama'] = $r['nama'];
echo json_encode(['ok' => true]);
