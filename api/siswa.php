<?php
require 'koneksi.php';
wajib_login();
echo json_encode(['siswa' => $_SESSION['id_kelas'] ? daftar_siswa($db) : []]);
