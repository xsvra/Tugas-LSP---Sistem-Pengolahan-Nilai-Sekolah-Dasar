CREATE DATABASE IF NOT EXISTS sistem_nilai;
USE sistem_nilai;

-- Hapus tabel lama jika ada (urutan dari yang memiliki FK ke yang di-refer)
DROP TABLE IF EXISTS banding_nilai;
DROP TABLE IF EXISTS nilai;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS siswa;
DROP TABLE IF EXISTS guru;

-- Tabel Siswa
CREATE TABLE siswa (
    id_siswa INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nisn VARCHAR(20) NOT NULL DEFAULT '0000000000',
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL DEFAULT 'L',
    tempat_lahir VARCHAR(100) DEFAULT NULL,
    tanggal_lahir DATE DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    kelas ENUM('1', '2', '3', '4', '5', '6') NOT NULL DEFAULT '1',
    tahun_masuk VARCHAR(4) DEFAULT NULL,
    nama_wali VARCHAR(100) DEFAULT NULL,
    no_hp_wali VARCHAR(20) DEFAULT NULL,
    status_siswa VARCHAR(50) NOT NULL DEFAULT 'Aktif',
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Guru
CREATE TABLE guru (
    id_guru INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(20) NOT NULL DEFAULT '',
    nama_guru VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL DEFAULT 'L',
    tempat_lahir VARCHAR(100) DEFAULT NULL,
    tanggal_lahir DATE DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    mata_pelajaran VARCHAR(100) NOT NULL,
    pendidikan_terakhir VARCHAR(50) DEFAULT NULL,
    status_kepegawaian VARCHAR(50) DEFAULT NULL,
    tanggal_masuk DATE DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) AUTO_INCREMENT = 1;

-- Tabel Users
CREATE TABLE users (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guru', 'siswa') NOT NULL,
    ref_id VARCHAR(20) DEFAULT NULL, -- NIS (INT) untuk siswa, ID Guru (INT) untuk guru, NULL untuk admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Nilai
CREATE TABLE nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL,
    id_guru INT NOT NULL,
    nilai_tugas DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    nilai_uts DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    nilai_uas DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    nilai_akhir DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    status_kelulusan VARCHAR(20) NOT NULL DEFAULT 'Tidak Lulus',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nis) REFERENCES siswa(nis) ON DELETE CASCADE,
    FOREIGN KEY (id_guru) REFERENCES guru(id_guru) ON DELETE CASCADE
);

-- Tabel Banding Nilai (Grade Appeal)
CREATE TABLE banding_nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nilai INT NOT NULL,
    alasan TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending', -- 'Pending', 'Disetujui', 'Ditolak'
    keterangan_guru TEXT DEFAULT NULL,
    nilai_tugas_asal DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    nilai_uts_asal DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    nilai_uas_asal DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    nilai_akhir_asal DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_nilai) REFERENCES nilai(id) ON DELETE CASCADE
);

-- Seed Akun Admin Default (Username: admin, Password: admin123)
INSERT INTO users (username, password, role, ref_id) 
VALUES ('admin', '$2y$10$oDa01xQlly1QXshRAD9P/O1penO.an3.MRFTmm2budTpwKnIXoT8q', 'admin', NULL)
ON DUPLICATE KEY UPDATE username=username;
