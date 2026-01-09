/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - laravel_laundry
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`laravel_laundry` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `laravel_laundry`;

/*Table structure for table `antarjemput_status` */

DROP TABLE IF EXISTS `antarjemput_status`;

CREATE TABLE `antarjemput_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `antarjemput_status` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `jenis_layanan` */

DROP TABLE IF EXISTS `jenis_layanan`;

CREATE TABLE `jenis_layanan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mitra_id` bigint unsigned NOT NULL,
  `nama_layanan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `harga` decimal(12,2) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jenis_layanan_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `jenis_layanan_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jenis_layanan` */

insert  into `jenis_layanan`(`id`,`mitra_id`,`nama_layanan`,`deskripsi`,`satuan`,`harga`,`created_at`,`updated_at`) values 
(1,1,'Cuci Kering','Pakaian dicuci dan dikeringkan tanpa setrika','kg',7000.00,NULL,NULL),
(2,1,'Cuci Setrika','Dicuci lalu disetrika rapi','kg',12000.00,NULL,NULL),
(3,1,'Setrika Saja','Hanya layanan setrika','kg',8000.00,NULL,NULL);

/*Table structure for table `kecamatan` */

DROP TABLE IF EXISTS `kecamatan`;

CREATE TABLE `kecamatan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kecamatan` */

insert  into `kecamatan`(`id`,`nama`,`deleted_at`,`created_at`,`updated_at`) values 
(1,'Benowo',NULL,NULL,NULL),
(2,'Pakal',NULL,NULL,NULL),
(3,'Asemrowo',NULL,NULL,NULL),
(4,'Sukomanunggal',NULL,NULL,NULL),
(5,'Tandes',NULL,NULL,NULL),
(6,'Sambikerep',NULL,NULL,NULL),
(7,'Lakarsantri',NULL,NULL,NULL),
(8,'Rungkut',NULL,NULL,NULL),
(9,'Gunung Anyar',NULL,NULL,NULL),
(10,'Sukolilo',NULL,NULL,NULL),
(11,'Mulyorejo',NULL,NULL,NULL),
(12,'Tambaksari',NULL,NULL,NULL),
(13,'Gubeng',NULL,NULL,NULL),
(14,'Wonokromo',NULL,NULL,NULL),
(15,'Gayungan',NULL,NULL,NULL),
(16,'Jambangan',NULL,NULL,NULL),
(17,'Wonocolo',NULL,NULL,NULL),
(18,'Karang Pilang',NULL,NULL,NULL),
(19,'Wiyung',NULL,NULL,NULL),
(20,'Kenjeran',NULL,NULL,NULL),
(21,'Bulak',NULL,NULL,NULL),
(22,'Simokerto',NULL,NULL,NULL),
(23,'Pabean Cantikan',NULL,NULL,NULL),
(24,'Krembangan',NULL,NULL,NULL),
(25,'Semampir',NULL,NULL,NULL),
(26,'Genteng',NULL,NULL,NULL),
(27,'Tegalsari',NULL,NULL,NULL),
(28,'Bubutan',NULL,NULL,NULL),
(29,'Sawahan',NULL,NULL,NULL);

/*Table structure for table `kurir` */

DROP TABLE IF EXISTS `kurir`;

CREATE TABLE `kurir` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `mitra_id` bigint unsigned NOT NULL,
  `plat_nomor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kendaraan` enum('motor','mobil') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'motor',
  `status` enum('aktif','offline','sibuk') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offline',
  `lokasi_lat` decimal(10,7) DEFAULT NULL,
  `lokasi_lng` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kurir_user_id_foreign` (`user_id`),
  KEY `kurir_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `kurir_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kurir_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kurir` */

/*Table structure for table `layanan` */

DROP TABLE IF EXISTS `layanan`;

CREATE TABLE `layanan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mitra_id` bigint unsigned NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(12,2) unsigned NOT NULL,
  `satuan` enum('kiloan','satuan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'satuan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `layanan_mitra_id_nama_index` (`mitra_id`,`nama`),
  CONSTRAINT `layanan_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `layanan` */

/*Table structure for table `layanan_prioritas` */

DROP TABLE IF EXISTS `layanan_prioritas`;

CREATE TABLE `layanan_prioritas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga` double NOT NULL,
  `prioritas` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `layanan_prioritas` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2023_12_12_072543_create_permission_tables',1),
(6,'2023_12_31_064553_create_settings_table',1),
(7,'2025_09_08_041811_create_services_table',1),
(8,'2025_09_08_084849_create_kecamatan_table',1),
(9,'2025_09_08_084850_create_pelanggan_table',1),
(10,'2025_09_09_065440_create_antarjemput_status_table',1),
(11,'2025_09_09_065842_create_toko_status_table',1),
(12,'2025_09_25_132053_create_pegawai_laundry_table',1),
(13,'2025_09_25_141235_create_layanan_prioritas_table',1),
(14,'2025_10_16_082748_create_mitra_table',1),
(15,'2025_11_13_085112_create_layanan_table',1),
(16,'2025_11_18_082342_create_kurir_table',1),
(17,'2025_11_18_083528_create_jenis_layanan_table',1),
(18,'2025_11_18_091102_create_transaksilama_table',1),
(19,'2025_11_18_091132_create_transaksi_item_table',1),
(20,'2025_11_18_091230_create_tracking_transaksi_table',1),
(21,'2025_11_18_091314_create_rating_transaksi_table',1),
(22,'2025_11_19_131527_create_order_table',1),
(23,'2025_11_19_151759_create_transaksi1233_table',1),
(24,'2025_12_02_105100_create_transaksi_table',1),
(25,'2026_01_05_132936_add_snap_token_to_order_table',1);

/*Table structure for table `mitra` */

DROP TABLE IF EXISTS `mitra`;

CREATE TABLE `mitra` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_laundry` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status_validasi` enum('menunggu','diterima','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `alamat_laundry` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_toko` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_toko` enum('buka','tutup') COLLATE utf8mb4_unicode_ci DEFAULT 'buka',
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mitra_user_id_foreign` (`user_id`),
  KEY `mitra_kecamatan_id_foreign` (`kecamatan_id`),
  CONSTRAINT `mitra_kecamatan_id_foreign` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mitra_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `mitra` */

insert  into `mitra`(`id`,`nama_laundry`,`user_id`,`status_validasi`,`alamat_laundry`,`foto_ktp`,`foto_toko`,`status_toko`,`jam_buka`,`jam_tutup`,`deskripsi`,`kecamatan_id`,`deleted_at`,`created_at`,`updated_at`) values 
(1,'Laundry Mitra',3,'diterima','Jl. Contoh No.10','default.png','mitra/QBB8SpZRCVzY9DgI2Z8tzPeByC3Wm4ROHhilRigM.jpg','buka','08:00:00','20:00:00',NULL,1,NULL,'2026-01-09 10:05:03','2026-01-09 17:25:36');

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_permissions` */

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_roles` */

insert  into `model_has_roles`(`role_id`,`model_type`,`model_id`) values 
(1,'App\\Models\\User','1'),
(3,'App\\Models\\User','2'),
(4,'App\\Models\\User','3'),
(2,'App\\Models\\User','4'),
(2,'App\\Models\\User','5');

/*Table structure for table `order` */

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pelanggan_id` bigint unsigned NOT NULL,
  `mitra_id` bigint unsigned NOT NULL,
  `jenis_layanan_id` bigint unsigned DEFAULT NULL,
  `kode_order` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_estimasi` decimal(5,2) DEFAULT NULL,
  `berat_aktual` decimal(5,2) DEFAULT NULL,
  `harga_final` int DEFAULT NULL,
  `foto_struk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('menunggu_konfirmasi_mitra','ditunggu_mitra','diterima','ditolak','diproses','dicuci','dikeringkan','disetrika','siap_diambil','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_konfirmasi_mitra',
  `alasan_penolakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimasi_selesai` timestamp NULL DEFAULT NULL,
  `estimasi_jam` timestamp NULL DEFAULT NULL,
  `waktu_pelanggan_antar` timestamp NULL DEFAULT NULL,
  `waktu_diambil` timestamp NULL DEFAULT NULL,
  `biaya` int DEFAULT NULL,
  `status_pembayaran` enum('belum dibayar','settlement','pending','expire','cancel','deny','failure','refund') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum dibayar',
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_kode_order_unique` (`kode_order`),
  KEY `order_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `order_mitra_id_foreign` (`mitra_id`),
  KEY `order_jenis_layanan_id_foreign` (`jenis_layanan_id`),
  CONSTRAINT `order_jenis_layanan_id_foreign` FOREIGN KEY (`jenis_layanan_id`) REFERENCES `jenis_layanan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `order` */

insert  into `order`(`id`,`pelanggan_id`,`mitra_id`,`jenis_layanan_id`,`kode_order`,`berat_estimasi`,`berat_aktual`,`harga_final`,`foto_struk`,`catatan`,`status`,`alasan_penolakan`,`estimasi_selesai`,`estimasi_jam`,`waktu_pelanggan_antar`,`waktu_diambil`,`biaya`,`status_pembayaran`,`snap_token`,`waktu`,`created_at`,`updated_at`) values 
(1,1,1,1,'ORD-1001',3.00,NULL,15000,NULL,'Tolong cepat ya','menunggu_konfirmasi_mitra',NULL,NULL,NULL,NULL,NULL,NULL,'belum dibayar',NULL,'2026-01-09 10:05:04','2026-01-09 10:05:04','2026-01-09 10:05:04'),
(2,2,1,2,'ORD-1002',5.00,NULL,15000,NULL,'Pakaian kantor','diterima',NULL,NULL,NULL,NULL,NULL,NULL,'belum dibayar',NULL,'2026-01-09 10:05:04','2026-01-09 10:05:04','2026-01-09 10:05:04'),
(3,1,1,3,'ORD-1003',2.00,NULL,15000,NULL,NULL,'diproses',NULL,NULL,NULL,NULL,NULL,NULL,'belum dibayar',NULL,'2026-01-09 10:05:04','2026-01-09 10:05:04','2026-01-09 10:05:04');

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `pegawai_laundry` */

DROP TABLE IF EXISTS `pegawai_laundry`;

CREATE TABLE `pegawai_laundry` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mulai_kerja` date DEFAULT NULL,
  `selesai_kerja` date DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pegawai_laundry_user_id_foreign` (`user_id`),
  CONSTRAINT `pegawai_laundry_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pegawai_laundry` */

/*Table structure for table `pelanggan` */

DROP TABLE IF EXISTS `pelanggan`;

CREATE TABLE `pelanggan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `kecamatan_id` bigint unsigned DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `kode_pos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pelanggan_user_id_foreign` (`user_id`),
  KEY `pelanggan_kecamatan_id_foreign` (`kecamatan_id`),
  CONSTRAINT `pelanggan_kecamatan_id_foreign` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pelanggan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pelanggan` */

insert  into `pelanggan`(`id`,`user_id`,`kecamatan_id`,`alamat`,`kode_pos`,`created_at`,`updated_at`) values 
(1,4,1,'Jl. Pelanggan No.5','12345','2026-01-09 10:05:03','2026-01-09 10:05:03'),
(2,5,1,'Jl. Pelanggan No.10','1234567','2026-01-09 10:05:04','2026-01-09 10:05:04');

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values 
(1,'dashboard','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(2,'master','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(3,'master-user','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(4,'master-role','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(5,'mitra','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(6,'website','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(7,'setting','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(8,'antar-jemput','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(9,'datapelanggan','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(10,'pendapatan','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(11,'tambahpelanggan','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(12,'laundrydetail','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(13,'pendaftaran','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(14,'kecamatan','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(15,'transaksi','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(16,'profil','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(17,'layanan','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(18,'order-masuk','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(19,'order-proses','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(20,'order-siap-ambil','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(21,'order-selesai','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(22,'beranda','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(23,'pelanggan','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(24,'antar','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(25,'jemput','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(26,'riwayat','api','2026-01-09 10:05:01','2026-01-09 10:05:01'),
(27,'dashboard-mitra','api','2026-01-09 10:05:01','2026-01-09 10:05:01');

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `rating_transaksi` */

DROP TABLE IF EXISTS `rating_transaksi`;

CREATE TABLE `rating_transaksi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `pelanggan_id` bigint unsigned NOT NULL,
  `rating` tinyint NOT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rating_transaksi_transaksi_id_foreign` (`transaksi_id`),
  KEY `rating_transaksi_pelanggan_id_foreign` (`pelanggan_id`),
  CONSTRAINT `rating_transaksi_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `users` (`id`),
  CONSTRAINT `rating_transaksi_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksilama` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rating_transaksi` */

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

insert  into `role_has_permissions`(`permission_id`,`role_id`) values 
(1,1),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1),
(10,1),
(11,1),
(12,1),
(13,1),
(14,1),
(15,1),
(16,1),
(17,1),
(18,1),
(19,1),
(20,1),
(21,1),
(15,4),
(16,4),
(17,4),
(18,4),
(19,4),
(20,4),
(21,4),
(27,4),
(22,5),
(23,5),
(24,5),
(25,5),
(26,5);

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`full_name`,`guard_name`,`created_at`,`updated_at`) values 
(1,'admin','Administrator','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(2,'pelanggan','pelanggan','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(3,'pegawai','Pegawai','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(4,'mitra','Mitra','api','2026-01-09 10:05:00','2026-01-09 10:05:00'),
(5,'pelangan','Pelangan','api','2026-01-09 10:05:01','2026-01-09 10:05:01');

/*Table structure for table `services` */

DROP TABLE IF EXISTS `services`;

CREATE TABLE `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga_per_kg` decimal(10,2) NOT NULL,
  `durasi_jam` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `services` */

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bg_auth` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dinas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pemerintah` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `settings` */

insert  into `settings`(`id`,`uuid`,`app`,`description`,`logo`,`banner`,`bg_auth`,`dinas`,`pemerintah`,`alamat`,`telepon`,`email`,`created_at`,`updated_at`) values 
(1,'3f19c2cf-9a8b-42a3-863d-198e8e2f93e4','e-SAKIP DLH','Aplikasi e-SAKIP Dinas Lingkungan Hidup','/media/logo.png','/media/misc/banner.jpg','/media/misc/bg-auth.jpg','Dinas Lingkungan Hidup','Pemerintah Provinsi Jawa Timur','','','','2026-01-09 10:05:04','2026-01-09 10:05:04');

/*Table structure for table `toko_status` */

DROP TABLE IF EXISTS `toko_status`;

CREATE TABLE `toko_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('buka','tutup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buka',
  `waktu` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `toko_status` */

/*Table structure for table `tracking_transaksi` */

DROP TABLE IF EXISTS `tracking_transaksi`;

CREATE TABLE `tracking_transaksi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `kurir_id` bigint unsigned DEFAULT NULL,
  `status` enum('pickup','menuju_mitra','dicuci','selesai_cuci','menuju_antar','selesai') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracking_transaksi_transaksi_id_foreign` (`transaksi_id`),
  KEY `tracking_transaksi_kurir_id_foreign` (`kurir_id`),
  CONSTRAINT `tracking_transaksi_kurir_id_foreign` FOREIGN KEY (`kurir_id`) REFERENCES `kurir` (`id`),
  CONSTRAINT `tracking_transaksi_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksilama` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tracking_transaksi` */

/*Table structure for table `transaksi` */

DROP TABLE IF EXISTS `transaksi`;

CREATE TABLE `transaksi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `mitra_id` bigint unsigned DEFAULT NULL,
  `berat` int DEFAULT NULL,
  `harga_final` bigint DEFAULT NULL,
  `midtrans_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `pdf_url` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_order_id_foreign` (`order_id`),
  KEY `transaksi_user_id_foreign` (`user_id`),
  KEY `transaksi_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `transaksi_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaksi_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transaksi` */

/*Table structure for table `transaksi1233` */

DROP TABLE IF EXISTS `transaksi1233`;

CREATE TABLE `transaksi1233` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `mitra_id` bigint unsigned NOT NULL,
  `berat` int DEFAULT NULL,
  `harga_final` int DEFAULT NULL,
  `status` enum('menunggu_konfirmasi','diproses','dijemput','dicuci','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_konfirmasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi1233_user_id_foreign` (`user_id`),
  KEY `transaksi1233_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `transaksi1233_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi1233_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transaksi1233` */

/*Table structure for table `transaksi_item` */

DROP TABLE IF EXISTS `transaksi_item`;

CREATE TABLE `transaksi_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `nama_item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_item_transaksi_id_foreign` (`transaksi_id`),
  CONSTRAINT `transaksi_item_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksilama` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transaksi_item` */

/*Table structure for table `transaksilama` */

DROP TABLE IF EXISTS `transaksilama`;

CREATE TABLE `transaksilama` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pelanggan_id` bigint unsigned NOT NULL,
  `mitra_id` bigint unsigned NOT NULL,
  `kurir_pickup_id` bigint unsigned DEFAULT NULL,
  `kurir_antar_id` bigint unsigned DEFAULT NULL,
  `layanan_id` bigint unsigned NOT NULL,
  `alamat_pickup` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_dropoff` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat` decimal(8,2) DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `biaya_pickup` decimal(12,2) NOT NULL DEFAULT '0.00',
  `biaya_antar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `metode_pembayaran` enum('cash','transfer','ewallet') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','menunggu_kurir','pickup','dicuci','selesai_cuci','antar','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `tanggal_order` datetime NOT NULL,
  `tanggal_pickup` datetime DEFAULT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `tanggal_antar` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksilama_kode_transaksi_unique` (`kode_transaksi`),
  KEY `transaksilama_pelanggan_id_foreign` (`pelanggan_id`),
  KEY `transaksilama_mitra_id_foreign` (`mitra_id`),
  KEY `transaksilama_kurir_pickup_id_foreign` (`kurir_pickup_id`),
  KEY `transaksilama_kurir_antar_id_foreign` (`kurir_antar_id`),
  KEY `transaksilama_layanan_id_foreign` (`layanan_id`),
  CONSTRAINT `transaksilama_kurir_antar_id_foreign` FOREIGN KEY (`kurir_antar_id`) REFERENCES `kurir` (`id`),
  CONSTRAINT `transaksilama_kurir_pickup_id_foreign` FOREIGN KEY (`kurir_pickup_id`) REFERENCES `kurir` (`id`),
  CONSTRAINT `transaksilama_layanan_id_foreign` FOREIGN KEY (`layanan_id`) REFERENCES `jenis_layanan` (`id`),
  CONSTRAINT `transaksilama_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`),
  CONSTRAINT `transaksilama_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transaksilama` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`uuid`,`name`,`email`,`phone`,`photo`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(1,'bb802b46-71f2-43f5-ba88-9454128748b5','Admin','admin@gmail.com','08123456789',NULL,'$2y$12$ClMQFwTU4j3OWi7QLD2imOhTVIraOdYlE.pVL4a5k/ScVpq8aowwq',NULL,'2026-01-09 10:05:02','2026-01-09 10:05:02'),
(2,'b3b37033-fb7e-4cd1-a193-e4194cb6e072','Pegawai','pegawai@gmail.com','08123456788',NULL,'$2y$12$Whncd177.orHK.IMwmX2W.A.UAwfl3FRnCCVY2z3gHBh.h6hPz8Ou',NULL,'2026-01-09 10:05:02','2026-01-09 10:05:02'),
(3,'b8367df4-1021-4e7d-89e0-9cddf0e8c9b7','Mitra Laundry','mitra@gmail.com','08123459998',NULL,'$2y$12$8dLv8fYO9RChMimhi6gBuOoyNvBgbKztRzrbZ/blSLVoogyV5M18e',NULL,'2026-01-09 10:05:03','2026-01-09 10:05:03'),
(4,'22c81729-f6c6-4be5-9ce3-10739dea8f8d','Pelanggan 1','pelanggan@gmail.com','08767676767','photos/profile/jwFdwKk56RL4JspRjZ7s1xrP8Ix0ulH1OVCb62SL.jpg','$2y$12$bU9EYZeSvPM6IB3M8QuLE.dm20WlI46GCjEEMc6XfTDW.lQJZhMiC',NULL,'2026-01-09 10:05:03','2026-01-09 10:57:38'),
(5,'32f490ed-f017-4ed6-a294-2a9ef9ca971e','Pelanggan 2','pelanggan2@gmail.com','08123859999',NULL,'$2y$12$qVB/CsomVUbaoi/srtd7s.UHybzpoCImPxHK6Ig.tOsvFTgUuAajy',NULL,'2026-01-09 10:05:04','2026-01-09 10:05:04');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
