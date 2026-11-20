<?php
require 'koneksi.php';
wajib_login();
$s = $db->prepare('SELECT n.mata_pelajaran, ROUND(AVG(n.hasil_semester), 1) AS rata
  FROM nilai n JOIN siswa s ON s.id_siswa = n.id_siswa WHERE s.id_kelas = ?
  GROUP BY n.mata_pelajaran ORDER BY n.mata_pelajaran');
$s->execute([$_SESSION['id_kelas']]);
echo json_encode(['mapel' => $s->fetchAll()]);
