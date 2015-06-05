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
INSERT INTO `cms_accounts` VALUES (1,1,1,'admin','admin@yahoo.com','169e781bd52860b584879cbe117085da596238f3','2015-06-05 12:17:06','2013-01-04 00:00:00',1,'2014-05-05 15:15:38',1);
INSERT INTO `cms_accounts` VALUES (2,2,2,'Andy Basuki','andybasuki88@gmail.com','d82dff1679e0137a0bab60cc67cc6a2ad36f10a0','2015-06-05 17:35:11','2015-06-02 20:19:53',1,'2015-06-02 20:19:53',1);
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
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
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
INSERT INTO `cms_entries` VALUES (14,'warehouse','Atom WH','atom-wh','',0,0,1,0,'2015-06-03 22:41:50',1,'2015-06-03 22:41:50',1,14,'en-14');
INSERT INTO `cms_entries` VALUES (15,'warehouse','Tunjungan Plaza WH','tunjungan-plaza-wh','',0,0,1,0,'2015-06-03 22:42:27',1,'2015-06-03 22:42:27',1,15,'en-15');
INSERT INTO `cms_entries` VALUES (16,'product-type','DPF','dpf','Diamond Pendants Finish',0,0,1,0,'2015-06-03 23:22:58',1,'2015-06-03 23:24:31',1,16,'en-16');
INSERT INTO `cms_entries` VALUES (17,'product-type','DRF','drf','Diamond Rings Finish',0,0,1,0,'2015-06-03 23:23:16',1,'2015-06-03 23:24:24',1,17,'en-17');
INSERT INTO `cms_entries` VALUES (18,'product-type','DEF','def','Diamond Earrings Finish',0,0,1,0,'2015-06-03 23:24:13',1,'2015-06-03 23:24:13',1,18,'en-18');
INSERT INTO `cms_entries` VALUES (19,'product-type','Pipe Necklace','pipe-necklace','',0,0,1,0,'2015-06-03 23:25:33',1,'2015-06-03 23:25:33',1,19,'en-19');
INSERT INTO `cms_entries` VALUES (20,'product-type','Pipe Bracelet','pipe-bracelet','',0,0,1,0,'2015-06-03 23:25:53',1,'2015-06-03 23:25:53',1,20,'en-20');
INSERT INTO `cms_entries` VALUES (21,'product-brand','BVLGARI','bvlgari','',0,0,1,0,'2015-06-03 23:31:03',1,'2015-06-03 23:31:03',1,21,'en-21');
INSERT INTO `cms_entries` VALUES (22,'product-brand','VAN CLEEF','van-cleef','',0,0,1,0,'2015-06-03 23:31:14',1,'2015-06-03 23:31:14',1,22,'en-22');
INSERT INTO `cms_entries` VALUES (23,'product-brand','HERMES','hermes','',0,0,1,0,'2015-06-03 23:31:27',1,'2015-06-03 23:31:27',1,23,'en-23');
INSERT INTO `cms_entries` VALUES (24,'exhibition','JIJF 2015 (8th)','jijf-2015-8th','',0,0,1,0,'2015-06-05 12:41:43',1,'2015-06-05 12:41:43',1,24,'en-24');
INSERT INTO `cms_entries` VALUES (25,'client','Novi','novi','G243730',0,0,1,0,'2015-06-05 13:41:35',1,'2015-06-05 13:46:35',1,25,'en-25');
INSERT INTO `cms_entries` VALUES (26,'client','Gisela Tania','gisela-tania','G243935',0,0,1,0,'2015-06-05 13:41:56',1,'2015-06-05 13:46:43',1,26,'en-26');
INSERT INTO `cms_entries` VALUES (27,'client','Reyner Gunawan','reyner-gunawan','G243913',0,0,1,0,'2015-06-05 13:42:31',1,'2015-06-05 13:46:53',1,27,'en-27');
INSERT INTO `cms_entries` VALUES (28,'client','Hanna','hanna','G113678',0,0,1,0,'2015-06-05 13:44:06',1,'2015-06-05 13:47:04',1,28,'en-28');
INSERT INTO `cms_entries` VALUES (29,'client','Vonza Silvia','vonza-silvia','G113369',0,0,1,0,'2015-06-05 13:44:30',1,'2015-06-05 13:47:17',1,29,'en-29');
INSERT INTO `cms_entries` VALUES (30,'client','Widya','widya','G113336',0,0,1,0,'2015-06-05 13:44:49',1,'2015-06-05 13:47:27',1,30,'en-30');
INSERT INTO `cms_entries` VALUES (31,'client','Hendra','hendra','G113223',0,0,1,0,'2015-06-05 13:45:09',1,'2015-06-05 13:47:35',1,31,'en-31');
INSERT INTO `cms_entries` VALUES (32,'client','Yani','yani','G1151167 , G113240',0,0,1,0,'2015-06-05 13:45:30',1,'2015-06-05 13:47:44',1,32,'en-32');
INSERT INTO `cms_entries` VALUES (33,'client','Grace Novarinus','grace-novarinus','G1231100 , G1531341 , G113659',0,0,1,0,'2015-06-05 13:46:21',1,'2015-06-05 13:46:21',1,33,'en-33');
INSERT INTO `cms_entries` VALUES (34,'client','Sabrina','sabrina','G243931',0,0,1,0,'2015-06-05 13:48:23',1,'2015-06-05 13:48:23',1,34,'en-34');
INSERT INTO `cms_entries` VALUES (35,'client','Cik Ninih','cik-ninih','G146957 , G234932',0,0,1,0,'2015-06-05 13:48:47',1,'2015-06-05 13:50:59',1,35,'en-35');
INSERT INTO `cms_entries` VALUES (36,'client','Yuli','yuli','G542880, G5131033',0,0,1,0,'2015-06-05 13:49:10',1,'2015-06-05 13:49:10',1,36,'en-36');
INSERT INTO `cms_entries` VALUES (37,'client','Sri Rahayu','sri-rahayu','G5131024',0,0,1,0,'2015-06-05 13:49:46',1,'2015-06-05 13:49:46',1,37,'en-37');
INSERT INTO `cms_entries` VALUES (38,'client','Susan','susan','G1451362, G1452063, G1151941',0,0,1,0,'2015-06-05 13:51:21',1,'2015-06-05 13:51:21',1,38,'en-38');
INSERT INTO `cms_entries` VALUES (39,'client','Michael','michael','G243940, G243905',0,0,1,0,'2015-06-05 13:51:45',1,'2015-06-05 13:51:45',1,39,'en-39');
INSERT INTO `cms_entries` VALUES (40,'diamond','102374','102374','',0,0,1,0,'2015-06-05 17:11:11',1,'2015-06-05 17:35:22',2,40,'en-40');
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
) ENGINE=MyISAM AUTO_INCREMENT=187 DEFAULT CHARSET=latin1;
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
INSERT INTO `cms_entry_metas` VALUES (31,14,'form-warehouse_employee','');
INSERT INTO `cms_entry_metas` VALUES (32,15,'backup-slug','\ntunjungan-plaza-wh\n');
INSERT INTO `cms_entry_metas` VALUES (33,15,'form-warehouse_employee','');
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
INSERT INTO `cms_entry_metas` VALUES (114,27,'form-telepon','081355527131');
INSERT INTO `cms_entry_metas` VALUES (113,27,'form-alamat','Apart Water Palace blok C no. 1912');
INSERT INTO `cms_entry_metas` VALUES (112,27,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (66,28,'backup-slug','\nhanna\n');
INSERT INTO `cms_entry_metas` VALUES (120,28,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (119,28,'form-telepon','031-70574934 / 7E715559');
INSERT INTO `cms_entry_metas` VALUES (118,28,'form-alamat','Rungkut Asri Tengah 4/31');
INSERT INTO `cms_entry_metas` VALUES (117,28,'form-kategori','End User');
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
INSERT INTO `cms_entry_metas` VALUES (97,33,'form-kategori','End User');
INSERT INTO `cms_entry_metas` VALUES (98,33,'form-alamat','JL. W.R Supratman 64 Rambipuji Jbr');
INSERT INTO `cms_entry_metas` VALUES (99,33,'form-telepon','0811361231 / 5229861F');
INSERT INTO `cms_entry_metas` VALUES (100,33,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (101,33,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (106,25,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (110,26,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (111,26,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (115,27,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (116,27,'form-exhibition','');
INSERT INTO `cms_entry_metas` VALUES (121,28,'form-exhibition','');
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
INSERT INTO `cms_entry_metas` VALUES (166,35,'form-telepon','08122351037');
INSERT INTO `cms_entry_metas` VALUES (165,35,'form-alamat','THI 1 B2 NO 6, Bandung');
INSERT INTO `cms_entry_metas` VALUES (164,35,'form-kategori','End User');
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
INSERT INTO `cms_entry_metas` VALUES (167,35,'form-warehouse','');
INSERT INTO `cms_entry_metas` VALUES (168,35,'form-exhibition','');
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
INSERT INTO `cms_entry_metas` VALUES (180,40,'backup-slug','\n003524\n102373\n102374\n');
INSERT INTO `cms_entry_metas` VALUES (186,40,'form-report_type','SR');
INSERT INTO `cms_entry_metas` VALUES (185,40,'form-vendor_currency','USD');
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
) ENGINE=MyISAM AUTO_INCREMENT=248 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_type_metas`
--

LOCK TABLES `cms_type_metas` WRITE;
/*!40000 ALTER TABLE `cms_type_metas` DISABLE KEYS */;
INSERT INTO `cms_type_metas` VALUES (5,4,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (4,4,'category','partners',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (6,4,'form-kode_pelanggan','','text','','Kode singkat unik pelanggan.');
INSERT INTO `cms_type_metas` VALUES (7,4,'form-kategori','End User\r\nRetailer\r\nWholesaler','radio','not_empty|','Tingkatan kategori pelanggan.');
INSERT INTO `cms_type_metas` VALUES (8,4,'form-alamat','','textarea','','Alamat pribadi / toko pelanggan.');
INSERT INTO `cms_type_metas` VALUES (9,4,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (10,4,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (11,4,'form-salesman','','browse','','Sales(wo)man yang melayani client ini.');
INSERT INTO `cms_type_metas` VALUES (12,4,'form-warehouse','','multibrowse','','Client pernah ambil produk dari WH mana saja.');
INSERT INTO `cms_type_metas` VALUES (13,4,'form-exhibition','','multibrowse','','Client pernah ambil produk dari pameran mana saja.');
INSERT INTO `cms_type_metas` VALUES (14,4,'form-diamond_sell_x','','text','is_numeric|','Nilai Sell X untuk produk diamond.');
INSERT INTO `cms_type_metas` VALUES (15,4,'form-italy_sell_x','','text','is_numeric|','Nilai Sell X untuk produk COR Italy (1.25)');
INSERT INTO `cms_type_metas` VALUES (16,4,'form-korea_sell_x','','text','is_numeric|','Nilai Sell X untuk produk COR Korea (1.00)');
INSERT INTO `cms_type_metas` VALUES (17,4,'form-cor_999_simple_x','','text','is_numeric|','Nilai Sell X untuk produk COR 999 Simple (1.10)');
INSERT INTO `cms_type_metas` VALUES (18,4,'form-cor_999_3d_x','','text','is_numeric|','Nilai Sell X untuk produk COR 999 3D (1.15)');
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
INSERT INTO `cms_type_metas` VALUES (33,8,'category','general',NULL,NULL,NULL);
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
INSERT INTO `cms_type_metas` VALUES (242,14,'form-payment_checks_2','','text','','Payment from client using bank checks.');
INSERT INTO `cms_type_metas` VALUES (241,14,'form-payment_checks_1','','text','','Payment from client using bank checks.');
INSERT INTO `cms_type_metas` VALUES (240,14,'form-payment_cash','','text','','Payment from client using cash / bank transfer / debit card.');
INSERT INTO `cms_type_metas` VALUES (239,14,'form-payment_cicilan','','text','','Payment from client using bank installment (HSBC / PERMATA / CITI) 3 / 6 / 12 months.');
INSERT INTO `cms_type_metas` VALUES (238,14,'form-payment_credit_card','','text','','Payment from client using credit card.');
INSERT INTO `cms_type_metas` VALUES (237,14,'form-client_outstanding','','textarea','','');
INSERT INTO `cms_type_metas` VALUES (236,14,'form-rp_rate','','text','is_numeric|','IDR rate to $1 USD.');
INSERT INTO `cms_type_metas` VALUES (235,14,'form-sold_price_rp','','text','is_numeric|','Sold price paid in IDR.');
INSERT INTO `cms_type_metas` VALUES (234,14,'form-sold_price_usd','','text','','Sold price paid in USD.');
INSERT INTO `cms_type_metas` VALUES (233,14,'form-total_sold_price','','text','','Total sold price to client in USD.');
INSERT INTO `cms_type_metas` VALUES (232,14,'form-client_invoice_code','','text','','Kode invoice untuk pihak client.');
INSERT INTO `cms_type_metas` VALUES (231,14,'form-client_invoice_date','','datepicker','','Sold date to client.');
INSERT INTO `cms_type_metas` VALUES (230,14,'form-retail_x','','text','is_numeric|','Retail client sell X value.');
INSERT INTO `cms_type_metas` VALUES (229,14,'form-retail_client','','browse','','Retail client purchasing this product.');
INSERT INTO `cms_type_metas` VALUES (228,14,'form-wholesale_x','','text','is_numeric|','Wholesale client sell X value.');
INSERT INTO `cms_type_metas` VALUES (227,14,'form-wholesale_client','','browse','','Wholesale client purchasing this product.');
INSERT INTO `cms_type_metas` VALUES (226,14,'form-omzet','','text','','Produk masuk omzet mana dan kapan.');
INSERT INTO `cms_type_metas` VALUES (225,14,'form-return_detail','','textarea','','Return information detail.');
INSERT INTO `cms_type_metas` VALUES (203,14,'form-carat_2','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (204,14,'form-carat_3','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (205,14,'form-carat_4','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (206,14,'form-gold_carat','','text','','Kadar carat GOLD pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (207,14,'form-gold_weight','','text','','Berat emas yg terkandung pada produk (gram).');
INSERT INTO `cms_type_metas` VALUES (208,14,'form-item_ref_code','','text','','Item reference code.');
INSERT INTO `cms_type_metas` VALUES (209,14,'form-item_ref_code_x2','','text','','Item reference code (X2)');
INSERT INTO `cms_type_metas` VALUES (210,14,'form-vendor','','browse','','Pihak vendor yang menyediakan produk ini.');
INSERT INTO `cms_type_metas` VALUES (211,14,'form-vendor_item_code','','text','','Kode produk asal dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (212,14,'form-vendor_invoice_code','','text','','Kode invoice dari pihak vendor.');
INSERT INTO `cms_type_metas` VALUES (213,14,'form-vendor_invoice_date','','datepicker','','Purchase date from vendor.');
INSERT INTO `cms_type_metas` VALUES (214,14,'form-vendor_status','','text','','Product status with vendor (Sold / Credit / Consignment / Return / Syute / etc.)');
INSERT INTO `cms_type_metas` VALUES (215,14,'form-vendor_note','','text','','Additional information for this vendor invoice.');
INSERT INTO `cms_type_metas` VALUES (216,14,'form-vendor_currency','USD\r\nHKD','radio','not_empty|','Vendor Price in USD / HKD.');
INSERT INTO `cms_type_metas` VALUES (217,14,'form-vendor_barcode','','text','is_numeric|','Vendor original price.');
INSERT INTO `cms_type_metas` VALUES (218,14,'form-vendor_x','','text','is_numeric|','Vendor capital X for calculating price.');
INSERT INTO `cms_type_metas` VALUES (219,14,'form-vendor_usd','','text','is_numeric|','Vendor USD price result.');
INSERT INTO `cms_type_metas` VALUES (220,14,'form-vendor_hkd','','text','is_numeric|','Vendor HKD price result.');
INSERT INTO `cms_type_metas` VALUES (221,14,'form-report_date','','datepicker','','');
INSERT INTO `cms_type_metas` VALUES (222,14,'form-report_type','SR\r\nRR','radio','not_empty|','Sold Report / Return Report.');
INSERT INTO `cms_type_metas` VALUES (223,14,'form-temp_report','','text','','Temporary report date / notes.');
INSERT INTO `cms_type_metas` VALUES (224,14,'form-return_date','','datepicker','','When this product returned to vendor.');
INSERT INTO `cms_type_metas` VALUES (133,15,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (134,15,'title_key','Nama',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (138,15,'form-end_date','','datepicker','','Tanggal terakhir pameran ini berlangsung.');
INSERT INTO `cms_type_metas` VALUES (137,15,'form-start_date','','datepicker','','Kapan pameran ini mulai berlangsung.');
INSERT INTO `cms_type_metas` VALUES (139,15,'form-alamat','','textarea','','Alamat lengkap di mana pameran diadakan.');
INSERT INTO `cms_type_metas` VALUES (140,15,'form-telepon','','text','','Nomer Telp stand pameran yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (141,15,'form-warehouse_employee','','multibrowse','','Pegawai yg bertanggung jawab untuk pengadaan event pameran ini.');
INSERT INTO `cms_type_metas` VALUES (142,16,'category','storage',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (202,14,'form-carat_1','','text','','Kadar carat DIAMOND pada produk ini.');
INSERT INTO `cms_type_metas` VALUES (200,14,'form-exhibition','','browse','','Exhibition di mana produk ini sedang dipamerkan.');
INSERT INTO `cms_type_metas` VALUES (199,14,'form-warehouse','','browse','','Gudang di mana produk tersimpan.');
INSERT INTO `cms_type_metas` VALUES (198,14,'form-status_in_wan','','text','','Current product status (Stock / Sold / Consignment / Return / Changed / etc.)');
INSERT INTO `cms_type_metas` VALUES (197,14,'form-sell_barcode','','text','is_numeric|','Adjusted product price tag.');
INSERT INTO `cms_type_metas` VALUES (196,14,'form-barcode','','text','is_numeric|','Product price tag.');
INSERT INTO `cms_type_metas` VALUES (195,14,'form-product_type','','browse','','Tipe produk berlian.');
INSERT INTO `cms_type_metas` VALUES (243,14,'form-payment_others_1','','text','','Payment from client using others method.');
INSERT INTO `cms_type_metas` VALUES (244,14,'form-payment_others_2','','text','','Payment from client using others method.');
INSERT INTO `cms_type_metas` VALUES (245,14,'form-prev_sold_price','','text','','Previous sold price.');
INSERT INTO `cms_type_metas` VALUES (246,14,'form-prev_barcode','','text','','Previous barcode / price tag.');
INSERT INTO `cms_type_metas` VALUES (247,14,'form-prev_sold_note','','textarea','','Previous sold note for transaction history.');
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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_types`
--

LOCK TABLES `cms_types` WRITE;
/*!40000 ALTER TABLE `cms_types` DISABLE KEYS */;
INSERT INTO `cms_types` VALUES (1,'Media Library','media','All media image is stored here.',0,0,'2013-01-15 03:35:14',1,'2013-01-15 03:35:14',1);
INSERT INTO `cms_types` VALUES (4,'Client','client','Daftar pelanggan WAN Signature, baik berupa Toko maupun End User.',0,0,'2015-06-02 22:37:24',1,'2015-06-02 22:37:24',1);
INSERT INTO `cms_types` VALUES (5,'Vendor','vendor','Daftar vendor WAN Signature.',0,0,'2015-06-02 23:23:54',1,'2015-06-02 23:23:54',1);
INSERT INTO `cms_types` VALUES (6,'Bank','bank','Daftar bank yang bekerja sama dengan WAN Signature.',0,0,'2015-06-02 23:42:49',1,'2015-06-02 23:42:49',1);
INSERT INTO `cms_types` VALUES (7,'Salesman','salesman','Sales(wo)man yang bertugas melayani client WAN Signature.',0,0,'2015-06-03 10:55:24',1,'2015-06-03 10:55:24',1);
INSERT INTO `cms_types` VALUES (8,'USD Rate','usd-rate','Live Exchange Rate (terhadap $ USD)',0,0,'2015-06-03 15:39:44',1,'2015-06-03 15:39:44',1);
INSERT INTO `cms_types` VALUES (9,'Warehouse','warehouse','Daftar Gudang tempat penyimpanan produk WAN Signature beserta barang Logistic / Pelengkap.',0,2,'2015-06-03 22:26:59',1,'2015-06-03 22:38:14',1);
INSERT INTO `cms_types` VALUES (10,'History Masuk','history-masuk','Seluruh pencatatan history barang yg masuk ke warehouse ini.',9,0,'2015-06-03 22:36:32',1,'2015-06-03 22:36:32',1);
INSERT INTO `cms_types` VALUES (11,'History Keluar','history-keluar','Seluruh pencatatan history barang yg keluar dari warehouse ini.',9,0,'2015-06-03 22:38:14',1,'2015-06-03 22:38:14',1);
INSERT INTO `cms_types` VALUES (12,'Product Type','product-type','Berbagai macam tipe produk WAN Signature.',0,0,'2015-06-03 23:22:25',1,'2015-06-03 23:22:25',1);
INSERT INTO `cms_types` VALUES (13,'Product Brand','product-brand','Berbagai macam merk produk WAN Signature.',0,0,'2015-06-03 23:30:45',1,'2015-06-03 23:30:45',1);
INSERT INTO `cms_types` VALUES (14,'Diamond','diamond','Diamond product variations by WAN Signature.',0,0,'2015-06-04 15:43:39',1,'2015-06-05 16:04:54',1);
INSERT INTO `cms_types` VALUES (15,'Exhibition','exhibition','Data lengkap pameran yang diadakan oleh WAN Signature.',0,1,'2015-06-05 11:06:28',1,'2015-06-05 14:20:27',1);
INSERT INTO `cms_types` VALUES (16,'Showpiece','showpiece','Produk WAN Signature yang dipamerkan dalam exhibition ini.',15,0,'2015-06-05 14:20:27',1,'2015-06-05 14:20:27',1);
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

-- Dump completed on 2015-06-05 17:42:41
