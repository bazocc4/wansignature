-- MySQL dump 10.13  Distrib 5.6.21, for Win32 (x86)
--
-- Host: localhost    Database: wansignature
-- ------------------------------------------------------
-- Server version	5.6.21

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
INSERT INTO `cms_accounts` VALUES (1,1,1,'Admin Basuki','admin@yahoo.com','169e781bd52860b584879cbe117085da596238f3','2015-06-11 09:25:04','2013-01-04 00:00:00',1,'2014-05-05 15:15:38',1);
INSERT INTO `cms_accounts` VALUES (2,2,2,'Andy Basuki','andybasuki88@gmail.com','d82dff1679e0137a0bab60cc67cc6a2ad36f10a0','2015-06-08 09:34:35','2015-06-02 20:19:53',1,'2015-06-02 20:19:53',1);
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
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_entries`
--

LOCK TABLES `cms_entries` WRITE;
/*!40000 ALTER TABLE `cms_entries` DISABLE KEYS */;
INSERT INTO `cms_entries` VALUES (3,'bank','BCA Group','bca-group','https://ibank.klikbca.com/',2,0,1,0,'2015-06-03 00:42:27',1,'2015-06-05 13:56:45',1,3,'en-3');
INSERT INTO `cms_entries` VALUES (2,'media','Bank BCA','bank-bca',NULL,0,0,1,0,'2015-06-03 00:40:46',1,'2015-06-03 00:40:47',1,2,'en-2');
INSERT INTO `cms_entries` VALUES (4,'media','logo-bank-mandiri','logo-bank-mandiri',NULL,0,0,1,0,'2015-06-03 00:44:38',1,'2015-06-03 00:44:38',1,4,'en-4');
INSERT INTO `cms_entries` VALUES (5,'bank','Mandiri','mandiri','https://ib.bankmandiri.co.id/',4,0,1,0,'2015-06-03 00:45:20',1,'2015-06-05 13:56:51',1,5,'en-5');
INSERT INTO `cms_entries` VALUES (7,'bank','HSBC','hsbc-1','The world\'s local bank',8,0,1,0,'2015-06-03 00:47:51',1,'2015-06-05 13:54:56',1,7,'en-7');
INSERT INTO `cms_entries` VALUES (8,'media','hsbc','hsbc-2',NULL,0,0,1,0,'2015-06-03 00:49:07',1,'2015-06-03 00:49:07',1,8,'en-8');
INSERT INTO `cms_entries` VALUES (9,'usd-rate','IDR','idr','Indonesian Rupiah.',0,0,1,0,'2015-06-03 15:58:49',1,'2015-06-03 16:04:40',1,9,'en-9');
INSERT INTO `cms_entries` VALUES (10,'usd-rate','HKD','hkd','Hongkong Dollar.',0,0,1,0,'2015-06-03 16:04:27',1,'2015-06-03 16:04:27',1,10,'en-10');
INSERT INTO `cms_entries` VALUES (11,'usd-rate','CNY','cny','Chinese Yuan.',0,0,1,0,'2015-06-03 16:05:14',1,'2015-06-03 16:05:14',1,11,'en-11');
INSERT INTO `cms_entries` VALUES (12,'usd-rate','Euro','euro','',0,0,1,0,'2015-06-03 21:13:08',1,'2015-06-03 21:13:08',1,12,'en-12');
INSERT INTO `cms_entries` VALUES (13,'usd-rate','Gold Bar (gr)','gold-bar-gr','',0,0,1,0,'2015-06-03 21:52:59',1,'2015-06-03 21:52:59',1,13,'en-13');
INSERT INTO `cms_entries` VALUES (14,'warehouse','Atom WH','atom-wh','',0,0,1,0,'2015-06-03 22:41:50',1,'2015-06-07 09:55:49',1,14,'en-14');
INSERT INTO `cms_entries` VALUES (15,'warehouse','Tunjungan Plaza WH','tunjungan-plaza-wh','',0,0,1,0,'2015-06-03 22:42:27',1,'2015-06-07 09:55:14',1,15,'en-15');
INSERT INTO `cms_entries` VALUES (16,'product-type','DPF','dpf','Diamond Pendants Finish',0,0,1,0,'2015-06-03 23:22:58',1,'2015-06-05 21:30:02',1,16,'en-16');
INSERT INTO `cms_entries` VALUES (17,'product-type','DRF','drf','Diamond Rings Finish',0,0,1,0,'2015-06-03 23:23:16',1,'2015-06-05 21:29:57',1,17,'en-17');
INSERT INTO `cms_entries` VALUES (18,'product-type','DEF','def','Diamond Earrings Finish',0,0,1,0,'2015-06-03 23:24:13',1,'2015-06-05 21:29:52',1,18,'en-18');
INSERT INTO `cms_entries` VALUES (19,'product-type','Pipe Necklace','pipe-necklace','',0,0,1,0,'2015-06-03 23:25:33',1,'2015-06-05 23:38:05',1,19,'en-19');
INSERT INTO `cms_entries` VALUES (20,'product-type','Pipe Bracelet','pipe-bracelet','',0,0,1,0,'2015-06-03 23:25:53',1,'2015-06-05 23:38:12',1,20,'en-20');
INSERT INTO `cms_entries` VALUES (21,'product-brand','BVLGARI','bvlgari','',0,0,1,0,'2015-06-03 23:31:03',1,'2015-06-03 23:31:03',1,21,'en-21');
INSERT INTO `cms_entries` VALUES (22,'product-brand','VAN CLEEF','van-cleef','',0,0,1,0,'2015-06-03 23:31:14',1,'2015-06-03 23:31:14',1,22,'en-22');
INSERT INTO `cms_entries` VALUES (23,'product-brand','HERMES','hermes','',0,0,1,0,'2015-06-03 23:31:27',1,'2015-06-03 23:31:27',1,23,'en-23');
INSERT INTO `cms_entries` VALUES (24,'exhibition','JIJF 2015 (8th)','jijf-2015-8th','',0,0,1,0,'2015-06-05 12:41:43',1,'2015-06-05 12:41:43',1,24,'en-24');
INSERT INTO `cms_entries` VALUES (25,'client','Novi','novi','G243730',0,0,1,0,'2015-06-05 13:41:35',1,'2015-06-05 13:46:35',1,25,'en-25');
INSERT INTO `cms_entries` VALUES (26,'client','Gisela Tania','gisela-tania','G243935',0,0,1,0,'2015-06-05 13:41:56',1,'2015-06-05 13:46:43',1,26,'en-26');
INSERT INTO `cms_entries` VALUES (27,'client','Reyner Gunawan','reyner-gunawan','G243913',0,0,1,0,'2015-06-05 13:42:31',1,'2015-06-08 12:40:15',1,27,'en-27');
INSERT INTO `cms_entries` VALUES (28,'client','Hanna','hanna','G113678',0,0,1,0,'2015-06-05 13:44:06',1,'2015-06-08 13:15:20',1,28,'en-28');
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
INSERT INTO `cms_entries` VALUES (39,'client','Michael','michael','G243940, G243905',0,0,1,0,'2015-06-05 13:51:45',1,'2015-06-05 13:51:45',1,39,'en-39');
INSERT INTO `cms_entries` VALUES (43,'cor-jewelry','G123160','g123160','',0,0,1,0,'2015-06-06 16:32:20',1,'2015-06-06 16:32:20',1,43,'en-43');
INSERT INTO `cms_entries` VALUES (41,'product-type','MADE IN ITALY','made-in-italy','',0,0,1,0,'2015-06-05 23:48:05',1,'2015-06-05 23:48:05',1,41,'en-41');
INSERT INTO `cms_entries` VALUES (42,'product-type','MADE IN KOREA','made-in-korea','',0,0,1,0,'2015-06-05 23:48:27',1,'2015-06-08 16:01:29',1,42,'en-42');
INSERT INTO `cms_entries` VALUES (44,'diamond','100773','100773','',0,0,1,0,'2015-06-06 16:33:08',1,'2015-06-06 16:34:22',1,44,'en-44');
INSERT INTO `cms_entries` VALUES (45,'product-type','Earring','earring','',0,0,1,0,'2015-06-07 08:22:24',1,'2015-06-07 08:22:24',1,45,'en-45');
INSERT INTO `cms_entries` VALUES (46,'product-type','Earring','earring-1','',0,0,1,0,'2015-06-07 08:22:40',1,'2015-06-07 08:22:40',1,46,'en-46');
INSERT INTO `cms_entries` VALUES (47,'logistic','Baut Mur','baut-mur','',0,0,1,0,'2015-06-07 09:18:39',1,'2015-06-11 12:51:09',1,47,'en-47');
INSERT INTO `cms_entries` VALUES (58,'logistic','Obeng','obeng','[untuk reparasi cincin]',0,0,1,0,'2015-06-11 12:30:38',1,'2015-06-11 12:30:38',1,58,'en-58');
INSERT INTO `cms_entries` VALUES (51,'surat-jalan','asdfgfg','asdfgfg','',0,0,1,0,'2015-06-10 10:36:24',1,'2015-06-10 10:36:50',1,51,'en-51');
INSERT INTO `cms_entries` VALUES (52,'surat-jalan','test sjoke','test-sjoke','',0,0,1,0,'2015-06-10 11:28:07',1,'2015-06-10 12:10:13',1,52,'en-52');
INSERT INTO `cms_entries` VALUES (53,'surat-jalan','test kedua surat jalan','test-kedua-surat-jalan','',0,0,1,0,'2015-06-10 12:11:21',1,'2015-06-10 12:11:42',1,53,'en-53');
INSERT INTO `cms_entries` VALUES (54,'surat-jalan','wqwqwqwqw','wqwqwqwqw','',0,0,0,0,'2015-06-10 16:08:57',1,'2015-06-11 16:45:22',1,54,'en-54');
INSERT INTO `cms_entries` VALUES (55,'logistic','Souvenir X','souvenir-x','[contoh sample]',0,0,1,0,'2015-06-10 17:15:41',1,'2015-06-11 12:43:32',1,55,'en-55');
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
) ENGINE=MyISAM AUTO_INCREMENT=328 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_entry_metas`
--

LOCK TABLES `cms_entry_metas` WRITE;
/*!40000 ALTER TABLE `cms_entry_metas` DISABLE KEYS */;
INSERT INTO `cms_entry_metas` VALUES (5,2,'image_type','png');
INSERT INTO `cms_entry_metas` VALUES (4,2,'backup-slug','\nbank-bca\n');
INSERT INTO `cms_entry_metas` VALUES (6,2,'image_size','6106');
INSERT INTO `cms_entry_metas` VALUES (7,3,'backup-slug','\nbca-group\n');
INSERT INTO `cms_entry_metas` VALUES (8,4,'backup-slug','\nlogo-bank-mandiri\n');
INSERT INTO `cms_entry_metas` VALUES (9,4,'image_type','png');
INSERT INTO `cms_entry_metas` VALUES (10,4,'image_size','5722');
INSERT INTO `cms_entry_metas` VALUES (11,5,'backup-slug','\nmandiri\n');
INSERT INTO `cms_entry_metas` VALUES (15,7,'backup-slug','\nhsbc-1\n');
INSERT INTO `cms_entry_metas` VALUES (16,8,'backup-slug','\nhsbc-2\n');
INSERT INTO `cms_entry_metas` VALUES (17,8,'image_type','gif');
INSERT INTO `cms_entry_metas` VALUES (18,8,'image_size','1569');
INSERT INTO `cms_entry_metas` VALUES (19,9,'backup-slug','\nidr\n');
INSERT INTO `cms_entry_metas` VALUES (23,9,'form-rate_value','13230.50');
INSERT INTO `cms_entry_metas` VALUES (21,10,'backup-slug','\nhkd\n');
INSERT INTO `cms_entry_metas` VALUES (22,10,'form-rate_value','7.75');
INSERT INTO `cms_entry_metas` VALUES (24,11,'backup-slug','\ncny\n');
INSERT INTO `cms_entry_metas` VALUES (25,11,'form-rate_value','6.20');
INSERT INTO `cms_entry_metas` VALUES (26,12,'backup-slug','\neuro\n');
INSERT INTO `cms_entry_metas` VALUES (27,12,'form-rate_value','0.89');
INSERT INTO `cms_entry_metas` VALUES (28,13,'backup-slug','\ngold-bar-gr\n');
INSERT INTO `cms_entry_metas` VALUES (29,13,'form-rate_value','38.29');
INSERT INTO `cms_entry_metas` VALUES (30,14,'backup-slug','\natom-wh\n');
INSERT INTO `cms_entry_metas` VALUES (221,14,'form-alamat','Jalan Bunguran No. 45, Surabaya, Jawa Timur');
INSERT INTO `cms_entry_metas` VALUES (32,15,'backup-slug','\ntunjungan-plaza-wh\n');
INSERT INTO `cms_entry_metas` VALUES (218,15,'form-alamat','Jl. Basuki Rahmat No. 8-12, Surabaya, Jawa Timur 60261');
INSERT INTO `cms_entry_metas` VALUES (34,16,'backup-slug','\ndpf\n');
INSERT INTO `cms_entry_metas` VALUES (35,17,'backup-slug','\ndrf\n');
INSERT INTO `cms_entry_metas` VALUES (36,18,'backup-slug','\ndef\n');
INSERT INTO `cms_entry_metas` VALUES (37,19,'backup-slug','\npipe-necklace\n');
INSERT INTO `cms_entry_metas` VALUES (38,20,'backup-slug','\npipe-bracelet\n');
INSERT INTO `cms_entry_metas` VALUES (39,21,'backup-slug','\nbvlgari\n');
INSERT INTO `cms_entry_metas` VALUES (40,22,'backup-slug','\nvan-cleef\n');
INSERT INTO `cms_entry_metas` VALUES (41,23,'backup-slug','\nhermes\n');
INSERT INTO `cms_entry_metas` VALUES (42,24,'backup-slug','\njijf-2015-8th\n');
INSERT INTO `cms_entry_metas` VALUES (43,24,'form-start_date','05/07/2015');
INSERT INTO `cms_entry_metas` VALUES (44,24,'form-end_date','05/10/2015');
INSERT INTO `cms_entry_metas` VALUES (45,24,'form-alamat','Assembly Hall - Jakarta Convention Center\r\nJAKARTA - INDONESIA');
INSERT INTO `cms_entry_metas` VALUES (46,24,'form-telepon','+62 (21) 5726000');
INSERT INTO `cms_entry_metas` VALUES (47,24,'form-warehouse_employee','');
INSERT INTO `cms_entry_metas` VALUES (48,25,'backup-slug','\nnovi\n');
INSERT INTO `cms_entry_metas` VALUES (105,25,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (104,25,'form-telepon','0812309688884');
INSERT INTO `cms_entry_metas` VALUES (103,25,'form-alamat','Rangkah 1/46');
INSERT INTO `cms_entry_metas` VALUES (102,25,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (54,26,'backup-slug','\ngisela-tania\n');
INSERT INTO `cms_entry_metas` VALUES (109,26,'form-telepon','081232034442');
INSERT INTO `cms_entry_metas` VALUES (108,26,'form-alamat','Jl. Villa Puncak Tidar VE IX/20 Malang');
INSERT INTO `cms_entry_metas` VALUES (107,26,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (60,27,'backup-slug','\nreyner-gunawan\n');
INSERT INTO `cms_entry_metas` VALUES (239,27,'form-wholesaler','grace-novarinus');
INSERT INTO `cms_entry_metas` VALUES (240,27,'form-alamat','Apart Water Palace blok C no. 1912');
INSERT INTO `cms_entry_metas` VALUES (66,28,'backup-slug','\nhanna\n');
INSERT INTO `cms_entry_metas` VALUES (245,28,'form-wholesaler','cik-ninih');
INSERT INTO `cms_entry_metas` VALUES (246,28,'form-alamat','Rungkut Asri Tengah 4/31');
INSERT INTO `cms_entry_metas` VALUES (72,29,'backup-slug','\nvonza-silvia\n');
INSERT INTO `cms_entry_metas` VALUES (124,29,'form-telepon','0818381010');
INSERT INTO `cms_entry_metas` VALUES (123,29,'form-alamat','Tarakan');
INSERT INTO `cms_entry_metas` VALUES (122,29,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (78,30,'backup-slug','\nwidya\n');
INSERT INTO `cms_entry_metas` VALUES (129,30,'form-telepon','082143542255');
INSERT INTO `cms_entry_metas` VALUES (128,30,'form-alamat','Kencana Sari Barat 2/811');
INSERT INTO `cms_entry_metas` VALUES (127,30,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (84,31,'backup-slug','\nhendra\n');
INSERT INTO `cms_entry_metas` VALUES (134,31,'form-telepon','081241960000');
INSERT INTO `cms_entry_metas` VALUES (133,31,'form-alamat','Makassar');
INSERT INTO `cms_entry_metas` VALUES (132,31,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (90,32,'backup-slug','\nyani\n');
INSERT INTO `cms_entry_metas` VALUES (139,32,'form-telepon','081226377777');
INSERT INTO `cms_entry_metas` VALUES (138,32,'form-alamat','JL. Tanggulangin no. 1');
INSERT INTO `cms_entry_metas` VALUES (137,32,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (96,33,'backup-slug','\ngrace-novarinus\n');
INSERT INTO `cms_entry_metas` VALUES (235,33,'form-telepon','0811361231 / 5229861F');
INSERT INTO `cms_entry_metas` VALUES (234,33,'form-alamat','JL. W.R Supratman 64 Rambipuji Jbr');
INSERT INTO `cms_entry_metas` VALUES (233,33,'form-kategori','Wholesaler');
INSERT INTO `cms_entry_metas` VALUES (106,25,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (110,26,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (111,26,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (238,27,'form-kategori','Retailer');
INSERT INTO `cms_entry_metas` VALUES (244,28,'form-kategori','Retailer');
INSERT INTO `cms_entry_metas` VALUES (125,29,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (126,29,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (130,30,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (131,30,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (135,31,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (136,31,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (140,32,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (141,32,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (142,34,'backup-slug','\nsabrina\n');
INSERT INTO `cms_entry_metas` VALUES (143,34,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (144,34,'form-telepon','081703096624');
INSERT INTO `cms_entry_metas` VALUES (145,34,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (146,34,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (147,35,'backup-slug','\ncik-ninih\n');
INSERT INTO `cms_entry_metas` VALUES (229,35,'form-alamat','THI 1 B2 NO 6, Bandung');
INSERT INTO `cms_entry_metas` VALUES (230,35,'form-telepon','08122351037');
INSERT INTO `cms_entry_metas` VALUES (153,36,'backup-slug','\nyuli\n');
INSERT INTO `cms_entry_metas` VALUES (154,36,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (155,36,'form-telepon','081321816161');
INSERT INTO `cms_entry_metas` VALUES (156,36,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (157,36,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (158,37,'backup-slug','\nsri-rahayu\n');
INSERT INTO `cms_entry_metas` VALUES (159,37,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (160,37,'form-alamat','Jl. Gagak Lumayung 141 Garut');
INSERT INTO `cms_entry_metas` VALUES (161,37,'form-telepon','08112111375');
INSERT INTO `cms_entry_metas` VALUES (162,37,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (163,37,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (228,35,'form-kategori','Wholesaler');
INSERT INTO `cms_entry_metas` VALUES (169,38,'backup-slug','\nsusan\n');
INSERT INTO `cms_entry_metas` VALUES (170,38,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (171,38,'form-telepon','082330788899');
INSERT INTO `cms_entry_metas` VALUES (172,38,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (173,38,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (174,39,'backup-slug','\nmichael\n');
INSERT INTO `cms_entry_metas` VALUES (175,39,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (176,39,'form-alamat','Jl. Darmahusada indah 57 ');
INSERT INTO `cms_entry_metas` VALUES (177,39,'form-telepon','081330568888');
INSERT INTO `cms_entry_metas` VALUES (178,39,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (179,39,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (199,43,'form-product_type','made-in-italy');
INSERT INTO `cms_entry_metas` VALUES (198,43,'backup-slug','\ng123160\n');
INSERT INTO `cms_entry_metas` VALUES (193,20,'form-category','999 Simple (110%)');
INSERT INTO `cms_entry_metas` VALUES (192,19,'form-category','999 3D (115%)');
INSERT INTO `cms_entry_metas` VALUES (189,18,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (190,17,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (191,16,'form-category','Diamond');
INSERT INTO `cms_entry_metas` VALUES (194,41,'backup-slug','\nmade-in-italy\n');
INSERT INTO `cms_entry_metas` VALUES (195,41,'form-category','Italy (125%)');
INSERT INTO `cms_entry_metas` VALUES (196,42,'backup-slug','\nmade-in-korea\n');
INSERT INTO `cms_entry_metas` VALUES (250,42,'form-category','Korea (100%)');
INSERT INTO `cms_entry_metas` VALUES (200,43,'form-product_brand','bvlgari');
INSERT INTO `cms_entry_metas` VALUES (201,44,'backup-slug','\n013093\n100773\n');
INSERT INTO `cms_entry_metas` VALUES (207,44,'form-report_type','SR');
INSERT INTO `cms_entry_metas` VALUES (206,44,'form-vendor_currency','USD');
INSERT INTO `cms_entry_metas` VALUES (205,44,'form-product_type','dpf');
INSERT INTO `cms_entry_metas` VALUES (208,45,'backup-slug','\nearring\n');
INSERT INTO `cms_entry_metas` VALUES (209,45,'form-category','999 3D (115%)');
INSERT INTO `cms_entry_metas` VALUES (210,46,'backup-slug','\nearring-1\n');
INSERT INTO `cms_entry_metas` VALUES (211,46,'form-category','999 Simple (110%)');
INSERT INTO `cms_entry_metas` VALUES (212,47,'backup-slug','\nbaut-mur\n');
INSERT INTO `cms_entry_metas` VALUES (319,47,'form-logistic_type','Supporting');
INSERT INTO `cms_entry_metas` VALUES (317,55,'form-warehouse','tunjungan-plaza-wh_20');
INSERT INTO `cms_entry_metas` VALUES (215,47,'count-logistic-warehouse','0');
INSERT INTO `cms_entry_metas` VALUES (312,58,'backup-slug','\nobeng\n');
INSERT INTO `cms_entry_metas` VALUES (219,15,'form-telepon','(031) 5311088');
INSERT INTO `cms_entry_metas` VALUES (220,15,'form-warehouse_employee','');
INSERT INTO `cms_entry_metas` VALUES (222,14,'form-telepon','(031) 3551995');
INSERT INTO `cms_entry_metas` VALUES (223,14,'form-warehouse_employee','');
INSERT INTO `cms_entry_metas` VALUES (313,58,'form-logistic_type','Supporting');
INSERT INTO `cms_entry_metas` VALUES (231,35,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (232,35,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (236,33,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (237,33,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (241,27,'form-telepon','081355527131');
INSERT INTO `cms_entry_metas` VALUES (242,27,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (243,27,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (247,28,'form-telepon','031-70574934 / 7E715559');
INSERT INTO `cms_entry_metas` VALUES (248,28,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (249,28,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (265,51,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (266,51,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (257,51,'backup-slug','\nasdfgfg\n');
INSERT INTO `cms_entry_metas` VALUES (264,51,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (263,51,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (267,51,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (268,52,'backup-slug','\ntest-sjoke\n');
INSERT INTO `cms_entry_metas` VALUES (269,52,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (270,52,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (271,52,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (272,52,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (273,52,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (274,53,'backup-slug','\ntest-kedua-surat-jalan\n');
INSERT INTO `cms_entry_metas` VALUES (275,53,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (276,53,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (277,53,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (278,53,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (279,53,'form-logistic','');
INSERT INTO `cms_entry_metas` VALUES (280,54,'backup-slug','\nwqwqwqwqw\n');
INSERT INTO `cms_entry_metas` VALUES (323,54,'form-delivery_type','Warehouse To Warehouse');
INSERT INTO `cms_entry_metas` VALUES (324,54,'form-warehouse_origin','atom-wh');
INSERT INTO `cms_entry_metas` VALUES (325,54,'form-diamond','');
INSERT INTO `cms_entry_metas` VALUES (326,54,'form-cor_jewelry','');
INSERT INTO `cms_entry_metas` VALUES (291,55,'backup-slug','\nsouvenir-x\n');
INSERT INTO `cms_entry_metas` VALUES (316,55,'form-logistic_type','Souvenir');
INSERT INTO `cms_entry_metas` VALUES (327,54,'form-logistic','_');
INSERT INTO `cms_entry_metas` VALUES (322,54,'form-date','06/10/2015');
INSERT INTO `cms_entry_metas` VALUES (304,55,'count-logistic-warehouse','0');
INSERT INTO `cms_entry_metas` VALUES (315,58,'form-exhibition','jijf-2015-8th_12');
INSERT INTO `cms_entry_metas` VALUES (307,55,'count-logistic-exhibition','0');
INSERT INTO `cms_entry_metas` VALUES (314,58,'form-warehouse','tunjungan-plaza-wh_30|atom-wh_40');
INSERT INTO `cms_entry_metas` VALUES (318,55,'form-exhibition','jijf-2015-8th_35');
INSERT INTO `cms_entry_metas` VALUES (320,47,'form-warehouse','tunjungan-plaza-wh_3|atom-wh_7');
INSERT INTO `cms_entry_metas` VALUES (321,47,'form-exhibition','_');
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
) ENGINE=MyISAM AUTO_INCREMENT=867 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_type_metas`
--

LOCK TABLES `cms_type_metas` WRITE;
/*!40000 ALTER TABLE `cms_type_metas` DISABLE KEYS */;
INSERT INTO `cms_type_metas` VALUES (5,4,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (4,4,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (550,4,'form-italy_sell_x','','text','is_numeric|','Nilai Sell X untuk produk COR Italy (125%)');
INSERT INTO `cms_type_metas` VALUES (549,4,'form-diamond_sell_x','','text','is_numeric|','Nilai Sell X untuk produk diamond.');
INSERT INTO `cms_type_metas` VALUES (548,4,'form-exhibition','','multibrowse','','Client pernah ambil produk dari pameran mana saja.');
INSERT INTO `cms_type_metas` VALUES (547,4,'form-warehouse','','multibrowse','','Client pernah ambil produk dari WH mana saja.');
INSERT INTO `cms_type_metas` VALUES (546,4,'form-salesman','','browse','','Sales(wo)man yang melayani client ini.');
INSERT INTO `cms_type_metas` VALUES (545,4,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (544,4,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (543,4,'form-alamat','','textarea','','Alamat pribadi / toko pelanggan.');
INSERT INTO `cms_type_metas` VALUES (19,5,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (20,5,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (21,5,'form-kode_vendor','','text','','Kode singkat unik vendor.');
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
INSERT INTO `cms_type_metas` VALUES (786,14,'form-return_date','','datepicker','','When this product returned to vendor.');
INSERT INTO `cms_type_metas` VALUES (785,14,'form-temp_report','','text','','Temporary report date / notes.');
INSERT INTO `cms_type_metas` VALUES (784,14,'form-report_type','SR\r\nRR','radio','not_empty|','Sold Report / Return Report.');
INSERT INTO `cms_type_metas` VALUES (783,14,'form-report_date','','datepicker','','');
INSERT INTO `cms_type_metas` VALUES (782,14,'form-vendor_hkd','','text','is_numeric|','Vendor HKD price result.');
INSERT INTO `cms_type_metas` VALUES (781,14,'form-vendor_usd','','text','is_numeric|','Vendor USD price result.');
INSERT INTO `cms_type_metas` VALUES (780,14,'form-vendor_x','','text','is_numeric|','Vendor capital X for calculating price.');
INSERT INTO `cms_type_metas` VALUES (779,14,'form-vendor_barcode','','text','is_numeric|','Vendor original price.');
INSERT INTO `cms_type_metas` VALUES (778,14,'form-vendor_currency','USD\r\nHKD','radio','not_empty|','Vendor Price in USD / HKD.');
INSERT INTO `cms_type_metas` VALUES (777,14,'form-vendor_note','','text','','Additional information for this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (776,14,'form-vendor_status','','text','','Product status with vendor (Sold / Credit / Consignment / Return / Syute / etc.)');
INSERT INTO `cms_type_metas` VALUES (775,14,'form-vendor_item_code','','text','','Kode produk asal dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (774,14,'form-vendor','','browse','','Pihak vendor yang menyediakan produk ini.');
INSERT INTO `cms_type_metas` VALUES (773,14,'form-vendor_invoice_date','','datepicker','','Purchase date from vendor.');
INSERT INTO `cms_type_metas` VALUES (772,14,'form-vendor_invoice_code','','text','','Kode invoice dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (771,14,'form-item_ref_code_x2','','text','','Item reference code (X2)');
INSERT INTO `cms_type_metas` VALUES (770,14,'form-item_ref_code','','text','','Item reference code.');
INSERT INTO `cms_type_metas` VALUES (769,14,'form-gold_weight','','text','','Berat emas yg terkandung pada produk (gram).');
INSERT INTO `cms_type_metas` VALUES (768,14,'form-gold_carat','','text','','Kadar carat GOLD pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (133,15,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (134,15,'title_key','Nama',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (138,15,'form-end_date','','datepicker','','Tanggal terakhir pameran ini berlangsung.');
INSERT INTO `cms_type_metas` VALUES (137,15,'form-start_date','','datepicker','','Kapan pameran ini mulai berlangsung.');
INSERT INTO `cms_type_metas` VALUES (139,15,'form-alamat','','textarea','','Alamat lengkap di mana pameran diadakan.');
INSERT INTO `cms_type_metas` VALUES (140,15,'form-telepon','','text','','Nomer Telp stand pameran yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (141,15,'form-warehouse_employee','','multibrowse','','Pegawai yg bertanggung jawab untuk pengadaan event pameran ini.');
INSERT INTO `cms_type_metas` VALUES (142,16,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (838,17,'form-payment_cash','','text','','Payment from client using cash / bank transfer / debit card.');
INSERT INTO `cms_type_metas` VALUES (767,14,'form-carat_4','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (766,14,'form-carat_3','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (765,14,'form-carat_2','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (764,14,'form-carat_1','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (763,14,'form-exhibition','','browse','','Exhibition di mana produk ini sedang dipamerkan.');
INSERT INTO `cms_type_metas` VALUES (761,14,'form-warehouse','','browse','','Gudang di mana produk tersimpan.');
INSERT INTO `cms_type_metas` VALUES (762,14,'form-stock_date','','datepicker','','Kapan terakhir produk ini masuk ke WH sbg ready stock.');
INSERT INTO `cms_type_metas` VALUES (539,12,'form-category','Diamond\r\nItaly (125%)\r\nKorea (100%)\r\n999 Simple (110%)\r\n999 3D (115%)','dropdown','not_empty|','Category group for this product type.');
INSERT INTO `cms_type_metas` VALUES (553,4,'form-cor_999_3d_x','','text','is_numeric|','Nilai Sell X untuk produk COR 999 3D (115%)');
INSERT INTO `cms_type_metas` VALUES (551,4,'form-korea_sell_x','','text','is_numeric|','Nilai Sell X untuk produk COR Korea (100%)');
INSERT INTO `cms_type_metas` VALUES (552,4,'form-cor_999_simple_x','','text','is_numeric|','Nilai Sell X untuk produk COR 999 Simple (110%)');
INSERT INTO `cms_type_metas` VALUES (264,17,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (265,17,'title_key','Item Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (827,17,'form-client_invoice_sold_100','','text','is_numeric|','Weight of <strong>SOLD 100</strong> on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (828,17,'form-client_invoice_x_100','','text','is_numeric|','<strong>X 100</strong> factor on this client invoice (KOREA).');
INSERT INTO `cms_type_metas` VALUES (829,17,'form-client_invoice_sold_110','','text','is_numeric|','Weight of <strong>SOLD 110</strong> on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (830,17,'form-client_invoice_x_110','','text','is_numeric|','<strong>X 110</strong> factor on this client invoice (999 SIMPLE).');
INSERT INTO `cms_type_metas` VALUES (831,17,'form-client_invoice_sold_115','','text','is_numeric|','Weight of <strong>SOLD 115</strong> on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (832,17,'form-client_invoice_x_115','','text','is_numeric|','<strong>X 115</strong> factor on this client invoice (999 3D).');
INSERT INTO `cms_type_metas` VALUES (833,17,'form-client_invoice_disc_adjustment','','text','is_numeric|','Weight discount adjustment on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (834,17,'form-gold_price','','text','is_numeric|','Current gold price per Gram (IDR).');
INSERT INTO `cms_type_metas` VALUES (835,17,'form-payment_ct_ld','','text','','Payment from client using CT (local material) & LD.');
INSERT INTO `cms_type_metas` VALUES (836,17,'form-payment_rosok','','text','','Payment from client using rosok item.');
INSERT INTO `cms_type_metas` VALUES (837,17,'form-payment_checks','','text','','Payment from client using bank checks.');
INSERT INTO `cms_type_metas` VALUES (760,14,'form-status_in_wan','','text','','Current product status (Stock / Sold / Consignment / Return / Changed / etc.)');
INSERT INTO `cms_type_metas` VALUES (759,14,'form-sell_barcode','','text','is_numeric|','Adjusted product price tag.');
INSERT INTO `cms_type_metas` VALUES (758,14,'form-barcode','','text','is_numeric|','Product price tag.');
INSERT INTO `cms_type_metas` VALUES (757,14,'form-product_type','','browse','','Tipe produk berlian.');
INSERT INTO `cms_type_metas` VALUES (826,17,'form-client_invoice_x_125','','text','is_numeric|','<strong>X 125</strong> factor on this client invoice (ITALY).');
INSERT INTO `cms_type_metas` VALUES (825,17,'form-client_invoice_sold_125','','text','is_numeric|','Weight of <strong>SOLD 125</strong> on this client invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (823,17,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (824,17,'form-client_invoice_pcs','','text','','Total pcs of jewelries sold on this client invoice.');
INSERT INTO `cms_type_metas` VALUES (822,17,'form-client','','browse','','Who purchase this product.');
INSERT INTO `cms_type_metas` VALUES (820,17,'form-client_invoice_code','','text','','Kode invoice untuk pihak client.');
INSERT INTO `cms_type_metas` VALUES (821,17,'form-client_invoice_date','','datepicker','','Sold date to client.');
INSERT INTO `cms_type_metas` VALUES (819,17,'form-product_status','','text','','Current product status (Stock / Sold / Consignment / Return / Changed / etc.)');
INSERT INTO `cms_type_metas` VALUES (818,17,'form-exhibition','','browse','','Exhibition di mana produk ini sedang dipamerkan.');
INSERT INTO `cms_type_metas` VALUES (372,18,'category','inventory',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (373,18,'title_key','Nama',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (864,18,'form-logistic_type','Supporting\r\nSouvenir','radio','not_empty|','Apakah termasuk barang pelengkap / pendukung atau souvenir.');
INSERT INTO `cms_type_metas` VALUES (865,18,'form-warehouse','','multibrowse','','Pencatatan stok barang di berbagai gudang tertentu.');
INSERT INTO `cms_type_metas` VALUES (866,18,'form-exhibition','','multibrowse','','Pencatatan stok barang yang dibawa ke berbagai pameran tertentu untuk sementara waktu.');
INSERT INTO `cms_type_metas` VALUES (379,20,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (380,20,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (704,20,'form-payment_balance','','text','is_numeric|','Current payment balance to vendor (USD).');
INSERT INTO `cms_type_metas` VALUES (703,20,'form-grand_total_price','','text','is_numeric|','Seluruh total harga pembelian produk diamond dalam satuan currency terpilih.');
INSERT INTO `cms_type_metas` VALUES (702,20,'form-currency','USD\r\nHKD','radio','not_empty|','Vendor Price in USD or HKD.');
INSERT INTO `cms_type_metas` VALUES (701,20,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (700,20,'form-total_pcs','','text','is_numeric|','Total pcs of diamond purchased.');
INSERT INTO `cms_type_metas` VALUES (698,20,'form-vendor','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (699,20,'form-warehouse','','browse','','Gudang tempat penerimaan produk diamond yang dikirim vendor.');
INSERT INTO `cms_type_metas` VALUES (409,21,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (410,21,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (706,21,'form-vendor','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (707,21,'form-warehouse','','browse','','Gudang tempat penerimaan produk cor yang dikirim vendor.');
INSERT INTO `cms_type_metas` VALUES (708,21,'form-total_pcs','','text','is_numeric|','Total pcs of jewelries purchased.');
INSERT INTO `cms_type_metas` VALUES (709,21,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (710,21,'form-total_weight','','text','is_numeric|','Total weight of jewelries purchased (gram).');
INSERT INTO `cms_type_metas` VALUES (711,21,'form-payment_balance','','text','is_numeric|','Current payment balance to vendor (gram).');
INSERT INTO `cms_type_metas` VALUES (418,22,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (419,22,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (542,4,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (541,4,'form-kategori','End User\r\nRetailer\r\nWholesaler','radio','not_empty|','Tingkatan kategori pelanggan.');
INSERT INTO `cms_type_metas` VALUES (540,4,'form-kode_pelanggan','','text','','Kode singkat unik pelanggan.');
INSERT INTO `cms_type_metas` VALUES (787,14,'form-return_detail','','textarea','','Return information detail.');
INSERT INTO `cms_type_metas` VALUES (788,14,'form-omzet','','text','','Produk masuk omzet mana dan kapan.');
INSERT INTO `cms_type_metas` VALUES (789,14,'form-client_invoice_code','','text','','Kode invoice untuk pihak client.');
INSERT INTO `cms_type_metas` VALUES (790,14,'form-client_invoice_date','','datepicker','','Sold date to client.');
INSERT INTO `cms_type_metas` VALUES (791,14,'form-client','','browse','','Who purchase this product.');
INSERT INTO `cms_type_metas` VALUES (792,14,'form-client_x','','text','is_numeric|','Client sell X value.');
INSERT INTO `cms_type_metas` VALUES (793,14,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (794,14,'form-wholesaler_x','','text','is_numeric|','Client wholesaler sell X value.');
INSERT INTO `cms_type_metas` VALUES (795,14,'form-total_sold_price','','text','','Total sold price to client in USD.');
INSERT INTO `cms_type_metas` VALUES (753,22,'form-grand_total_price','','text','is_numeric|','Seluruh total harga penjualan produk diamond (USD).');
INSERT INTO `cms_type_metas` VALUES (839,17,'form-payment_credit_card','','text','','Payment from client using credit card.');
INSERT INTO `cms_type_metas` VALUES (840,17,'form-payment_return_goods','','text','','Payment from client using return goods.');
INSERT INTO `cms_type_metas` VALUES (841,17,'form-total_payment_24k','','text','','Total payment 24K that should be paid by client (gram).');
INSERT INTO `cms_type_metas` VALUES (752,22,'form-disc_adjustment','','text','is_numeric|','Special discount adjustment for this invoice (USD).');
INSERT INTO `cms_type_metas` VALUES (751,22,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (750,22,'form-total_pcs','','text','is_numeric|','Total pcs of diamond sold.');
INSERT INTO `cms_type_metas` VALUES (749,22,'form-exhibition','','browse','','Tempat pameran di mana produk terjual.');
INSERT INTO `cms_type_metas` VALUES (748,22,'form-warehouse','','browse','','Gudang tempat pengiriman produk diamond kepada client.');
INSERT INTO `cms_type_metas` VALUES (747,22,'form-sale_venue','Warehouse\r\nExhibition','radio','not_empty|','Apakah penjualan terjadi dari warehouse atau exhibition (pameran).');
INSERT INTO `cms_type_metas` VALUES (746,22,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (744,22,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (745,22,'form-client','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (697,20,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (705,21,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (754,22,'form-payment_balance','','text','is_numeric|','Current payment balance from client (USD).');
INSERT INTO `cms_type_metas` VALUES (755,22,'form-rp_rate','','text','is_numeric|','IDR rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (626,23,'category','invoice',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (627,23,'title_key','Invoice Code',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (741,23,'form-payment_balance','','text','is_numeric|','Current payment balance from client (gram).');
INSERT INTO `cms_type_metas` VALUES (735,23,'form-x_100','','text','is_numeric|','<strong>X 100</strong> factor (KOREA).');
INSERT INTO `cms_type_metas` VALUES (736,23,'form-sold_110','','text','is_numeric|','Weight of <strong>SOLD 110</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (737,23,'form-x_110','','text','is_numeric|','<strong>X 110</strong> factor (999 SIMPLE).');
INSERT INTO `cms_type_metas` VALUES (738,23,'form-sold_115','','text','is_numeric|','Weight of <strong>SOLD 115</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (739,23,'form-x_115','','text','is_numeric|','<strong>X 115</strong> factor (999 3D).');
INSERT INTO `cms_type_metas` VALUES (740,23,'form-disc_adjustment','','text','is_numeric|','Special discount adjustment for this invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (817,17,'form-stock_date','','datepicker','','Kapan terakhir produk ini masuk ke WH sbg ready stock.');
INSERT INTO `cms_type_metas` VALUES (816,17,'form-warehouse','','browse','','Gudang di mana produk tersimpan.');
INSERT INTO `cms_type_metas` VALUES (815,17,'form-vendor_gr','','text','','Total weight of jewelries purchased on this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (814,17,'form-vendor_pcs','','text','is_numeric|','Total pcs of jewelries purchased on this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (813,17,'form-vendor_x','','text','is_numeric|','Product X for this vendor.');
INSERT INTO `cms_type_metas` VALUES (812,17,'form-vendor','','browse','','Pihak vendor yang menyediakan produk ini.');
INSERT INTO `cms_type_metas` VALUES (811,17,'form-vendor_invoice_code','','text','','Kode invoice dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (810,17,'form-item_size','','text','is_numeric|','Ukuran produk cor.');
INSERT INTO `cms_type_metas` VALUES (809,17,'form-item_weight','','text','is_numeric|','Berat produk cor dalam satuan <strong>gram</strong>.');
INSERT INTO `cms_type_metas` VALUES (808,17,'form-product_brand','','browse','','Merk produk cor.');
INSERT INTO `cms_type_metas` VALUES (807,17,'form-product_type','','browse','','Tipe produk cor.');
INSERT INTO `cms_type_metas` VALUES (734,23,'form-sold_100','','text','is_numeric|','Weight of <strong>SOLD 100</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (733,23,'form-x_125','','text','is_numeric|','<strong>X 125</strong> factor (ITALY).');
INSERT INTO `cms_type_metas` VALUES (732,23,'form-sold_125','','text','is_numeric|','Weight of <strong>SOLD 125</strong> (gram).');
INSERT INTO `cms_type_metas` VALUES (731,23,'form-total_item_sent','','text','is_numeric|','Jumlah produk yang sudah terkirim (pcs).');
INSERT INTO `cms_type_metas` VALUES (730,23,'form-total_pcs','','text','is_numeric|','Total pcs of jewelries sold.');
INSERT INTO `cms_type_metas` VALUES (729,23,'form-exhibition','','browse','','Tempat pameran di mana produk terjual.');
INSERT INTO `cms_type_metas` VALUES (728,23,'form-warehouse','','browse','','Gudang tempat pengiriman produk cor kepada client.');
INSERT INTO `cms_type_metas` VALUES (727,23,'form-sale_venue','Warehouse\r\nExhibition','radio','not_empty|','Apakah penjualan terjadi dari warehouse atau exhibition (pameran).');
INSERT INTO `cms_type_metas` VALUES (726,23,'form-wholesaler','','browse','','The wholesaler of selected client.');
INSERT INTO `cms_type_metas` VALUES (725,23,'form-client','','browse','not_empty|','');
INSERT INTO `cms_type_metas` VALUES (724,23,'form-date','','datepicker','not_empty|','Issued invoice date.');
INSERT INTO `cms_type_metas` VALUES (742,23,'form-gold_price','','text','is_numeric|','Current gold price per Gram (IDR).');
INSERT INTO `cms_type_metas` VALUES (743,23,'form-additional_cost','','text','is_numeric|','Total additional cost for this invoice (gram).');
INSERT INTO `cms_type_metas` VALUES (756,22,'form-additional_cost','','text','is_numeric|','Total additional cost for this invoice (USD).');
INSERT INTO `cms_type_metas` VALUES (796,14,'form-sold_price_usd','','text','','Sold price paid in USD.');
INSERT INTO `cms_type_metas` VALUES (797,14,'form-sold_price_rp','','text','is_numeric|','Sold price paid in IDR.');
INSERT INTO `cms_type_metas` VALUES (798,14,'form-rp_rate','','text','is_numeric|','IDR rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (799,14,'form-client_outstanding','','textarea','','');
INSERT INTO `cms_type_metas` VALUES (800,14,'form-payment_credit_card','','text','','Payment from client using credit card.');
INSERT INTO `cms_type_metas` VALUES (801,14,'form-payment_cicilan','','text','','Payment from client using bank installment (HSBC / PERMATA / CITI) 3 / 6 / 12 months.');
INSERT INTO `cms_type_metas` VALUES (802,14,'form-payment_cash','','text','','Payment from client using cash / bank transfer / debit card.');
INSERT INTO `cms_type_metas` VALUES (803,14,'form-payment_checks','','text','','Payment from client using bank checks.');
INSERT INTO `cms_type_metas` VALUES (804,14,'form-prev_sold_price','','text','','Previous sold price.');
INSERT INTO `cms_type_metas` VALUES (805,14,'form-prev_barcode','','text','','Previous barcode / price tag.');
INSERT INTO `cms_type_metas` VALUES (806,14,'form-prev_sold_note','','textarea','','Previous sold note for transaction history.');
INSERT INTO `cms_type_metas` VALUES (842,17,'form-payment_balance','','text','','Total payment balance on this client invoice.');
INSERT INTO `cms_type_metas` VALUES (843,17,'form-transaction_history','','textarea','','');
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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_types`
--

LOCK TABLES `cms_types` WRITE;
/*!40000 ALTER TABLE `cms_types` DISABLE KEYS */;
INSERT INTO `cms_types` VALUES (1,'Media Library','media','All media image is stored here.',0,0,'2013-01-15 03:35:14',1,'2013-01-15 03:35:14',1);
INSERT INTO `cms_types` VALUES (4,'Client','client','Daftar pelanggan WAN Signature, baik berupa Toko maupun End User.',0,0,'2015-06-02 22:37:24',1,'2015-06-08 16:28:54',1);
INSERT INTO `cms_types` VALUES (5,'Vendor','vendor','Daftar vendor WAN Signature.',0,0,'2015-06-02 23:23:54',1,'2015-06-02 23:23:54',1);
INSERT INTO `cms_types` VALUES (6,'Bank','bank','Daftar bank yang bekerja sama dengan WAN Signature.',0,0,'2015-06-02 23:42:49',1,'2015-06-02 23:42:49',1);
INSERT INTO `cms_types` VALUES (7,'Salesman','salesman','Sales(wo)man yang bertugas melayani client WAN Signature.',0,0,'2015-06-03 10:55:24',1,'2015-06-03 10:55:24',1);
INSERT INTO `cms_types` VALUES (8,'USD Rate','usd-rate','Live Exchange Rate (terhadap $ USD)',0,0,'2015-06-03 15:39:44',1,'2015-06-03 15:39:44',1);
INSERT INTO `cms_types` VALUES (9,'Warehouse','warehouse','Daftar Gudang tempat penyimpanan produk WAN Signature beserta barang Logistic / Pelengkap.',0,2,'2015-06-03 22:26:59',1,'2015-06-03 22:38:14',1);
INSERT INTO `cms_types` VALUES (10,'History Masuk','history-masuk','Seluruh pencatatan history barang yg masuk ke warehouse ini.',9,0,'2015-06-03 22:36:32',1,'2015-06-03 22:36:32',1);
INSERT INTO `cms_types` VALUES (11,'History Keluar','history-keluar','Seluruh pencatatan history barang yg keluar dari warehouse ini.',9,0,'2015-06-03 22:38:14',1,'2015-06-03 22:38:14',1);
INSERT INTO `cms_types` VALUES (12,'Product Type','product-type','Berbagai macam tipe produk WAN Signature.',0,0,'2015-06-03 23:22:25',1,'2015-06-08 16:28:09',1);
INSERT INTO `cms_types` VALUES (13,'Product Brand','product-brand','Berbagai macam merk produk WAN Signature.',0,0,'2015-06-03 23:30:45',1,'2015-06-03 23:30:45',1);
INSERT INTO `cms_types` VALUES (14,'Diamond','diamond','Diamond product variations by WAN Signature.',0,0,'2015-06-04 15:43:39',1,'2015-06-09 16:48:00',1);
INSERT INTO `cms_types` VALUES (15,'Exhibition','exhibition','Data lengkap pameran yang diadakan oleh WAN Signature.',0,1,'2015-06-05 11:06:28',1,'2015-06-05 14:20:27',1);
INSERT INTO `cms_types` VALUES (16,'Showpiece','showpiece','Produk WAN Signature yang dipamerkan dalam exhibition ini.',15,0,'2015-06-05 14:20:27',1,'2015-06-05 14:20:27',1);
INSERT INTO `cms_types` VALUES (17,'Cor Jewelry','cor-jewelry','Cor jewelry produced by WAN Signature.',0,0,'2015-06-06 00:27:13',1,'2015-06-09 16:49:04',1);
INSERT INTO `cms_types` VALUES (18,'Logistic','logistic','Barang-barang pelengkap / pendukung / souvenir (dapat dimasukan bbrp stok sekaligus).',0,0,'2015-06-07 09:16:54',1,'2015-06-11 10:42:00',1);
INSERT INTO `cms_types` VALUES (20,'Dmd Vendor Invoice','dmd-vendor-invoice','Surat pemesanan produk diamond terhadap vendor.',0,0,'2015-06-07 12:50:45',1,'2015-06-09 16:15:59',1);
INSERT INTO `cms_types` VALUES (21,'Cor Vendor Invoice','cor-vendor-invoice','Surat pemesanan produk cor terhadap vendor.',0,0,'2015-06-07 14:19:23',1,'2015-06-09 16:18:31',1);
INSERT INTO `cms_types` VALUES (22,'Dmd Client Invoice','dmd-client-invoice','Dokumen invoice penjualan produk diamond terhadap client.',0,0,'2015-06-07 14:34:39',1,'2015-06-09 16:43:58',1);
INSERT INTO `cms_types` VALUES (23,'Cor Client Invoice','cor-client-invoice','Dokumen invoice penjualan produk cor terhadap client.',0,0,'2015-06-09 13:30:14',1,'2015-06-09 16:26:59',1);
INSERT INTO `cms_types` VALUES (24,'Surat Jalan','surat-jalan','Dokumen tanda bukti pengiriman barang, baik terhadap client (penjualan), vendor (return), maupun perpindahan antar warehouse / exhibition.',0,0,'2015-06-09 23:46:04',1,'2015-06-09 23:46:04',1);
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

-- Dump completed on 2015-06-11 18:17:42
