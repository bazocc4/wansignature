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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` tinyint(4) NOT NULL,
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
INSERT INTO `cms_accounts` VALUES (1,1,1,'admin','admin@yahoo.com','169e781bd52860b584879cbe117085da596238f3','2015-06-03 21:11:29','2013-01-04 00:00:00',1,'2013-01-04 00:00:00',1);
INSERT INTO `cms_accounts` VALUES (2,2,2,'Andy Basuki','andybasuki88@gmail.com','d82dff1679e0137a0bab60cc67cc6a2ad36f10a0','2015-06-02 20:20:02','2015-06-02 20:19:53',1,'2015-06-02 20:19:53',1);
/*!40000 ALTER TABLE `cms_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_entries`
--

DROP TABLE IF EXISTS `cms_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_type` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `description` text,
  `main_image` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `count` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `lang_code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_entries`
--

LOCK TABLES `cms_entries` WRITE;
/*!40000 ALTER TABLE `cms_entries` DISABLE KEYS */;
INSERT INTO `cms_entries` VALUES (3,'bank','BCA Group','bca-group','[info here]',2,0,1,0,'2015-06-03 00:42:27',1,'2015-06-03 00:42:27',1,3,'en-3');
INSERT INTO `cms_entries` VALUES (2,'media','Bank BCA','bank-bca',NULL,0,0,1,0,'2015-06-03 00:40:46',1,'2015-06-03 00:40:47',1,2,'en-2');
INSERT INTO `cms_entries` VALUES (4,'media','logo-bank-mandiri','logo-bank-mandiri',NULL,0,0,1,0,'2015-06-03 00:44:38',1,'2015-06-03 00:44:38',1,4,'en-4');
INSERT INTO `cms_entries` VALUES (5,'bank','Mandiri','mandiri','[info here]',4,0,1,0,'2015-06-03 00:45:20',1,'2015-06-03 00:45:20',1,5,'en-5');
INSERT INTO `cms_entries` VALUES (7,'bank','HSBC','hsbc-1','[info here]',8,0,1,0,'2015-06-03 00:47:51',1,'2015-06-03 00:49:13',1,7,'en-7');
INSERT INTO `cms_entries` VALUES (8,'media','hsbc','hsbc-2',NULL,0,0,1,0,'2015-06-03 00:49:07',1,'2015-06-03 00:49:07',1,8,'en-8');
INSERT INTO `cms_entries` VALUES (9,'usd-rate','IDR','idr','Indonesian Rupiah.',0,0,1,0,'2015-06-03 15:58:49',1,'2015-06-03 16:04:40',1,9,'en-9');
INSERT INTO `cms_entries` VALUES (10,'usd-rate','HKD','hkd','Hongkong Dollar.',0,0,1,0,'2015-06-03 16:04:27',1,'2015-06-03 16:04:27',1,10,'en-10');
INSERT INTO `cms_entries` VALUES (11,'usd-rate','CNY','cny','Chinese Yuan.',0,0,1,0,'2015-06-03 16:05:14',1,'2015-06-03 16:05:14',1,11,'en-11');
INSERT INTO `cms_entries` VALUES (12,'usd-rate','Euro','euro','',0,0,1,0,'2015-06-03 21:13:08',1,'2015-06-03 21:13:08',1,12,'en-12');
/*!40000 ALTER TABLE `cms_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_entry_metas`
--

DROP TABLE IF EXISTS `cms_entry_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_entry_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
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
/*!40000 ALTER TABLE `cms_entry_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_roles`
--

DROP TABLE IF EXISTS `cms_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `description` text,
  `count` int(11) DEFAULT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text,
  `input_type` varchar(500) DEFAULT NULL,
  `validation` text,
  `instruction` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_type_metas`
--

LOCK TABLES `cms_type_metas` WRITE;
/*!40000 ALTER TABLE `cms_type_metas` DISABLE KEYS */;
INSERT INTO `cms_type_metas` VALUES (5,4,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (4,4,'category','master',NULL,NULL,NULL);
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
INSERT INTO `cms_type_metas` VALUES (19,5,'category','master',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (20,5,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (21,5,'form-kode_vendor','','text','','Kode singkat unik vendor.');
INSERT INTO `cms_type_metas` VALUES (22,5,'form-alamat','','textarea','','Alamat perusahaan vendor.');
INSERT INTO `cms_type_metas` VALUES (23,5,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (24,5,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (25,5,'form-capital_x','','text','is_numeric|','Nilai Capital X untuk produk diamond.');
INSERT INTO `cms_type_metas` VALUES (26,6,'category','master',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (27,7,'category','master',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (28,7,'title_key','Nama Lengkap',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (29,7,'form-alamat','','textarea','','Alamat lengkap tempat tinggal.');
INSERT INTO `cms_type_metas` VALUES (30,7,'form-telepon','','text','','Nomer Telp / HP yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (31,7,'form-email','','text','is_email|','Alamat E-mail yang dapat dihubungi.');
INSERT INTO `cms_type_metas` VALUES (32,7,'form-tgl_join','','datepicker','','First time working date at WAN Signature.');
INSERT INTO `cms_type_metas` VALUES (33,8,'category','master',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (34,8,'title_key','Currency',NULL,NULL,NULL);
INSERT INTO `cms_type_metas` VALUES (35,8,'form-rate_value','','text','not_empty|is_numeric|','Harga nominal kurs per $1 USD.');
/*!40000 ALTER TABLE `cms_type_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_types`
--

DROP TABLE IF EXISTS `cms_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `description` text,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
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
/*!40000 ALTER TABLE `cms_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_user_metas`
--

DROP TABLE IF EXISTS `cms_user_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_user_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(500) NOT NULL,
  `lastname` varchar(500) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '1',
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

-- Dump completed on 2015-06-03 21:14:00
