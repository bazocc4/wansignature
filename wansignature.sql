-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2015 at 07:50 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wansignature`
--

-- --------------------------------------------------------

--
-- Table structure for table `cms_accounts`
--

CREATE TABLE IF NOT EXISTS `cms_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` tinyint(4) NOT NULL,
  `username` varchar(500) DEFAULT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `last_login` datetime NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_accounts`
--

INSERT INTO `cms_accounts` (`id`, `user_id`, `role_id`, `username`, `email`, `password`, `last_login`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(1, 1, 1, 'admin', 'admin@yahoo.com', '169e781bd52860b584879cbe117085da596238f3', '2015-06-03 00:35:40', '2013-01-04 00:00:00', 1, '2013-01-04 00:00:00', 1),
(2, 2, 2, 'Andy Basuki', 'andybasuki88@gmail.com', 'd82dff1679e0137a0bab60cc67cc6a2ad36f10a0', '2015-06-02 20:20:02', '2015-06-02 20:19:53', 1, '2015-06-02 20:19:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cms_entries`
--

CREATE TABLE IF NOT EXISTS `cms_entries` (
  `id` int(11) NOT NULL,
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
  `lang_code` varchar(10) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_entries`
--

INSERT INTO `cms_entries` (`id`, `entry_type`, `title`, `slug`, `description`, `main_image`, `parent_id`, `status`, `count`, `created`, `created_by`, `modified`, `modified_by`, `sort_order`, `lang_code`) VALUES
(3, 'bank', 'BCA Group', 'bca-group', '[info here]', 2, 0, 1, 0, '2015-06-03 00:42:27', 1, '2015-06-03 00:42:27', 1, 3, 'en-3'),
(2, 'media', 'Bank BCA', 'bank-bca', NULL, 0, 0, 1, 0, '2015-06-03 00:40:46', 1, '2015-06-03 00:40:47', 1, 2, 'en-2'),
(4, 'media', 'logo-bank-mandiri', 'logo-bank-mandiri', NULL, 0, 0, 1, 0, '2015-06-03 00:44:38', 1, '2015-06-03 00:44:38', 1, 4, 'en-4'),
(5, 'bank', 'Mandiri', 'mandiri', '[info here]', 4, 0, 1, 0, '2015-06-03 00:45:20', 1, '2015-06-03 00:45:20', 1, 5, 'en-5'),
(7, 'bank', 'HSBC', 'hsbc-1', '[info here]', 8, 0, 1, 0, '2015-06-03 00:47:51', 1, '2015-06-03 00:49:13', 1, 7, 'en-7'),
(8, 'media', 'hsbc', 'hsbc-2', NULL, 0, 0, 1, 0, '2015-06-03 00:49:07', 1, '2015-06-03 00:49:07', 1, 8, 'en-8');

-- --------------------------------------------------------

--
-- Table structure for table `cms_entry_metas`
--

CREATE TABLE IF NOT EXISTS `cms_entry_metas` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_entry_metas`
--

INSERT INTO `cms_entry_metas` (`id`, `entry_id`, `key`, `value`) VALUES
(5, 2, 'image_type', 'png'),
(4, 2, 'backup-slug', '\nbank-bca\n'),
(6, 2, 'image_size', '6106'),
(7, 3, 'backup-slug', '\nbca-group\n'),
(8, 4, 'backup-slug', '\nlogo-bank-mandiri\n'),
(9, 4, 'image_type', 'png'),
(10, 4, 'image_size', '5722'),
(11, 5, 'backup-slug', '\nmandiri\n'),
(15, 7, 'backup-slug', '\nhsbc-1\n'),
(16, 8, 'backup-slug', '\nhsbc-2\n'),
(17, 8, 'image_type', 'gif'),
(18, 8, 'image_size', '1569');

-- --------------------------------------------------------

--
-- Table structure for table `cms_roles`
--

CREATE TABLE IF NOT EXISTS `cms_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `description` text,
  `count` int(11) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_roles`
--

INSERT INTO `cms_roles` (`id`, `name`, `description`, `count`) VALUES
(1, 'Super Admin', 'Administrator who has all access for the web without exceptions.', 1),
(2, 'Admin', 'Administrator from the clients.', NULL),
(3, 'Regular User', 'Anyone with no access to admin panel.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cms_settings`
--

CREATE TABLE IF NOT EXISTS `cms_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_settings`
--

INSERT INTO `cms_settings` (`id`, `key`, `value`) VALUES
(1, 'title', 'WAN Signature'),
(2, 'tagline', ''),
(3, 'description', 'Our Company Description here.'),
(4, 'date_format', 'd F Y'),
(5, 'time_format', 'h:i A'),
(6, 'header', ''),
(7, 'top_insert', ''),
(8, 'bottom_insert', ''),
(9, 'google_analytics_code', ''),
(10, 'display_width', '120'),
(11, 'display_height', '120'),
(12, 'display_crop', '0'),
(13, 'thumb_width', '120'),
(14, 'thumb_height', '120'),
(15, 'thumb_crop', '0'),
(16, 'language', 'en_english'),
(17, 'table_view', 'complex'),
(18, 'usd_sell', '9732.00'),
(19, 'custom-pagination', '10'),
(20, 'custom-email_contact', 'andybasuki88@gmail.com'),
(21, 'custom-overwrite_image', 'enable'),
(22, 'custom-bunga_cek', '1.25');

-- --------------------------------------------------------

--
-- Table structure for table `cms_types`
--

CREATE TABLE IF NOT EXISTS `cms_types` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL,
  `description` text,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_types`
--

INSERT INTO `cms_types` (`id`, `name`, `slug`, `description`, `parent_id`, `count`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(1, 'Media Library', 'media', 'All media image is stored here.', 0, 0, '2013-01-15 03:35:14', 1, '2013-01-15 03:35:14', 1),
(4, 'Client', 'client', 'Daftar pelanggan WAN Signature, baik berupa Toko maupun End User.', 0, 0, '2015-06-02 22:37:24', 1, '2015-06-02 22:37:24', 1),
(5, 'Vendor', 'vendor', 'Daftar vendor WAN Signature.', 0, 0, '2015-06-02 23:23:54', 1, '2015-06-02 23:23:54', 1),
(6, 'Bank', 'bank', 'Daftar bank yang bekerja sama dengan WAN Signature.', 0, 0, '2015-06-02 23:42:49', 1, '2015-06-02 23:42:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cms_type_metas`
--

CREATE TABLE IF NOT EXISTS `cms_type_metas` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text,
  `input_type` varchar(500) DEFAULT NULL,
  `validation` text,
  `instruction` varchar(300) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_type_metas`
--

INSERT INTO `cms_type_metas` (`id`, `type_id`, `key`, `value`, `input_type`, `validation`, `instruction`) VALUES
(5, 4, 'title_key', 'Nama Lengkap', NULL, NULL, NULL),
(4, 4, 'category', 'master', NULL, NULL, NULL),
(6, 4, 'form-kode_pelanggan', '', 'text', '', 'Kode singkat unik pelanggan.'),
(7, 4, 'form-kategori', 'End User\r\nRetailer\r\nWholesaler', 'radio', 'not_empty|', 'Tingkatan kategori pelanggan.'),
(8, 4, 'form-alamat', '', 'textarea', '', 'Alamat pribadi / toko pelanggan.'),
(9, 4, 'form-telepon', '', 'text', '', 'Nomer Telp / HP yang dapat dihubungi.'),
(10, 4, 'form-email', '', 'text', 'is_email|', 'Alamat E-mail yang dapat dihubungi.'),
(11, 4, 'form-salesman', '', 'browse', '', 'Sales(wo)man yang menghandle client ini.'),
(12, 4, 'form-warehouse', '', 'multibrowse', '', 'Client pernah ambil produk dari WH mana saja.'),
(13, 4, 'form-exhibition', '', 'multibrowse', '', 'Client pernah ambil produk dari pameran mana saja.'),
(14, 4, 'form-diamond_sell_x', '', 'text', 'is_numeric|', 'Nilai Sell X untuk produk diamond.'),
(15, 4, 'form-italy_sell_x', '', 'text', 'is_numeric|', 'Nilai Sell X untuk produk COR Italy (1.25)'),
(16, 4, 'form-korea_sell_x', '', 'text', 'is_numeric|', 'Nilai Sell X untuk produk COR Korea (1.00)'),
(17, 4, 'form-cor_999_simple_x', '', 'text', 'is_numeric|', 'Nilai Sell X untuk produk COR 999 Simple (1.10)'),
(18, 4, 'form-cor_999_3d_x', '', 'text', 'is_numeric|', 'Nilai Sell X untuk produk COR 999 3D (1.15)'),
(19, 5, 'category', 'master', NULL, NULL, NULL),
(20, 5, 'title_key', 'Nama Lengkap', NULL, NULL, NULL),
(21, 5, 'form-kode_vendor', '', 'text', '', 'Kode singkat unik vendor.'),
(22, 5, 'form-alamat', '', 'textarea', '', 'Alamat perusahaan vendor.'),
(23, 5, 'form-telepon', '', 'text', '', 'Nomer Telp / HP yang dapat dihubungi.'),
(24, 5, 'form-email', '', 'text', 'is_email|', 'Alamat E-mail yang dapat dihubungi.'),
(25, 5, 'form-capital_x', '', 'text', 'is_numeric|', 'Nilai Capital X untuk produk diamond.'),
(26, 6, 'category', 'master', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cms_users`
--

CREATE TABLE IF NOT EXISTS `cms_users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(500) NOT NULL,
  `lastname` varchar(500) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_users`
--

INSERT INTO `cms_users` (`id`, `firstname`, `lastname`, `created`, `created_by`, `modified`, `modified_by`, `status`) VALUES
(1, 'admin', 'zpanel', '2013-01-04 00:00:00', 1, '2014-02-06 10:50:29', 1, 1),
(2, 'Andy', 'Basuki', '2015-06-02 20:18:22', 1, '2015-06-02 20:18:22', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cms_user_metas`
--

CREATE TABLE IF NOT EXISTS `cms_user_metas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key` varchar(500) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_user_metas`
--

INSERT INTO `cms_user_metas` (`id`, `user_id`, `key`, `value`) VALUES
(1, 1, 'gender', 'male'),
(2, 1, 'address', 'Jl. Dharmahusada Indah 43'),
(3, 1, 'zip_code', '60258'),
(4, 1, 'city', 'Surabaya, Indonesia'),
(5, 1, 'mobile_phone', '089 67367 1110'),
(6, 1, 'dob_day', '28'),
(7, 1, 'dob_month', '10'),
(8, 1, 'dob_year', '1988'),
(9, 1, 'job', 'Web Developer'),
(10, 1, 'company', 'PT. Creazi'),
(11, 1, 'company_address', 'Jl. Nginden Semolo 101'),
(12, 2, 'gender', 'male'),
(13, 2, 'address', 'DHI 43'),
(14, 2, 'city', 'Surabaya, Indonesia'),
(15, 2, 'mobile_phone', '123456'),
(16, 2, 'dob_day', '28'),
(17, 2, 'dob_month', '10'),
(18, 2, 'dob_year', '1988'),
(19, 2, 'job', 'WAN admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cms_accounts`
--
ALTER TABLE `cms_accounts`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`), ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cms_entries`
--
ALTER TABLE `cms_entries`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cms_entry_metas`
--
ALTER TABLE `cms_entry_metas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_roles`
--
ALTER TABLE `cms_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_settings`
--
ALTER TABLE `cms_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_types`
--
ALTER TABLE `cms_types`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cms_type_metas`
--
ALTER TABLE `cms_type_metas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_users`
--
ALTER TABLE `cms_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_user_metas`
--
ALTER TABLE `cms_user_metas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cms_accounts`
--
ALTER TABLE `cms_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `cms_entries`
--
ALTER TABLE `cms_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `cms_entry_metas`
--
ALTER TABLE `cms_entry_metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `cms_roles`
--
ALTER TABLE `cms_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `cms_settings`
--
ALTER TABLE `cms_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `cms_types`
--
ALTER TABLE `cms_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `cms_type_metas`
--
ALTER TABLE `cms_type_metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `cms_users`
--
ALTER TABLE `cms_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `cms_user_metas`
--
ALTER TABLE `cms_user_metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
