CREATE DATABASE IF NOT EXISTS `PROJECT PERPUSTAKAAN` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `PROJECT PERPUSTAKAAN`;

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(100) NOT NULL,
  `Password` VARCHAR(255) NOT NULL,
  `Role` VARCHAR(30) NOT NULL DEFAULT 'petugas',
  `akses_khusus` TINYINT(1) NOT NULL DEFAULT 0,
  `batas_akses` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `buku` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Judul` VARCHAR(255) NOT NULL,
  `Penulis` VARCHAR(255) NOT NULL,
  `Kategori` VARCHAR(150) NULL,
  `penerbit` VARCHAR(255) NULL,
  `tahun` YEAR NULL,
  `Stok` INT NOT NULL DEFAULT 0,
  `Cover` VARCHAR(255) NOT NULL DEFAULT 'default.jpeg',
  PRIMARY KEY (`id`),
  KEY `idx_buku_judul` (`Judul`),
  KEY `idx_buku_kategori` (`Kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `anggota` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Nama` VARCHAR(150) NOT NULL,
  `Alamat` TEXT NOT NULL,
  `no_hp` VARCHAR(40) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_anggota_nama` (`Nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_anggota` INT UNSIGNED NOT NULL,
  `id_buku` INT UNSIGNED NOT NULL,
  `tanggal_pinjam` DATE NOT NULL,
  `tanggal_kembali` DATE NOT NULL,
  `status` ENUM('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `denda` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pinjam_anggota` (`id_anggota`),
  KEY `idx_pinjam_buku` (`id_buku`),
  KEY `idx_pinjam_status` (`status`),
  CONSTRAINT `fk_pinjam_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_pinjam_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `log_aktivitas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `aktivitas` TEXT NOT NULL,
  `waktu` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_waktu` (`waktu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`Username`,`Password`,`Role`,`akses_khusus`,`batas_akses`)
SELECT 'admin','admin','admin',1,NULL
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `Username`='admin');

INSERT INTO `buku` (`Judul`,`Penulis`,`Kategori`,`Stok`,`Cover`,`penerbit`,`tahun`)
SELECT 'Naruto Vol. 1','Masashi Kishimoto','Manga',1,'Cover Naruto.jpg','Shueisha',1999
WHERE NOT EXISTS (SELECT 1 FROM `buku` WHERE `Judul`='Naruto Vol. 1');

INSERT INTO `buku` (`Judul`,`Penulis`,`Kategori`,`Stok`,`Cover`,`penerbit`,`tahun`)
SELECT 'Laskar Pelangi','Andrea Hirata','Novel',1,'Cover Laskar Pelangi.jpeg','Bentang Pustaka',2005
WHERE NOT EXISTS (SELECT 1 FROM `buku` WHERE `Judul`='Laskar Pelangi');

INSERT INTO `buku` (`Judul`,`Penulis`,`Kategori`,`Stok`,`Cover`,`penerbit`,`tahun`)
SELECT '1001 Malam','Anonim','Cerita Rakyat',1,'1001 Malam Cover.jpeg','-','1900'
WHERE NOT EXISTS (SELECT 1 FROM `buku` WHERE `Judul`='1001 Malam');
