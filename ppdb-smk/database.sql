CREATE DATABASE IF NOT EXISTS db_psb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_psb;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS siswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  nisn VARCHAR(10) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  jenis_kelamin ENUM('L','P') NOT NULL,
  tempat_lahir VARCHAR(60) NOT NULL,
  tanggal_lahir DATE NOT NULL,
  alamat TEXT NOT NULL,
  asal_sekolah VARCHAR(100) NOT NULL,
  no_hp VARCHAR(20) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jurusan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(10) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  kuota INT NOT NULL DEFAULT 0,
  aktif TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pendaftaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  no_pendaftaran VARCHAR(30) NOT NULL UNIQUE,
  siswa_id INT NOT NULL UNIQUE,
  jurusan_id INT NOT NULL,
  status_verifikasi ENUM('Menunggu','Diverifikasi','Ditolak') NOT NULL DEFAULT 'Menunggu',
  hasil_seleksi ENUM('Belum','Diterima','Ditolak') NOT NULL DEFAULT 'Belum',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
  FOREIGN KEY (jurusan_id) REFERENCES jurusan(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS berkas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL,
  jenis ENUM('kk','akta','rapor','foto') NOT NULL,
  nama_file VARCHAR(100) NOT NULL,
  nama_asli VARCHAR(150) NOT NULL,
  ukuran INT NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_berkas (pendaftaran_id, jenis),
  FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS riwayat_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pendaftaran_id INT NOT NULL,
  status VARCHAR(50) NOT NULL,
  keterangan VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO jurusan (kode,nama,kuota,aktif) VALUES
('RPL','Rekayasa Perangkat Lunak',36,1),
('TKJ','Teknik Komputer dan Jaringan',36,1),
('AKL','Akuntansi dan Keuangan Lembaga',36,1),
('TBG','Tata Boga',32,1),
('TKR','Teknik Kendaraan Ringan',36,1);
