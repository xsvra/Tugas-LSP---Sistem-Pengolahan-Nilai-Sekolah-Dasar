USE sistem_nilai;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guru', 'siswa') NOT NULL,
    ref_id VARCHAR(20) DEFAULT NULL, -- NIS untuk siswa, ID Guru untuk guru, NULL untuk admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Banding Nilai (Grade Appeal)
CREATE TABLE IF NOT EXISTS banding_nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nilai INT NOT NULL,
    alasan TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending', -- 'Pending', 'Disetujui', 'Ditolak'
    keterangan_guru TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_nilai) REFERENCES nilai(id) ON DELETE CASCADE
);

-- Insert Default Admin Account (Username: admin, Password: admin123)
INSERT INTO users (username, password, role, ref_id) 
VALUES ('admin', '$2y$10$oDa01xQlly1QXshRAD9P/O1penO.an3.MRFTmm2budTpwKnIXoT8q', 'admin', NULL)
ON DUPLICATE KEY UPDATE username=username;
