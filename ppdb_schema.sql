-- ============================================================
-- PPDB - Sistem Penerimaan Peserta Didik Baru
-- Database: MySQL 8 / MariaDB 10.4+
-- Cara pakai: import file ini di phpMyAdmin / MySQL CLI
--   mysql -u root -p < ppdb_schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS ppdb_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ppdb_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_logs, notifications, announcements,
  selection_results, payments, application_documents,
  path_document_requirements, applications, guardians, applicants,
  period_major_quotas, document_types, majors, admission_paths,
  admission_periods, academic_years, users, roles, settings;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. AKUN & HAK AKSES
-- ------------------------------------------------------------
CREATE TABLE roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE, -- applicant, admin, verifikator, finance, kepala_sekolah
  name VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20) NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  email_verified_at DATETIME NULL,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. MASTER PPDB
-- ------------------------------------------------------------
CREATE TABLE academic_years (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  year VARCHAR(20) NOT NULL UNIQUE, -- 2027/2028
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admission_periods (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academic_year_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL, -- Gelombang 1, Gelombang 2
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('draft','open','closed') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
) ENGINE=InnoDB;

CREATE TABLE admission_paths (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE, -- reguler, prestasi, afirmasi, zonasi, mutasi
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  quota INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE majors (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE, -- IPA, IPS, TKJ, RPL, UMUM
  name VARCHAR(100) NOT NULL,
  quota INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Kuota jurusan bisa beda tiap periode/gelombang
CREATE TABLE period_major_quotas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  period_id BIGINT UNSIGNED NOT NULL,
  major_id BIGINT UNSIGNED NOT NULL,
  quota INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_period_major (period_id, major_id),
  FOREIGN KEY (period_id) REFERENCES admission_periods(id) ON DELETE CASCADE,
  FOREIGN KEY (major_id) REFERENCES majors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE document_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE, -- kk, akta, ijazah, rapor, foto, kip
  name VARCHAR(100) NOT NULL,
  allowed_extensions VARCHAR(100) NOT NULL DEFAULT 'pdf,jpg,jpeg,png',
  max_size_mb INT NOT NULL DEFAULT 2,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Syarat dokumen per jalur (formulir jadi dinamis, tidak hardcode)
CREATE TABLE path_document_requirements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path_id BIGINT UNSIGNED NOT NULL,
  document_type_id BIGINT UNSIGNED NOT NULL,
  is_required TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_path_doc (path_id, document_type_id),
  FOREIGN KEY (path_id) REFERENCES admission_paths(id) ON DELETE CASCADE,
  FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. CALON SISWA & WALI
-- ------------------------------------------------------------
CREATE TABLE applicants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE, -- 1 akun = 1 calon siswa
  full_name VARCHAR(150) NOT NULL,
  nik VARCHAR(20) NULL UNIQUE,
  nisn VARCHAR(20) NULL UNIQUE,
  gender ENUM('L','P') NOT NULL,
  birth_place VARCHAR(100) NULL,
  birth_date DATE NULL,
  religion VARCHAR(30) NULL,
  address TEXT NULL,
  rt_rw VARCHAR(20) NULL,
  village VARCHAR(100) NULL,
  district VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  province VARCHAR(100) NULL,
  postal_code VARCHAR(10) NULL,
  phone VARCHAR(20) NULL,
  previous_school VARCHAR(150) NULL,
  previous_school_address TEXT NULL,
  photo_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE guardians (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  applicant_id BIGINT UNSIGNED NOT NULL,
  relationship ENUM('ayah','ibu','wali') NOT NULL,
  name VARCHAR(150) NOT NULL,
  nik VARCHAR(20) NULL,
  birth_date DATE NULL,
  education VARCHAR(50) NULL,
  occupation VARCHAR(100) NULL,
  income_range VARCHAR(50) NULL, -- <1jt, 1-3jt, 3-5jt, >5jt
  phone VARCHAR(20) NULL,
  email VARCHAR(150) NULL,
  address TEXT NULL, -- kosongkan jika sama dengan siswa
  is_emergency_contact TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. PENDAFTARAN (ENTITAS PUSAT)
-- ------------------------------------------------------------
CREATE TABLE applications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  applicant_id BIGINT UNSIGNED NOT NULL,
  period_id BIGINT UNSIGNED NOT NULL,
  path_id BIGINT UNSIGNED NOT NULL,
  major_id BIGINT UNSIGNED NULL,
  major_choice_2_id BIGINT UNSIGNED NULL, -- pilihan jurusan ke-2 (opsional)
  registration_no VARCHAR(30) NOT NULL UNIQUE, -- contoh: PPDB-2027-0001
  -- Pisahkan 3 status ini, jangan digabung:
  status ENUM('draft','submitted','document_review','verified','rejected_document','selection','accepted','waitlisted','rejected','registered','withdrawn')
    NOT NULL DEFAULT 'draft',
  notes TEXT NULL, -- catatan panitia
  submitted_at DATETIME NULL,
  verified_at DATETIME NULL,
  verified_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE,
  FOREIGN KEY (period_id) REFERENCES admission_periods(id),
  FOREIGN KEY (path_id) REFERENCES admission_paths(id),
  FOREIGN KEY (major_id) REFERENCES majors(id),
  FOREIGN KEY (major_choice_2_id) REFERENCES majors(id),
  FOREIGN KEY (verified_by) REFERENCES users(id),
  INDEX idx_status (status),
  INDEX idx_period (period_id)
) ENGINE=InnoDB;

CREATE TABLE application_documents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id BIGINT UNSIGNED NOT NULL,
  document_type_id BIGINT UNSIGNED NOT NULL,
  file_path VARCHAR(255) NOT NULL, -- simpan PATH saja, file di storage private
  original_filename VARCHAR(255) NULL,
  file_size_kb INT NULL,
  verification_status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  verified_by BIGINT UNSIGNED NULL,
  verified_at DATETIME NULL,
  notes TEXT NULL, -- alasan jika ditolak: "KK buram, upload ulang"
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (document_type_id) REFERENCES document_types(id),
  FOREIGN KEY (verified_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. PEMBAYARAN
-- ------------------------------------------------------------
CREATE TABLE payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id BIGINT UNSIGNED NOT NULL,
  payment_type ENUM('formulir','tes','daftar_ulang','lainnya') NOT NULL DEFAULT 'formulir',
  invoice_no VARCHAR(30) NOT NULL UNIQUE, -- INV-2027-0001
  amount DECIMAL(12,2) NOT NULL,
  method ENUM('transfer','qris','tunai','va','ewallet') NULL,
  reference_no VARCHAR(100) NULL, -- no. referensi / id transaksi gateway
  proof_path VARCHAR(255) NULL, -- bukti transfer (jika manual)
  status ENUM('pending','waiting_verification','paid','failed','expired','refunded')
    NOT NULL DEFAULT 'pending',
  paid_at DATETIME NULL,
  verified_by BIGINT UNSIGNED NULL,
  verified_at DATETIME NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (verified_by) REFERENCES users(id),
  INDEX idx_payment_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. SELEKSI & HASIL
-- ------------------------------------------------------------
CREATE TABLE selection_results (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id BIGINT UNSIGNED NOT NULL,
  stage VARCHAR(50) NOT NULL, -- tes_tulis, wawancara, seleksi_berkas, prestasi
  subject VARCHAR(100) NULL, -- Matematika, Bahasa Inggris, dsb.
  score DECIMAL(5,2) NULL,
  max_score DECIMAL(5,2) NULL DEFAULT 100,
  `rank` INT NULL,
  result_status ENUM('pending','passed','failed','waitlisted') NOT NULL DEFAULT 'pending',
  notes TEXT NULL,
  assessed_by BIGINT UNSIGNED NULL,
  assessed_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (assessed_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. PENDUKUNG: PENGUMUMAN, NOTIFIKASI, AUDIT, PENGATURAN
-- ------------------------------------------------------------
CREATE TABLE announcements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  period_id BIGINT UNSIGNED NULL,
  title VARCHAR(200) NOT NULL,
  content TEXT NOT NULL,
  publish_at DATETIME NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (period_id) REFERENCES admission_periods(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  channel ENUM('app','email','whatsapp','sms') NOT NULL DEFAULT 'app',
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(50) NOT NULL, -- create, update, delete, verify, login
  table_name VARCHAR(50) NOT NULL,
  record_id BIGINT UNSIGNED NULL,
  old_value JSON NULL,
  new_value JSON NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE, -- nama_sekolah, biaya_formulir, no_rekening
  `value` TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SEED DATA AWAL
-- ------------------------------------------------------------
INSERT INTO roles (code, name) VALUES
  ('applicant','Calon Siswa'),
  ('admin','Admin / Panitia'),
  ('verifikator','Verifikator Berkas'),
  ('finance','Keuangan'),
  ('kepala_sekolah','Kepala Sekolah');

INSERT INTO admission_paths (code, name, quota) VALUES
  ('reguler','Reguler', 0),
  ('prestasi','Prestasi', 0),
  ('afirmasi','Afirmasi', 0),
  ('zonasi','Zonasi', 0),
  ('mutasi','Mutasi Orang Tua', 0);

INSERT INTO document_types (code, name) VALUES
  ('kk','Kartu Keluarga'),
  ('akta','Akta Kelahiran'),
  ('ijazah','Ijazah / SKL'),
  ('rapor','Rapor Terakhir'),
  ('foto','Pas Foto'),
  ('kip','Kartu Indonesia Pintar (jika ada)'),
  ('sertifikat','Sertifikat Prestasi (jalur prestasi)');

INSERT INTO settings (`key`, `value`) VALUES
  ('nama_sekolah',''),
  ('biaya_formulir','0'),
  ('biaya_daftar_ulang','0');
