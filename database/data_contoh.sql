-- Data contoh untuk uji coba. Login: username "sari" (atau NIP 198501012010), sandi "guru123"
USE db_pemantauan_siswa;

INSERT INTO pengguna (nama, username, password, peran) VALUES ('Bu Sari', 'sari', '$2y$10$kfdmGfODJQ9ZaimYbZAsY.O7fswrKn7iTrtsiv9CHqDWBV6.GcBYi', 'Guru');
SET @p = LAST_INSERT_ID();
INSERT INTO guru (id_pengguna, nip, email) VALUES (@p, '198501012010', 'sari@sipintar.sch.id');
SET @g = LAST_INSERT_ID();
INSERT INTO kelas (nama_kelas, tingkat, tahun_ajaran, id_guru) VALUES ('4A', 4, '2026/2027', @g);
SET @k = LAST_INSERT_ID();

INSERT INTO siswa (nis, nama_siswa, jenis_kelamin, tanggal_lahir, id_kelas) VALUES
('4A001','Aisyah Putri','P','2016-03-14',@k),('4A002','Budi Santoso','L','2016-07-02',@k),
('4A003','Citra Lestari','P','2016-01-21',@k),('4A004','Dimas Pratama','L','2016-09-09',@k),
('4A005','Eka Wulandari','P','2016-05-30',@k),('4A006','Fajar Ramadhan','L','2016-11-17',@k);
SET @s = LAST_INSERT_ID();

INSERT INTO nilai (id_siswa, mata_pelajaran, semester, nilai_tugas, nilai_uts, nilai_uas) VALUES
(@s,'Matematika','Ganjil',60,62,64),(@s,'Bahasa Indonesia','Ganjil',66,65,67),(@s,'IPAS','Ganjil',62,60,63),
(@s+1,'Matematika','Ganjil',85,82,88),(@s+1,'Bahasa Indonesia','Ganjil',80,84,83),(@s+1,'IPAS','Ganjil',86,85,84),
(@s+2,'Matematika','Ganjil',90,88,92),(@s+2,'Bahasa Indonesia','Ganjil',88,90,91),(@s+2,'IPAS','Ganjil',89,87,90),
(@s+3,'Matematika','Ganjil',78,75,80),(@s+3,'Bahasa Indonesia','Ganjil',76,78,77),(@s+3,'IPAS','Ganjil',79,80,78),
(@s+4,'Matematika','Ganjil',92,90,94),(@s+4,'Bahasa Indonesia','Ganjil',91,93,92),(@s+4,'IPAS','Ganjil',90,92,91),
(@s+5,'Matematika','Ganjil',74,76,75),(@s+5,'Bahasa Indonesia','Ganjil',77,75,78),(@s+5,'IPAS','Ganjil',73,74,76);

INSERT INTO kehadiran (id_siswa, tanggal, status_kehadiran) VALUES
(@s,CURDATE(),'Hadir'),(@s+1,CURDATE(),'Hadir'),(@s+2,CURDATE(),'Hadir'),
(@s+3,CURDATE(),'Alpa'),(@s+4,CURDATE(),'Hadir'),(@s+5,CURDATE(),'Hadir'),
(@s+3,CURDATE() - INTERVAL 1 DAY,'Alpa'),(@s+3,CURDATE() - INTERVAL 2 DAY,'Alpa');

INSERT INTO tugas (id_siswa, mata_pelajaran, judul_tugas, tanggal, status_pengumpulan, nilai) VALUES
(@s,'Matematika','Tugas Tematik 1',CURDATE() - INTERVAL 3 DAY,'Sudah',80),
(@s+1,'Matematika','Tugas Tematik 1',CURDATE() - INTERVAL 3 DAY,'Sudah',85),
(@s,'Matematika','Penilaian Formatif Matematika',CURDATE() + INTERVAL 1 DAY,'Belum',0),
(@s+1,'Matematika','Penilaian Formatif Matematika',CURDATE() + INTERVAL 1 DAY,'Belum',0);

INSERT INTO perilaku (id_siswa, tanggal, jenis_perilaku, keterangan) VALUES
(@s+2,CURDATE(),'Positif','Membantu teman yang kesulitan belajar'),
(@s+5,CURDATE(),'Negatif','Beberapa kali tidak mengerjakan tugas');

INSERT INTO notifikasi (id_siswa, id_pengguna, jenis_peringatan, pesan) VALUES
(@s,@p,'Penurunan Nilai','Nilai rata-rata Aisyah Putri di bawah 70.'),
(@s+3,@p,'Presensi Buruk','Dimas Pratama alpa 3 kali berturut-turut.'),
(@s+5,@p,'Tugas Belum Lengkap','Fajar Ramadhan belum mengumpulkan beberapa tugas.');
