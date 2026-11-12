CREATE DATABASE IF NOT EXISTS bumdes_sejahtera;
USE bumdes_sejahtera;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE unit_usaha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_unit VARCHAR(100) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    alamat TEXT,
    status ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pendapatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    unit_usaha_id INT NULL,
    keterangan TEXT,
    jumlah DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pendapatan_tanggal (tanggal),
    INDEX idx_pendapatan_unit (unit_usaha_id),
    FOREIGN KEY (unit_usaha_id)
        REFERENCES unit_usaha(id)
        ON DELETE SET NULL
);

CREATE TABLE pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    unit_usaha_id INT NULL,
    keterangan TEXT,
    jumlah DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pengeluaran_tanggal (tanggal),
    INDEX idx_pengeluaran_unit (unit_usaha_id),
    FOREIGN KEY (unit_usaha_id)
        REFERENCES unit_usaha(id)
        ON DELETE SET NULL
);

CREATE TABLE login_attempts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login_attempts (username, ip_address, attempted_at),
    INDEX idx_login_ip (ip_address, attempted_at)
);

CREATE TABLE audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    aksi VARCHAR(100) NOT NULL,
    detail TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_created (created_at),
    CONSTRAINT fk_audit_admin FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL
);

INSERT INTO admin (nama, username, password)
VALUES (
    'Administrator',
    'admin',
    '$2y$12$S1nn7H063JoViDOnRzwkK.PGD05Dlj36WQceZWW1ciAiH/1/Q/kJK'
);

INSERT INTO unit_usaha (nama_unit, kategori, deskripsi, status) VALUES
('Simpan Desa', 'Keuangan', 'Layanan simpan pinjam masyarakat desa.', 'Aktif'),
('Pertanian Mandiri', 'Pertanian', 'Pengembangan pertanian desa.', 'Aktif'),
('Perdagangan Desa', 'Perdagangan', 'Pengelolaan perdagangan dan produk desa.', 'Aktif'),
('Wisata Desa', 'Pariwisata', 'Pengembangan wisata desa.', 'Aktif');

INSERT INTO pendapatan
(kode_transaksi,tanggal,kategori,keterangan,jumlah)
VALUES
('PD001','2026-11-01','Perdagangan','Hasil perdagangan desa',18500000),
('PD002','2026-11-03','Pertanian','Penjualan hasil pertanian',22000000);

INSERT INTO pengeluaran
(kode_transaksi,tanggal,kategori,keterangan,jumlah)
VALUES
('PG001','2026-11-05','Operasional','Biaya operasional',4500000),
('PG002','2026-11-08','Pemeliharaan','Pemeliharaan fasilitas',2500000);
