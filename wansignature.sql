-- MySQL dump 10.13  Distrib 5.6.24, for Win32 (x86)
--
-- Host: localhost    Database: wansignature
-- ------------------------------------------------------
-- Server version	5.6.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cms_accounts`
--

DROP TABLE IF EXISTS `cms_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` tinyint(3) unsigned NOT NULL,
  `username` varchar(500) DEFAULT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `last_login` datetime NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_accounts`
--

LOCK TABLES `cms_accounts` WRITE;
/*!40000 ALTER TABLE `cms_accounts` DISABLE KEYS */;
INSERT INTO `cms_accounts` VALUES (1,1,1,'Admin Basuki','admin@yahoo.com','169e781bd52860b584879cbe117085da596238f3','2015-06-29 22:00:27','2013-01-04 00:00:00',1,'2014-05-05 15:15:38',1);
INSERT INTO `cms_accounts` VALUES (2,2,2,'Andy Basuki','andybasuki88@gmail.com','d82dff1679e0137a0bab60cc67cc6a2ad36f10a0','2015-06-13 11:09:54','2015-06-02 20:19:53',1,'2015-06-02 20:19:53',1);
/*!40000 ALTER TABLE `cms_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_entries`
--

DROP TABLE IF EXISTS `cms_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_type` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `description` text,
  `main_image` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '1',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `lang_code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_entries`
--

LOCK TABLES `cms_entries` WRITE;
/*!40000 ALTER TABLE `cms_entries` DISABLE KEYS */;
INSERT INTO `cms_entries` VALUES (3,'bank','BCA Group','bca-group','https://ibank.klikbca.com/',2,0,1,0,'2015-06-03 00:42:27',1,'2015-06-23 12:22:22',1,3,'en-3');
INSERT INTO `cms_entries` VALUES (2,'media','Bank BCA','bank-bca',NULL,0,0,1,0,'2015-06-03 00:40:46',1,'2015-06-03 00:40:47',1,2,'en-2');
INSERT INTO `cms_entries` VALUES (4,'media','logo-bank-mandiri','logo-bank-mandiri',NULL,0,0,1,0,'2015-06-03 00:44:38',1,'2015-06-03 00:44:38',1,4,'en-4');
INSERT INTO `cms_entries` VALUES (5,'bank','Mandiri','mandiri','https://ib.bankmandiri.co.id/',4,0,1,0,'2015-06-03 00:45:20',1,'2015-06-14 14:16:03',1,5,'en-5');
INSERT INTO `cms_entries` VALUES (7,'bank','HSBC','hsbc-1','The world\'s local bank',8,0,1,0,'2015-06-03 00:47:51',1,'2015-06-14 14:09:26',1,7,'en-7');
INSERT INTO `cms_entries` VALUES (8,'media','hsbc','hsbc-2',NULL,0,0,1,0,'2015-06-03 00:49:07',1,'2015-06-03 00:49:07',1,8,'en-8');
INSERT INTO `cms_entries` VALUES (9,'usd-rate','IDR','idr','Indonesian Rupiah.',0,0,1,0,'2015-06-03 15:58:49',1,'2015-06-15 16:54:48',1,9,'en-9');
INSERT INTO `cms_entries` VALUES (10,'usd-rate','HKD','hkd','Hongkong Dollar.',0,0,1,0,'2015-06-03 16:04:27',1,'2015-06-03 16:04:27',1,10,'en-10');
INSERT INTO `cms_entries` VALUES (11,'usd-rate','CNY','cny','Chinese Yuan.',0,0,1,0,'2015-06-03 16:05:14',1,'2015-06-15 16:10:56',1,11,'en-11');
INSERT INTO `cms_entries` VALUES (12,'usd-rate','Euro','euro','',0,0,1,0,'2015-06-03 21:13:08',1,'2015-06-03 21:13:08',1,12,'en-12');
INSERT INTO `cms_entries` VALUES (13,'usd-rate','Gold Bar (gr)','gold-bar-gr','',0,0,1,0,'2015-06-03 21:52:59',1,'2015-06-15 15:39:56',1,13,'en-13');
INSERT INTO `cms_entries` VALUES (14,'warehouse','ATOM','atom-wh','',0,0,1,0,'2015-06-03 22:41:50',1,'2015-06-29 16:11:32',1,14,'en-14');
INSERT INTO `cms_entries` VALUES (15,'warehouse','TP','tunjungan-plaza-wh','',0,0,1,0,'2015-06-03 22:42:27',1,'2015-06-29 16:11:39',1,15,'en-15');
INSERT INTO `cms_entries` VALUES (16,'product-type','DPF','dpf','Diamond Pendants Finish',0,0,1,0,'2015-06-03 23:22:58',1,'2015-06-05 21:30:02',1,16,'en-16');
INSERT INTO `cms_entries` VALUES (17,'product-type','DRF','drf','Diamond Rings Finish',0,0,1,0,'2015-06-03 23:23:16',1,'2015-06-05 21:29:57',1,17,'en-17');
INSERT INTO `cms_entries` VALUES (18,'product-type','DEF','def','Diamond Earrings Finish',0,0,1,0,'2015-06-03 23:24:13',1,'2015-06-05 21:29:52',1,18,'en-18');
INSERT INTO `cms_entries` VALUES (19,'product-type','Pipe Necklace','pipe-necklace','',0,0,1,0,'2015-06-03 23:25:33',1,'2015-06-05 23:38:05',1,19,'en-19');
INSERT INTO `cms_entries` VALUES (20,'product-type','Pipe Bracelet','pipe-bracelet','',0,0,1,0,'2015-06-03 23:25:53',1,'2015-06-05 23:38:12',1,20,'en-20');
INSERT INTO `cms_entries` VALUES (21,'product-brand','BVLGARI','bvlgari','',0,0,1,0,'2015-06-03 23:31:03',1,'2015-06-12 15:42:01',1,21,'en-21');
INSERT INTO `cms_entries` VALUES (22,'product-brand','VAN CLEEF','van-cleef','',0,0,1,0,'2015-06-03 23:31:14',1,'2015-06-03 23:31:14',1,22,'en-22');
INSERT INTO `cms_entries` VALUES (23,'product-brand','HERMES','hermes','',0,0,1,0,'2015-06-03 23:31:27',1,'2015-06-03 23:31:27',1,23,'en-23');
INSERT INTO `cms_entries` VALUES (24,'exhibition','JIJF 2015 (8th)','jijf-2015-8th','',0,0,1,0,'2015-06-05 12:41:43',1,'2015-06-05 12:41:43',1,24,'en-24');
INSERT INTO `cms_entries` VALUES (25,'client','Novi','novi','G243730',0,0,1,0,'2015-06-05 13:41:35',1,'2015-06-05 13:46:35',1,25,'en-25');
INSERT INTO `cms_entries` VALUES (26,'client','Gisela Tania','gisela-tania','G243935',0,0,1,0,'2015-06-05 13:41:56',1,'2015-06-05 13:46:43',1,26,'en-26');
INSERT INTO `cms_entries` VALUES (27,'client','Reyner Gunawan','reyner-gunawan','G243913',0,0,1,0,'2015-06-05 13:42:31',1,'2015-06-26 10:52:15',1,27,'en-27');
INSERT INTO `cms_entries` VALUES (28,'client','Hanna','hanna','G113678',0,0,1,0,'2015-06-05 13:44:06',1,'2015-06-26 10:51:49',1,28,'en-28');
INSERT INTO `cms_entries` VALUES (29,'client','Vonza Silvia','vonza-silvia','G113369',0,0,1,0,'2015-06-05 13:44:30',1,'2015-06-05 13:47:17',1,29,'en-29');
INSERT INTO `cms_entries` VALUES (30,'client','Widya','widya','G113336',0,0,1,0,'2015-06-05 13:44:49',1,'2015-06-05 13:47:27',1,30,'en-30');
INSERT INTO `cms_entries` VALUES (31,'client','Hendra','hendra','G113223',0,0,1,0,'2015-06-05 13:45:09',1,'2015-06-05 13:47:35',1,31,'en-31');
INSERT INTO `cms_entries` VALUES (32,'client','Yani','yani','G1151167 , G113240',0,0,1,0,'2015-06-05 13:45:30',1,'2015-06-05 13:47:44',1,32,'en-32');
INSERT INTO `cms_entries` VALUES (33,'client','Grace Novarinus','grace-novarinus','G1231100 , G1531341 , G113659',0,0,1,0,'2015-06-05 13:46:21',1,'2015-06-08 12:22:25',1,33,'en-33');
INSERT INTO `cms_entries` VALUES (34,'client','Sabrina','sabrina','G243931',0,0,1,0,'2015-06-05 13:48:23',1,'2015-06-05 13:48:23',1,34,'en-34');
INSERT INTO `cms_entries` VALUES (35,'client','Cik Ninih','cik-ninih','G146957 , G234932',0,0,1,0,'2015-06-05 13:48:47',1,'2015-06-08 12:22:18',1,35,'en-35');
INSERT INTO `cms_entries` VALUES (36,'client','Yuli','yuli','G542880, G5131033',0,0,1,0,'2015-06-05 13:49:10',1,'2015-06-05 13:49:10',1,36,'en-36');
INSERT INTO `cms_entries` VALUES (37,'client','Sri Rahayu','sri-rahayu','G5131024',0,0,1,0,'2015-06-05 13:49:46',1,'2015-06-05 13:49:46',1,37,'en-37');
INSERT INTO `cms_entries` VALUES (38,'client','Susan','susan','G1451362, G1452063, G1151941',0,0,1,0,'2015-06-05 13:51:21',1,'2015-06-05 13:51:21',1,38,'en-38');
INSERT INTO `cms_entries` VALUES (39,'client','Michael','michael','G243940, G243905',0,0,1,0,'2015-06-05 13:51:45',1,'2015-06-19 17:29:27',1,39,'en-39');
INSERT INTO `cms_entries` VALUES (43,'cor-jewelry','G123160','g123160','',0,0,1,0,'2015-06-06 16:32:20',1,'2015-06-19 12:00:58',1,43,'en-43');
INSERT INTO `cms_entries` VALUES (41,'product-type','Made in Italy','made-in-italy','',0,0,1,0,'2015-06-05 23:48:05',1,'2015-06-21 15:00:34',1,41,'en-41');
INSERT INTO `cms_entries` VALUES (42,'product-type','Made in Korea','made-in-korea','',0,0,1,0,'2015-06-05 23:48:27',1,'2015-06-21 15:00:43',1,42,'en-42');
INSERT INTO `cms_entries` VALUES (44,'diamond','100773','100773','',88,0,1,0,'2015-06-06 16:33:08',1,'2015-06-23 12:28:49',1,44,'en-44');
INSERT INTO `cms_entries` VALUES (45,'product-type','Earring','earring','',0,0,1,0,'2015-06-07 08:22:24',1,'2015-06-07 08:22:24',1,45,'en-45');
INSERT INTO `cms_entries` VALUES (46,'product-type','Earring','earring-1','',0,0,1,0,'2015-06-07 08:22:40',1,'2015-06-07 08:22:40',1,46,'en-46');
INSERT INTO `cms_entries` VALUES (47,'logistic','Baut Mur','baut-mur','',0,0,1,0,'2015-06-07 09:18:39',1,'2015-06-11 12:51:09',1,47,'en-47');
INSERT INTO `cms_entries` VALUES (58,'logistic','Obeng','obeng','[untuk reparasi cincin]',0,0,1,0,'2015-06-11 12:30:38',1,'2015-06-11 12:30:38',1,58,'en-58');
INSERT INTO `cms_entries` VALUES (51,'surat-jalan','asdfgfg','asdfgfg','',0,0,1,0,'2015-06-10 10:36:24',1,'2015-06-10 10:36:50',1,51,'en-51');
INSERT INTO `cms_entries` VALUES (52,'surat-jalan','test sjoke','test-sjoke','',0,0,1,0,'2015-06-10 11:28:07',1,'2015-06-10 12:10:13',1,52,'en-52');
INSERT INTO `cms_entries` VALUES (53,'surat-jalan','test kedua surat jalan','test-kedua-surat-jalan','',0,0,1,0,'2015-06-10 12:11:21',1,'2015-06-10 12:11:42',1,53,'en-53');
INSERT INTO `cms_entries` VALUES (54,'surat-jalan','wqwq','wqwqwqwqw','TEST WQWQWQ',0,0,1,0,'2015-06-10 16:08:57',1,'2015-06-12 16:37:07',1,54,'en-54');
INSERT INTO `cms_entries` VALUES (55,'logistic','Souvenir X','souvenir-x','[contoh sample]',0,0,1,0,'2015-06-10 17:15:41',1,'2015-06-11 12:43:32',1,55,'en-55');
INSERT INTO `cms_entries` VALUES (59,'surat-jalan','N123456','n123456','',0,0,0,0,'2015-06-12 00:16:59',1,'2015-06-12 00:16:59',1,59,'en-59');
INSERT INTO `cms_entries` VALUES (60,'surat-jalan','SRJ005','srj001','SURAT JALAN DITERIMA\r\nTHANKS :)',0,0,1,0,'2015-06-12 13:34:45',1,'2015-06-12 16:27:27',1,60,'en-60');
INSERT INTO `cms_entries` VALUES (61,'diamond','012115','012115','',89,0,1,0,'2015-06-12 14:32:34',1,'2015-06-23 12:28:39',1,61,'en-61');
INSERT INTO `cms_entries` VALUES (64,'vendor','FR SWI','front-sriwijaya','Ancestor vendor.',0,0,1,0,'2015-06-12 16:42:22',1,'2015-06-27 15:43:20',1,64,'en-64');
INSERT INTO `cms_entries` VALUES (65,'vendor','EW','elang-wijaya','',0,0,1,0,'2015-06-12 16:44:20',1,'2015-06-27 15:43:13',1,65,'en-65');
INSERT INTO `cms_entries` VALUES (66,'dmd-client-invoice','INV / DMD / C001','inv-dmd-c001','',0,0,1,1,'2015-06-12 16:46:13',1,'2015-06-23 11:41:05',1,66,'en-66');
INSERT INTO `cms_entries` VALUES (67,'cor-client-invoice','INV / COR / C005','inv-cor-c005','',0,0,1,1,'2015-06-12 16:47:21',1,'2015-06-23 13:56:15',1,67,'en-67');
INSERT INTO `cms_entries` VALUES (68,'dmd-vendor-invoice','INV / DMD / V012','inv-dmd-v012','WAN First Invoice.',0,0,1,2,'2015-06-12 16:48:32',1,'2015-06-22 11:20:48',1,68,'en-68');
INSERT INTO `cms_entries` VALUES (69,'cor-vendor-invoice','INV / COR / C021','inv-cor-c021','',0,0,1,2,'2015-06-12 16:49:03',1,'2015-06-24 23:07:10',1,69,'en-69');
INSERT INTO `cms_entries` VALUES (73,'surat-jalan','SRJ FIN 007','srj-fin-007','SRJ selesai :)\r\nThank You.',0,0,1,0,'2015-06-13 01:20:46',1,'2015-06-13 12:51:23',1,73,'en-73');
INSERT INTO `cms_entries` VALUES (74,'surat-jalan','SRJ RTR 001','srj-rtr-001','SRJ done for a moment.',0,0,1,0,'2015-06-13 01:58:37',1,'2015-06-13 12:43:14',1,74,'en-74');
INSERT INTO `cms_entries` VALUES (75,'usd-rate','USD','usd','United States Dollar.',0,0,1,0,'2015-06-14 11:01:57',1,'2015-06-14 11:01:57',1,75,'en-75');
INSERT INTO `cms_entries` VALUES (83,'cv-payment','First Payment C021','first-payment-c021','',0,69,1,0,'2015-06-22 17:32:19',1,'2015-06-22 17:32:20',1,83,'en-83');
INSERT INTO `cms_entries` VALUES (77,'dmd-vendor-invoice','TEST 005','test-005','',0,0,1,0,'2015-06-16 13:09:08',1,'2015-06-16 13:48:35',1,77,'en-77');
INSERT INTO `cms_entries` VALUES (78,'dv-payment','Test DV Payment V012','test-dv-payment-v012','',0,68,1,0,'2015-06-18 15:45:58',1,'2015-06-18 16:32:58',1,78,'en-78');
INSERT INTO `cms_entries` VALUES (79,'product-type','Ring','ring','Ordinary Ring 999',0,0,1,0,'2015-06-19 11:23:19',1,'2015-06-19 11:23:19',1,79,'en-79');
INSERT INTO `cms_entries` VALUES (80,'cor-jewelry','10003906','10003906','',0,0,1,0,'2015-06-19 11:23:52',1,'2015-06-21 15:01:49',1,80,'en-80');
INSERT INTO `cms_entries` VALUES (82,'dv-payment','Bayar lengkap invoice V012','bayar-lengkap-invoice-v012','Bayar pertama ke vendor.',0,68,1,0,'2015-06-22 11:20:48',1,'2015-06-22 11:20:48',1,82,'en-82');
INSERT INTO `cms_entries` VALUES (87,'cc-payment','Client Original C005 - bayar retur','client-special-c005-bayar-retur','bayar retur bbrp brg yg cacat.\r\nSegera diatasi ya.. Thx',0,67,1,0,'2015-06-23 11:48:05',1,'2015-06-23 13:56:15',1,87,'en-87');
INSERT INTO `cms_entries` VALUES (86,'dc-payment','Diamond C001','diamond-c001','permbayaran client diamond pertama\r\nThanks\r\nGBU.',0,66,1,0,'2015-06-23 11:41:04',1,'2015-06-23 11:41:04',1,86,'en-86');
INSERT INTO `cms_entries` VALUES (88,'media','heart_diamond','heart-diamond',NULL,0,0,1,0,'2015-06-23 12:26:50',1,'2015-06-23 12:26:50',1,88,'en-88');
INSERT INTO `cms_entries` VALUES (89,'media','ring_diamond','ring-diamond',NULL,0,0,1,0,'2015-06-23 12:26:50',1,'2015-06-23 12:26:50',1,89,'en-89');
INSERT INTO `cms_entries` VALUES (90,'cv-payment','Second payment C021','second-payment-c021','This is\r\nVendor Outstanding\r\nThank You\r\nGBU :)',0,69,1,0,'2015-06-24 23:07:09',1,'2015-06-24 23:07:09',1,90,'en-90');
INSERT INTO `cms_entries` VALUES (91,'cor-client-invoice','INV / COR / 025 A','inv-cor-025','Tambahan deskripsi\r\nCor Client Invoice\r\n\r\nThank You & have a good day.\r\nGBU O:)',0,0,1,0,'2015-06-25 14:03:39',1,'2015-06-25 14:04:55',1,91,'en-91');
INSERT INTO `cms_entries` VALUES (92,'dmd-client-invoice','DMD / CLI / 006 A','dmd-cli-006','Ada client baru si Reyner beli diamond XXI.\r\nMohon dilayani dengan baik.\r\nThanks :)',0,0,1,0,'2015-06-25 15:54:40',1,'2015-06-25 16:05:41',1,92,'en-92');
INSERT INTO `cms_entries` VALUES (93,'salesman','Bejo Sugiantoro','bejo-sugiantoro','Salah satu karyawan yang rajin dan ulet bekerja.\r\nMohon bimbingannya, terima kasih\r\nGBU :)',0,0,1,0,'2015-06-26 09:32:54',1,'2015-06-26 09:32:54',1,93,'en-93');
INSERT INTO `cms_entries` VALUES (94,'salesman','Soeharman Prasetyo','soeharman-prasetyo','Salah satu karyawan senior yang paling aktif.\r\nBijak dalam mengambil keputusan.',0,0,1,0,'2015-06-26 09:35:17',1,'2015-06-26 09:35:17',1,94,'en-94');
INSERT INTO `cms_entries` VALUES (96,'product-type','D','d','General Diamond Type.',0,0,1,0,'2015-06-28 12:40:25',1,'2015-06-28 12:40:25',1,96,'en-96');
/*!40000 ALTER TABLE `cms_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_entry_metas`
--

DROP TABLE IF EXISTS `cms_entry_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_entry_metas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) unsigned NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=938 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_entry_metas`
--

LOCK TABLES `cms_entry_metas` WRITE;
/*!40000 ALTER TABLE `cms_entry_metas` DISABLE KEYS */;
INSERT INTO `cms_entry_metas` VALUES (5,2,'image_type','png');
INSERT INTO `cms_entry_metas` VALUES (6,2,'image_size','6106');
INSERT INTO `cms_entry_metas` VALUES (9,4,'image_type','png');
INSERT INTO `cms_entry_metas` VALUES (10,4,'image_size','5722');
INSERT INTO `cms_entry_metas` VALUES (17,8,'image_type','gif');
INSERT INTO `cms_entry_metas` VALUES (18,8,'image_size','1569');
INSERT INTO `cms_entry_metas` VALUES (488,9,'form-rate_value','13230');
INSERT INTO `cms_entry_metas` VALUES (22,10,'form-rate_value','7.75');
INSERT INTO `cms_entry_metas` VALUES (487,11,'form-rate_value','6.20');
INSERT INTO `cms_entry_metas` VALUES (27,12,'form-rate_value','0.89');
INSERT INTO `cms_entry_metas` VALUES (483,13,'form-rate_value','0.02637826431');
INSERT INTO `cms_entry_metas` VALUES (933,14,'form-telepon','(031) 3551995');
INSERT INTO `cms_entry_metas` VALUES (936,15,'form-telepon','(031) 5311088');
INSERT INTO `cms_entry_metas` VALUES (43,24,'form-start_date','05/07/2015');
INSERT INTO `cms_entry_metas` VALUES (44,24,'form-end_date','05/10/2015');
INSERT INTO `cms_entry_metas` VALUES (45,24,'form-alamat','Assembly Hall - Jakarta Convention Center\r\nJAKARTA - INDONESIA');
INSERT INTO `cms_entry_metas` VALUES (46,24,'form-telepon','+62 (21) 5726000');
INSERT INTO `cms_entry_metas` VALUES (47,24,'form-warehouse_employee','');
INSERT INTO `cms_entry_metas` VALUES (105,25,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (104,25,'form-telepon','0812309688884');
INSERT INTO `cms_entry_metas` VALUES (103,25,'form-alamat','Rangkah 1/46');
INSERT INTO `cms_entry_metas` VALUES (102,25,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (109,26,'form-telepon','081232034442');
INSERT INTO `cms_entry_metas` VALUES (108,26,'form-alamat','Jl. Villa Puncak Tidar VE IX/20 Malang');
INSERT INTO `cms_entry_metas` VALUES (107,26,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (908,27,'form-salesman','bejo-sugiantoro');
INSERT INTO `cms_entry_metas` VALUES (900,28,'form-telepon','031-70574934 / 7E715559');
INSERT INTO `cms_entry_metas` VALUES (899,28,'form-alamat','Rungkut Asri Tengah 4/31');
INSERT INTO `cms_entry_metas` VALUES (124,29,'form-telepon','0818381010');
INSERT INTO `cms_entry_metas` VALUES (123,29,'form-alamat','Tarakan');
INSERT INTO `cms_entry_metas` VALUES (122,29,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (129,30,'form-telepon','082143542255');
INSERT INTO `cms_entry_metas` VALUES (128,30,'form-alamat','Kencana Sari Barat 2/811');
INSERT INTO `cms_entry_metas` VALUES (127,30,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (134,31,'form-telepon','081241960000');
INSERT INTO `cms_entry_metas` VALUES (133,31,'form-alamat','Makassar');
INSERT INTO `cms_entry_metas` VALUES (132,31,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (139,32,'form-telepon','081226377777');
INSERT INTO `cms_entry_metas` VALUES (138,32,'form-alamat','JL. Tanggulangin no. 1');
INSERT INTO `cms_entry_metas` VALUES (137,32,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (235,33,'form-telepon','0811361231 / 5229861F');
INSERT INTO `cms_entry_metas` VALUES (234,33,'form-alamat','JL. W.R Supratman 64 Rambipuji Jbr');
INSERT INTO `cms_entry_metas` VALUES (233,33,'form-kategori','Wholesaler');
INSERT INTO `cms_entry_metas` VALUES (106,25,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (110,26,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (111,26,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (909,27,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (125,29,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (126,29,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (130,30,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (131,30,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (135,31,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (136,31,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (140,32,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (141,32,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (143,34,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (144,34,'form-telepon','081703096624');
INSERT INTO `cms_entry_metas` VALUES (145,34,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (146,34,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (229,35,'form-alamat','THI 1 B2 NO 6, Bandung');
INSERT INTO `cms_entry_metas` VALUES (230,35,'form-telepon','08122351037');
INSERT INTO `cms_entry_metas` VALUES (154,36,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (155,36,'form-telepon','081321816161');
INSERT INTO `cms_entry_metas` VALUES (156,36,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (157,36,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (159,37,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (160,37,'form-alamat','Jl. Gagak Lumayung 141 Garut');
INSERT INTO `cms_entry_metas` VALUES (161,37,'form-telepon','08112111375');
INSERT INTO `cms_entry_metas` VALUES (162,37,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (163,37,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (228,35,'form-kategori','Wholesaler');
INSERT INTO `cms_entry_metas` VALUES (170,38,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (171,38,'form-telepon','082330788899');
INSERT INTO `cms_entry_metas` VALUES (172,38,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (173,38,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (672,39,'form-telepon','081330568888');
INSERT INTO `cms_entry_metas` VALUES (673,39,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (674,39,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (675,39,'form-diamond_sell_x','0.35');
INSERT INTO `cms_entry_metas` VALUES (193,20,'form-category','999 Simple (110%)');
INSERT INTO `cms_entry_metas` VALUES (192,19,'form-category','999 3D (115%)');
INSERT INTO `cms_entry_metas` VALUES (189,18,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (190,17,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (191,16,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (712,41,'form-category','Italy (125%)');
INSERT INTO `cms_entry_metas` VALUES (713,42,'form-category','Korea (100%)');
INSERT INTO `cms_entry_metas` VALUES (620,43,'form-product_brand','bvlgari');
INSERT INTO `cms_entry_metas` VALUES (209,45,'form-category','999 3D (115%)');
INSERT INTO `cms_entry_metas` VALUES (211,46,'form-category','999 Simple (110%)');
INSERT INTO `cms_entry_metas` VALUES (319,47,'form-logistic_type','Supporting');
INSERT INTO `cms_entry_metas` VALUES (317,55,'form-warehouse','tunjungan-plaza-wh_20');
INSERT INTO `cms_entry_metas` VALUES (215,47,'count-logistic-warehouse','0');
INSERT INTO `cms_entry_metas` VALUES (935,15,'form-alamat','Jl. Basuki Rahmat No. 8-12, Surabaya, Jawa Timur 60261');
INSERT INTO `cms_entry_metas` VALUES (932,14,'form-alamat','Jalan Bunguran No. 45, Surabaya, Jawa Timur');
INSERT INTO `cms_entry_metas` VALUES (313,58,'form-logistic_type','Supporting');
INSERT INTO `cms_entry_metas` VALUES (231,35,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (232,35,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (236,33,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (237,33,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (907,27,'form-telepon','081355527131');
INSERT INTO `cms_entry_metas` VALUES (898,28,'form-wholesaler','cik-ninih');
INSERT INTO `cms_entry_metas` VALUES (897,28,'form-kategori','Retailer');
INSERT INTO `cms_entry_metas` VALUES (265,51,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (266,51,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (264,51,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (263,51,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (267,51,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (269,52,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (270,52,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (271,52,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (272,52,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (273,52,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (275,53,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (276,53,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (277,53,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (278,53,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (279,53,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (330,54,'form-warehouse_origin','atom-wh');
INSERT INTO `cms_entry_metas` VALUES (329,54,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (316,55,'form-logistic_type','Souvenir');
INSERT INTO `cms_entry_metas` VALUES (328,54,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (304,55,'count-logistic-warehouse','0');
INSERT INTO `cms_entry_metas` VALUES (315,58,'form-exhibition','jijf-2015-8th_12');
INSERT INTO `cms_entry_metas` VALUES (307,55,'count-logistic-exhibition','0');
INSERT INTO `cms_entry_metas` VALUES (314,58,'form-warehouse','tunjungan-plaza-wh_30|atom-wh_40');
INSERT INTO `cms_entry_metas` VALUES (318,55,'form-exhibition','jijf-2015-8th_35');
INSERT INTO `cms_entry_metas` VALUES (320,47,'form-warehouse','tunjungan-plaza-wh_3|atom-wh_7');
INSERT INTO `cms_entry_metas` VALUES (321,47,'form-exhibition','_');
INSERT INTO `cms_entry_metas` VALUES (331,54,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (332,54,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (333,54,'form-logistic','obeng_20|baut-mur_5');
INSERT INTO `cms_entry_metas` VALUES (335,59,'form-date','06/12/2015');
INSERT INTO `cms_entry_metas` VALUES (336,59,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (337,59,'form-warehouse_origin','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (338,59,'form-diamond','100773');
INSERT INTO `cms_entry_metas` VALUES (339,59,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (340,59,'form-logistic','obeng_10|souvenir-x_15|baut-mur_2');
INSERT INTO `cms_entry_metas` VALUES (342,60,'form-date','06/12/2015');
INSERT INTO `cms_entry_metas` VALUES (343,60,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (344,60,'form-warehouse_origin','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (345,60,'form-diamond','100773');
INSERT INTO `cms_entry_metas` VALUES (346,60,'form-cor_jewelry','g123160');
INSERT INTO `cms_entry_metas` VALUES (347,60,'form-logistic','obeng_20');
INSERT INTO `cms_entry_metas` VALUES (834,44,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (823,61,'form-product_status','consignment');
INSERT INTO `cms_entry_metas` VALUES (822,61,'form-sell_barcode','24784.04');
INSERT INTO `cms_entry_metas` VALUES (821,61,'form-barcode','23957');
INSERT INTO `cms_entry_metas` VALUES (930,64,'form-capital_x','0.32');
INSERT INTO `cms_entry_metas` VALUES (929,64,'form-email','frontsriwijaya@yahoo.com');
INSERT INTO `cms_entry_metas` VALUES (928,64,'form-telepon','031 123 456');
INSERT INTO `cms_entry_metas` VALUES (927,64,'form-alamat','Jl. Sriwijaya Utara 50\r\nSurabaya, Indonesia');
INSERT INTO `cms_entry_metas` VALUES (926,65,'form-capital_x','0.265');
INSERT INTO `cms_entry_metas` VALUES (925,65,'form-email','elangwijaya@yahoo.com');
INSERT INTO `cms_entry_metas` VALUES (924,65,'form-telepon','081 5757 1234');
INSERT INTO `cms_entry_metas` VALUES (923,65,'form-alamat','Jl. Wijaya Mas 27\r\nSurabaya, Indonesia');
INSERT INTO `cms_entry_metas` VALUES (545,66,'form-client','michael');
INSERT INTO `cms_entry_metas` VALUES (546,66,'form-wholesaler','cik-ninih');
INSERT INTO `cms_entry_metas` VALUES (547,66,'form-sale_venue','Exhibition');
INSERT INTO `cms_entry_metas` VALUES (741,67,'form-client','reyner-gunawan');
INSERT INTO `cms_entry_metas` VALUES (742,67,'form-wholesaler','grace-novarinus');
INSERT INTO `cms_entry_metas` VALUES (743,67,'form-sale_venue','Warehouse');
INSERT INTO `cms_entry_metas` VALUES (744,67,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (750,68,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (699,69,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (698,69,'form-vendor','front-sriwijaya');
INSERT INTO `cms_entry_metas` VALUES (621,43,'form-item_weight','4.41');
INSERT INTO `cms_entry_metas` VALUES (833,44,'form-product_status','STOCK');
INSERT INTO `cms_entry_metas` VALUES (619,43,'form-product_type','made-in-italy');
INSERT INTO `cms_entry_metas` VALUES (460,73,'form-warehouse_origin','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (459,73,'form-client','michael');
INSERT INTO `cms_entry_metas` VALUES (458,73,'form-dmd_client_invoice','inv-dmd-c001');
INSERT INTO `cms_entry_metas` VALUES (457,73,'form-delivery_type','Diamond Sale');
INSERT INTO `cms_entry_metas` VALUES (456,73,'form-date','06/13/2015');
INSERT INTO `cms_entry_metas` VALUES (461,73,'form-diamond','100773');
INSERT INTO `cms_entry_metas` VALUES (462,73,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (463,73,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (544,66,'form-date','06/12/2015');
INSERT INTO `cms_entry_metas` VALUES (470,74,'form-date','06/13/2015');
INSERT INTO `cms_entry_metas` VALUES (471,74,'form-delivery_type','Diamond Return');
INSERT INTO `cms_entry_metas` VALUES (472,74,'form-dmd_vendor_invoice','inv-dmd-v012');
INSERT INTO `cms_entry_metas` VALUES (473,74,'form-vendor','elang-wijaya');
INSERT INTO `cms_entry_metas` VALUES (474,74,'form-warehouse_origin','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (475,74,'form-diamond','100773');
INSERT INTO `cms_entry_metas` VALUES (476,74,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (477,74,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (479,75,'form-rate_value','1');
INSERT INTO `cms_entry_metas` VALUES (815,3,'form-loan_interest_rate','5.75');
INSERT INTO `cms_entry_metas` VALUES (482,7,'form-loan_interest_rate','6');
INSERT INTO `cms_entry_metas` VALUES (489,69,'count-cv-payment','2');
INSERT INTO `cms_entry_metas` VALUES (776,83,'form-additional_cost','500');
INSERT INTO `cms_entry_metas` VALUES (775,83,'form-gold_bar_rate','235.04');
INSERT INTO `cms_entry_metas` VALUES (774,83,'form-cost_currency','cny');
INSERT INTO `cms_entry_metas` VALUES (773,83,'form-gold_loss','15');
INSERT INTO `cms_entry_metas` VALUES (772,83,'form-cor_jewelry','g123160_4.41');
INSERT INTO `cms_entry_metas` VALUES (771,83,'form-type','Credit Card');
INSERT INTO `cms_entry_metas` VALUES (770,83,'form-statement','Debit');
INSERT INTO `cms_entry_metas` VALUES (769,83,'form-date','06/22/2015');
INSERT INTO `cms_entry_metas` VALUES (749,68,'form-vendor','elang-wijaya');
INSERT INTO `cms_entry_metas` VALUES (535,77,'form-date','06/16/2015');
INSERT INTO `cms_entry_metas` VALUES (536,77,'form-vendor','elang-wijaya');
INSERT INTO `cms_entry_metas` VALUES (537,77,'form-warehouse','atom-wh');
INSERT INTO `cms_entry_metas` VALUES (538,77,'form-currency','USD');
INSERT INTO `cms_entry_metas` VALUES (548,66,'form-exhibition','jijf-2015-8th');
INSERT INTO `cms_entry_metas` VALUES (549,66,'form-rp_rate','13250');
INSERT INTO `cms_entry_metas` VALUES (740,67,'form-date','06/12/2015');
INSERT INTO `cms_entry_metas` VALUES (836,44,'form-vendor_barcode','1839');
INSERT INTO `cms_entry_metas` VALUES (835,44,'form-vendor_currency','HKD');
INSERT INTO `cms_entry_metas` VALUES (824,61,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (825,61,'form-exhibition','jijf-2015-8th');
INSERT INTO `cms_entry_metas` VALUES (583,68,'count-dv-payment','2');
INSERT INTO `cms_entry_metas` VALUES (602,78,'form-additional_charge','5.7');
INSERT INTO `cms_entry_metas` VALUES (603,78,'form-statement','Debit');
INSERT INTO `cms_entry_metas` VALUES (604,78,'form-amount','8000');
INSERT INTO `cms_entry_metas` VALUES (601,78,'form-diamond','012115_7187.20|100773_58.34');
INSERT INTO `cms_entry_metas` VALUES (600,78,'form-hkd_rate','7.25');
INSERT INTO `cms_entry_metas` VALUES (599,78,'form-type','Cash');
INSERT INTO `cms_entry_metas` VALUES (598,78,'form-date','06/18/2015');
INSERT INTO `cms_entry_metas` VALUES (606,79,'form-category','999 Simple (110%)');
INSERT INTO `cms_entry_metas` VALUES (716,80,'form-item_weight','3.47');
INSERT INTO `cms_entry_metas` VALUES (717,80,'form-vendor_x','110');
INSERT INTO `cms_entry_metas` VALUES (715,80,'form-product_brand','van-cleef');
INSERT INTO `cms_entry_metas` VALUES (714,80,'form-product_type','ring');
INSERT INTO `cms_entry_metas` VALUES (622,43,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (623,43,'form-product_status','STOCK');
INSERT INTO `cms_entry_metas` VALUES (820,61,'form-product_type','drf');
INSERT INTO `cms_entry_metas` VALUES (832,44,'form-barcode','1962');
INSERT INTO `cms_entry_metas` VALUES (831,44,'form-product_type','dpf');
INSERT INTO `cms_entry_metas` VALUES (826,61,'form-vendor_currency','USD');
INSERT INTO `cms_entry_metas` VALUES (671,39,'form-alamat','Jl. Darmahusada indah 57 ');
INSERT INTO `cms_entry_metas` VALUES (670,39,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (700,69,'form-total_weight','17.79');
INSERT INTO `cms_entry_metas` VALUES (697,69,'form-date','06/12/2015');
INSERT INTO `cms_entry_metas` VALUES (751,68,'form-total_pcs','25');
INSERT INTO `cms_entry_metas` VALUES (748,68,'form-date','06/12/2015');
INSERT INTO `cms_entry_metas` VALUES (701,69,'form-payment_balance','2.5');
INSERT INTO `cms_entry_metas` VALUES (910,27,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (911,27,'form-x_125','1.26');
INSERT INTO `cms_entry_metas` VALUES (912,27,'form-x_110','1.11');
INSERT INTO `cms_entry_metas` VALUES (718,80,'form-warehouse','atom-wh');
INSERT INTO `cms_entry_metas` VALUES (719,80,'form-product_status','STOCK');
INSERT INTO `cms_entry_metas` VALUES (720,80,'form-client_x','1.35');
INSERT INTO `cms_entry_metas` VALUES (906,27,'form-alamat','Apart Water Palace blok C no. 1912');
INSERT INTO `cms_entry_metas` VALUES (745,67,'form-gold_price','501500');
INSERT INTO `cms_entry_metas` VALUES (746,67,'form-total_weight','1250');
INSERT INTO `cms_entry_metas` VALUES (747,67,'form-payment_balance','200');
INSERT INTO `cms_entry_metas` VALUES (752,68,'form-total_item_sent','3');
INSERT INTO `cms_entry_metas` VALUES (753,68,'form-currency','HKD');
INSERT INTO `cms_entry_metas` VALUES (754,68,'form-hkd_rate','7.25');
INSERT INTO `cms_entry_metas` VALUES (755,68,'form-total_price','17000');
INSERT INTO `cms_entry_metas` VALUES (756,68,'form-payment_balance','1500');
INSERT INTO `cms_entry_metas` VALUES (757,82,'form-date','06/22/2015');
INSERT INTO `cms_entry_metas` VALUES (758,82,'form-statement','Debit');
INSERT INTO `cms_entry_metas` VALUES (759,82,'form-type','Cash');
INSERT INTO `cms_entry_metas` VALUES (760,82,'form-hkd_rate','7.25');
INSERT INTO `cms_entry_metas` VALUES (761,82,'form-diamond','012115_7187.20|100773_58.34');
INSERT INTO `cms_entry_metas` VALUES (762,82,'form-additional_charge','1.25');
INSERT INTO `cms_entry_metas` VALUES (763,82,'form-amount','7336.11');
INSERT INTO `cms_entry_metas` VALUES (764,82,'form-bank','bca-group');
INSERT INTO `cms_entry_metas` VALUES (765,82,'form-loan_period','3');
INSERT INTO `cms_entry_metas` VALUES (766,82,'form-loan_interest_rate','5.75');
INSERT INTO `cms_entry_metas` VALUES (767,82,'form-checks_status','Cek Lunas');
INSERT INTO `cms_entry_metas` VALUES (768,82,'form-checks_date','08/22/2015');
INSERT INTO `cms_entry_metas` VALUES (777,83,'form-additional_charge','1.25');
INSERT INTO `cms_entry_metas` VALUES (778,83,'form-payment_jewelry','10003906_3.47');
INSERT INTO `cms_entry_metas` VALUES (779,83,'form-amount','3.75');
INSERT INTO `cms_entry_metas` VALUES (780,83,'form-bank','mandiri');
INSERT INTO `cms_entry_metas` VALUES (800,86,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (799,86,'form-rp_rate','13250');
INSERT INTO `cms_entry_metas` VALUES (798,86,'form-type','Cash');
INSERT INTO `cms_entry_metas` VALUES (797,86,'form-statement','Credit');
INSERT INTO `cms_entry_metas` VALUES (796,86,'form-date','06/23/2015');
INSERT INTO `cms_entry_metas` VALUES (795,66,'count-dc-payment','1');
INSERT INTO `cms_entry_metas` VALUES (804,86,'form-checks_status','Cek Lunas');
INSERT INTO `cms_entry_metas` VALUES (803,86,'form-loan_interest_rate','5.75');
INSERT INTO `cms_entry_metas` VALUES (802,86,'form-bank','bca-group');
INSERT INTO `cms_entry_metas` VALUES (801,86,'form-amount','250');
INSERT INTO `cms_entry_metas` VALUES (805,86,'form-checks_date','06/18/2015');
INSERT INTO `cms_entry_metas` VALUES (806,67,'count-cc-payment','1');
INSERT INTO `cms_entry_metas` VALUES (807,87,'form-date','06/23/2015');
INSERT INTO `cms_entry_metas` VALUES (808,87,'form-statement','Debit');
INSERT INTO `cms_entry_metas` VALUES (809,87,'form-type','Cash');
INSERT INTO `cms_entry_metas` VALUES (810,87,'form-gold_price','501500');
INSERT INTO `cms_entry_metas` VALUES (811,87,'form-cor_jewelry','10003906_4.68|g123160_5.56');
INSERT INTO `cms_entry_metas` VALUES (812,87,'form-payment_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (813,87,'form-amount','15');
INSERT INTO `cms_entry_metas` VALUES (816,88,'image_type','jpg');
INSERT INTO `cms_entry_metas` VALUES (817,88,'image_size','5779');
INSERT INTO `cms_entry_metas` VALUES (818,89,'image_type','jpg');
INSERT INTO `cms_entry_metas` VALUES (819,89,'image_size','3306');
INSERT INTO `cms_entry_metas` VALUES (827,61,'form-vendor_barcode','22460');
INSERT INTO `cms_entry_metas` VALUES (828,61,'form-vendor_x','0.32');
INSERT INTO `cms_entry_metas` VALUES (829,61,'form-report_type','SR');
INSERT INTO `cms_entry_metas` VALUES (830,61,'form-client_x','0.29');
INSERT INTO `cms_entry_metas` VALUES (837,44,'form-vendor_x','0.230');
INSERT INTO `cms_entry_metas` VALUES (838,44,'form-report_type','SR');
INSERT INTO `cms_entry_metas` VALUES (839,90,'form-date','06/24/2015');
INSERT INTO `cms_entry_metas` VALUES (840,90,'form-statement','Debit');
INSERT INTO `cms_entry_metas` VALUES (841,90,'form-type','Cash');
INSERT INTO `cms_entry_metas` VALUES (842,90,'form-cor_jewelry','g123160_4.41');
INSERT INTO `cms_entry_metas` VALUES (843,90,'form-gold_loss','15');
INSERT INTO `cms_entry_metas` VALUES (844,90,'form-cost_currency','cny');
INSERT INTO `cms_entry_metas` VALUES (845,90,'form-gold_bar_rate','235.04');
INSERT INTO `cms_entry_metas` VALUES (846,90,'form-additional_cost','1500');
INSERT INTO `cms_entry_metas` VALUES (847,90,'form-payment_jewelry','10003906_3.47');
INSERT INTO `cms_entry_metas` VALUES (848,90,'form-amount','8');
INSERT INTO `cms_entry_metas` VALUES (849,90,'form-bank','hsbc-1');
INSERT INTO `cms_entry_metas` VALUES (850,90,'form-checks_status','Cek Titip');
INSERT INTO `cms_entry_metas` VALUES (851,90,'form-checks_date','06/20/2015');
INSERT INTO `cms_entry_metas` VALUES (905,27,'form-wholesaler','grace-novarinus');
INSERT INTO `cms_entry_metas` VALUES (904,27,'form-kategori','Retailer');
INSERT INTO `cms_entry_metas` VALUES (861,91,'form-date','06/25/2015');
INSERT INTO `cms_entry_metas` VALUES (862,91,'form-client','reyner-gunawan');
INSERT INTO `cms_entry_metas` VALUES (863,91,'form-wholesaler','grace-novarinus');
INSERT INTO `cms_entry_metas` VALUES (864,91,'form-sale_venue','Warehouse');
INSERT INTO `cms_entry_metas` VALUES (865,91,'form-warehouse','atom-wh');
INSERT INTO `cms_entry_metas` VALUES (866,91,'form-total_pcs','15');
INSERT INTO `cms_entry_metas` VALUES (867,91,'form-total_item_sent','2');
INSERT INTO `cms_entry_metas` VALUES (868,91,'form-gold_price','480500');
INSERT INTO `cms_entry_metas` VALUES (869,91,'form-sold_125','2.3');
INSERT INTO `cms_entry_metas` VALUES (870,91,'form-x_125','1.26');
INSERT INTO `cms_entry_metas` VALUES (871,91,'form-sold_110','3.1');
INSERT INTO `cms_entry_metas` VALUES (872,91,'form-x_110','1.11');
INSERT INTO `cms_entry_metas` VALUES (873,91,'form-sold_115','1.5');
INSERT INTO `cms_entry_metas` VALUES (874,91,'form-x_115','1.16');
INSERT INTO `cms_entry_metas` VALUES (875,91,'form-disc_adjustment','0.08');
INSERT INTO `cms_entry_metas` VALUES (876,91,'form-total_weight','8.00');
INSERT INTO `cms_entry_metas` VALUES (877,91,'form-additional_cost','1.5');
INSERT INTO `cms_entry_metas` VALUES (878,92,'form-date','06/25/2015');
INSERT INTO `cms_entry_metas` VALUES (879,92,'form-client','reyner-gunawan');
INSERT INTO `cms_entry_metas` VALUES (880,92,'form-wholesaler','grace-novarinus');
INSERT INTO `cms_entry_metas` VALUES (881,92,'form-sale_venue','Warehouse');
INSERT INTO `cms_entry_metas` VALUES (882,92,'form-warehouse','tunjungan-plaza-wh');
INSERT INTO `cms_entry_metas` VALUES (883,92,'form-total_pcs','5');
INSERT INTO `cms_entry_metas` VALUES (884,92,'form-total_item_sent','1');
INSERT INTO `cms_entry_metas` VALUES (885,92,'form-rp_rate','12800');
INSERT INTO `cms_entry_metas` VALUES (886,92,'form-disc_adjustment','7.5');
INSERT INTO `cms_entry_metas` VALUES (887,92,'form-total_price','250');
INSERT INTO `cms_entry_metas` VALUES (888,92,'form-payment_balance','20');
INSERT INTO `cms_entry_metas` VALUES (889,93,'form-alamat','Jl. Lebak Indah 59\r\nSurabaya, Indonesia');
INSERT INTO `cms_entry_metas` VALUES (890,93,'form-telepon','031 31 22 34');
INSERT INTO `cms_entry_metas` VALUES (891,93,'form-email','bejosugiantoro@yahoo.com');
INSERT INTO `cms_entry_metas` VALUES (892,93,'form-tgl_join','06/26/2015');
INSERT INTO `cms_entry_metas` VALUES (893,94,'form-alamat','Jl. Manyar Kertoadjo 171 C\r\nSurabaya, Indonesia');
INSERT INTO `cms_entry_metas` VALUES (894,94,'form-telepon','031 594 1234');
INSERT INTO `cms_entry_metas` VALUES (895,94,'form-email','soeharman@yahoo.com');
INSERT INTO `cms_entry_metas` VALUES (896,94,'form-tgl_join','01/29/2015');
INSERT INTO `cms_entry_metas` VALUES (901,28,'form-salesman','soeharman-prasetyo');
INSERT INTO `cms_entry_metas` VALUES (902,28,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (903,28,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (913,27,'form-x_115','1.16');
INSERT INTO `cms_entry_metas` VALUES (931,96,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (934,14,'form-warehouse_employee','');
INSERT INTO `cms_entry_metas` VALUES (937,15,'form-warehouse_employee','');
/*!40000 ALTER TABLE `cms_entry_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_roles`
--

DROP TABLE IF EXISTS `cms_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_roles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `description` text,
  `count` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_roles`
--

LOCK TABLES `cms_roles` WRITE;
/*!40000 ALTER TABLE `cms_roles` DISABLE KEYS */;
INSERT INTO `cms_roles` VALUES (1,'Super Admin','Administrator who has all access for the web without exceptions.',1);
INSERT INTO `cms_roles` VALUES (2,'Admin','Administrator from the clients.',NULL);
INSERT INTO `cms_roles` VALUES (3,'Regular User','Anyone with no access to admin panel.',NULL);
/*!40000 ALTER TABLE `cms_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_settings`
--

DROP TABLE IF EXISTS `cms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_settings`
--

LOCK TABLES `cms_settings` WRITE;
/*!40000 ALTER TABLE `cms_settings` DISABLE KEYS */;
INSERT INTO `cms_settings` VALUES (1,'title','WAN Signature');
INSERT INTO `cms_settings` VALUES (2,'tagline','');
INSERT INTO `cms_settings` VALUES (3,'description','Our Company Description here.');
INSERT INTO `cms_settings` VALUES (4,'date_format','d F Y');
INSERT INTO `cms_settings` VALUES (5,'time_format','h:i A');
INSERT INTO `cms_settings` VALUES (6,'header','');
INSERT INTO `cms_settings` VALUES (7,'top_insert','');
INSERT INTO `cms_settings` VALUES (8,'bottom_insert','');
INSERT INTO `cms_settings` VALUES (9,'google_analytics_code','');
INSERT INTO `cms_settings` VALUES (10,'display_width','120');
INSERT INTO `cms_settings` VALUES (11,'display_height','120');
INSERT INTO `cms_settings` VALUES (12,'display_crop','0');
INSERT INTO `cms_settings` VALUES (13,'thumb_width','120');
INSERT INTO `cms_settings` VALUES (14,'thumb_height','120');
INSERT INTO `cms_settings` VALUES (15,'thumb_crop','0');
INSERT INTO `cms_settings` VALUES (16,'language','en_english');
INSERT INTO `cms_settings` VALUES (17,'table_view','complex');
INSERT INTO `cms_settings` VALUES (18,'usd_sell','9732.00');
INSERT INTO `cms_settings` VALUES (19,'custom-pagination','10');
INSERT INTO `cms_settings` VALUES (20,'custom-email_contact','andybasuki88@gmail.com');
INSERT INTO `cms_settings` VALUES (21,'custom-overwrite_image','enable');
INSERT INTO `cms_settings` VALUES (22,'custom-bunga_cek','1.25');
/*!40000 ALTER TABLE `cms_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_type_metas`
--

DROP TABLE IF EXISTS `cms_type_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_type_metas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text,
  `input_type` varchar(500) DEFAULT NULL,
  `validation` text,
  `instruction` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1412 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_type_metas`
--

LOCK TABLES `cms_type_metas` WRITE;
/*!40000 ALTER TABLE `cms_type_metas` DISABLE KEYS */;
INSERT INTO `cms_type_metas` VALUES (5,4,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (4,4,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (550,4,'form-x_125','','text','is_numeric|','Nilai Sell X untuk produk COR Italy (125%)');
INSERT INTO `cms_type_metas` VALUES (549,4,'form-diamond_sell_x','','text','is_numeric|','Nilai Sell X untuk produk diamond.');
INSERT INTO `cms_type_metas` VALUES (548,4,'form-exhibition','','multibrowse','','Client pernah ambil produk dari pameran mana saja.');
INSERT INTO `cms_type_metas` VALUES (547,4,'form-warehouse','','multibrowse','','Client pernah ambil produk dari WH mana saja.');
INSERT INTO `cms_type_metas` VALUES (546,4,'form-salesman','','browse','','Sales(wo)man yang melayani client ini.');
INSERT INTO `cms_type_metas` VALUES (545,4,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (544,4,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (543,4,'form-alamat','','textarea','','Alamat pribadi / toko pelanggan.');
INSERT INTO `cms_type_metas` VALUES (19,5,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (20,5,'title_key','Kode Vendor',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1341,14,'form-carat','','textarea','','Kadar carat DIAMOND pada produk ini.<br><span style=\'color:red;\'>NB: Tekan <strong>Enter</strong> untuk memisahkan carat 1 dengan lainnya.</span>');
INSERT INTO `cms_type_metas` VALUES (22,5,'form-alamat','','textarea','','Alamat perusahaan vendor.');
INSERT INTO `cms_type_metas` VALUES (23,5,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (24,5,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (25,5,'form-capital_x','','text','is_numeric|','Nilai Capital X untuk produk diamond.');
INSERT INTO `cms_type_metas` VALUES (26,6,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (27,7,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (28,7,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (29,7,'form-alamat','','textarea','','Alamat lengkap tempat tinggal.');
INSERT INTO `cms_type_metas` VALUES (30,7,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (31,7,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (32,7,'form-tgl_join','','datepicker','','First time working date at WAN Signature.');
INSERT INTO `cms_type_metas` VALUES (33,8,'category','tools',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (34,8,'title_key','Currency',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (35,8,'form-rate_value','','text','not_empty|is_numeric|','Harga nominal kurs per $1 USD.');
INSERT INTO `cms_type_metas` VALUES (36,9,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (37,9,'title_key','Nama',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (38,9,'form-alamat','','textarea','','Alamat lengkap posisi WH berada.');
INSERT INTO `cms_type_metas` VALUES (39,9,'form-telepon','','text','','Nomer Telp WH yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (40,9,'form-warehouse_employee','','multibrowse','','Pegawai yg bertanggung jawab untuk WH ini.');
INSERT INTO `cms_type_metas` VALUES (41,10,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (42,11,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (43,12,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (44,13,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (45,14,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (46,14,'title_key','Serial Number',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1351,14,'form-vendor_currency','USD\r\nHKD','radio','not_empty|','Vendor Price in USD / HKD.');
INSERT INTO `cms_type_metas` VALUES (1352,14,'form-vendor_barcode','','text','is_numeric|not_empty|','Vendor original price.');
INSERT INTO `cms_type_metas` VALUES (1353,14,'form-vendor_x','','text','is_numeric|','Vendor capital X for calculating price.');
INSERT INTO `cms_type_metas` VALUES (1354,14,'form-vendor_usd','','text','is_numeric|','Vendor USD price result.');
INSERT INTO `cms_type_metas` VALUES (1355,14,'form-vendor_hkd','','text','is_numeric|','Vendor HKD price result.');
INSERT INTO `cms_type_metas` VALUES (1356,14,'form-report_date','','datepicker','','');
INSERT INTO `cms_type_metas` VALUES (1357,14,'form-report_type','SR\r\nRR','radio','not_empty|','Sold Report / Return Report.');
INSERT INTO `cms_type_metas` VALUES (1358,14,'form-temp_report','','text','','Temporary report date / notes.');
INSERT INTO `cms_type_metas` VALUES (1359,14,'form-return_date','','datepicker','','When this product returned to vendor.');
INSERT INTO `cms_type_metas` VALUES (1360,14,'form-return_detail','','textarea','','Return information detail.');
INSERT INTO `cms_type_metas` VALUES (1361,14,'form-omzet','','text','','Produk masuk omzet mana dan kapan.');
INSERT INTO `cms_type_metas` VALUES (1362,14,'form-client_invoice_code','','browse','','Kode invoice untuk pihak client.');
INSERT INTO `cms_type_metas` VALUES (1363,14,'form-client_invoice_date','','datepicker','','Sold date to client.');
INSERT INTO `cms_type_metas` VALUES (133,15,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (134,15,'title_key','Nama',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (138,15,'form-end_date','','datepicker','','Tanggal terakhir pameran ini berlangsung.');
INSERT INTO `cms_type_metas` VALUES (137,15,'form-start_date','','datepicker','','Kapan pameran ini mulai berlangsung.');
INSERT INTO `cms_type_metas` VALUES (139,15,'form-alamat','','textarea','','Alamat lengkap di mana pameran diadakan.');
INSERT INTO `cms_type_metas` VALUES (140,15,'form-telepon','','text','','Nomer Telp stand pameran yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (141,15,'form-warehouse_employee','','multibrowse','','Pegawai yg bertanggung jawab untuk pengadaan event pameran ini.');
INSERT INTO `cms_type_metas` VALUES (142,16,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1382,17,'form-item_weight','','text','is_numeric|not_empty|','Berat produk cor dalam satuan <strong>gram</strong>.');
INSERT INTO `cms_type_metas` VALUES (1364,14,'form-client','','browse','','Who purchase this product.');
INSERT INTO `cms_type_metas` VALUES (1365,14,'form-client_x','','text','is_numeric|','Client sell X value.');
INSERT INTO `cms_type_metas` VALUES (1366,14,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (1367,14,'form-salesman','','browse','','Sales(wo)man yg berhasil menjual produk ini kepada klien WAN.');
INSERT INTO `cms_type_metas` VALUES (1368,14,'form-total_sold_price','','text','is_numeric|','Total sold price to client in USD.');
INSERT INTO `cms_type_metas` VALUES (539,12,'form-category','Diamond\r\nItaly (125%)\r\nKorea (100%)\r\n999 Simple (110%)\r\n999 3D (115%)','dropdown','not_empty|','Category group for this product type.');
INSERT INTO `cms_type_metas` VALUES (553,4,'form-x_115','','text','is_numeric|','Nilai Sell X untuk produk COR 999 3D (115%)');
INSERT INTO `cms_type_metas` VALUES (551,4,'form-x_100','','text','is_numeric|','Nilai Sell X untuk produk COR Korea (100%)');
INSERT INTO `cms_type_metas` VALUES (552,4,'form-x_110','','text','is_numeric|','Nilai Sell X untuk produk COR 999 Simple (110%)');
INSERT INTO `cms_type_metas` VALUES (264,17,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (265,17,'title_key','Item Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1395,17,'form-client','','browse','','Who purchase this product.');
INSERT INTO `cms_type_metas` VALUES (1396,17,'form-client_x','','text','is_numeric|','Client sell X value.');
INSERT INTO `cms_type_metas` VALUES (1397,17,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (1398,17,'form-salesman','','browse','','Sales(wo)man yg berhasil menjual produk ini kepada klien WAN.');
INSERT INTO `cms_type_metas` VALUES (1399,17,'form-client_invoice_pcs','','text','','Total pcs of jewelries sold on this client invoice.');
INSERT INTO `cms_type_metas` VALUES (1400,17,'form-client_invoice_disc','','text','is_numeric|','Weight discount adjustment on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (1401,17,'form-client_invoice_sold_24k','','text','is_numeric|','Total weight of jewelries sold on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (1402,17,'form-gold_price','','text','is_numeric|','Current gold price per Gram (IDR).');
INSERT INTO `cms_type_metas` VALUES (1403,17,'form-payment_ct_ld','','textarea','','Payment from client using CT (local material) & LD.');
INSERT INTO `cms_type_metas` VALUES (1404,17,'form-payment_rosok','','textarea','','Payment from client using rosok item.');
INSERT INTO `cms_type_metas` VALUES (1405,17,'form-payment_checks','','textarea','','Payment from client using bank checks.');
INSERT INTO `cms_type_metas` VALUES (1369,14,'form-sold_price_usd','','text','is_numeric|','Sold price paid in USD.');
INSERT INTO `cms_type_metas` VALUES (1370,14,'form-sold_price_rp','','text','is_numeric|','Sold price paid in IDR.');
INSERT INTO `cms_type_metas` VALUES (1383,17,'form-item_size','','text','is_numeric|','Ukuran produk cor.');
INSERT INTO `cms_type_metas` VALUES (1384,17,'form-vendor_invoice_code','','browse','','Kode invoice dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (1385,17,'form-vendor','','browse','','Pihak vendor yang menyediakan produk ini.');
INSERT INTO `cms_type_metas` VALUES (1386,17,'form-vendor_x','','text','is_numeric|','Product X for this vendor.');
INSERT INTO `cms_type_metas` VALUES (1387,17,'form-vendor_pcs','','text','is_numeric|','Total pcs of jewelries purchased on this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (1388,17,'form-vendor_gr','','text','','Total weight of jewelries purchased on this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (372,18,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (373,18,'title_key','Nama',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (864,18,'form-logistic_type','Supporting\r\nSouvenir','radio','not_empty|','Apakah termasuk barang pelengkap / pendukung atau souvenir.');
INSERT INTO `cms_type_metas` VALUES (865,18,'form-warehouse','','multibrowse','','Pencatatan stok barang di berbagai gudang tertentu.');
INSERT INTO `cms_type_metas` VALUES (866,18,'form-exhibition','','multibrowse','','Pencatatan stok barang yang dibawa ke berbagai pameran tertentu untuk sementara waktu.');
INSERT INTO `cms_type_metas` VALUES (379,20,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (380,20,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1155,20,'form-hkd_rate','','text','is_numeric|not_empty|','HKD rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (1154,20,'form-currency','USD\r\nHKD','radio','not_empty|','Vendor Price in USD or HKD.');
INSERT INTO `cms_type_metas` VALUES (1153,20,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (1152,20,'form-total_pcs','','text','is_numeric|not_empty|','Total pcs of diamond purchased.');
INSERT INTO `cms_type_metas` VALUES (409,21,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (410,21,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1164,21,'form-total_weight','','text','is_numeric|not_empty|','Total weight of jewelries purchased (gram).');
INSERT INTO `cms_type_metas` VALUES (1163,21,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (1162,21,'form-total_pcs','','text','is_numeric|not_empty|','Total pcs of jewelries purchased.');
INSERT INTO `cms_type_metas` VALUES (1160,21,'form-vendor','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (1161,21,'form-warehouse','','browse','not_empty|','Gudang tempat penerimaan produk cor yang dikirim vendor.');
INSERT INTO `cms_type_metas` VALUES (1159,21,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (418,22,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (419,22,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (542,4,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (541,4,'form-kategori','End User\r\nRetailer\r\nWholesaler','radio','not_empty|','Tingkatan kategori pelanggan.');
INSERT INTO `cms_type_metas` VALUES (540,4,'form-kode_pelanggan','','text','','Kode singkat unik pelanggan.');
INSERT INTO `cms_type_metas` VALUES (1350,14,'form-vendor_note','','text','','Additional information for this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (1349,14,'form-vendor_status','','text','','Product status with vendor (Sold / Credit / Consignment / Return / Syute / etc.)');
INSERT INTO `cms_type_metas` VALUES (1348,14,'form-vendor_item_code','','text','','Kode produk asal dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (1381,17,'form-product_brand','','browse','','Merk produk cor.');
INSERT INTO `cms_type_metas` VALUES (1380,17,'form-product_type','','browse','not_empty|','Tipe produk cor.');
INSERT INTO `cms_type_metas` VALUES (1257,22,'form-warehouse','','browse','','Gudang tempat pengiriman produk diamond kepada client.');
INSERT INTO `cms_type_metas` VALUES (1258,22,'form-exhibition','','browse','','Tempat pameran di mana produk terjual.');
INSERT INTO `cms_type_metas` VALUES (1259,22,'form-total_pcs','','text','is_numeric|not_empty|','Total pcs of diamond sold.');
INSERT INTO `cms_type_metas` VALUES (1260,22,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (1261,22,'form-rp_rate','','text','is_numeric|not_empty|','IDR rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (1150,20,'form-vendor','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (1151,20,'form-warehouse','','browse','not_empty|','Gudang tempat penerimaan produk diamond yang dikirim vendor.');
INSERT INTO `cms_type_metas` VALUES (1256,22,'form-sale_venue','Warehouse\r\nExhibition','radio','not_empty|','Apakah penjualan terjadi dari warehouse atau exhibition (pameran).');
INSERT INTO `cms_type_metas` VALUES (626,23,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (627,23,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1276,23,'form-sold_125','','text','is_numeric|','Weight of <strong>SOLD 125</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (1277,23,'form-x_125','','text','is_numeric|','<strong>X 125</strong> factor (ITALY).');
INSERT INTO `cms_type_metas` VALUES (1278,23,'form-sold_100','','text','is_numeric|','Weight of <strong>SOLD 100</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (1279,23,'form-x_100','','text','is_numeric|','<strong>X 100</strong> factor (KOREA).');
INSERT INTO `cms_type_metas` VALUES (1280,23,'form-sold_110','','text','is_numeric|','Weight of <strong>SOLD 110</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (1389,17,'form-warehouse','','browse','','Gudang di mana produk tersimpan.');
INSERT INTO `cms_type_metas` VALUES (1390,17,'form-stock_date','','datepicker','','Kapan terakhir produk ini masuk ke WH sbg ready stock.');
INSERT INTO `cms_type_metas` VALUES (1391,17,'form-exhibition','','browse','','Exhibition di mana produk ini sedang dipamerkan.');
INSERT INTO `cms_type_metas` VALUES (1392,17,'form-product_status','','text','','Current product status (Stock / Sold / Consignment / Return / Changed / etc.)');
INSERT INTO `cms_type_metas` VALUES (1393,17,'form-client_invoice_code','','browse','','Kode invoice untuk pihak client.');
INSERT INTO `cms_type_metas` VALUES (1394,17,'form-client_invoice_date','','datepicker','','Sold date to client.');
INSERT INTO `cms_type_metas` VALUES (1275,23,'form-gold_price','','text','is_numeric|not_empty|','Current gold price per Gram (IDR).');
INSERT INTO `cms_type_metas` VALUES (1274,23,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (1273,23,'form-total_pcs','','text','is_numeric|not_empty|','Total pcs of jewelries sold.');
INSERT INTO `cms_type_metas` VALUES (1272,23,'form-exhibition','','browse','','Tempat pameran di mana produk terjual.');
INSERT INTO `cms_type_metas` VALUES (1271,23,'form-warehouse','','browse','','Gudang tempat pengiriman produk cor kepada client.');
INSERT INTO `cms_type_metas` VALUES (1255,22,'form-salesman','','browse','','Sales(wo)man doing diamond sale for this invoice.');
INSERT INTO `cms_type_metas` VALUES (1346,14,'form-vendor_invoice_date','','datepicker','','Purchase date from vendor.');
INSERT INTO `cms_type_metas` VALUES (1345,14,'form-vendor_invoice_code','','browse','','Kode invoice dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (1344,14,'form-item_ref_code','','textarea','','Item Reference Code.<br><span style=\'color:red;\'>NB: Tekan <strong>Enter</strong> untuk memisahkan IRC 1 dengan lainnya.</span>');
INSERT INTO `cms_type_metas` VALUES (1343,14,'form-gold_weight','','text','is_numeric|','Berat emas yg terkandung pada produk (gram).');
INSERT INTO `cms_type_metas` VALUES (1342,14,'form-gold_carat','','text','','Kadar carat GOLD pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (844,24,'category','tools',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (845,24,'title_key','Document Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (846,24,'form-date','','datepicker','not_empty|','Tanggal kirim / jalan.');
INSERT INTO `cms_type_metas` VALUES (847,24,'form-delivery_type','Warehouse To Warehouse\r\nWarehouse To Exhibition\r\nExhibition To Warehouse\r\nExhibition To Exhibition\r\nSouvenir\r\nDiamond Sale\r\nCor Jewelry Sale\r\nDiamond Return\r\nCor Jewelry Return','dropdown','not_empty|','What type of delivery is this document for.');
INSERT INTO `cms_type_metas` VALUES (848,24,'form-dmd_client_invoice','','browse','','Diamond Sale Invoice.');
INSERT INTO `cms_type_metas` VALUES (849,24,'form-cor_client_invoice','','browse','','Cor Jewelry Sale Invoice.');
INSERT INTO `cms_type_metas` VALUES (850,24,'form-client','','browse','','Client destination to sent.');
INSERT INTO `cms_type_metas` VALUES (851,24,'form-dmd_vendor_invoice','','browse','','Diamond Return Invoice.');
INSERT INTO `cms_type_metas` VALUES (852,24,'form-cor_vendor_invoice','','browse','','Cor Jewelry Return Invoice.');
INSERT INTO `cms_type_metas` VALUES (853,24,'form-vendor','','browse','','Vendor destination to sent.');
INSERT INTO `cms_type_metas` VALUES (854,24,'form-warehouse_origin','','browse','','Gudang asal tempat pengambilan barang.');
INSERT INTO `cms_type_metas` VALUES (855,24,'form-exhibition_origin','','browse','','Pameran asal tempat pengambilan barang.');
INSERT INTO `cms_type_metas` VALUES (856,24,'form-warehouse_destination','','browse','','Gudang tujuan pengiriman barang.');
INSERT INTO `cms_type_metas` VALUES (857,24,'form-exhibition_destination','','browse','','Pameran tujuan pengiriman barang.');
INSERT INTO `cms_type_metas` VALUES (858,24,'form-diamond','','multibrowse','','Produk diamond yang hendak dikirim.');
INSERT INTO `cms_type_metas` VALUES (859,24,'form-cor_jewelry','','multibrowse','','Produk perhiasan cor yang hendak dikirim.');
INSERT INTO `cms_type_metas` VALUES (860,24,'form-logistic','','multibrowse','','Barang logistik maupun souvenir yang hendak dikirim.');
INSERT INTO `cms_type_metas` VALUES (1371,14,'form-rp_rate','','text','is_numeric|','IDR rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (1156,20,'form-total_price','','text','is_numeric|not_empty|','Total price of diamond purchased (USD).');
INSERT INTO `cms_type_metas` VALUES (1372,14,'form-payment_credit_card','','textarea','','Payment from client using credit card.');
INSERT INTO `cms_type_metas` VALUES (1373,14,'form-payment_cicilan','','textarea','','Payment from client using bank installment (HSBC / PERMATA / CITI) 3 / 6 / 12 months.');
INSERT INTO `cms_type_metas` VALUES (1374,14,'form-payment_cash','','textarea','','Payment from client using cash / bank transfer / debit card.');
INSERT INTO `cms_type_metas` VALUES (1375,14,'form-payment_checks','','textarea','','Payment from client using bank checks.');
INSERT INTO `cms_type_metas` VALUES (1376,14,'form-payment_balance','','text','','Total payment balance on this client invoice.');
INSERT INTO `cms_type_metas` VALUES (1377,14,'form-prev_sold_price','','text','','Previous sold price.');
INSERT INTO `cms_type_metas` VALUES (1378,14,'form-prev_barcode','','text','','Previous barcode / price tag.');
INSERT INTO `cms_type_metas` VALUES (934,27,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (935,27,'title_key','Keterangan',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1206,27,'form-hkd_rate','','text','is_numeric|not_empty|','HKD rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (1207,27,'form-diamond','','multibrowse','','Paid <span style=\'color:red;\'>(return)</span> diamond product (with price result in USD).');
INSERT INTO `cms_type_metas` VALUES (1208,27,'form-additional_charge','','text','is_numeric|','NB: Just for payment using credit card.');
INSERT INTO `cms_type_metas` VALUES (1209,27,'form-amount','','text','is_numeric|','Total price paid by this transaction (USD).');
INSERT INTO `cms_type_metas` VALUES (1210,27,'form-bank','','browse','','Used bank for this transaction.');
INSERT INTO `cms_type_metas` VALUES (1211,27,'form-loan_period','','text','is_numeric|','Lama periode pinjaman (months).');
INSERT INTO `cms_type_metas` VALUES (1212,27,'form-loan_interest_rate','','text','is_numeric|','Suku bunga kredit dari pihak bank yang memberi pinjaman.');
INSERT INTO `cms_type_metas` VALUES (969,6,'form-loan_interest_rate','','text','is_numeric|','Suku bunga kredit dari pihak bank.');
INSERT INTO `cms_type_metas` VALUES (1205,27,'form-type','Cash\r\nCredit Card\r\nCicilan\r\nChecks','dropdown','not_empty|','Type of payment.');
INSERT INTO `cms_type_metas` VALUES (1203,27,'form-date','','datepicker','not_empty|','Transaction date.');
INSERT INTO `cms_type_metas` VALUES (1204,27,'form-statement','Debit\r\nCredit','radio','not_empty|','Debit untuk pembayaran hutang / menambah beban retur.<br>Credit untuk menambah beban hutang / pembayaran retur.');
INSERT INTO `cms_type_metas` VALUES (982,28,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (983,28,'title_key','Keterangan',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1225,28,'form-amount','','text','is_numeric|','Total weight of jewelries paid by this transaction <span style=\'color:red;\'>(EXCLUDE payment jewelry).</span>');
INSERT INTO `cms_type_metas` VALUES (1224,28,'form-payment_jewelry','','multibrowse','','Produk perhiasan yg dipakai sebagai alat pembayaran transaksi ini (sistem barter).');
INSERT INTO `cms_type_metas` VALUES (1223,28,'form-additional_charge','','text','is_numeric|','NB: Just for payment using credit card.');
INSERT INTO `cms_type_metas` VALUES (1221,28,'form-gold_bar_rate','','text','is_numeric|','Selected cost currency rate value per 1 gram Gold Bar.');
INSERT INTO `cms_type_metas` VALUES (1222,28,'form-additional_cost','','text','is_numeric|','Tambahan ongkos yang dibebankan dari vendor dalam satuan currency terpilih (ongkos kerja, pasang, dll).');
INSERT INTO `cms_type_metas` VALUES (1220,28,'form-cost_currency','','browse','','Cost currency from vendor side.');
INSERT INTO `cms_type_metas` VALUES (1219,28,'form-gold_loss','','text','is_numeric|','Nilai prosentase susut dari produk cor yang dibayarkan.');
INSERT INTO `cms_type_metas` VALUES (1218,28,'form-cor_jewelry','','multibrowse','','Paid <span style=\'color:red;\'>(return)</span> cor jewelry product (with price result in gram weight).');
INSERT INTO `cms_type_metas` VALUES (1217,28,'form-type','Cash\r\nCT LD\r\nRosok\r\nChecks\r\nCredit Card\r\nReturn Goods','dropdown','not_empty|','Type of payment.');
INSERT INTO `cms_type_metas` VALUES (1215,28,'form-date','','datepicker','not_empty|','Transaction date.');
INSERT INTO `cms_type_metas` VALUES (1216,28,'form-statement','Debit\r\nCredit','radio','not_empty|','Debit untuk pembayaran hutang / menambah beban retur.<br>Credit untuk menambah beban hutang / pembayaran retur.');
INSERT INTO `cms_type_metas` VALUES (1149,20,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (1262,22,'form-disc_adjustment','','text','is_numeric|','Special discount adjustment for this invoice (USD).');
INSERT INTO `cms_type_metas` VALUES (1270,23,'form-sale_venue','Warehouse\r\nExhibition','radio','not_empty|','Apakah penjualan terjadi dari warehouse atau exhibition (pameran).');
INSERT INTO `cms_type_metas` VALUES (1040,29,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1041,29,'title_key','Keterangan',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1238,29,'form-loan_interest_rate','','text','is_numeric|','Suku bunga kredit dari pihak bank yang memberi pinjaman.');
INSERT INTO `cms_type_metas` VALUES (1237,29,'form-loan_period','','text','is_numeric|','Lama periode pinjaman (months).');
INSERT INTO `cms_type_metas` VALUES (1236,29,'form-bank','','browse','','Used bank for this transaction.');
INSERT INTO `cms_type_metas` VALUES (1235,29,'form-amount','','text','is_numeric|','Total price paid by this transaction (USD).');
INSERT INTO `cms_type_metas` VALUES (1234,29,'form-additional_charge','','text','is_numeric|','NB: Just for payment using credit card.');
INSERT INTO `cms_type_metas` VALUES (1233,29,'form-diamond','','multibrowse','','Paid <span style=\'color:red;\'>(return)</span> diamond product (with price result in USD).');
INSERT INTO `cms_type_metas` VALUES (1232,29,'form-rp_rate','','text','not_empty|is_numeric|','IDR rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (1231,29,'form-type','Cash\r\nCredit Card\r\nCicilan\r\nChecks','dropdown','not_empty|','Type of payment.');
INSERT INTO `cms_type_metas` VALUES (1229,29,'form-date','','datepicker','not_empty|','Transaction date.');
INSERT INTO `cms_type_metas` VALUES (1230,29,'form-statement','Credit\r\nDebit','radio','not_empty|','Credit untuk pembayaran piutang / menambah beban retur.<br>Debit untuk menambah beban piutang / pembayaran retur.');
INSERT INTO `cms_type_metas` VALUES (1054,30,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1055,30,'title_key','Keterangan',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (1249,30,'form-bank','','browse','','Used bank for this transaction.');
INSERT INTO `cms_type_metas` VALUES (1248,30,'form-amount','','text','is_numeric|','Total weight of jewelries paid by this transaction <span style=\'color:red;\'>(EXCLUDE payment jewelry).</span>');
INSERT INTO `cms_type_metas` VALUES (1247,30,'form-payment_jewelry','','multibrowse','','Produk perhiasan yg dipakai sebagai alat pembayaran transaksi ini (sistem barter).');
INSERT INTO `cms_type_metas` VALUES (1246,30,'form-additional_charge','','text','is_numeric|','NB: Just for payment using credit card.');
INSERT INTO `cms_type_metas` VALUES (1245,30,'form-cor_jewelry','','multibrowse','','Paid <span style=\'color:red;\'>(return)</span> cor jewelry product (with price result in gram weight).');
INSERT INTO `cms_type_metas` VALUES (1244,30,'form-gold_price','','text','not_empty|is_numeric|','Current gold price per Gram (IDR).');
INSERT INTO `cms_type_metas` VALUES (1243,30,'form-type','Cash\r\nCT LD\r\nRosok\r\nChecks\r\nCredit Card\r\nReturn Goods','dropdown','not_empty|','Type of payment.');
INSERT INTO `cms_type_metas` VALUES (1241,30,'form-date','','datepicker','not_empty|','Transaction date.');
INSERT INTO `cms_type_metas` VALUES (1242,30,'form-statement','Credit\r\nDebit','radio','not_empty|','Credit untuk pembayaran piutang / menambah beban retur.<br>Debit untuk menambah beban piutang / pembayaran retur.');
INSERT INTO `cms_type_metas` VALUES (1281,23,'form-x_110','','text','is_numeric|','<strong>X 110</strong> factor (999 SIMPLE).');
INSERT INTO `cms_type_metas` VALUES (1282,23,'form-sold_115','','text','is_numeric|','Weight of <strong>SOLD 115</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (1283,23,'form-x_115','','text','is_numeric|','<strong>X 115</strong> factor (999 3D).');
INSERT INTO `cms_type_metas` VALUES (1284,23,'form-disc_adjustment','','text','is_numeric|','Special discount adjustment for this invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (1157,20,'form-payment_balance','','text','is_numeric|','Current payment balance to vendor (USD).');
INSERT INTO `cms_type_metas` VALUES (1165,21,'form-payment_balance','','text','is_numeric|','Current payment balance to vendor (gram).');
INSERT INTO `cms_type_metas` VALUES (1253,22,'form-client','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (1254,22,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (1252,22,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (1269,23,'form-salesman','','browse','','Sales(wo)man doing jewelry sale for this invoice.');
INSERT INTO `cms_type_metas` VALUES (1267,23,'form-client','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (1268,23,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (1266,23,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (1213,27,'form-checks_status','Cek Lunas\r\nCek Titip','radio','','NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1214,27,'form-checks_date','','datepicker','','When due date of this checks.<br>NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1226,28,'form-bank','','browse','','Used bank for this transaction.');
INSERT INTO `cms_type_metas` VALUES (1227,28,'form-checks_status','Cek Lunas\r\nCek Titip','radio','','NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1228,28,'form-checks_date','','datepicker','','When due date of this checks.<br>NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1239,29,'form-checks_status','Cek Lunas\r\nCek Titip','radio','','NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1240,29,'form-checks_date','','datepicker','','When due date of this checks.<br>NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1250,30,'form-checks_status','Cek Lunas\r\nCek Titip','radio','','NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1251,30,'form-checks_date','','datepicker','','When due date of this checks.<br>NB: Just for payment using checks.');
INSERT INTO `cms_type_metas` VALUES (1263,22,'form-total_price','','text','is_numeric|not_empty|','Total price of diamond sold <strong>after discount adjustment</strong> (USD).');
INSERT INTO `cms_type_metas` VALUES (1264,22,'form-payment_balance','','text','is_numeric|','Current payment balance from client (USD).');
INSERT INTO `cms_type_metas` VALUES (1265,22,'form-additional_cost','','text','is_numeric|','Total additional cost for this invoice (USD).');
INSERT INTO `cms_type_metas` VALUES (1285,23,'form-total_weight','','text','is_numeric|not_empty|','Total weight of jewelries sold <strong>after discount adjustment</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (1286,23,'form-payment_balance','','text','is_numeric|','Current payment balance from client (gram).');
INSERT INTO `cms_type_metas` VALUES (1287,23,'form-additional_cost','','text','is_numeric|','Total additional cost for this invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (1347,14,'form-vendor','','browse','','Pihak vendor yang menyediakan produk ini.');
INSERT INTO `cms_type_metas` VALUES (1340,14,'form-exhibition','','browse','','Exhibition di mana produk ini sedang dipamerkan.');
INSERT INTO `cms_type_metas` VALUES (1339,14,'form-stock_date','','datepicker','','Kapan terakhir produk ini masuk ke WH sbg ready stock.');
INSERT INTO `cms_type_metas` VALUES (1338,14,'form-warehouse','','browse','','Gudang di mana produk tersimpan.');
INSERT INTO `cms_type_metas` VALUES (1337,14,'form-product_status','','text','','Current product status (Stock / Sold / Consignment / Return / Changed / etc.)');
INSERT INTO `cms_type_metas` VALUES (1336,14,'form-sell_barcode','','text','is_numeric|','Adjusted product price tag (USD).');
INSERT INTO `cms_type_metas` VALUES (1335,14,'form-barcode','','text','is_numeric|not_empty|','Product price tag (USD).');
INSERT INTO `cms_type_metas` VALUES (1334,14,'form-product_type','','browse','not_empty|','Tipe produk berlian.');
INSERT INTO `cms_type_metas` VALUES (1379,14,'form-prev_sold_note','','textarea','','Previous sold note for transaction history.');
INSERT INTO `cms_type_metas` VALUES (1406,17,'form-payment_cash','','textarea','','Payment from client using cash / bank transfer / debit card.');
INSERT INTO `cms_type_metas` VALUES (1407,17,'form-payment_credit_card','','textarea','','Payment from client using credit card.');
INSERT INTO `cms_type_metas` VALUES (1408,17,'form-payment_return_goods','','textarea','','Payment from client using return goods.');
INSERT INTO `cms_type_metas` VALUES (1409,17,'form-total_payment_24k','','text','','Total payment 24K that should be paid by client (gram).');
INSERT INTO `cms_type_metas` VALUES (1410,17,'form-payment_balance','','text','','Total payment balance on this client invoice.');
INSERT INTO `cms_type_metas` VALUES (1411,17,'form-transaction_history','','textarea','','');
/*!40000 ALTER TABLE `cms_type_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_types`
--

DROP TABLE IF EXISTS `cms_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `description` text,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_types`
--

LOCK TABLES `cms_types` WRITE;
/*!40000 ALTER TABLE `cms_types` DISABLE KEYS */;
INSERT INTO `cms_types` VALUES (1,'Media Library','media','All media image is stored here.',0,0,'2013-01-15 03:35:14',1,'2013-01-15 03:35:14',1);
INSERT INTO `cms_types` VALUES (4,'Client','client','Daftar pelanggan WAN Signature, baik berupa Toko maupun End User.',0,0,'2015-06-02 22:37:24',1,'2015-06-08 16:28:54',1);
INSERT INTO `cms_types` VALUES (5,'Vendor','vendor','Daftar vendor WAN Signature.',0,0,'2015-06-02 23:23:54',1,'2015-06-02 23:23:54',1);
INSERT INTO `cms_types` VALUES (6,'Bank','bank','Daftar bank yang bekerja sama dengan WAN Signature.',0,0,'2015-06-02 23:42:49',1,'2015-06-14 14:03:42',1);
INSERT INTO `cms_types` VALUES (7,'Salesman','salesman','Sales(wo)man yang bertugas melayani client WAN Signature.',0,0,'2015-06-03 10:55:24',1,'2015-06-03 10:55:24',1);
INSERT INTO `cms_types` VALUES (8,'USD Rate','usd-rate','Live Exchange Rate (terhadap $ USD)',0,0,'2015-06-03 15:39:44',1,'2015-06-03 15:39:44',1);
INSERT INTO `cms_types` VALUES (9,'Warehouse','warehouse','Daftar Gudang tempat penyimpanan produk WAN Signature beserta barang Logistic / Pelengkap.',0,2,'2015-06-03 22:26:59',1,'2015-06-03 22:38:14',1);
INSERT INTO `cms_types` VALUES (10,'History Masuk','history-masuk','Seluruh pencatatan history barang yg masuk ke warehouse ini.',9,0,'2015-06-03 22:36:32',1,'2015-06-03 22:36:32',1);
INSERT INTO `cms_types` VALUES (11,'History Keluar','history-keluar','Seluruh pencatatan history barang yg keluar dari warehouse ini.',9,0,'2015-06-03 22:38:14',1,'2015-06-03 22:38:14',1);
INSERT INTO `cms_types` VALUES (12,'Product Type','product-type','Berbagai macam tipe produk WAN Signature.',0,0,'2015-06-03 23:22:25',1,'2015-06-08 16:28:09',1);
INSERT INTO `cms_types` VALUES (13,'Product Brand','product-brand','Berbagai macam merk produk WAN Signature.',0,0,'2015-06-03 23:30:45',1,'2015-06-03 23:30:45',1);
INSERT INTO `cms_types` VALUES (14,'Diamond','diamond','Diamond product variations by WAN Signature.',0,0,'2015-06-04 15:43:39',1,'2015-06-28 13:27:58',1);
INSERT INTO `cms_types` VALUES (15,'Exhibition','exhibition','Data lengkap pameran yang diadakan oleh WAN Signature.',0,1,'2015-06-05 11:06:28',1,'2015-06-05 14:20:27',1);
INSERT INTO `cms_types` VALUES (16,'Showpiece','showpiece','Produk WAN Signature yang dipamerkan dalam exhibition ini.',15,0,'2015-06-05 14:20:27',1,'2015-06-05 14:20:27',1);
INSERT INTO `cms_types` VALUES (17,'Cor Jewelry','cor-jewelry','Cor jewelry produced by WAN Signature.',0,0,'2015-06-06 00:27:13',1,'2015-06-28 13:29:07',1);
INSERT INTO `cms_types` VALUES (18,'Logistic','logistic','Barang-barang pelengkap / pendukung / souvenir (dapat dimasukan bbrp stok sekaligus).',0,0,'2015-06-07 09:16:54',1,'2015-06-11 10:42:00',1);
INSERT INTO `cms_types` VALUES (20,'Dmd Vendor Invoice','dmd-vendor-invoice','Surat pemesanan produk diamond terhadap vendor.',0,1,'2015-06-07 12:50:45',1,'2015-06-20 15:07:07',1);
INSERT INTO `cms_types` VALUES (21,'Cor Vendor Invoice','cor-vendor-invoice','Surat pemesanan produk cor terhadap vendor.',0,1,'2015-06-07 14:19:23',1,'2015-06-20 15:08:25',1);
INSERT INTO `cms_types` VALUES (22,'Dmd Client Invoice','dmd-client-invoice','Dokumen invoice penjualan produk diamond terhadap client.',0,1,'2015-06-07 14:34:39',1,'2015-06-26 10:47:21',1);
INSERT INTO `cms_types` VALUES (23,'Cor Client Invoice','cor-client-invoice','Dokumen invoice penjualan produk cor terhadap client.',0,1,'2015-06-09 13:30:14',1,'2015-06-26 10:49:18',1);
INSERT INTO `cms_types` VALUES (24,'Surat Jalan','surat-jalan','Dokumen tanda bukti pengiriman barang, baik terhadap client (penjualan), vendor (return), maupun perpindahan antar warehouse / exhibition.',0,0,'2015-06-09 23:46:04',1,'2015-06-09 23:46:04',1);
INSERT INTO `cms_types` VALUES (27,'DV Payment','dv-payment','Rekening Koran untuk transaksi pembayaran invoice terpilih.',20,0,'2015-06-14 13:23:23',1,'2015-06-20 23:44:53',1);
INSERT INTO `cms_types` VALUES (28,'CV Payment','cv-payment','Rekening Koran untuk transaksi pembayaran invoice terpilih.',21,0,'2015-06-15 12:20:04',1,'2015-06-20 23:45:26',1);
INSERT INTO `cms_types` VALUES (29,'DC Payment','dc-payment','Rekening Koran untuk transaksi pembayaran invoice terpilih.',22,0,'2015-06-16 16:49:43',1,'2015-06-20 23:45:54',1);
INSERT INTO `cms_types` VALUES (30,'CC Payment','cc-payment','Rekening Koran untuk transaksi pembayaran invoice terpilih.',23,0,'2015-06-16 22:30:29',1,'2015-06-20 23:46:21',1);
/*!40000 ALTER TABLE `cms_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_user_metas`
--

DROP TABLE IF EXISTS `cms_user_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_user_metas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_user_metas`
--

LOCK TABLES `cms_user_metas` WRITE;
/*!40000 ALTER TABLE `cms_user_metas` DISABLE KEYS */;
INSERT INTO `cms_user_metas` VALUES (1,1,'gender','male');
INSERT INTO `cms_user_metas` VALUES (2,1,'address','Jl. Dharmahusada Indah 43');
INSERT INTO `cms_user_metas` VALUES (3,1,'zip_code','60258');
INSERT INTO `cms_user_metas` VALUES (4,1,'city','Surabaya, Indonesia');
INSERT INTO `cms_user_metas` VALUES (5,1,'mobile_phone','089 67367 1110');
INSERT INTO `cms_user_metas` VALUES (6,1,'dob_day','28');
INSERT INTO `cms_user_metas` VALUES (7,1,'dob_month','10');
INSERT INTO `cms_user_metas` VALUES (8,1,'dob_year','1988');
INSERT INTO `cms_user_metas` VALUES (9,1,'job','Web Developer');
INSERT INTO `cms_user_metas` VALUES (10,1,'company','PT. Creazi');
INSERT INTO `cms_user_metas` VALUES (11,1,'company_address','Jl. Nginden Semolo 101');
INSERT INTO `cms_user_metas` VALUES (12,2,'gender','male');
INSERT INTO `cms_user_metas` VALUES (13,2,'address','DHI 43');
INSERT INTO `cms_user_metas` VALUES (14,2,'city','Surabaya, Indonesia');
INSERT INTO `cms_user_metas` VALUES (15,2,'mobile_phone','123456');
INSERT INTO `cms_user_metas` VALUES (16,2,'dob_day','28');
INSERT INTO `cms_user_metas` VALUES (17,2,'dob_month','10');
INSERT INTO `cms_user_metas` VALUES (18,2,'dob_year','1988');
INSERT INTO `cms_user_metas` VALUES (19,2,'job','WAN admin');
/*!40000 ALTER TABLE `cms_user_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_users`
--

DROP TABLE IF EXISTS `cms_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(500) NOT NULL,
  `lastname` varchar(500) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_users`
--

LOCK TABLES `cms_users` WRITE;
/*!40000 ALTER TABLE `cms_users` DISABLE KEYS */;
INSERT INTO `cms_users` VALUES (1,'admin','zpanel','2013-01-04 00:00:00',1,'2014-02-06 10:50:29',1,1);
INSERT INTO `cms_users` VALUES (2,'Andy','Basuki','2015-06-02 20:18:22',1,'2015-06-02 20:18:22',1,1);
/*!40000 ALTER TABLE `cms_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-30  0:26:14
