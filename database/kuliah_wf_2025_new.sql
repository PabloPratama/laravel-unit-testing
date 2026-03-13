SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS `kuliah_wf_2025_baru`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE `kuliah_wf_2025_baru`;

-- =========================
-- MASTER TABLES
-- =========================

CREATE TABLE `user` (
  `iduser` bigint NOT NULL AUTO_INCREMENT,
  `nama` varchar(500) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(300) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role` (
  `idrole` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idrole`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `kategori` (
  `idkategori` int NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idkategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `kategori_klinis` (
  `idkategori_klinis` int NOT NULL,
  `nama_kategori_klinis` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idkategori_klinis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `jenis_hewan` (
  `idjenis_hewan` int NOT NULL AUTO_INCREMENT,
  `nama_jenis_hewan` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idjenis_hewan`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================
-- DEPENDENCY LEVEL 1
-- =========================

CREATE TABLE `ras_hewan` (
  `idras_hewan` int NOT NULL AUTO_INCREMENT,
  `nama_ras` varchar(100) DEFAULT NULL,
  `idjenis_hewan` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idras_hewan`),
  CONSTRAINT `fk_ras_hewan_jenis_hewan1`
    FOREIGN KEY (`idjenis_hewan`) REFERENCES `jenis_hewan` (`idjenis_hewan`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `kode_tindakan_terapi` (
  `idkode_tindakan_terapi` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(5) DEFAULT NULL,
  `deskripsi_tindakan_terapi` varchar(1000) DEFAULT NULL,
  `idkategori` int NOT NULL,
  `idkategori_klinis` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idkode_tindakan_terapi`),
  CONSTRAINT `fk_kode_tindakan_terapi_kategori1`
    FOREIGN KEY (`idkategori`) REFERENCES `kategori` (`idkategori`),
  CONSTRAINT `fk_kode_tindakan_terapi_kategori_klinis1`
    FOREIGN KEY (`idkategori_klinis`) REFERENCES `kategori_klinis` (`idkategori_klinis`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role_user` (
  `idrole_user` int NOT NULL AUTO_INCREMENT,
  `iduser` bigint NOT NULL,
  `idrole` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`idrole_user`),
  CONSTRAINT `fk_role_user_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`),
  CONSTRAINT `fk_role_user_role1`
    FOREIGN KEY (`idrole`) REFERENCES `role` (`idrole`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pemilik` (
  `idpemilik` int NOT NULL AUTO_INCREMENT,
  `no_wa` varchar(45) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `iduser` bigint NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idpemilik`),
  CONSTRAINT `fk_pemilik_user1`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dokter` (
  `iddokter` int NOT NULL AUTO_INCREMENT,
  `alamat` varchar(100) NOT NULL,
  `no_hp` varchar(45) NOT NULL,
  `bidang_dokter` varchar(100) NOT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `iduser` bigint NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`iddokter`),
  CONSTRAINT `fk_dokter_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `perawat` (
  `idperawat` int NOT NULL AUTO_INCREMENT,
  `alamat` varchar(100) NOT NULL,
  `no_hp` varchar(45) NOT NULL,
  `pendidikan` varchar(100) NOT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `iduser` bigint NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idperawat`),
  CONSTRAINT `fk_perawat_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================
-- LEVEL 2+
-- =========================

CREATE TABLE `pet` (
  `idpet` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `warna_tanda` varchar(45) DEFAULT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `idpemilik` int NOT NULL,
  `idras_hewan` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idpet`),
  CONSTRAINT `fk_pet_pemilik1`
    FOREIGN KEY (`idpemilik`) REFERENCES `pemilik` (`idpemilik`),
  CONSTRAINT `fk_pet_ras_hewan1`
    FOREIGN KEY (`idras_hewan`) REFERENCES `ras_hewan` (`idras_hewan`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `temu_dokter` (
  `idreservasi_dokter` int NOT NULL AUTO_INCREMENT,
  `no_urut` int DEFAULT NULL,
  `waktu_daftar` timestamp NULL DEFAULT NULL,
  `status` char(1) DEFAULT '',
  `idpet` int NOT NULL,
  `idrole_user` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idreservasi_dokter`),
  CONSTRAINT `fk_temudokter_pet`
    FOREIGN KEY (`idpet`) REFERENCES `pet` (`idpet`) ON DELETE RESTRICT,
  CONSTRAINT `fk_temudokter_roleuser`
    FOREIGN KEY (`idrole_user`) REFERENCES `role_user` (`idrole_user`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `rekam_medis` (
  `idrekam_medis` int NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `anamnesa` varchar(1000) DEFAULT NULL,
  `temuan_klinis` varchar(1000) DEFAULT NULL,
  `diagnosa` varchar(1000) DEFAULT NULL,
  `dokter_pemeriksa` int NOT NULL,
  `idreservasi_dokter` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`idrekam_medis`),
  CONSTRAINT `fk_rekammedis_temudokter`
    FOREIGN KEY (`idreservasi_dokter`) REFERENCES `temu_dokter` (`idreservasi_dokter`),
  CONSTRAINT `rekam_medis_ibfk_1`
    FOREIGN KEY (`dokter_pemeriksa`) REFERENCES `role_user` (`idrole_user`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `detail_rekam_medis` (
  `iddetail_rekam_medis` int NOT NULL AUTO_INCREMENT,
  `idrekam_medis` int NOT NULL,
  `idkode_tindakan_terapi` int NOT NULL,
  `detail` varchar(1000) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint DEFAULT NULL,
  PRIMARY KEY (`iddetail_rekam_medis`),
  CONSTRAINT `detail_rekam_medis_ibfk_1`
    FOREIGN KEY (`idkode_tindakan_terapi`) REFERENCES `kode_tindakan_terapi` (`idkode_tindakan_terapi`),
  CONSTRAINT `fk_detail_rekam_medis_rekam_medis1`
    FOREIGN KEY (`idrekam_medis`) REFERENCES `rekam_medis` (`idrekam_medis`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================
-- INSERT DATA FINAL
-- =========================

-- 1 USER
INSERT INTO `user` VALUES
(6,'admin','admin@mail.com','$2y$12$pFxMKHgQ9f34pHdGQj3S7.nUNb2KMeZqP4cjazT5z6YYWNJwXVziu',NULL,NULL),
(26,'resepsionis fernand','resepsionis@mail.com','$2y$10$evS5EHSvHxXte11ajvYvL.7J3pGmJLUrr/DmbmCTOCqQcRYrgDA7y',NULL,NULL),
(27,'Perawat Brili','perawat@mail.com','$2y$10$pDOf7KbzzVLBxCiG/qUL9uTlaVcuN4CxZ5.WZkIPr06ZS5Kii46IG',NULL,NULL),
(28,'dokter ikshan','dokterikshan@mail.com','$2y$12$sd64bUA3iQsZk8NOajDDZ.EF8XICC1K0oPdUDBNFdtEQdj2kcEcpm',NULL,NULL),
(29,'Dokter Udin','dokterudins@mail.com','$2y$10$p.ZlXE7SaiD9gzhrJa2wA.B23nOdWO3MjaGhR9WRh.wuaw1jLPhEG',NULL,NULL),
(30,'dokter aralan','dokteraralan@mail.com','$2y$12$9K1doFa3V1GhLi7/S3uFu.xoyzHzFlBPRBpn6rXvAZASaMIGkY55a',NULL,NULL),
(31,'Hyacine','pemilikhyacine@mail.com','$2y$10$9PInnDvVPZBuPAOk8Arv6uyEMxH3GFtDby3Q5UAOXvId.Z/NyQHtO',NULL,NULL),
(32,'Castorice','pemilikcastorice@mail.com','$2y$10$JSeSShVLYLU/nQFNnUMp/.hByXniDS4QBgAq95L01NWzCMnNRwZ2i',NULL,NULL),
(42,'Aaaaaaaaa','aaaaasss@mail.com','$2y$12$XtfIRm36AoMf0BGkjEeVU.TENJS2FkpyLWun1IWmB1fORoBhXob9u','2025-12-14 03:54:00',6),
(44,'Dokter Udin Karbit','dokterudinkarbit@mail.com','$2y$12$OP7dYYCOTSoTlzNuyHwSl.faXMnakgBuXVrgIWXmgt4DOEFAaRymu',NULL,NULL),
(46,'Perawat Susanti','perawatsusanti@mail.com','$2y$12$K.a8xML3W3npkvfsdq/rGOqti3XdGBe3AJJhGDpkli1xPyl7yBvSG',NULL,NULL),
(47,'Pemilik Phainon','pemilikphainon@mail.com','$2y$12$Rj4VIBPbC25SOxInxSE/uOYxwPm36QQUYcZRkfEpfLVDd7HVQjW8W',NULL,NULL);

-- 2 ROLE
INSERT INTO `role` VALUES
(1,'Administrator'),
(2,'Dokter'),
(3,'Perawat'),
(4,'Resepsionis'),
(5,'Pemilik');

-- 3 ROLE_USER
INSERT INTO `role_user` VALUES
(41,28,2,1),
(42,29,2,1),
(43,30,2,1),
(44,31,5,1),
(45,32,5,1),
(51,42,5,1),
(53,44,2,1),
(55,46,3,1),
(73,26,4,1),
(76,27,3,1),
(77,27,4,0),
(78,6,1,1),
(79,6,3,0),
(80,6,4,0),
(81,47,5,1);

-- 4 KATEGORI
INSERT INTO `kategori` VALUES
(1,'Vaksinasi',NULL,NULL),
(2,'Bedah / Operasi',NULL,NULL),
(3,'Cairan infus',NULL,NULL),
(4,'Terapi Injeksi',NULL,NULL),
(5,'Terapi Oral',NULL,NULL),
(6,'Diagnostik',NULL,NULL),
(7,'Rawat Inap',NULL,NULL),
(8,'Lain-lain',NULL,NULL);

-- 5 KATEGORI_KLINIS
INSERT INTO `kategori_klinis` VALUES
(1,'Terapi',NULL,NULL),
(2,'Tindakan',NULL,NULL);

-- 6 JENIS_HEWAN
INSERT INTO `jenis_hewan` VALUES
(1,'Anjing (Canis lupus familiaris)',NULL,NULL),
(2,'Kucing (Felis catus)',NULL,NULL),
(3,'Kelinci (Oryctolagus cuniculus)',NULL,NULL),
(4,'Burung',NULL,NULL),
(5,'Reptil',NULL,NULL),
(6,'Rodent / Hewan Kecil',NULL,NULL),
(14,'Kuda','2025-12-14 03:35:08',6);

-- 7 RAS_HEWAN
INSERT INTO `ras_hewan` VALUES
(1,'Golden Retriever',1,NULL,NULL),
(2,'Labrador Retriever',1,NULL,NULL),
(3,'German Shepherd',1,NULL,NULL),
(4,'Bulldog (English, French)',1,NULL,NULL),
(5,'Poodle (Toy, Miniature, Standard)',1,NULL,NULL),
(6,'Beagle',1,NULL,NULL),
(7,'Siberian Husky',1,NULL,NULL),
(8,'Shih Tzu',1,NULL,NULL),
(9,'Dachshund',1,NULL,NULL),
(10,'Chihuahua',1,NULL,NULL),
(11,'Persia',2,NULL,NULL),
(12,'Maine Coon',2,NULL,NULL),
(13,'Siamese',2,NULL,NULL),
(14,'Bengal',2,NULL,NULL),
(15,'Sphynx',2,NULL,NULL),
(16,'Scottish Fold',2,NULL,NULL),
(17,'British Shorthair',2,NULL,NULL),
(18,'Anggora',2,NULL,NULL),
(19,'Domestic Shorthair (kampung)',2,NULL,NULL),
(20,'Ragdoll',2,NULL,NULL),
(21,'Holland Lop',3,NULL,NULL),
(22,'Netherland Dwarf',3,NULL,NULL),
(23,'Flemish Giant',3,NULL,NULL),
(24,'Lionhead',3,NULL,NULL),
(25,'Rex',3,NULL,NULL),
(26,'Angora Rabbit',3,NULL,NULL),
(27,'Mini Lop',3,NULL,NULL),
(28,'Lovebird (Agapornis sp.)',4,NULL,NULL),
(29,'Kakatua (Cockatoo)',4,NULL,NULL),
(30,'Parrot / Nuri (Macaw, African Grey, Amazon Parrot)',4,NULL,NULL),
(31,'Kenari (Serinus canaria)',4,NULL,NULL),
(32,'Merpati (Columba livia)',4,NULL,NULL),
(33,'Parkit (Budgerigar / Melopsittacus undulatus)',4,NULL,NULL),
(34,'Jalak (Sturnus sp.)',4,NULL,NULL),
(35,'Kura-kura Sulcata (African spurred tortoise)',5,NULL,NULL),
(36,'Red-Eared Slider (Trachemys scripta elegans)',5,NULL,NULL),
(37,'Leopard Gecko',5,NULL,NULL),
(38,'Iguana hijau',5,NULL,NULL),
(39,'Ball Python',5,NULL,NULL),
(40,'Corn Snake',5,NULL,NULL),
(41,'Hamster (Syrian, Roborovski, Campbell, Winter White)',6,NULL,NULL),
(42,'Guinea Pig (Abyssinian, Peruvian, American Shorthair)',6,NULL,NULL),
(43,'Gerbil',6,NULL,NULL),
(44,'Chinchilla',6,NULL,NULL);

-- 8 PEMILIK
INSERT INTO `pemilik` VALUES
(10,'12345','Jalan Epiphany',31,NULL,NULL),
(11,'11111','Jl. Jalan',32,NULL,NULL),
(18,'0973829655','sfaafasd',27,NULL,NULL),
(19,'14134134333','casdsdasadda232sssss',42,'2025-12-14 03:54:00',6);

-- 9 PET
INSERT INTO `pet` VALUES
(8,'Ica','2025-10-01','Pink','B',10,18,NULL,NULL),
(9,'Anaxa','2025-10-08','Hijau','J',10,1,NULL,NULL),
(10,'Pollux','2025-10-08','Merah','B',11,16,NULL,NULL);

-- 10 KODE_TINDAKAN_TERAPI
INSERT INTO `kode_tindakan_terapi` VALUES
(1,'T01','Vaksinasi Rabies',1,1,NULL,NULL),
(2,'T02','Vaksinasi Polivalen (DHPPi/L untuk anjing)',1,1,NULL,NULL),
(3,'T03','Vaksinasi Panleukopenia / Tricat kucing',1,1,NULL,NULL),
(4,'T04','Vaksinasi lainnya (bordetella, influenza, dsb.)',1,1,NULL,NULL),
(5,'T05','Sterilisasi jantan',2,2,NULL,NULL),
(6,'T06','Sterilisasi betina',2,2,NULL,NULL),
(9,'T07','Minor surgery (luka, abses)',2,2,NULL,NULL),
(10,'T08','Major surgery (laparotomi, tumor)',2,2,NULL,NULL),
(11,'T09','Infus intravena cairan kristaloid',3,1,NULL,NULL),
(12,'T10','Infus intravena cairan koloid',3,1,NULL,NULL),
(13,'T11','Antibiotik injeksi',4,1,NULL,NULL),
(14,'T12','Antiparasit injeksi',4,1,NULL,NULL),
(15,'T13','Antiemetik / gastroprotektor',4,1,NULL,NULL),
(16,'T14','Analgesik / antiinflamasi',4,1,NULL,NULL),
(17,'T15','Kortikosteroid',4,1,NULL,NULL),
(18,'T16','Antibiotik oral',5,1,NULL,NULL),
(19,'T17','Antiparasit oral',5,1,NULL,NULL),
(20,'T18','Vitamin / suplemen',5,1,NULL,NULL),
(21,'T19','Diet khusus',5,1,NULL,NULL),
(22,'T20','Pemeriksaan darah rutin',6,2,NULL,NULL),
(23,'T21','Pemeriksaan kimia darah',6,2,NULL,NULL),
(24,'T22','Pemeriksaan feses / parasitologi',6,2,NULL,NULL),
(25,'T23','Pemeriksaan urin',6,2,NULL,NULL),
(26,'T24','Radiografi (rontgen)',6,2,NULL,NULL),
(27,'T25','USG Abdomen',6,2,NULL,NULL),
(28,'T26','Sitologi / biopsi',6,2,NULL,NULL),
(29,'T27','Rapid test penyakit infeksi',6,2,NULL,NULL),
(30,'T28','Observasi sehari',7,2,NULL,NULL),
(31,'T29','Observasi lebih dari 1 hari',7,2,NULL,NULL),
(32,'T30','Grooming medis',8,2,NULL,NULL),
(33,'T31','Deworming',8,1,NULL,NULL),
(34,'T32','Ektoparasit control',8,1,NULL,NULL);

-- 11 TEMU_DOKTER
INSERT INTO `temu_dokter` VALUES
(17,1,'2025-10-06 12:14:35','D',8,41,NULL,NULL),
(18,2,'2025-10-06 12:14:46','W',8,41,NULL,NULL),
(19,1,'2025-10-06 12:14:52','W',8,42,NULL,NULL),
(20,2,'2025-10-06 12:15:09','W',9,42,NULL,NULL),
(21,1,'2025-10-06 12:15:12','W',9,43,NULL,NULL),
(22,3,'2025-10-06 12:19:01','D',10,41,NULL,NULL),
(23,3,'2025-10-06 12:19:14','W',10,42,NULL,NULL),
(24,1,'2025-10-06 21:45:54','W',10,43,NULL,NULL),
(25,1,'2025-12-04 11:00:53','W',10,53,NULL,NULL),
(28,1,'2025-12-14 04:01:03','W',8,41,'2025-12-14 04:01:06',6),
(29,2,'2025-12-14 08:55:06','W',8,41,NULL,NULL);

-- 12 REKAM_MEDIS
INSERT INTO `rekam_medis` VALUES
(5,'2025-10-06 19:21:18','Penyakit parah banget','Banyak sekali','Sakit',53,17,NULL,NULL),
(6,'2025-10-06 19:23:15','Penyakit parah banget','karbit tingkat lanjut','penyakit karbitan',41,22,NULL,NULL),
(7,'2025-12-04 12:49:15','bitbitbtibitbti','ngeriiiiiiiiiiiiiiiiiiii','asawaawawwawwawawwa',53,25,'2025-12-14 03:49:35',6),
(8,'2025-12-14 09:18:04','asdasdadds','aku adalah','asdasdadasdsa',53,29,NULL,NULL),
(9,'2025-12-14 09:18:44','adaa','aaaa','aaaaa',53,24,'2025-12-14 09:18:54',46);

-- 13 DETAIL_REKAM_MEDIS
INSERT INTO `detail_rekam_medis` VALUES
(4,5,20,'Muntah api 5x sehari',NULL,NULL),
(5,6,5,'karbit tiap lihat my my penyakit mybini',NULL,NULL),
(6,7,6,'njirlah','2025-12-14 03:49:35',6),
(7,8,1,'bismillah allahuakbar','2025-12-15 04:13:07',44),
(8,8,4,'bismillah',NULL,NULL);

ALTER TABLE kode_tindakan_terapi 
MODIFY kode VARCHAR(20) NOT NULL;

SET FOREIGN_KEY_CHECKS=1;