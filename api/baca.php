<?php
require 'koneksi.php';
wajib_login();
$db->prepare('UPDATE notifikasi SET status_baca = 1 WHERE id_pengguna = ?')->execute([$_SESSION['id_pengguna']]);
echo json_encode(['ok' => true]);
