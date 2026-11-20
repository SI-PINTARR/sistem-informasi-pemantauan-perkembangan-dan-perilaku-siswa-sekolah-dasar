<?php
require 'koneksi.php';
wajib_login();
$id = (int)(input()['id_siswa'] ?? 0);

$c = $db->prepare('SELECT s.nis, s.nama_siswa, k.nama_kelas, k.tahun_ajaran
  FROM siswa s JOIN kelas k ON k.id_kelas = s.id_kelas WHERE s.id_siswa = ? AND s.id_kelas = ?');
$c->execute([$id, $_SESSION['id_kelas']]);
$s = $c->fetch();
if (!$s) keluar(403, 'Siswa bukan di kelas Anda.');

$n = $db->prepare('SELECT mata_pelajaran, ROUND(hasil_semester, 1) AS hasil FROM nilai WHERE id_siswa = ? ORDER BY mata_pelajaran');
$n->execute([$id]); $nilai = $n->fetchAll();
$h = $db->prepare('SELECT status_kehadiran, COUNT(*) FROM kehadiran WHERE id_siswa = ? GROUP BY status_kehadiran');
$h->execute([$id]); $hadir = $h->fetchAll(PDO::FETCH_KEY_PAIR);
$p = $db->prepare('SELECT jenis_perilaku, COUNT(*) FROM perilaku WHERE id_siswa = ? GROUP BY jenis_perilaku');
$p->execute([$id]); $per = $p->fetchAll(PDO::FETCH_KEY_PAIR);

$periode = 'Tahun ajaran ' . $s['tahun_ajaran'];
$ring = 'Nilai: ' . implode(', ', array_map(fn($x) => $x['mata_pelajaran'] . ' ' . $x['hasil'], $nilai));
$db->prepare('INSERT INTO laporan (id_siswa, id_pengguna, periode, ringkasan) VALUES (?, ?, ?, ?)')
   ->execute([$id, $_SESSION['id_pengguna'], $periode, $ring]);

echo json_encode(['siswa' => $s, 'periode' => $periode, 'nilai' => $nilai, 'kehadiran' => $hadir, 'perilaku' => $per]);
