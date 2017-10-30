-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2017 at 10:15 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `perfex`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblactivitylog`
--

CREATE TABLE IF NOT EXISTS `tblactivitylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` mediumtext NOT NULL,
  `date` datetime NOT NULL,
  `staffid` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staffid` (`staffid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblannouncements`
--

CREATE TABLE IF NOT EXISTS `tblannouncements` (
  `announcementid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `message` text,
  `showtousers` int(11) NOT NULL,
  `showtostaff` int(11) NOT NULL,
  `showname` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  `userid` varchar(100) NOT NULL,
  PRIMARY KEY (`announcementid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblclients`
--

CREATE TABLE IF NOT EXISTS `tblclients` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(100) DEFAULT NULL,
  `vat` varchar(50) DEFAULT NULL,
  `phonenumber` varchar(30) DEFAULT NULL,
  `country` int(11) NOT NULL DEFAULT '0',
  `city` varchar(100) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `datecreated` datetime NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `leadid` int(11) DEFAULT NULL,
  `billing_street` varchar(200) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_zip` varchar(100) DEFAULT NULL,
  `billing_country` int(11) DEFAULT '0',
  `shipping_street` varchar(200) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_zip` varchar(100) DEFAULT NULL,
  `shipping_country` int(11) DEFAULT '0',
  `longitude` varchar(300) DEFAULT NULL,
  `latitude` varchar(300) DEFAULT NULL,
  `default_language` varchar(40) DEFAULT NULL,
  `default_currency` int(11) NOT NULL DEFAULT '0',
  `show_primary_contact` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcommentlikes`
--

CREATE TABLE IF NOT EXISTS `tblcommentlikes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postid` int(11) NOT NULL,
  `commentid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `dateliked` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactpermissions`
--

CREATE TABLE IF NOT EXISTS `tblcontactpermissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcontacts`
--

CREATE TABLE IF NOT EXISTS `tblcontacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `is_primary` int(11) NOT NULL DEFAULT '1',
  `firstname` varchar(300) NOT NULL,
  `lastname` varchar(300) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phonenumber` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `datecreated` datetime NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `new_pass_key` varchar(32) DEFAULT NULL,
  `new_pass_key_requested` datetime DEFAULT NULL,
  `last_ip` varchar(40) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `profile_image` varchar(300) DEFAULT NULL,
  `direction` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `is_primary` (`is_primary`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcontractrenewals`
--

CREATE TABLE IF NOT EXISTS `tblcontractrenewals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contractid` int(11) NOT NULL,
  `old_start_date` date NOT NULL,
  `new_start_date` date NOT NULL,
  `old_end_date` date DEFAULT NULL,
  `new_end_date` date DEFAULT NULL,
  `old_value` decimal(11,2) DEFAULT NULL,
  `new_value` decimal(11,2) DEFAULT NULL,
  `date_renewed` datetime NOT NULL,
  `renewed_by` varchar(100) NOT NULL,
  `renewed_by_staff_id` int(11) NOT NULL DEFAULT '0',
  `is_on_old_expiry_notified` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcontracts`
--

CREATE TABLE IF NOT EXISTS `tblcontracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext,
  `description` text,
  `subject` varchar(300) DEFAULT NULL,
  `client` int(11) NOT NULL,
  `datestart` date DEFAULT NULL,
  `dateend` date DEFAULT NULL,
  `contract_type` int(11) DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  `isexpirynotified` int(11) NOT NULL DEFAULT '0',
  `contract_value` decimal(11,2) DEFAULT NULL,
  `trash` tinyint(1) DEFAULT '0',
  `not_visible_to_client` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client` (`client`),
  KEY `contract_type` (`contract_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcontracttypes`
--

CREATE TABLE IF NOT EXISTS `tblcontracttypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcountries`
--

CREATE TABLE IF NOT EXISTS `tblcountries` (
  `country_id` int(5) NOT NULL AUTO_INCREMENT,
  `iso2` char(2) DEFAULT NULL,
  `short_name` varchar(80) NOT NULL DEFAULT '',
  `long_name` varchar(80) NOT NULL DEFAULT '',
  `iso3` char(3) DEFAULT NULL,
  `numcode` varchar(6) DEFAULT NULL,
  `un_member` varchar(12) DEFAULT NULL,
  `calling_code` varchar(8) DEFAULT NULL,
  `cctld` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=251 ;

--
-- Dumping data for table `tblcountries`
--

INSERT INTO `tblcountries` (`country_id`, `iso2`, `short_name`, `long_name`, `iso3`, `numcode`, `un_member`, `calling_code`, `cctld`) VALUES
(1, 'AF', 'Afghanistan', 'Islamic Republic of Afghanistan', 'AFG', '004', 'yes', '93', '.af'),
(2, 'AX', 'Aland Islands', '&Aring;land Islands', 'ALA', '248', 'no', '358', '.ax'),
(3, 'AL', 'Albania', 'Republic of Albania', 'ALB', '008', 'yes', '355', '.al'),
(4, 'DZ', 'Algeria', 'People''s Democratic Republic of Algeria', 'DZA', '012', 'yes', '213', '.dz'),
(5, 'AS', 'American Samoa', 'American Samoa', 'ASM', '016', 'no', '1+684', '.as'),
(6, 'AD', 'Andorra', 'Principality of Andorra', 'AND', '020', 'yes', '376', '.ad'),
(7, 'AO', 'Angola', 'Republic of Angola', 'AGO', '024', 'yes', '244', '.ao'),
(8, 'AI', 'Anguilla', 'Anguilla', 'AIA', '660', 'no', '1+264', '.ai'),
(9, 'AQ', 'Antarctica', 'Antarctica', 'ATA', '010', 'no', '672', '.aq'),
(10, 'AG', 'Antigua and Barbuda', 'Antigua and Barbuda', 'ATG', '028', 'yes', '1+268', '.ag'),
(11, 'AR', 'Argentina', 'Argentine Republic', 'ARG', '032', 'yes', '54', '.ar'),
(12, 'AM', 'Armenia', 'Republic of Armenia', 'ARM', '051', 'yes', '374', '.am'),
(13, 'AW', 'Aruba', 'Aruba', 'ABW', '533', 'no', '297', '.aw'),
(14, 'AU', 'Australia', 'Commonwealth of Australia', 'AUS', '036', 'yes', '61', '.au'),
(15, 'AT', 'Austria', 'Republic of Austria', 'AUT', '040', 'yes', '43', '.at'),
(16, 'AZ', 'Azerbaijan', 'Republic of Azerbaijan', 'AZE', '031', 'yes', '994', '.az'),
(17, 'BS', 'Bahamas', 'Commonwealth of The Bahamas', 'BHS', '044', 'yes', '1+242', '.bs'),
(18, 'BH', 'Bahrain', 'Kingdom of Bahrain', 'BHR', '048', 'yes', '973', '.bh'),
(19, 'BD', 'Bangladesh', 'People''s Republic of Bangladesh', 'BGD', '050', 'yes', '880', '.bd'),
(20, 'BB', 'Barbados', 'Barbados', 'BRB', '052', 'yes', '1+246', '.bb'),
(21, 'BY', 'Belarus', 'Republic of Belarus', 'BLR', '112', 'yes', '375', '.by'),
(22, 'BE', 'Belgium', 'Kingdom of Belgium', 'BEL', '056', 'yes', '32', '.be'),
(23, 'BZ', 'Belize', 'Belize', 'BLZ', '084', 'yes', '501', '.bz'),
(24, 'BJ', 'Benin', 'Republic of Benin', 'BEN', '204', 'yes', '229', '.bj'),
(25, 'BM', 'Bermuda', 'Bermuda Islands', 'BMU', '060', 'no', '1+441', '.bm'),
(26, 'BT', 'Bhutan', 'Kingdom of Bhutan', 'BTN', '064', 'yes', '975', '.bt'),
(27, 'BO', 'Bolivia', 'Plurinational State of Bolivia', 'BOL', '068', 'yes', '591', '.bo'),
(28, 'BQ', 'Bonaire, Sint Eustatius and Saba', 'Bonaire, Sint Eustatius and Saba', 'BES', '535', 'no', '599', '.bq'),
(29, 'BA', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'BIH', '070', 'yes', '387', '.ba'),
(30, 'BW', 'Botswana', 'Republic of Botswana', 'BWA', '072', 'yes', '267', '.bw'),
(31, 'BV', 'Bouvet Island', 'Bouvet Island', 'BVT', '074', 'no', 'NONE', '.bv'),
(32, 'BR', 'Brazil', 'Federative Republic of Brazil', 'BRA', '076', 'yes', '55', '.br'),
(33, 'IO', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'IOT', '086', 'no', '246', '.io'),
(34, 'BN', 'Brunei', 'Brunei Darussalam', 'BRN', '096', 'yes', '673', '.bn'),
(35, 'BG', 'Bulgaria', 'Republic of Bulgaria', 'BGR', '100', 'yes', '359', '.bg'),
(36, 'BF', 'Burkina Faso', 'Burkina Faso', 'BFA', '854', 'yes', '226', '.bf'),
(37, 'BI', 'Burundi', 'Republic of Burundi', 'BDI', '108', 'yes', '257', '.bi'),
(38, 'KH', 'Cambodia', 'Kingdom of Cambodia', 'KHM', '116', 'yes', '855', '.kh'),
(39, 'CM', 'Cameroon', 'Republic of Cameroon', 'CMR', '120', 'yes', '237', '.cm'),
(40, 'CA', 'Canada', 'Canada', 'CAN', '124', 'yes', '1', '.ca'),
(41, 'CV', 'Cape Verde', 'Republic of Cape Verde', 'CPV', '132', 'yes', '238', '.cv'),
(42, 'KY', 'Cayman Islands', 'The Cayman Islands', 'CYM', '136', 'no', '1+345', '.ky'),
(43, 'CF', 'Central African Republic', 'Central African Republic', 'CAF', '140', 'yes', '236', '.cf'),
(44, 'TD', 'Chad', 'Republic of Chad', 'TCD', '148', 'yes', '235', '.td'),
(45, 'CL', 'Chile', 'Republic of Chile', 'CHL', '152', 'yes', '56', '.cl'),
(46, 'CN', 'China', 'People''s Republic of China', 'CHN', '156', 'yes', '86', '.cn'),
(47, 'CX', 'Christmas Island', 'Christmas Island', 'CXR', '162', 'no', '61', '.cx'),
(48, 'CC', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'CCK', '166', 'no', '61', '.cc'),
(49, 'CO', 'Colombia', 'Republic of Colombia', 'COL', '170', 'yes', '57', '.co'),
(50, 'KM', 'Comoros', 'Union of the Comoros', 'COM', '174', 'yes', '269', '.km'),
(51, 'CG', 'Congo', 'Republic of the Congo', 'COG', '178', 'yes', '242', '.cg'),
(52, 'CK', 'Cook Islands', 'Cook Islands', 'COK', '184', 'some', '682', '.ck'),
(53, 'CR', 'Costa Rica', 'Republic of Costa Rica', 'CRI', '188', 'yes', '506', '.cr'),
(54, 'CI', 'Cote d''ivoire (Ivory Coast)', 'Republic of C&ocirc;te D''Ivoire (Ivory Coast)', 'CIV', '384', 'yes', '225', '.ci'),
(55, 'HR', 'Croatia', 'Republic of Croatia', 'HRV', '191', 'yes', '385', '.hr'),
(56, 'CU', 'Cuba', 'Republic of Cuba', 'CUB', '192', 'yes', '53', '.cu'),
(57, 'CW', 'Curacao', 'Cura&ccedil;ao', 'CUW', '531', 'no', '599', '.cw'),
(58, 'CY', 'Cyprus', 'Republic of Cyprus', 'CYP', '196', 'yes', '357', '.cy'),
(59, 'CZ', 'Czech Republic', 'Czech Republic', 'CZE', '203', 'yes', '420', '.cz'),
(60, 'CD', 'Democratic Republic of the Congo', 'Democratic Republic of the Congo', 'COD', '180', 'yes', '243', '.cd'),
(61, 'DK', 'Denmark', 'Kingdom of Denmark', 'DNK', '208', 'yes', '45', '.dk'),
(62, 'DJ', 'Djibouti', 'Republic of Djibouti', 'DJI', '262', 'yes', '253', '.dj'),
(63, 'DM', 'Dominica', 'Commonwealth of Dominica', 'DMA', '212', 'yes', '1+767', '.dm'),
(64, 'DO', 'Dominican Republic', 'Dominican Republic', 'DOM', '214', 'yes', '1+809, 8', '.do'),
(65, 'EC', 'Ecuador', 'Republic of Ecuador', 'ECU', '218', 'yes', '593', '.ec'),
(66, 'EG', 'Egypt', 'Arab Republic of Egypt', 'EGY', '818', 'yes', '20', '.eg'),
(67, 'SV', 'El Salvador', 'Republic of El Salvador', 'SLV', '222', 'yes', '503', '.sv'),
(68, 'GQ', 'Equatorial Guinea', 'Republic of Equatorial Guinea', 'GNQ', '226', 'yes', '240', '.gq'),
(69, 'ER', 'Eritrea', 'State of Eritrea', 'ERI', '232', 'yes', '291', '.er'),
(70, 'EE', 'Estonia', 'Republic of Estonia', 'EST', '233', 'yes', '372', '.ee'),
(71, 'ET', 'Ethiopia', 'Federal Democratic Republic of Ethiopia', 'ETH', '231', 'yes', '251', '.et'),
(72, 'FK', 'Falkland Islands (Malvinas)', 'The Falkland Islands (Malvinas)', 'FLK', '238', 'no', '500', '.fk'),
(73, 'FO', 'Faroe Islands', 'The Faroe Islands', 'FRO', '234', 'no', '298', '.fo'),
(74, 'FJ', 'Fiji', 'Republic of Fiji', 'FJI', '242', 'yes', '679', '.fj'),
(75, 'FI', 'Finland', 'Republic of Finland', 'FIN', '246', 'yes', '358', '.fi'),
(76, 'FR', 'France', 'French Republic', 'FRA', '250', 'yes', '33', '.fr'),
(77, 'GF', 'French Guiana', 'French Guiana', 'GUF', '254', 'no', '594', '.gf'),
(78, 'PF', 'French Polynesia', 'French Polynesia', 'PYF', '258', 'no', '689', '.pf'),
(79, 'TF', 'French Southern Territories', 'French Southern Territories', 'ATF', '260', 'no', NULL, '.tf'),
(80, 'GA', 'Gabon', 'Gabonese Republic', 'GAB', '266', 'yes', '241', '.ga'),
(81, 'GM', 'Gambia', 'Republic of The Gambia', 'GMB', '270', 'yes', '220', '.gm'),
(82, 'GE', 'Georgia', 'Georgia', 'GEO', '268', 'yes', '995', '.ge'),
(83, 'DE', 'Germany', 'Federal Republic of Germany', 'DEU', '276', 'yes', '49', '.de'),
(84, 'GH', 'Ghana', 'Republic of Ghana', 'GHA', '288', 'yes', '233', '.gh'),
(85, 'GI', 'Gibraltar', 'Gibraltar', 'GIB', '292', 'no', '350', '.gi'),
(86, 'GR', 'Greece', 'Hellenic Republic', 'GRC', '300', 'yes', '30', '.gr'),
(87, 'GL', 'Greenland', 'Greenland', 'GRL', '304', 'no', '299', '.gl'),
(88, 'GD', 'Grenada', 'Grenada', 'GRD', '308', 'yes', '1+473', '.gd'),
(89, 'GP', 'Guadaloupe', 'Guadeloupe', 'GLP', '312', 'no', '590', '.gp'),
(90, 'GU', 'Guam', 'Guam', 'GUM', '316', 'no', '1+671', '.gu'),
(91, 'GT', 'Guatemala', 'Republic of Guatemala', 'GTM', '320', 'yes', '502', '.gt'),
(92, 'GG', 'Guernsey', 'Guernsey', 'GGY', '831', 'no', '44', '.gg'),
(93, 'GN', 'Guinea', 'Republic of Guinea', 'GIN', '324', 'yes', '224', '.gn'),
(94, 'GW', 'Guinea-Bissau', 'Republic of Guinea-Bissau', 'GNB', '624', 'yes', '245', '.gw'),
(95, 'GY', 'Guyana', 'Co-operative Republic of Guyana', 'GUY', '328', 'yes', '592', '.gy'),
(96, 'HT', 'Haiti', 'Republic of Haiti', 'HTI', '332', 'yes', '509', '.ht'),
(97, 'HM', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', 'HMD', '334', 'no', 'NONE', '.hm'),
(98, 'HN', 'Honduras', 'Republic of Honduras', 'HND', '340', 'yes', '504', '.hn'),
(99, 'HK', 'Hong Kong', 'Hong Kong', 'HKG', '344', 'no', '852', '.hk'),
(100, 'HU', 'Hungary', 'Hungary', 'HUN', '348', 'yes', '36', '.hu'),
(101, 'IS', 'Iceland', 'Republic of Iceland', 'ISL', '352', 'yes', '354', '.is'),
(102, 'IN', 'India', 'Republic of India', 'IND', '356', 'yes', '91', '.in'),
(103, 'ID', 'Indonesia', 'Republic of Indonesia', 'IDN', '360', 'yes', '62', '.id'),
(104, 'IR', 'Iran', 'Islamic Republic of Iran', 'IRN', '364', 'yes', '98', '.ir'),
(105, 'IQ', 'Iraq', 'Republic of Iraq', 'IRQ', '368', 'yes', '964', '.iq'),
(106, 'IE', 'Ireland', 'Ireland', 'IRL', '372', 'yes', '353', '.ie'),
(107, 'IM', 'Isle of Man', 'Isle of Man', 'IMN', '833', 'no', '44', '.im'),
(108, 'IL', 'Israel', 'State of Israel', 'ISR', '376', 'yes', '972', '.il'),
(109, 'IT', 'Italy', 'Italian Republic', 'ITA', '380', 'yes', '39', '.jm'),
(110, 'JM', 'Jamaica', 'Jamaica', 'JAM', '388', 'yes', '1+876', '.jm'),
(111, 'JP', 'Japan', 'Japan', 'JPN', '392', 'yes', '81', '.jp'),
(112, 'JE', 'Jersey', 'The Bailiwick of Jersey', 'JEY', '832', 'no', '44', '.je'),
(113, 'JO', 'Jordan', 'Hashemite Kingdom of Jordan', 'JOR', '400', 'yes', '962', '.jo'),
(114, 'KZ', 'Kazakhstan', 'Republic of Kazakhstan', 'KAZ', '398', 'yes', '7', '.kz'),
(115, 'KE', 'Kenya', 'Republic of Kenya', 'KEN', '404', 'yes', '254', '.ke'),
(116, 'KI', 'Kiribati', 'Republic of Kiribati', 'KIR', '296', 'yes', '686', '.ki'),
(117, 'XK', 'Kosovo', 'Republic of Kosovo', '---', '---', 'some', '381', ''),
(118, 'KW', 'Kuwait', 'State of Kuwait', 'KWT', '414', 'yes', '965', '.kw'),
(119, 'KG', 'Kyrgyzstan', 'Kyrgyz Republic', 'KGZ', '417', 'yes', '996', '.kg'),
(120, 'LA', 'Laos', 'Lao People''s Democratic Republic', 'LAO', '418', 'yes', '856', '.la'),
(121, 'LV', 'Latvia', 'Republic of Latvia', 'LVA', '428', 'yes', '371', '.lv'),
(122, 'LB', 'Lebanon', 'Republic of Lebanon', 'LBN', '422', 'yes', '961', '.lb'),
(123, 'LS', 'Lesotho', 'Kingdom of Lesotho', 'LSO', '426', 'yes', '266', '.ls'),
(124, 'LR', 'Liberia', 'Republic of Liberia', 'LBR', '430', 'yes', '231', '.lr'),
(125, 'LY', 'Libya', 'Libya', 'LBY', '434', 'yes', '218', '.ly'),
(126, 'LI', 'Liechtenstein', 'Principality of Liechtenstein', 'LIE', '438', 'yes', '423', '.li'),
(127, 'LT', 'Lithuania', 'Republic of Lithuania', 'LTU', '440', 'yes', '370', '.lt'),
(128, 'LU', 'Luxembourg', 'Grand Duchy of Luxembourg', 'LUX', '442', 'yes', '352', '.lu'),
(129, 'MO', 'Macao', 'The Macao Special Administrative Region', 'MAC', '446', 'no', '853', '.mo'),
(130, 'MK', 'Macedonia', 'The Former Yugoslav Republic of Macedonia', 'MKD', '807', 'yes', '389', '.mk'),
(131, 'MG', 'Madagascar', 'Republic of Madagascar', 'MDG', '450', 'yes', '261', '.mg'),
(132, 'MW', 'Malawi', 'Republic of Malawi', 'MWI', '454', 'yes', '265', '.mw'),
(133, 'MY', 'Malaysia', 'Malaysia', 'MYS', '458', 'yes', '60', '.my'),
(134, 'MV', 'Maldives', 'Republic of Maldives', 'MDV', '462', 'yes', '960', '.mv'),
(135, 'ML', 'Mali', 'Republic of Mali', 'MLI', '466', 'yes', '223', '.ml'),
(136, 'MT', 'Malta', 'Republic of Malta', 'MLT', '470', 'yes', '356', '.mt'),
(137, 'MH', 'Marshall Islands', 'Republic of the Marshall Islands', 'MHL', '584', 'yes', '692', '.mh'),
(138, 'MQ', 'Martinique', 'Martinique', 'MTQ', '474', 'no', '596', '.mq'),
(139, 'MR', 'Mauritania', 'Islamic Republic of Mauritania', 'MRT', '478', 'yes', '222', '.mr'),
(140, 'MU', 'Mauritius', 'Republic of Mauritius', 'MUS', '480', 'yes', '230', '.mu'),
(141, 'YT', 'Mayotte', 'Mayotte', 'MYT', '175', 'no', '262', '.yt'),
(142, 'MX', 'Mexico', 'United Mexican States', 'MEX', '484', 'yes', '52', '.mx'),
(143, 'FM', 'Micronesia', 'Federated States of Micronesia', 'FSM', '583', 'yes', '691', '.fm'),
(144, 'MD', 'Moldava', 'Republic of Moldova', 'MDA', '498', 'yes', '373', '.md'),
(145, 'MC', 'Monaco', 'Principality of Monaco', 'MCO', '492', 'yes', '377', '.mc'),
(146, 'MN', 'Mongolia', 'Mongolia', 'MNG', '496', 'yes', '976', '.mn'),
(147, 'ME', 'Montenegro', 'Montenegro', 'MNE', '499', 'yes', '382', '.me'),
(148, 'MS', 'Montserrat', 'Montserrat', 'MSR', '500', 'no', '1+664', '.ms'),
(149, 'MA', 'Morocco', 'Kingdom of Morocco', 'MAR', '504', 'yes', '212', '.ma'),
(150, 'MZ', 'Mozambique', 'Republic of Mozambique', 'MOZ', '508', 'yes', '258', '.mz'),
(151, 'MM', 'Myanmar (Burma)', 'Republic of the Union of Myanmar', 'MMR', '104', 'yes', '95', '.mm'),
(152, 'NA', 'Namibia', 'Republic of Namibia', 'NAM', '516', 'yes', '264', '.na'),
(153, 'NR', 'Nauru', 'Republic of Nauru', 'NRU', '520', 'yes', '674', '.nr'),
(154, 'NP', 'Nepal', 'Federal Democratic Republic of Nepal', 'NPL', '524', 'yes', '977', '.np'),
(155, 'NL', 'Netherlands', 'Kingdom of the Netherlands', 'NLD', '528', 'yes', '31', '.nl'),
(156, 'NC', 'New Caledonia', 'New Caledonia', 'NCL', '540', 'no', '687', '.nc'),
(157, 'NZ', 'New Zealand', 'New Zealand', 'NZL', '554', 'yes', '64', '.nz'),
(158, 'NI', 'Nicaragua', 'Republic of Nicaragua', 'NIC', '558', 'yes', '505', '.ni'),
(159, 'NE', 'Niger', 'Republic of Niger', 'NER', '562', 'yes', '227', '.ne'),
(160, 'NG', 'Nigeria', 'Federal Republic of Nigeria', 'NGA', '566', 'yes', '234', '.ng'),
(161, 'NU', 'Niue', 'Niue', 'NIU', '570', 'some', '683', '.nu'),
(162, 'NF', 'Norfolk Island', 'Norfolk Island', 'NFK', '574', 'no', '672', '.nf'),
(163, 'KP', 'North Korea', 'Democratic People''s Republic of Korea', 'PRK', '408', 'yes', '850', '.kp'),
(164, 'MP', 'Northern Mariana Islands', 'Northern Mariana Islands', 'MNP', '580', 'no', '1+670', '.mp'),
(165, 'NO', 'Norway', 'Kingdom of Norway', 'NOR', '578', 'yes', '47', '.no'),
(166, 'OM', 'Oman', 'Sultanate of Oman', 'OMN', '512', 'yes', '968', '.om'),
(167, 'PK', 'Pakistan', 'Islamic Republic of Pakistan', 'PAK', '586', 'yes', '92', '.pk'),
(168, 'PW', 'Palau', 'Republic of Palau', 'PLW', '585', 'yes', '680', '.pw'),
(169, 'PS', 'Palestine', 'State of Palestine (or Occupied Palestinian Territory)', 'PSE', '275', 'some', '970', '.ps'),
(170, 'PA', 'Panama', 'Republic of Panama', 'PAN', '591', 'yes', '507', '.pa'),
(171, 'PG', 'Papua New Guinea', 'Independent State of Papua New Guinea', 'PNG', '598', 'yes', '675', '.pg'),
(172, 'PY', 'Paraguay', 'Republic of Paraguay', 'PRY', '600', 'yes', '595', '.py'),
(173, 'PE', 'Peru', 'Republic of Peru', 'PER', '604', 'yes', '51', '.pe'),
(174, 'PH', 'Phillipines', 'Republic of the Philippines', 'PHL', '608', 'yes', '63', '.ph'),
(175, 'PN', 'Pitcairn', 'Pitcairn', 'PCN', '612', 'no', 'NONE', '.pn'),
(176, 'PL', 'Poland', 'Republic of Poland', 'POL', '616', 'yes', '48', '.pl'),
(177, 'PT', 'Portugal', 'Portuguese Republic', 'PRT', '620', 'yes', '351', '.pt'),
(178, 'PR', 'Puerto Rico', 'Commonwealth of Puerto Rico', 'PRI', '630', 'no', '1+939', '.pr'),
(179, 'QA', 'Qatar', 'State of Qatar', 'QAT', '634', 'yes', '974', '.qa'),
(180, 'RE', 'Reunion', 'R&eacute;union', 'REU', '638', 'no', '262', '.re'),
(181, 'RO', 'Romania', 'Romania', 'ROU', '642', 'yes', '40', '.ro'),
(182, 'RU', 'Russia', 'Russian Federation', 'RUS', '643', 'yes', '7', '.ru'),
(183, 'RW', 'Rwanda', 'Republic of Rwanda', 'RWA', '646', 'yes', '250', '.rw'),
(184, 'BL', 'Saint Barthelemy', 'Saint Barth&eacute;lemy', 'BLM', '652', 'no', '590', '.bl'),
(185, 'SH', 'Saint Helena', 'Saint Helena, Ascension and Tristan da Cunha', 'SHN', '654', 'no', '290', '.sh'),
(186, 'KN', 'Saint Kitts and Nevis', 'Federation of Saint Christopher and Nevis', 'KNA', '659', 'yes', '1+869', '.kn'),
(187, 'LC', 'Saint Lucia', 'Saint Lucia', 'LCA', '662', 'yes', '1+758', '.lc'),
(188, 'MF', 'Saint Martin', 'Saint Martin', 'MAF', '663', 'no', '590', '.mf'),
(189, 'PM', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'SPM', '666', 'no', '508', '.pm'),
(190, 'VC', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', 'VCT', '670', 'yes', '1+784', '.vc'),
(191, 'WS', 'Samoa', 'Independent State of Samoa', 'WSM', '882', 'yes', '685', '.ws'),
(192, 'SM', 'San Marino', 'Republic of San Marino', 'SMR', '674', 'yes', '378', '.sm'),
(193, 'ST', 'Sao Tome and Principe', 'Democratic Republic of S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'STP', '678', 'yes', '239', '.st'),
(194, 'SA', 'Saudi Arabia', 'Kingdom of Saudi Arabia', 'SAU', '682', 'yes', '966', '.sa'),
(195, 'SN', 'Senegal', 'Republic of Senegal', 'SEN', '686', 'yes', '221', '.sn'),
(196, 'RS', 'Serbia', 'Republic of Serbia', 'SRB', '688', 'yes', '381', '.rs'),
(197, 'SC', 'Seychelles', 'Republic of Seychelles', 'SYC', '690', 'yes', '248', '.sc'),
(198, 'SL', 'Sierra Leone', 'Republic of Sierra Leone', 'SLE', '694', 'yes', '232', '.sl'),
(199, 'SG', 'Singapore', 'Republic of Singapore', 'SGP', '702', 'yes', '65', '.sg'),
(200, 'SX', 'Sint Maarten', 'Sint Maarten', 'SXM', '534', 'no', '1+721', '.sx'),
(201, 'SK', 'Slovakia', 'Slovak Republic', 'SVK', '703', 'yes', '421', '.sk'),
(202, 'SI', 'Slovenia', 'Republic of Slovenia', 'SVN', '705', 'yes', '386', '.si'),
(203, 'SB', 'Solomon Islands', 'Solomon Islands', 'SLB', '090', 'yes', '677', '.sb'),
(204, 'SO', 'Somalia', 'Somali Republic', 'SOM', '706', 'yes', '252', '.so'),
(205, 'ZA', 'South Africa', 'Republic of South Africa', 'ZAF', '710', 'yes', '27', '.za'),
(206, 'GS', 'South Georgia and the South Sandwich Islands', 'South Georgia and the South Sandwich Islands', 'SGS', '239', 'no', '500', '.gs'),
(207, 'KR', 'South Korea', 'Republic of Korea', 'KOR', '410', 'yes', '82', '.kr'),
(208, 'SS', 'South Sudan', 'Republic of South Sudan', 'SSD', '728', 'yes', '211', '.ss'),
(209, 'ES', 'Spain', 'Kingdom of Spain', 'ESP', '724', 'yes', '34', '.es'),
(210, 'LK', 'Sri Lanka', 'Democratic Socialist Republic of Sri Lanka', 'LKA', '144', 'yes', '94', '.lk'),
(211, 'SD', 'Sudan', 'Republic of the Sudan', 'SDN', '729', 'yes', '249', '.sd'),
(212, 'SR', 'Suriname', 'Republic of Suriname', 'SUR', '740', 'yes', '597', '.sr'),
(213, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard and Jan Mayen', 'SJM', '744', 'no', '47', '.sj'),
(214, 'SZ', 'Swaziland', 'Kingdom of Swaziland', 'SWZ', '748', 'yes', '268', '.sz'),
(215, 'SE', 'Sweden', 'Kingdom of Sweden', 'SWE', '752', 'yes', '46', '.se'),
(216, 'CH', 'Switzerland', 'Swiss Confederation', 'CHE', '756', 'yes', '41', '.ch'),
(217, 'SY', 'Syria', 'Syrian Arab Republic', 'SYR', '760', 'yes', '963', '.sy'),
(218, 'TW', 'Taiwan', 'Republic of China (Taiwan)', 'TWN', '158', 'former', '886', '.tw'),
(219, 'TJ', 'Tajikistan', 'Republic of Tajikistan', 'TJK', '762', 'yes', '992', '.tj'),
(220, 'TZ', 'Tanzania', 'United Republic of Tanzania', 'TZA', '834', 'yes', '255', '.tz'),
(221, 'TH', 'Thailand', 'Kingdom of Thailand', 'THA', '764', 'yes', '66', '.th'),
(222, 'TL', 'Timor-Leste (East Timor)', 'Democratic Republic of Timor-Leste', 'TLS', '626', 'yes', '670', '.tl'),
(223, 'TG', 'Togo', 'Togolese Republic', 'TGO', '768', 'yes', '228', '.tg'),
(224, 'TK', 'Tokelau', 'Tokelau', 'TKL', '772', 'no', '690', '.tk'),
(225, 'TO', 'Tonga', 'Kingdom of Tonga', 'TON', '776', 'yes', '676', '.to'),
(226, 'TT', 'Trinidad and Tobago', 'Republic of Trinidad and Tobago', 'TTO', '780', 'yes', '1+868', '.tt'),
(227, 'TN', 'Tunisia', 'Republic of Tunisia', 'TUN', '788', 'yes', '216', '.tn'),
(228, 'TR', 'Turkey', 'Republic of Turkey', 'TUR', '792', 'yes', '90', '.tr'),
(229, 'TM', 'Turkmenistan', 'Turkmenistan', 'TKM', '795', 'yes', '993', '.tm'),
(230, 'TC', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'TCA', '796', 'no', '1+649', '.tc'),
(231, 'TV', 'Tuvalu', 'Tuvalu', 'TUV', '798', 'yes', '688', '.tv'),
(232, 'UG', 'Uganda', 'Republic of Uganda', 'UGA', '800', 'yes', '256', '.ug'),
(233, 'UA', 'Ukraine', 'Ukraine', 'UKR', '804', 'yes', '380', '.ua'),
(234, 'AE', 'United Arab Emirates', 'United Arab Emirates', 'ARE', '784', 'yes', '971', '.ae'),
(235, 'GB', 'United Kingdom', 'United Kingdom of Great Britain and Nothern Ireland', 'GBR', '826', 'yes', '44', '.uk'),
(236, 'US', 'United States', 'United States of America', 'USA', '840', 'yes', '1', '.us'),
(237, 'UM', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands', 'UMI', '581', 'no', 'NONE', 'NONE'),
(238, 'UY', 'Uruguay', 'Eastern Republic of Uruguay', 'URY', '858', 'yes', '598', '.uy'),
(239, 'UZ', 'Uzbekistan', 'Republic of Uzbekistan', 'UZB', '860', 'yes', '998', '.uz'),
(240, 'VU', 'Vanuatu', 'Republic of Vanuatu', 'VUT', '548', 'yes', '678', '.vu'),
(241, 'VA', 'Vatican City', 'State of the Vatican City', 'VAT', '336', 'no', '39', '.va'),
(242, 'VE', 'Venezuela', 'Bolivarian Republic of Venezuela', 'VEN', '862', 'yes', '58', '.ve'),
(243, 'VN', 'Vietnam', 'Socialist Republic of Vietnam', 'VNM', '704', 'yes', '84', '.vn'),
(244, 'VG', 'Virgin Islands, British', 'British Virgin Islands', 'VGB', '092', 'no', '1+284', '.vg'),
(245, 'VI', 'Virgin Islands, US', 'Virgin Islands of the United States', 'VIR', '850', 'no', '1+340', '.vi'),
(246, 'WF', 'Wallis and Futuna', 'Wallis and Futuna', 'WLF', '876', 'no', '681', '.wf'),
(247, 'EH', 'Western Sahara', 'Western Sahara', 'ESH', '732', 'no', '212', '.eh'),
(248, 'YE', 'Yemen', 'Republic of Yemen', 'YEM', '887', 'yes', '967', '.ye'),
(249, 'ZM', 'Zambia', 'Republic of Zambia', 'ZMB', '894', 'yes', '260', '.zm'),
(250, 'ZW', 'Zimbabwe', 'Republic of Zimbabwe', 'ZWE', '716', 'yes', '263', '.zw');

-- --------------------------------------------------------

--
-- Table structure for table `tblcurrencies`
--

CREATE TABLE IF NOT EXISTS `tblcurrencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `isdefault` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tblcurrencies`
--

INSERT INTO `tblcurrencies` (`id`, `symbol`, `name`, `isdefault`) VALUES
(1, '$', 'USD', 1),
(2, '€', 'EUR', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomeradmins`
--

CREATE TABLE IF NOT EXISTS `tblcustomeradmins` (
  `staff_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_assigned` text NOT NULL,
  KEY `customer_id` (`customer_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomerfiles_shares`
--

CREATE TABLE IF NOT EXISTS `tblcustomerfiles_shares` (
  `file_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomergroups_in`
--

CREATE TABLE IF NOT EXISTS `tblcustomergroups_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `groupid` (`groupid`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomersgroups`
--

CREATE TABLE IF NOT EXISTS `tblcustomersgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(600) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomfields`
--

CREATE TABLE IF NOT EXISTS `tblcustomfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldto` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `options` mediumtext,
  `field_order` int(11) DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1',
  `show_on_pdf` int(11) NOT NULL DEFAULT '0',
  `only_admin` tinyint(1) NOT NULL DEFAULT '0',
  `show_on_table` tinyint(1) NOT NULL DEFAULT '0',
  `show_on_client_portal` int(11) NOT NULL DEFAULT '0',
  `disalow_client_to_edit` int(11) NOT NULL DEFAULT '0',
  `bs_column` int(11) NOT NULL DEFAULT '12',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomfieldsvalues`
--

CREATE TABLE IF NOT EXISTS `tblcustomfieldsvalues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `fieldto` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `relid` (`relid`),
  KEY `fieldid` (`fieldid`),
  KEY `fieldto` (`fieldto`),
  KEY `relid_2` (`relid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartments`
--

CREATE TABLE IF NOT EXISTS `tbldepartments` (
  `departmentid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `imap_username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `host` varchar(150) DEFAULT NULL,
  `password` mediumtext,
  `encryption` varchar(3) DEFAULT NULL,
  `delete_after_import` int(11) NOT NULL DEFAULT '0',
  `calendar_id` mediumtext,
  `hidefromclient` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`departmentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbldismissedannouncements`
--

CREATE TABLE IF NOT EXISTS `tbldismissedannouncements` (
  `dismissedannouncementid` int(11) NOT NULL AUTO_INCREMENT,
  `announcementid` int(11) NOT NULL,
  `staff` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`dismissedannouncementid`),
  KEY `announcementid` (`announcementid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblemaillists`
--

CREATE TABLE IF NOT EXISTS `tblemaillists` (
  `listid` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `creator` varchar(100) NOT NULL,
  `datecreated` datetime NOT NULL,
  PRIMARY KEY (`listid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblemailtemplates`
--

CREATE TABLE IF NOT EXISTS `tblemailtemplates` (
  `emailtemplateid` int(11) NOT NULL AUTO_INCREMENT,
  `type` mediumtext NOT NULL,
  `slug` varchar(100) NOT NULL,
  `language` varchar(40) DEFAULT NULL,
  `name` mediumtext NOT NULL,
  `subject` mediumtext NOT NULL,
  `message` text NOT NULL,
  `fromname` mediumtext NOT NULL,
  `fromemail` varchar(100) DEFAULT NULL,
  `plaintext` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`emailtemplateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Dumping data for table `tblemailtemplates`
--

INSERT INTO `tblemailtemplates` (`emailtemplateid`, `type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES
(1, 'client', 'new-client-created', 'english', 'New Contact Added/Registered (Welcome Email)', 'Welcome aboard', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {contact_firstname}&nbsp;{contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><br /></span><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Thank you for registering on the {companyname} CRM System.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We just wanted to say welcome.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Please contact us if you need any help.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Click here to view your profile: <a href="{crm_url}">{crm_url}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">(This is an automated email, so please don''t reply to this email address)</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(2, 'invoice', 'invoice-send-to-client', 'english', 'Send Invoice to Customer', 'Invoice with number {invoice_number} created', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {contact_firstname}&nbsp;{contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We have prepared the following invoice for you: #&nbsp;{invoice_number}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Invoice status:&nbsp;<strong style="font-family: Helvetica, Arial, sans-serif;">{invoice_status}</strong></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span>&nbsp;</div>\r\n<div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the invoice on the following link: <a href="{invoice_link}">{invoice_number}</a></span></div>\r\n</div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Please contact us for more information.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(3, 'ticket', 'new-ticket-opened-admin', 'english', 'New Ticket Opened (Opened by Staff, Sent to Customer)', 'New Support Ticket Opened', '<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi&nbsp;{contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">New support ticket has been opened.</span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: <span style="background-color: inherit;">{ticket_subject}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; color: #000000; font-size: 12pt; background-color: inherit;">Department: {ticket_department}</span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: <span style="background-color: inherit;">{ticket_priority}</span></span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Ticket message:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; color: #000000; font-size: 12pt; background-color: inherit;">{ticket_message}<br /><br />You can view the ticket on the following link:&nbsp;<a href="{ticket_url}">#{ticket_id}</a><br /></span></div>\r\n<div>&nbsp;</div>\r\n<div><span style="color: #000000; font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(4, 'ticket', 'ticket-reply', 'english', 'Ticket Reply (Sent to Customer)', 'New Ticket Reply', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi&nbsp;<span style="background-color: inherit;">{contact_firstname} </span>{contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You have a new ticket reply to ticket #{ticket_id}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: <span style="background-color: inherit;">{ticket_subject}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Department: {ticket_department}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: <span style="background-color: inherit;">{ticket_priority}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Ticket message:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">{ticket_message}<br /><br />You can view the ticket on the following link:&nbsp;<a href="{ticket_url}">#{ticket_id}</a><br /></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(5, 'ticket', 'ticket-autoresponse', 'english', 'New Ticket Opened - Autoresponse', 'New Support Ticket Opened', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi&nbsp;{contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Thank you for contacting our support team. A support ticket has now been opened for your request. You will be notified when a response is made by email.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: {ticket_subject}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Department: {ticket_department}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: {ticket_priority}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Ticket message:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">{ticket_message}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(6, 'invoice', 'invoice-payment-recorded', 'english', 'Invoice Payment Recorded (Sent to Customer)', 'Invoice Payment Recorded', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Payment recorded for invoice&nbsp;# {invoice_number}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(7, 'invoice', 'invoice-overdue-notice', 'english', 'Invoice Overdue Notice', 'Invoice Overdue Notice - {invoice_number}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">This is an overdue notice for invoice # {invoice_number}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">This invoice was due: {invoice_duedate}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span>&nbsp;</div>\r\n<div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the invoice on the following link: <a href="{invoice_link}">{invoice_number}</a></span></div>\r\n</div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(8, 'invoice', 'invoice-already-send', 'english', 'Invoice Already Sent to Customer', 'On your command here is the invoice', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi <span style="background-color: inherit;">{contact_firstname} {contact_lastname}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">At your request, here is the invoice with number #{invoice_number}</span></div>\r\n<div>\r\n<div>&nbsp;</div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the invoice on the following link: <a href="{invoice_link}">{invoice_number}</a></span></div>\r\n</div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Please contact us for more information.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(9, 'ticket', 'new-ticket-created-staff', 'english', 'New Ticket Created (Opened by Customer, Sent to Staff Members)', 'New Ticket Created', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">A new support ticket has been opened.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: <span style="background-color: inherit;">{ticket_subject}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Department: {ticket_department}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: <span style="background-color: inherit;">{ticket_priority}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Ticket message:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">{ticket_message}<br /><br />You can view the ticket on the following link:&nbsp;<a href="{ticket_url}">#{ticket_id}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(10, 'estimate', 'estimate-send-to-client', 'english', 'Send Estimate to Customer', 'Estimate # {estimate_number} created', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {contact_firstname}&nbsp;{contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Please find the attached estimate # {estimate_number}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Estimate status:&nbsp;<strong>{estimate_status}<br /><br /></strong>You can view the estimate on the following link:&nbsp;<a href="{estimate_link}">{estimate_number}</a><br /></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We look forward to your communication.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(11, 'ticket', 'ticket-reply-to-admin', 'english', 'Ticket Reply (Sent to Staff)', 'New Support Ticket Reply', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">A new support ticket reply from {contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: <span style="background-color: inherit;">{ticket_subject}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Department: {ticket_department}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: <span style="background-color: inherit;">{ticket_priority}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Ticket message:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">{ticket_message}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt;">You can view the ticket on the following link: <a href="{ticket_url}">#{ticket_id}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(12, 'estimate', 'estimate-already-send', 'english', 'Estimate Already Sent to Customer', 'Estimate # {estimate_number} ', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {contact_firstname}&nbsp;{contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Thank you for your estimate request.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the estimate on the following link:&nbsp;<a href="{estimate_link}">{estimate_number}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Please contact us for more information.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(13, 'contract', 'contract-expiration', 'english', 'Contract Expiration', 'Contract Expiration Reminder', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {client_company}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">This is a reminder that the following contract will expire soon:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: {contract_subject}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Description: {contract_description}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Date Start: {contract_datestart}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Date End: {contract_dateend}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Value: {contract_contract_value}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Please contact us for more information.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(14, 'tasks', 'task-assigned', 'english', 'New Task Assigned (Sent to Staff)', 'New Task Assigned to You - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {staff_firstname} {staff_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You have been assigned a new task:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Name: <span style="background-color: #ffffff;">{task_name}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Due date: <span style="background-color: #ffffff;">{task_duedate}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: <span style="background-color: #ffffff;">{task_priority}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt; font-family: arial, helvetica, sans-serif;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(15, 'tasks', 'task-added-as-follower', 'english', 'Staff Member Added as Follower on Task', 'You are added as follower on task - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {staff_firstname} <span style="background-color: #ffffff;">{staff_lastname}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You have been added as follower on the following task:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Name: {task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Description: {task_description}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: {task_priority}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Start date: {task_startdate}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Due date: {task_duedate}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div>&nbsp;</div>', '{companyname} | CRM', '', 0, 1, 0),
(16, 'tasks', 'task-commented', 'english', 'New Comment on Task (Sent to Staff)', 'New Comment on Task - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {staff_firstname} {staff_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">A comment has been made on the following task:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Name: &nbsp;{task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Description: &nbsp;{task_description}<br />Comment: {task_comment}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(17, 'tasks', 'task-marked-as-finished', 'english', 'Task Marked as Finished (Sent to Staff)', 'Task Marked as Finished - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {staff_firstname} {staff_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{task_user_take_action} marked the following task as complete:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Name: </span>{task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Description: </span><span style="background-color: inherit;">{task_description}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Due date: <span style="background-color: #ffffff;">{task_duedate}<br /></span></span><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt; font-family: arial, helvetica, sans-serif;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(18, 'tasks', 'task-added-attachment', 'english', 'New Attachment on Task (Sent to Staff)', 'New Attachment on Task - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {staff_firstname} {staff_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{task_user_take_action} added an attachment on the following task:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Name: </span>{task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Description: </span><span style="background-color: inherit;">{task_description}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(19, 'tasks', 'task-unmarked-as-finished', 'english', 'Task Unmarked as Finished (Sent to Staff)', 'Task UN-marked as finished - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {staff_firstname} {staff_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{task_user_take_action} <span style="background-color: inherit;">marked the </span>following task as <strong>in-complete / unfinished</strong></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Name: </span><span style="background-color: inherit;">{task_name}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Description: </span><span style="background-color: inherit;">{task_description}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Due date: {task_duedate}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(20, 'estimate', 'estimate-declined-to-staff', 'english', 'Estimate Declined (Sent to Staff)', 'Customer Declined Estimate', '<div><span style="font-size: 12pt;">Hi</span></div>\r\n<div><span style="font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt;">Customer ({client_company}) declined estimate with number <span style="background-color: inherit;">{estimate_number}</span></span></div>\r\n<div><span style="font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt;">You can view the estimate on the following link:&nbsp;<a href="{estimate_link}">{estimate_number}</a></span></div>\r\n<div><span style="font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(21, 'estimate', 'estimate-accepted-to-staff', 'english', 'Estimate Accepted (Sent to Staff)', 'Customer Accepted Estimate', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Customer ({client_company}) accepted estimate with number <span style="background-color: inherit;">{estimate_number}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the estimate on the following link:&nbsp;<a href="{estimate_link}">{estimate_number}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(22, 'proposals', 'proposal-client-accepted', 'english', 'Customer Action - Accepted (Sent to Staff)', 'Customer Accepted Proposal', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Client {proposal_proposal_to} accepted the following proposal:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Subject: {proposal_subject}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Total: {proposal_total}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">View the proposal on the following link: <a href="{proposal_link}">{proposal_subject}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>', '{companyname} | CRM', '', 0, 1, 0),
(23, 'proposals', 'proposal-send-to-customer', 'english', 'Send Proposal to Customer', 'Proposal', '<div>Dear {proposal_proposal_to}</div>\r\n<div>&nbsp;</div>\r\n<div>Please find our attached proposal.</div>\r\n<div>&nbsp;</div>\r\n<div>This proposal is valid until: {proposal_open_till}</div>\r\n<div>You can view the proposal on the following link: <a href="{proposal_link}">{proposal_subject}</a></div>\r\n<div>&nbsp;</div>\r\n<div>Please don''t hesitate to comment online if you have any questions.</div>\r\n<div>&nbsp;</div>\r\n<div>We look forward to your communication.</div>\r\n<div>&nbsp;</div>\r\n<div>Kind regards</div>\r\n<div>&nbsp;</div>\r\n<div>{email_signature}</div>', '{companyname} | CRM', '', 0, 1, 0),
(24, 'proposals', 'proposal-client-declined', 'english', 'Customer Action - Declined (Sent to Staff)', 'Client Declined Proposal', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Customer {proposal_proposal_to} declined the proposal {proposal_subject}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">View the proposal on the following link <a href="{proposal_link}">{proposal_subject}</a>&nbsp;or from the admin area.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div>&nbsp;</div>', '{companyname} | CRM', '', 0, 1, 0),
(25, 'proposals', 'proposal-client-thank-you', 'english', 'Thank You Email (Sent to Customer After Accept)', 'Thank for you accepting proposal', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {proposal_proposal_to}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Thank for for accepting the proposal.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We look forward to doing business with you.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We will contact you as soon as possible</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(26, 'proposals', 'proposal-comment-to-client', 'english', 'New Comment  (Sent to Customer Contacts)', 'New Proposal Comment', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {proposal_proposal_to}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">A new comment has been made on the following proposal: {proposal_subject}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view and reply to the comment on the following link: <a href="{proposal_link}">{proposal_subject}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(27, 'proposals', 'proposal-comment-to-admin', 'english', 'New Comment (Sent to Staff) ', 'New Proposal Comment', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hello</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">A new <strong>comment</strong> has been made to the proposal <span style="background-color: inherit;">{proposal_subject}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view and reply to the comment on the following link: <a href="{proposal_link}">{proposal_subject}</a><span style="background-color: inherit;">&nbsp;or from the admin area.</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(28, 'estimate', 'estimate-thank-you-to-customer', 'english', 'Thank You Email (Sent to Customer After Accept)', 'Thank for you accepting estimate', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {contact_firstname}&nbsp;{contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Thank for for accepting the estimate.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We look forward to doing business with you.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">We will contact you as soon as possible.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(29, 'tasks', 'task-deadline-notification', 'english', 'Task Deadline Reminder - Sent to Assigned Members', 'Task Deadline Reminder', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {staff_firstname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">This is an automated email from {companyname}.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">The task {task_name} deadline is on {task_duedate}. This task is still not finished.</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt; font-family: arial, helvetica, sans-serif;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(30, 'contract', 'send-contract', 'english', 'Send Contract to Customer', 'Contract - {contract_subject}', '<p>Hi&nbsp;{client_company}</p>\r\n<p>Please find the {contract_subject}&nbsp;attached.</p>\r\n<p>Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(31, 'invoice', 'invoice-payment-recorded-to-staff', 'english', 'Invoice Payment Recorded (Sent to Staff)', 'New Invoice Payment', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Customer recorded payment for invoice # {invoice_number}</span></div>\r\n<div>&nbsp;</div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the invoice on the following link: <a href="{invoice_link}">{invoice_number}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0);
INSERT INTO `tblemailtemplates` (`emailtemplateid`, `type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES
(32, 'ticket', 'auto-close-ticket', 'english', 'Auto Close Ticket', 'Ticket Auto Closed', '<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi&nbsp;{contact_firstname}&nbsp;{contact_lastname}</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Ticket&nbsp;{ticket_subject} has been auto close due to inactivity.</span></p>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Ticket #: <span style="background-color: inherit;">{ticket_id}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Department: {ticket_department}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Priority: <span style="background-color: inherit;">{ticket_priority}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">Ticket message:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt; background-color: inherit;">{ticket_message}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(33, 'project', 'new-project-discussion-created-to-staff', 'english', 'New Project Discussion (Sent to Project Members)', 'New Project Discussion Created', '<p>Hello&nbsp;{staff_firstname}&nbsp;{staff_lastname}</p>\r\n<p>New project discussion created from&nbsp;{discussion_creator}</p>\r\n<p>Subject:&nbsp;{discussion_subject}</p>\r\n<p>Description:&nbsp;{discussion_description}</p>\r\n<p>You can view the discussion on the following link: <a href="{discussion_link}">{discussion_subject}</a></p>\r\n<p>Kind Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(34, 'project', 'new-project-discussion-created-to-customer', 'english', 'New Project Discussion (Sent to Customer Contacts)', 'New Project Discussion Created', '<p>Hello&nbsp;{contact_firstname}&nbsp;{contact_lastname}</p>\r\n<p>New project discussion created from&nbsp;{discussion_creator}</p>\r\n<p>Subject:&nbsp;{discussion_subject}</p>\r\n<p>Description:&nbsp;{discussion_description}</p>\r\n<p>You can view the discussion on the following link: <a href="{discussion_link}">{discussion_subject}</a></p>\r\n<p>Kind Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(35, 'project', 'new-project-file-uploaded-to-customer', 'english', 'New Project File Uploaded (Sent to Customer Contacts)', 'New Project File Uploaded', '<p>Hello&nbsp;{contact_firstname}&nbsp;{contact_lastname}</p>\r\n<p>New project&nbsp;file is uploaded on&nbsp;{project_name} from&nbsp;{file_creator}</p>\r\n<p>You can view the project on the following link: <a href="{project_link}">{project_name}</a><br />To view&nbsp;the file in our CRM&nbsp;you can click on the following link: <a href="{discussion_link}">{discussion_subject}</a></p>\r\n<p>Kind Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(36, 'project', 'new-project-file-uploaded-to-staff', 'english', 'New Project File Uploaded (Sent to Project Members)', 'New Project File Uploaded', '<p>Hello&nbsp;{staff_firstname}&nbsp;{staff_lastname}</p>\r\n<p>New project&nbsp;file is uploaded on&nbsp;{project_name} from&nbsp;{file_creator}</p>\r\n<p>You can view the project on the following link: <a href="{project_link}">{project_name}</a><br />To view&nbsp;the file you can click on the following link: <a href="{discussion_link}">{discussion_subject}</a></p>\r\n<p>Kind Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(37, 'project', 'new-project-discussion-comment-to-customer', 'english', 'New Discussion Comment  (Sent to Customer Contacts)', 'New Discussion Comment', '<p>Hello&nbsp;{contact_firstname}&nbsp;{contact_lastname}</p>\r\n<p>New discussion comment has been made on {discussion_subject} from&nbsp;{comment_creator}</p>\r\n<p>Discussion subject:&nbsp;{discussion_subject}</p>\r\n<p>Comment:&nbsp;{discussion_comment}</p>\r\n<p>You can view the discussion on the following link: <a href="{discussion_link}">{discussion_subject}</a></p>\r\n<p>Kind Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(38, 'project', 'new-project-discussion-comment-to-staff', 'english', 'New Discussion Comment (Sent to Project Members)', 'New Discussion Comment', '<p>Hello&nbsp;{staff_firstname}&nbsp;{staff_lastname}</p>\r\n<p>New discussion comment has been made on {discussion_subject} from&nbsp;{comment_creator}</p>\r\n<p>Discussion subject:&nbsp;{discussion_subject}</p>\r\n<p>Comment:&nbsp;{discussion_comment}</p>\r\n<p>You can view the discussion on the following link: <a href="{discussion_link}">{discussion_subject}</a></p>\r\n<p>Kind Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(39, 'project', 'staff-added-as-project-member', 'english', 'Staff Added as Project Member', 'New project assigned to you', '<p>Hello&nbsp;{staff_firstname}&nbsp;{staff_lastname}</p>\r\n<p>New project has been assigned to you.</p>\r\n<p>You can view the project on the following link <a href="{project_link}">{project_name}</a></p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(40, 'estimate', 'estimate-expiry-reminder', 'english', 'Estimate Expiration Reminder', 'Estimate Expiration Reminder', '<p>Hello&nbsp;{client_company}</p>\r\n<p>The estimate with&nbsp;{estimate_number} will expire on&nbsp;{estimate_expirydate}</p>\r\n<p>You can view the estimate on the following link: <a href="{estimate_link}">{estimate_number}</a></p>\r\n<p>Regards,</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(41, 'proposals', 'proposal-expiry-reminder', 'english', 'Proposal Expiration Reminder', 'Proposal Expiration Reminder', '<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hello&nbsp;{proposal_proposal_to}</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">The proposal {proposal_subject} will expire on&nbsp;{proposal_open_till}</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">You can view the proposal on the following link: <a href="{proposal_link}">{proposal_subject}</a></span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Regards,</span></p>\r\n<p><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></p>', '{companyname} | CRM', '', 0, 1, 0),
(42, 'staff', 'new-staff-created', 'english', 'New Staff Created (Welcome Email)', 'You are added as staff member', 'Hello&nbsp;{staff_firstname}&nbsp;{staff_lastname}<br /><br />You are added as member on our CRM.<br />You can login at {admin_url}<br /><br />Please use the following&nbsp;logic credentials:<br /><br />Email:&nbsp;{staff_email}<br />Password:&nbsp;{password}<br /><br />Best Regards,<br />{email_signature}', '{companyname} | CRM', '', 0, 1, 0),
(43, 'client', 'contact-forgot-password', 'english', 'Forgot Password', 'Create New Password', '<h2>Create a new password</h2>\r\nForgot your password?<br /> To create a new password, just follow this link:<br /> <br /><a href="{reset_password_url}">Reset Password</a><br /> <br /> You received this email, because it was requested by a {companyname}&nbsp;user. This is part of the procedure to create a new password on the system. If you DID NOT request a new password then please ignore this email and your password will remain the same. <br /><br /> {email_signature}', '{companyname} | CRM', '', 0, 1, 0),
(44, 'client', 'contact-password-reseted', 'english', 'Password Reset - Confirmation', 'Your password has been changed', '<strong>You have changed your password.<br /></strong><br /> Please, keep it in your records so you don''t forget it.<br /> <br /> Your email address for login is: {contact_email}<br />If this wasnt you, please contact us.<br /><br />{email_signature}', '{companyname} | CRM', '', 0, 1, 0),
(45, 'client', 'contact-set-password', 'english', 'Set New Password', 'Set new password on {companyname} ', '<h2>Setup your new password on {companyname}</h2>\r\nPlease use the following link to setup your new password.<br /><br />Keep it in your records so you don''t forget it.<br /><br /> Please set your new password in 48 hours. After that you wont be able to set your password. <br /><br />You can login at: {crm_url}<br /> Your email address for login: {contact_email}<br /> <br /><a href="{set_password_url}">Set New Password</a><br /> <br />{email_signature}', '{companyname} | CRM', '', 0, 1, 0),
(46, 'staff', 'staff-forgot-password', 'english', 'Forgot Password', 'Create New Password', '<h2>Create a new password</h2>\r\nForgot your password?<br /> To create a new password, just follow this link:<br /> <br /><a href="{reset_password_url}">Reset Password</a><br /> <br /> You received this email, because it was requested by a {companyname}&nbsp;user. This is part of the procedure to create a new password on the system. If you DID NOT request a new password then please ignore this email and your password will remain the same. <br /><br /> {email_signature}', '{companyname} | CRM', '', 0, 1, 0),
(47, 'staff', 'staff-password-reseted', 'english', 'Password Reset - Confirmation', 'Your password has been changed', '<strong>You have changed your password.<br /></strong><br /> Please, keep it in your records so you don''t forget it.<br /> <br /> Your email address for login is: {staff_email}<br /> If this wasnt you, please contact us.<br /><br />{email_signature}', '{companyname} | CRM', '', 0, 1, 0),
(48, 'project', 'assigned-to-project', 'english', 'New Project Created (Sent to Customer Contacts)', 'New Project Created', '<p>Hello&nbsp;{contact_firstname}</p>\r\n<p>New project is assigned to your company.<br />Project Name:&nbsp;{project_name}</p>\r\n<p>You can view the project on the following link:&nbsp;<a href="{project_link}">{project_name}</a></p>\r\n<p>We are looking forward hearing from you.</p>\r\n<p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0),
(49, 'tasks', 'task-marked-as-finished-to-contacts', 'english', 'Task Marked as Finished (Sent to customer contacts)', 'Task Marked as Finished - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{task_user_take_action} marked the following task as complete:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Name: </span>{task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Description: </span><span style="background-color: inherit;">{task_description}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Due date: <span style="background-color: #ffffff;">{task_duedate}<br /></span></span><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt; font-family: arial, helvetica, sans-serif;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(50, 'tasks', 'task-added-attachment-to-contacts', 'english', 'New Attachment on Task (Sent to Customer Contacts)', 'New Attachment on Task - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Hi {contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{task_user_take_action} added an attachment on the following task:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Name: </span>{task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;"><span style="background-color: inherit;">Description: </span><span style="background-color: inherit;">{task_description}</span></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-size: 12pt; font-family: arial, helvetica, sans-serif;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(51, 'tasks', 'task-commented-to-contacts', 'english', 'New Comment on Task (Sent to Customer Contacts)', 'New Comment on Task - {task_name}', '<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Dear {contact_firstname} {contact_lastname}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">A comment has been made on the following task:</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Name: &nbsp;{task_name}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Description: &nbsp;{task_description}<br />Comment: {task_comment}</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Click on the following link to view: <a href="{task_link}">{task_name}</a></span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">Kind regards,</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">&nbsp;</span></div>\r\n<div><span style="font-family: arial, helvetica, sans-serif; font-size: 12pt;">{email_signature}</span></div>', '{companyname} | CRM', '', 0, 1, 0),
(52, 'leads', 'new-lead-assigned', 'english', 'New Lead Assigned to Staff Member', 'New lead assigned to you', '<p>Hello {lead_assigned}</p>\r\n<p>New&nbsp;lead is assigned to you.<br /><br />You can view the lead on the following link: <a href="{lead_link}">{lead_name}<br /><br /></a>Kind Regards,<br />{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblestimates`
--

CREATE TABLE IF NOT EXISTS `tblestimates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `datesend` datetime DEFAULT NULL,
  `clientid` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `number` int(11) NOT NULL,
  `prefix` varchar(50) DEFAULT NULL,
  `number_format` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) DEFAULT NULL,
  `datecreated` datetime NOT NULL,
  `date` date NOT NULL,
  `expirydate` date DEFAULT NULL,
  `currency` int(11) NOT NULL,
  `subtotal` decimal(11,2) NOT NULL,
  `total_tax` decimal(11,2) NOT NULL DEFAULT '0.00',
  `total` decimal(11,2) NOT NULL,
  `adjustment` decimal(11,2) DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `clientnote` text,
  `adminnote` text,
  `discount_percent` decimal(11,2) DEFAULT '0.00',
  `discount_total` decimal(11,2) DEFAULT '0.00',
  `discount_type` varchar(30) DEFAULT NULL,
  `invoiceid` int(11) DEFAULT NULL,
  `invoiced_date` datetime DEFAULT NULL,
  `terms` text,
  `reference_no` varchar(100) DEFAULT NULL,
  `sale_agent` int(11) NOT NULL DEFAULT '0',
  `billing_street` varchar(200) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_zip` varchar(100) DEFAULT NULL,
  `billing_country` int(11) DEFAULT NULL,
  `shipping_street` varchar(200) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_zip` varchar(100) DEFAULT NULL,
  `shipping_country` int(11) DEFAULT NULL,
  `include_shipping` tinyint(1) NOT NULL,
  `show_shipping_on_estimate` tinyint(1) NOT NULL DEFAULT '1',
  `show_quantity_as` int(11) NOT NULL DEFAULT '1',
  `pipeline_order` int(11) NOT NULL DEFAULT '0',
  `is_expiry_notified` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `clientid` (`clientid`),
  KEY `currency` (`currency`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblevents`
--

CREATE TABLE IF NOT EXISTS `tblevents` (
  `eventid` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `description` text,
  `userid` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `public` int(11) NOT NULL DEFAULT '0',
  `color` varchar(10) DEFAULT NULL,
  `isstartnotified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblexpenses`
--

CREATE TABLE IF NOT EXISTS `tblexpenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `currency` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `tax` int(11) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `note` text,
  `expense_name` varchar(500) DEFAULT NULL,
  `clientid` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `billable` int(11) DEFAULT '0',
  `invoiceid` int(11) DEFAULT NULL,
  `paymentmode` varchar(50) DEFAULT NULL,
  `date` date NOT NULL,
  `recurring_type` varchar(10) DEFAULT NULL,
  `repeat_every` int(11) DEFAULT NULL,
  `recurring` int(11) NOT NULL DEFAULT '0',
  `recurring_ends_on` date DEFAULT NULL,
  `custom_recurring` int(11) NOT NULL DEFAULT '0',
  `last_recurring_date` date DEFAULT NULL,
  `create_invoice_billable` tinyint(1) DEFAULT NULL,
  `send_invoice_to_customer` tinyint(1) NOT NULL,
  `recurring_from` int(11) DEFAULT NULL,
  `dateadded` datetime NOT NULL,
  `addedfrom` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `clientid` (`clientid`),
  KEY `project_id` (`project_id`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblexpensescategories`
--

CREATE TABLE IF NOT EXISTS `tblexpensescategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblfiles`
--

CREATE TABLE IF NOT EXISTS `tblfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  `file_name` varchar(600) NOT NULL,
  `filetype` varchar(40) DEFAULT NULL,
  `visible_to_customer` int(11) NOT NULL DEFAULT '0',
  `attachment_key` varchar(32) DEFAULT NULL,
  `external` varchar(40) DEFAULT NULL,
  `external_link` text,
  `thumbnail_link` text COMMENT 'For external usage',
  `staffid` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT '0',
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblformquestionboxes`
--

CREATE TABLE IF NOT EXISTS `tblformquestionboxes` (
  `boxid` int(11) NOT NULL AUTO_INCREMENT,
  `boxtype` varchar(10) NOT NULL,
  `questionid` int(11) NOT NULL,
  PRIMARY KEY (`boxid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblformquestionboxesdescription`
--

CREATE TABLE IF NOT EXISTS `tblformquestionboxesdescription` (
  `questionboxdescriptionid` int(11) NOT NULL AUTO_INCREMENT,
  `description` mediumtext NOT NULL,
  `boxid` mediumtext NOT NULL,
  `questionid` int(11) NOT NULL,
  PRIMARY KEY (`questionboxdescriptionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblformquestions`
--

CREATE TABLE IF NOT EXISTS `tblformquestions` (
  `questionid` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) DEFAULT NULL,
  `question` mediumtext NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `question_order` int(11) NOT NULL,
  PRIMARY KEY (`questionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblformresults`
--

CREATE TABLE IF NOT EXISTS `tblformresults` (
  `resultid` int(11) NOT NULL AUTO_INCREMENT,
  `boxid` int(11) NOT NULL,
  `boxdescriptionid` int(11) DEFAULT NULL,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) DEFAULT NULL,
  `questionid` int(11) NOT NULL,
  `answer` text,
  `resultsetid` int(11) NOT NULL,
  PRIMARY KEY (`resultid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblgoals`
--

CREATE TABLE IF NOT EXISTS `tblgoals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(400) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `goal_type` int(11) NOT NULL,
  `contract_type` int(11) NOT NULL DEFAULT '0',
  `achievement` int(11) NOT NULL,
  `notify_when_fail` tinyint(1) NOT NULL DEFAULT '1',
  `notify_when_achieve` tinyint(1) NOT NULL DEFAULT '1',
  `notified` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblinvoicepaymentrecords`
--

CREATE TABLE IF NOT EXISTS `tblinvoicepaymentrecords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceid` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `paymentmode` varchar(40) DEFAULT NULL,
  `paymentmethod` varchar(200) DEFAULT NULL,
  `date` date NOT NULL,
  `daterecorded` datetime NOT NULL,
  `note` text NOT NULL,
  `transactionid` mediumtext,
  PRIMARY KEY (`id`),
  KEY `invoiceid` (`invoiceid`),
  KEY `paymentmethod` (`paymentmethod`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblinvoicepaymentsmodes`
--

CREATE TABLE IF NOT EXISTS `tblinvoicepaymentsmodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `show_on_pdf` int(11) NOT NULL DEFAULT '0',
  `invoices_only` int(11) NOT NULL DEFAULT '0',
  `expenses_only` int(11) NOT NULL DEFAULT '0',
  `selected_by_default` int(11) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tblinvoicepaymentsmodes`
--

INSERT INTO `tblinvoicepaymentsmodes` (`id`, `name`, `description`, `show_on_pdf`, `invoices_only`, `expenses_only`, `selected_by_default`, `active`) VALUES
(1, 'Bank', NULL, 0, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblinvoices`
--

CREATE TABLE IF NOT EXISTS `tblinvoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `datesend` datetime DEFAULT NULL,
  `clientid` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `prefix` varchar(50) DEFAULT NULL,
  `number_format` int(11) NOT NULL DEFAULT '0',
  `datecreated` datetime NOT NULL,
  `date` date NOT NULL,
  `duedate` date DEFAULT NULL,
  `currency` int(11) NOT NULL,
  `subtotal` decimal(11,2) NOT NULL,
  `total_tax` decimal(11,2) NOT NULL DEFAULT '0.00',
  `total` decimal(11,2) NOT NULL,
  `adjustment` decimal(11,2) DEFAULT NULL,
  `addedfrom` int(11) DEFAULT NULL,
  `hash` varchar(32) NOT NULL,
  `status` int(11) DEFAULT '1',
  `clientnote` text,
  `adminnote` text,
  `last_overdue_reminder` date DEFAULT NULL,
  `cancel_overdue_reminders` int(11) NOT NULL DEFAULT '0',
  `allowed_payment_modes` mediumtext,
  `token` mediumtext,
  `discount_percent` decimal(11,2) DEFAULT '0.00',
  `discount_total` decimal(11,2) DEFAULT '0.00',
  `discount_type` varchar(30) NOT NULL,
  `recurring` int(11) NOT NULL DEFAULT '0',
  `recurring_type` varchar(10) DEFAULT NULL,
  `custom_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_ends_on` date DEFAULT NULL,
  `is_recurring_from` int(11) DEFAULT NULL,
  `last_recurring_date` date DEFAULT NULL,
  `terms` text,
  `sale_agent` int(11) NOT NULL DEFAULT '0',
  `billing_street` varchar(200) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_zip` varchar(100) DEFAULT NULL,
  `billing_country` int(11) DEFAULT NULL,
  `shipping_street` varchar(200) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_zip` varchar(100) DEFAULT NULL,
  `shipping_country` int(11) DEFAULT NULL,
  `include_shipping` tinyint(1) NOT NULL,
  `show_shipping_on_invoice` tinyint(1) NOT NULL DEFAULT '1',
  `show_quantity_as` int(11) NOT NULL DEFAULT '1',
  `project_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `currency` (`currency`),
  KEY `clientid` (`clientid`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblitems`
--

CREATE TABLE IF NOT EXISTS `tblitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` mediumtext NOT NULL,
  `long_description` text,
  `rate` decimal(11,2) NOT NULL,
  `tax` int(11) DEFAULT NULL,
  `unit` varchar(40) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblitemsrelated`
--

CREATE TABLE IF NOT EXISTS `tblitemsrelated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(30) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblitemstax`
--

CREATE TABLE IF NOT EXISTS `tblitemstax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  `taxrate` decimal(11,2) NOT NULL,
  `taxname` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblitems_groups`
--

CREATE TABLE IF NOT EXISTS `tblitems_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblitems_in`
--

CREATE TABLE IF NOT EXISTS `tblitems_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(15) NOT NULL,
  `description` mediumtext NOT NULL,
  `long_description` mediumtext,
  `qty` decimal(11,2) NOT NULL,
  `rate` decimal(11,2) NOT NULL,
  `unit` varchar(40) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblknowledgebase`
--

CREATE TABLE IF NOT EXISTS `tblknowledgebase` (
  `articleid` int(11) NOT NULL AUTO_INCREMENT,
  `articlegroup` int(11) NOT NULL,
  `subject` mediumtext NOT NULL,
  `description` text NOT NULL,
  `slug` mediumtext NOT NULL,
  `active` tinyint(4) NOT NULL,
  `datecreated` datetime NOT NULL,
  `article_order` int(11) NOT NULL DEFAULT '0',
  `staff_article` int(11) NOT NULL DEFAULT '0',
  `views` int(11) DEFAULT NULL,
  PRIMARY KEY (`articleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblknowledgebasearticleanswers`
--

CREATE TABLE IF NOT EXISTS `tblknowledgebasearticleanswers` (
  `articleanswerid` int(11) NOT NULL AUTO_INCREMENT,
  `articleid` int(11) NOT NULL,
  `answer` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`articleanswerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblknowledgebasegroups`
--

CREATE TABLE IF NOT EXISTS `tblknowledgebasegroups` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `description` mediumtext,
  `active` tinyint(4) NOT NULL,
  `color` varchar(10) DEFAULT '#28B8DA',
  `group_order` int(11) DEFAULT '0',
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblleadactivitylog`
--

CREATE TABLE IF NOT EXISTS `tblleadactivitylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leadid` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `additional_data` varchar(600) DEFAULT NULL,
  `date` datetime NOT NULL,
  `staffid` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblleads`
--

CREATE TABLE IF NOT EXISTS `tblleads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `company` varchar(300) DEFAULT NULL,
  `description` text,
  `country` int(11) NOT NULL DEFAULT '0',
  `zip` varchar(15) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `assigned` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  `from_form_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `lastcontact` datetime DEFAULT NULL,
  `dateassigned` date DEFAULT NULL,
  `last_status_change` datetime DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `leadorder` int(11) DEFAULT '1',
  `phonenumber` varchar(50) DEFAULT NULL,
  `date_converted` datetime DEFAULT NULL,
  `lost` tinyint(1) NOT NULL DEFAULT '0',
  `junk` int(11) NOT NULL DEFAULT '0',
  `last_lead_status` int(11) NOT NULL DEFAULT '0',
  `is_imported_from_email_integration` tinyint(1) NOT NULL DEFAULT '0',
  `email_integration_uid` varchar(30) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `client_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `assigned` (`assigned`),
  KEY `source` (`source`),
  KEY `lastcontact` (`lastcontact`),
  KEY `leadorder` (`leadorder`),
  KEY `dateadded` (`dateadded`),
  KEY `from_form_id` (`from_form_id`),
  KEY `from_form_id_2` (`from_form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblleadsemailintegrationemails`
--

CREATE TABLE IF NOT EXISTS `tblleadsemailintegrationemails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` mediumtext,
  `body` mediumtext,
  `dateadded` datetime NOT NULL,
  `leadid` int(11) NOT NULL,
  `emailid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblleadsintegration`
--

CREATE TABLE IF NOT EXISTS `tblleadsintegration` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'the ID always must be 1',
  `active` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `imap_server` varchar(100) NOT NULL,
  `password` mediumtext NOT NULL,
  `check_every` int(11) NOT NULL DEFAULT '5',
  `responsible` int(11) NOT NULL,
  `lead_source` int(11) NOT NULL,
  `lead_status` int(11) NOT NULL,
  `encryption` varchar(3) DEFAULT NULL,
  `folder` varchar(100) NOT NULL,
  `last_run` varchar(50) DEFAULT NULL,
  `notify_lead_imported` tinyint(1) NOT NULL DEFAULT '1',
  `notify_lead_contact_more_times` tinyint(1) NOT NULL DEFAULT '1',
  `notify_type` varchar(20) DEFAULT NULL,
  `notify_ids` mediumtext,
  `only_loop_on_unseen_emails` tinyint(1) NOT NULL DEFAULT '1',
  `delete_after_import` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tblleadsintegration`
--

INSERT INTO `tblleadsintegration` (`id`, `active`, `email`, `imap_server`, `password`, `check_every`, `responsible`, `lead_source`, `lead_status`, `encryption`, `folder`, `last_run`, `notify_lead_imported`, `notify_lead_contact_more_times`, `notify_type`, `notify_ids`, `only_loop_on_unseen_emails`, `delete_after_import`) VALUES
(1, 0, '', '', '', 10, 0, 0, 0, 'tls', 'inbox', '', 1, 1, 'roles', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblleadssources`
--

CREATE TABLE IF NOT EXISTS `tblleadssources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tblleadssources`
--

INSERT INTO `tblleadssources` (`id`, `name`) VALUES
(1, 'Google'),
(2, 'Facebook');

-- --------------------------------------------------------

--
-- Table structure for table `tblleadsstatus`
--

CREATE TABLE IF NOT EXISTS `tblleadsstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `statusorder` int(11) DEFAULT NULL,
  `color` varchar(10) DEFAULT '#28B8DA',
  `isdefault` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tblleadsstatus`
--

INSERT INTO `tblleadsstatus` (`id`, `name`, `statusorder`, `color`, `isdefault`) VALUES
(1, 'Customer', 1000, '#7cb342', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbllistemails`
--

CREATE TABLE IF NOT EXISTS `tbllistemails` (
  `emailid` int(11) NOT NULL AUTO_INCREMENT,
  `listid` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`emailid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblmaillistscustomfields`
--

CREATE TABLE IF NOT EXISTS `tblmaillistscustomfields` (
  `customfieldid` int(11) NOT NULL AUTO_INCREMENT,
  `listid` int(11) NOT NULL,
  `fieldname` varchar(150) NOT NULL,
  `fieldslug` varchar(100) NOT NULL,
  PRIMARY KEY (`customfieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblmaillistscustomfieldvalues`
--

CREATE TABLE IF NOT EXISTS `tblmaillistscustomfieldvalues` (
  `customfieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `listid` int(11) NOT NULL,
  `customfieldid` int(11) NOT NULL,
  `emailid` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`customfieldvalueid`),
  KEY `listid` (`listid`),
  KEY `customfieldid` (`customfieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblmigrations`
--

CREATE TABLE IF NOT EXISTS `tblmigrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tblmigrations`
--

INSERT INTO `tblmigrations` (`version`) VALUES
(161);

-- --------------------------------------------------------

--
-- Table structure for table `tblmilestones`
--

CREATE TABLE IF NOT EXISTS `tblmilestones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(400) NOT NULL,
  `description` text,
  `description_visible_to_customer` tinyint(1) DEFAULT '0',
  `due_date` date NOT NULL,
  `project_id` int(11) NOT NULL,
  `color` varchar(10) DEFAULT NULL,
  `milestone_order` int(11) NOT NULL DEFAULT '0',
  `datecreated` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblnotes`
--

CREATE TABLE IF NOT EXISTS `tblnotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  `description` text,
  `date_contacted` datetime DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblnotifications`
--

CREATE TABLE IF NOT EXISTS `tblnotifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isread` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `description` text NOT NULL,
  `fromuserid` int(11) NOT NULL,
  `fromclientid` int(11) NOT NULL DEFAULT '0',
  `from_fullname` varchar(100) NOT NULL,
  `touserid` int(11) NOT NULL,
  `fromcompany` int(11) DEFAULT NULL,
  `link` mediumtext,
  `additional_data` varchar(600) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbloptions`
--

CREATE TABLE IF NOT EXISTS `tbloptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=192 ;

--
-- Dumping data for table `tbloptions`
--

INSERT INTO `tbloptions` (`id`, `name`, `value`) VALUES
(1, 'dateformat', 'Y-m-d|%Y-%m-%d'),
(2, 'companyname', ''),
(3, 'services', '1'),
(4, 'maximum_allowed_ticket_attachments', '4'),
(5, 'ticket_attachments_file_extensions', '.jpg,.png,.pdf,.doc'),
(6, 'staff_access_only_assigned_departments', '1'),
(7, 'use_knowledge_base', '1'),
(8, 'smtp_email', ''),
(9, 'smtp_password', ''),
(10, 'smtp_port', ''),
(11, 'smtp_host', ''),
(12, 'smtp_email_charset', 'utf-8'),
(13, 'default_timezone', 'Europe/Berlin'),
(14, 'clients_default_theme', 'perfex'),
(15, 'company_logo', 'logo.png'),
(16, 'tables_pagination_limit', '25'),
(17, 'main_domain', ''),
(18, 'allow_registration', '0'),
(19, 'knowledge_base_without_registration', '1'),
(20, 'email_signature', ''),
(21, 'default_staff_role', '1'),
(22, 'newsfeed_maximum_files_upload', '10'),
(23, 'newsfeed_maximum_file_size', '5'),
(24, 'contract_expiration_before', '4'),
(25, 'invoice_prefix', 'INV-'),
(26, 'decimal_separator', '.'),
(27, 'thousand_separator', ','),
(28, 'currency_placement', 'before'),
(29, 'invoice_company_name', ''),
(30, 'invoice_company_address', ''),
(31, 'invoice_company_city', ''),
(32, 'invoice_company_country_code', ''),
(33, 'invoice_company_postal_code', ''),
(34, 'invoice_company_phonenumber', ''),
(35, 'view_invoice_only_logged_in', '0'),
(36, 'invoice_number_format', '1'),
(37, 'next_invoice_number', '1'),
(38, 'cron_send_invoice_overdue_reminder', '1'),
(39, 'active_language', 'english'),
(40, 'invoice_number_decrement_on_delete', '1'),
(41, 'automatically_send_invoice_overdue_reminder_after', '1'),
(42, 'automatically_resend_invoice_overdue_reminder_after', '3'),
(43, 'expenses_auto_operations_hour', '21'),
(44, 'survey_send_emails_per_cron_run', '100'),
(45, 'delete_only_on_last_invoice', '1'),
(46, 'delete_only_on_last_estimate', '1'),
(47, 'create_invoice_from_recurring_only_on_paid_invoices', '0'),
(48, 'allow_payment_amount_to_be_modified', '1'),
(49, 'send_renewed_invoice_from_recurring_to_email', '1'),
(50, 'rtl_support_client', '0'),
(51, 'limit_top_search_bar_results_to', '10'),
(52, 'estimate_prefix', 'EST-'),
(53, 'next_estimate_number', '1'),
(54, 'estimate_number_decrement_on_delete', '1'),
(55, 'estimate_number_format', '1'),
(56, 'estimate_auto_convert_to_invoice_on_client_accept', '1'),
(57, 'exclude_estimate_from_client_area_with_draft_status', '1'),
(58, 'rtl_support_admin', '0'),
(59, 'last_cron_run', ''),
(60, 'show_sale_agent_on_estimates', '1'),
(61, 'show_sale_agent_on_invoices', '1'),
(62, 'predefined_terms_invoice', ''),
(63, 'predefined_terms_estimate', ''),
(64, 'default_task_priority', '2'),
(65, 'dropbox_app_key', ''),
(66, 'show_expense_reminders_on_calendar', '1'),
(67, 'only_show_contact_tickets', '1'),
(68, 'predefined_clientnote_invoice', ''),
(69, 'predefined_clientnote_estimate', ''),
(70, 'custom_pdf_logo_image_url', ''),
(71, 'favicon', 'favicon.ico'),
(72, 'auto_backup_enabled', '0'),
(73, 'invoice_due_after', '30'),
(74, 'google_api_key', ''),
(75, 'google_calendar_main_calendar', ''),
(76, 'default_tax', ''),
(77, 'show_invoices_on_calendar', '1'),
(78, 'show_estimates_on_calendar', '1'),
(79, 'show_contracts_on_calendar', '1'),
(80, 'show_tasks_on_calendar', '1'),
(81, 'show_customer_reminders_on_calendar', '1'),
(82, 'auto_backup_every', '7'),
(83, 'last_auto_backup', ''),
(84, 'output_client_pdfs_from_admin_area_in_client_language', '0'),
(85, 'show_lead_reminders_on_calendar', '1'),
(86, 'aside_menu_active', '{"aside_menu_active":[{"name":"als_dashboard","url":"\\/","permission":"","icon":"fa fa-tachometer","id":"dashboard"},{"name":"als_clients","url":"clients","permission":"customers","icon":"fa fa-users","id":"customers"},{"name":"projects","url":"projects","permission":"","icon":"fa fa-bars","id":"projects"},{"name":"als_sales","url":"#","permission":"","icon":"fa fa-balance-scale","id":"sales","children":[{"name":"proposals","url":"proposals","permission":"proposals","icon":"","id":"child-proposals"},{"name":"estimates","url":"estimates\\/list_estimates","permission":"estimates","icon":"","id":"child-estimates"},{"name":"invoices","url":"invoices\\/list_invoices","permission":"invoices","icon":"","id":"child-invoices"},{"name":"payments","url":"payments","permission":"payments","icon":"","id":"child-payments"},{"name":"items","url":"invoice_items","permission":"items","icon":"","id":"child-items"}]},{"name":"als_expenses","url":"expenses\\/list_expenses","permission":"expenses","icon":"fa fa-heartbeat","id":"expenses"},{"name":"support","url":"tickets","permission":"","icon":"fa fa-ticket","id":"tickets"},{"name":"als_contracts","url":"contracts","permission":"contracts","icon":"fa fa-file","id":"contracts"},{"name":"als_leads","url":"leads","permission":"is_staff_member","icon":"fa fa-tty","id":"leads"},{"name":"als_tasks","url":"tasks\\/list_tasks","permission":"","icon":"fa fa-tasks","id":"tasks"},{"name":"als_kb","url":"#","permission":"knowledge_base","icon":"fa fa-folder-open-o","id":"knowledge-base","children":[{"name":"als_all_articles","url":"knowledge_base","permission":"","icon":"","id":"child-all-articles"},{"name":"als_kb_groups","url":"knowledge_base\\/manage_groups","permission":"","icon":"","id":"child-groups"}]},{"name":"als_reports","url":"#","permission":"reports","icon":"fa fa-area-chart","id":"reports","children":[{"name":"als_reports_sales_submenu","url":"reports\\/sales","permission":"","icon":"","id":"child-sales"},{"name":"als_reports_expenses","url":"reports\\/expenses","permission":"","icon":"","id":"child-expenses"},{"name":"als_expenses_vs_income","url":"reports\\/expenses_vs_income","permission":"","icon":"","id":"child-expenses-vs-income"},{"name":"als_reports_leads_submenu","url":"reports\\/leads","permission":"","icon":"","id":"child-leads"},{"name":"als_kb_articles_submenu","url":"reports\\/knowledge_base_articles","permission":"","icon":"","id":"child-kb-articles"}]},{"name":"als_utilities","url":"#","permission":"","icon":"fa fa-cogs","id":"utilities","children":[{"name":"als_media","url":"utilities\\/media","permission":"","icon":"","id":"child-media"},{"name":"bulk_pdf_exporter","url":"utilities\\/bulk_pdf_exporter","permission":"bulk_pdf_exporter","icon":"","id":"child-bulk-pdf-exporter"},{"name":"als_calendar_submenu","url":"utilities\\/calendar","permission":"","icon":"","id":"child-calendar"},{"name":"als_goals_tracking","url":"goals","permission":"goals","icon":"","id":"child-goals-tracking"},{"name":"als_surveys","url":"surveys","permission":"surveys","icon":"","id":"child-surveys"},{"name":"als_announcements_submenu","url":"announcements","permission":"is_admin","icon":"","id":"child-announcements"},{"name":"utility_backup","url":"utilities\\/backup","permission":"is_admin","icon":"","id":"child-database-backup"},{"name":"als_activity_log_submenu","url":"utilities\\/activity_log","permission":"is_admin","icon":"","id":"child-activity-log"},{"name":"ticket_pipe_log","url":"utilities\\/pipe_log","permission":"is_admin","icon":"","id":"ticket-pipe-log"}]}]}'),
(87, 'estimate_expiry_reminder_enabled', '1'),
(88, 'send_estimate_expiry_reminder_before', '4'),
(89, 'leads_default_source', ''),
(90, 'leads_default_status', ''),
(91, 'proposal_expiry_reminder_enabled', '1'),
(92, 'send_proposal_expiry_reminder_before', '4'),
(93, 'default_contact_permissions', 'a:6:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";i:5;s:1:"6";}'),
(94, 'pdf_logo_width', '150'),
(95, 'aside_menu_inactive', '{"aside_menu_inactive":[]}'),
(96, 'setup_menu_active', '{"setup_menu_active":[{"permission":"staff","name":"als_staff","url":"staff","icon":"","id":"staff"},{"permission":"is_admin","name":"clients","url":"#","icon":"","id":"customers","children":[{"permission":"","name":"customer_groups","url":"clients\\/groups","icon":"","id":"groups"}]},{"permission":"","name":"support","url":"#","icon":"","id":"tickets","children":[{"permission":"is_admin","name":"acs_departments","url":"departments","icon":"","id":"departments"},{"permission":"is_admin","name":"acs_ticket_predefined_replies_submenu","url":"tickets\\/predifined_replies","icon":"","id":"predifined-replies"},{"permission":"is_admin","name":"acs_ticket_priority_submenu","url":"tickets\\/priorities","icon":"","id":"ticket-priority"},{"permission":"is_admin","name":"acs_ticket_statuses_submenu","url":"tickets\\/statuses","icon":"","id":"ticket-statuses"},{"permission":"is_admin","name":"acs_ticket_services_submenu","url":"tickets\\/services","icon":"","id":"services"},{"permission":"is_admin","name":"spam_filters","url":"tickets\\/spam_filters","icon":"","id":"spam-filters"}]},{"permission":"is_admin","name":"acs_leads","url":"#","icon":"","id":"leads","children":[{"permission":"","name":"acs_leads_sources_submenu","url":"leads\\/sources","icon":"","id":"sources"},{"permission":"","name":"acs_leads_statuses_submenu","url":"leads\\/statuses","icon":"","id":"statuses"},{"permission":"","name":"leads_email_integration","url":"leads\\/email_integration","icon":"","id":"email-integration"},{"name":"web_to_lead","permission":"is_admin","icon":"","url":"leads\\/forms","id":"web-to-lead"}]},{"permission":"is_admin","name":"acs_finance","url":"#","icon":"","id":"finance","children":[{"permission":"","name":"acs_sales_taxes_submenu","url":"taxes","icon":"","id":"taxes"},{"permission":"","name":"acs_sales_currencies_submenu","url":"currencies","icon":"","id":"currencies"},{"permission":"","name":"acs_sales_payment_modes_submenu","url":"paymentmodes","icon":"","id":"payment-modes"},{"permission":"","name":"acs_expense_categories","url":"expenses\\/categories","icon":"","id":"expenses-categories"}]},{"permission":"is_admin","name":"acs_contracts","url":"#","icon":"","id":"contracts","children":[{"permission":"","name":"acs_contract_types","url":"contracts\\/types","icon":"","id":"contract-types"}]},{"permission":"email_templates","name":"acs_email_templates","url":"emails","icon":"","id":"email-templates"},{"permission":"is_admin","name":"asc_custom_fields","url":"custom_fields","icon":"","id":"custom-fields"},{"permission":"roles","name":"acs_roles","url":"roles","icon":"","id":"roles"},{"permission":"is_admin","name":"menu_builder","url":"#","icon":"","id":"menu-builder","children":[{"permission":"","name":"main_menu","url":"utilities\\/main_menu","icon":"","id":"organize-sidebar"},{"permission":"is_admin","name":"setup_menu","url":"utilities\\/setup_menu","icon":"","id":"setup-menu"}]},{"name":"theme_style","permission":"is_admin","icon":"","url":"utilities\\/theme_style","id":"theme-style"},{"permission":"settings","name":"acs_settings","url":"settings","icon":"","id":"settings"}]}'),
(97, 'access_tickets_to_none_staff_members', '0'),
(98, 'setup_menu_inactive', '{"setup_menu_inactive":[]}'),
(99, 'customer_default_country', ''),
(100, 'view_estimate_only_logged_in', '0'),
(101, 'show_status_on_pdf_ei', '1'),
(102, 'email_piping_only_replies', '0'),
(103, 'email_piping_only_registered', '0'),
(104, 'default_view_calendar', 'month'),
(105, 'email_piping_default_priority', '2'),
(106, 'total_to_words_lowercase', '0'),
(107, 'show_tax_per_item', '1'),
(108, 'last_survey_send_cron', ''),
(109, 'total_to_words_enabled', '0'),
(110, 'receive_notification_on_new_ticket', '1'),
(111, 'autoclose_tickets_after', '0'),
(112, 'media_max_file_size_upload', '10'),
(113, 'client_staff_add_edit_delete_task_comments_first_hour', '0'),
(114, 'show_projects_on_calendar', '1'),
(115, 'leads_kanban_limit', '50'),
(116, 'tasks_reminder_notification_before', '2'),
(117, 'pdf_font', 'freesans'),
(118, 'pdf_table_heading_color', '#323a45'),
(119, 'pdf_table_heading_text_color', '#ffffff'),
(120, 'pdf_font_size', '10'),
(121, 'defaut_leads_kanban_sort', 'leadorder'),
(122, 'defaut_leads_kanban_sort_type', 'asc'),
(123, 'allowed_files', '.gif,.png,.jpeg,.jpg,.pdf,.doc,.txt,.docx,.xls,.zip,.rar,.xls,.mp4'),
(124, 'show_all_tasks_for_project_member', '1'),
(125, 'email_protocol', 'smtp'),
(126, 'calendar_first_day', '0'),
(127, 'recaptcha_secret_key', ''),
(128, 'show_help_on_setup_menu', '1'),
(129, 'show_proposals_on_calendar', '1'),
(130, 'smtp_encryption', ''),
(131, 'recaptcha_site_key', ''),
(132, 'smtp_username', ''),
(133, 'auto_stop_tasks_timers_on_new_timer', '0'),
(134, 'notification_when_customer_pay_invoice', '1'),
(135, 'theme_style', '[]'),
(136, 'calendar_invoice_color', '#FF6F00'),
(137, 'calendar_estimate_color', '#FF6F00'),
(138, 'calendar_proposal_color', '#84c529'),
(139, 'calendar_task_color', '#FC2D42'),
(140, 'calendar_reminder_color', '#03A9F4'),
(141, 'calendar_contract_color', '#B72974'),
(142, 'calendar_project_color', '#B72974'),
(143, 'update_info_message', ''),
(144, 'show_estimate_reminders_on_calendar', '1'),
(145, 'show_invoice_reminders_on_calendar', '1'),
(146, 'show_proposal_reminders_on_calendar', '1'),
(147, 'proposal_due_after', '7'),
(148, 'allow_customer_to_change_ticket_status', '0'),
(149, 'lead_lock_after_convert_to_customer', '0'),
(150, 'default_proposals_pipeline_sort', 'pipeline_order'),
(151, 'defaut_proposals_pipeline_sort_type', 'asc'),
(152, 'default_estimates_pipeline_sort', 'pipeline_order'),
(153, 'defaut_estimates_pipeline_sort_type', 'asc'),
(154, 'use_recaptcha_customers_area', '0'),
(155, 'remove_decimals_on_zero', '0'),
(156, 'remove_tax_name_from_item_table', '0'),
(157, 'pdf_format_invoice', 'A4-PORTRAIT'),
(158, 'pdf_format_estimate', 'A4-PORTRAIT'),
(159, 'pdf_format_proposal', 'A4-PORTRAIT'),
(160, 'pdf_format_payment', 'A4-PORTRAIT'),
(161, 'pdf_format_contract', 'A4-PORTRAIT'),
(162, 'pdf_text_color', '#000000'),
(163, 'auto_check_for_new_notifications', '0'),
(164, 'swap_pdf_info', '0'),
(165, 'exclude_invoice_from_client_area_with_draft_status', '1'),
(166, 'cron_has_run_from_cli', '0'),
(167, 'hide_cron_is_required_message', '0'),
(168, 'auto_assign_customer_admin_after_lead_convert', '1'),
(169, 'show_transactions_on_invoice_pdf', '1'),
(170, 'show_pay_link_to_invoice_pdf', '1'),
(171, 'tasks_kanban_limit', '50'),
(172, 'purchase_key', ''),
(173, 'estimates_pipeline_limit', '50'),
(174, 'proposals_pipeline_limit', '50'),
(175, 'proposal_number_prefix', 'PRO-'),
(176, 'number_padding_prefixes', '6'),
(177, 'show_page_number_on_pdf', '0'),
(178, 'calendar_events_limit', '4'),
(179, 'show_setup_menu_item_only_on_hover', '0'),
(180, 'company_requires_vat_number_field', '1'),
(181, 'company_is_required', '1'),
(182, 'allow_contact_to_delete_files', '0'),
(183, 'company_vat', ''),
(184, 'di', ''),
(185, 'invoice_auto_operations_hour', '21'),
(186, 'use_minified_files', '1'),
(187, 'only_own_files_contacts', '0'),
(188, 'allow_primary_contact_to_view_edit_billing_and_shipping', '0'),
(189, 'estimate_due_after', '7'),
(190, 'delete_backups_older_then', '0'),
(191, 'staff_members_open_tickets_to_all_contacts', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tblpermissions`
--

CREATE TABLE IF NOT EXISTS `tblpermissions` (
  `permissionid` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `shortname` mediumtext NOT NULL,
  PRIMARY KEY (`permissionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `tblpermissions`
--

INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES
(1, 'Contracts', 'contracts'),
(2, 'Tasks', 'tasks'),
(3, 'Reports', 'reports'),
(4, 'Settings', 'settings'),
(5, 'Projects', 'projects'),
(6, 'Surveys', 'surveys'),
(7, 'Staff', 'staff'),
(8, 'Customers', 'customers'),
(9, 'Email Templates', 'email_templates'),
(10, 'Roles', 'roles'),
(11, 'Estimates', 'estimates'),
(12, 'Knowledge base', 'knowledge_base'),
(13, 'Proposals', 'proposals'),
(14, 'Goals', 'goals'),
(15, 'Expenses', 'expenses'),
(16, 'Bulk PDF Exporter', 'bulk_pdf_exporter'),
(17, 'Payments', 'payments'),
(18, 'Invoices', 'invoices'),
(19, 'Items', 'items');

-- --------------------------------------------------------

--
-- Table structure for table `tblpinnedprojects`
--

CREATE TABLE IF NOT EXISTS `tblpinnedprojects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblpostcomments`
--

CREATE TABLE IF NOT EXISTS `tblpostcomments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `userid` int(11) NOT NULL,
  `postid` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblpostlikes`
--

CREATE TABLE IF NOT EXISTS `tblpostlikes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `dateliked` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblposts`
--

CREATE TABLE IF NOT EXISTS `tblposts` (
  `postid` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `datecreated` datetime NOT NULL,
  `visibility` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `pinned` int(11) NOT NULL,
  `datepinned` datetime DEFAULT NULL,
  PRIMARY KEY (`postid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblpredifinedreplies`
--

CREATE TABLE IF NOT EXISTS `tblpredifinedreplies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblpriorities`
--

CREATE TABLE IF NOT EXISTS `tblpriorities` (
  `priorityid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`priorityid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tblpriorities`
--

INSERT INTO `tblpriorities` (`priorityid`, `name`) VALUES
(1, 'Low'),
(2, 'Medium'),
(3, 'High');

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectactivity`
--

CREATE TABLE IF NOT EXISTS `tblprojectactivity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `fullname` varchar(100) DEFAULT NULL,
  `visible_to_customer` int(11) NOT NULL DEFAULT '0',
  `description_key` varchar(500) NOT NULL COMMENT 'Language file key',
  `additional_data` text,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectdiscussioncomments`
--

CREATE TABLE IF NOT EXISTS `tblprojectdiscussioncomments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discussion_id` int(11) NOT NULL,
  `discussion_type` varchar(10) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `content` text NOT NULL,
  `staff_id` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT '0',
  `fullname` varchar(300) DEFAULT NULL,
  `file_name` varchar(300) DEFAULT NULL,
  `file_mime_type` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectdiscussions`
--

CREATE TABLE IF NOT EXISTS `tblprojectdiscussions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `show_to_customer` tinyint(1) NOT NULL DEFAULT '0',
  `datecreated` datetime NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `staff_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectfiles`
--

CREATE TABLE IF NOT EXISTS `tblprojectfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` mediumtext NOT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `description` text,
  `filetype` varchar(50) DEFAULT NULL,
  `dateadded` datetime NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `visible_to_customer` tinyint(1) DEFAULT '0',
  `staffid` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `external` varchar(40) DEFAULT NULL,
  `external_link` text,
  `thumbnail_link` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectmembers`
--

CREATE TABLE IF NOT EXISTS `tblprojectmembers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectnotes`
--

CREATE TABLE IF NOT EXISTS `tblprojectnotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `staff_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojects`
--

CREATE TABLE IF NOT EXISTS `tblprojects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(600) NOT NULL,
  `description` text,
  `status` int(11) NOT NULL DEFAULT '0',
  `clientid` int(11) NOT NULL,
  `billing_type` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date DEFAULT NULL,
  `project_created` date NOT NULL,
  `date_finished` datetime DEFAULT NULL,
  `progress` int(11) DEFAULT '0',
  `progress_from_tasks` int(11) NOT NULL DEFAULT '1',
  `project_cost` decimal(11,2) DEFAULT NULL,
  `project_rate_per_hour` decimal(11,2) DEFAULT NULL,
  `addedfrom` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `clientid` (`clientid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblprojectsettings`
--

CREATE TABLE IF NOT EXISTS `tblprojectsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblproposalcomments`
--

CREATE TABLE IF NOT EXISTS `tblproposalcomments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` mediumtext,
  `proposalid` int(11) NOT NULL,
  `staffid` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblproposals`
--

CREATE TABLE IF NOT EXISTS `tblproposals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(500) DEFAULT NULL,
  `content` longtext,
  `addedfrom` int(11) NOT NULL,
  `datecreated` datetime NOT NULL,
  `total` decimal(11,2) DEFAULT NULL,
  `subtotal` decimal(11,2) NOT NULL,
  `total_tax` decimal(11,2) NOT NULL DEFAULT '0.00',
  `adjustment` decimal(11,2) DEFAULT NULL,
  `discount_percent` decimal(11,2) NOT NULL,
  `discount_total` decimal(11,2) NOT NULL,
  `discount_type` varchar(30) DEFAULT NULL,
  `show_quantity_as` int(11) NOT NULL DEFAULT '1',
  `currency` int(11) NOT NULL,
  `open_till` date DEFAULT NULL,
  `date` date NOT NULL,
  `rel_id` int(11) DEFAULT NULL,
  `rel_type` varchar(40) DEFAULT NULL,
  `assigned` int(11) DEFAULT NULL,
  `hash` varchar(32) NOT NULL,
  `proposal_to` varchar(600) DEFAULT NULL,
  `country` int(11) NOT NULL DEFAULT '0',
  `zip` varchar(50) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL,
  `estimate_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `date_converted` datetime DEFAULT NULL,
  `pipeline_order` int(11) NOT NULL DEFAULT '0',
  `is_expiry_notified` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblreminders`
--

CREATE TABLE IF NOT EXISTS `tblreminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text,
  `date` datetime NOT NULL,
  `isnotified` int(11) NOT NULL DEFAULT '0',
  `rel_id` int(11) NOT NULL,
  `staff` int(11) NOT NULL,
  `rel_type` varchar(40) NOT NULL,
  `notify_by_email` int(11) NOT NULL DEFAULT '1',
  `creator` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblrolepermissions`
--

CREATE TABLE IF NOT EXISTS `tblrolepermissions` (
  `rolepermissionid` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT '0',
  `can_view_own` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit` tinyint(1) DEFAULT '0',
  `can_create` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete` tinyint(1) NOT NULL DEFAULT '0',
  `permissionid` int(11) NOT NULL,
  PRIMARY KEY (`rolepermissionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblroles`
--

CREATE TABLE IF NOT EXISTS `tblroles` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`roleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tblroles`
--

INSERT INTO `tblroles` (`roleid`, `name`) VALUES
(1, 'Employee');

-- --------------------------------------------------------

--
-- Table structure for table `tblsalesactivity`
--

CREATE TABLE IF NOT EXISTS `tblsalesactivity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_type` varchar(20) DEFAULT NULL,
  `rel_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `additional_data` varchar(600) DEFAULT NULL,
  `staffid` varchar(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblservices`
--

CREATE TABLE IF NOT EXISTS `tblservices` (
  `serviceid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`serviceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsessions`
--

CREATE TABLE IF NOT EXISTS `tblsessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--

CREATE TABLE IF NOT EXISTS `tblstaff` (
  `staffid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `facebook` mediumtext,
  `linkedin` mediumtext,
  `phonenumber` varchar(30) DEFAULT NULL,
  `skype` varchar(50) DEFAULT NULL,
  `password` varchar(250) NOT NULL,
  `datecreated` datetime NOT NULL,
  `profile_image` varchar(300) DEFAULT NULL,
  `last_ip` varchar(40) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `new_pass_key` varchar(32) DEFAULT NULL,
  `new_pass_key_requested` datetime DEFAULT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `role` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `default_language` varchar(40) DEFAULT NULL,
  `direction` varchar(3) DEFAULT NULL,
  `media_path_slug` varchar(300) DEFAULT NULL,
  `is_not_staff` int(11) NOT NULL DEFAULT '0',
  `hourly_rate` decimal(11,2) NOT NULL DEFAULT '0.00',
  `email_signature` text,
  PRIMARY KEY (`staffid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblstaffdepartments`
--

CREATE TABLE IF NOT EXISTS `tblstaffdepartments` (
  `staffdepartmentid` int(11) NOT NULL AUTO_INCREMENT,
  `staffid` int(11) NOT NULL,
  `departmentid` int(11) NOT NULL,
  PRIMARY KEY (`staffdepartmentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblstaffpermissions`
--

CREATE TABLE IF NOT EXISTS `tblstaffpermissions` (
  `staffpermid` int(11) NOT NULL AUTO_INCREMENT,
  `permissionid` int(11) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT '0',
  `can_view_own` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `can_create` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete` tinyint(1) NOT NULL DEFAULT '0',
  `staffid` int(11) NOT NULL,
  PRIMARY KEY (`staffpermid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblstafftaskassignees`
--

CREATE TABLE IF NOT EXISTS `tblstafftaskassignees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staffid` int(11) NOT NULL,
  `taskid` int(11) NOT NULL,
  `assigned_from` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`),
  KEY `staffid` (`staffid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblstafftaskcomments`
--

CREATE TABLE IF NOT EXISTS `tblstafftaskcomments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `taskid` int(11) NOT NULL,
  `staffid` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblstafftasks`
--

CREATE TABLE IF NOT EXISTS `tblstafftasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext,
  `description` text,
  `priority` int(11) DEFAULT NULL,
  `dateadded` datetime NOT NULL,
  `startdate` date NOT NULL,
  `duedate` date DEFAULT NULL,
  `datefinished` datetime NOT NULL,
  `addedfrom` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `recurring_type` varchar(10) DEFAULT NULL,
  `repeat_every` int(11) DEFAULT NULL,
  `recurring` int(11) NOT NULL DEFAULT '0',
  `recurring_ends_on` date DEFAULT NULL,
  `custom_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `last_recurring_date` date DEFAULT NULL,
  `rel_id` int(11) DEFAULT NULL,
  `rel_type` varchar(30) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `billable` tinyint(1) NOT NULL DEFAULT '0',
  `billed` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `hourly_rate` decimal(11,2) NOT NULL DEFAULT '0.00',
  `milestone` int(11) DEFAULT '0',
  `kanban_order` int(11) NOT NULL DEFAULT '0',
  `milestone_order` int(11) NOT NULL DEFAULT '0',
  `visible_to_client` tinyint(1) NOT NULL DEFAULT '0',
  `deadline_notified` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`),
  KEY `milestone` (`milestone`),
  KEY `kanban_order` (`kanban_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblstafftasksfollowers`
--

CREATE TABLE IF NOT EXISTS `tblstafftasksfollowers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staffid` int(11) NOT NULL,
  `taskid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsurveyresultsets`
--

CREATE TABLE IF NOT EXISTS `tblsurveyresultsets` (
  `resultsetid` int(11) NOT NULL AUTO_INCREMENT,
  `surveyid` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `useragent` varchar(150) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`resultsetid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsurveys`
--

CREATE TABLE IF NOT EXISTS `tblsurveys` (
  `surveyid` int(11) NOT NULL AUTO_INCREMENT,
  `subject` mediumtext NOT NULL,
  `slug` mediumtext NOT NULL,
  `description` text NOT NULL,
  `viewdescription` text,
  `datecreated` datetime NOT NULL,
  `redirect_url` varchar(100) DEFAULT NULL,
  `send` tinyint(1) NOT NULL DEFAULT '0',
  `onlyforloggedin` int(11) DEFAULT '0',
  `fromname` varchar(100) DEFAULT NULL,
  `iprestrict` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY (`surveyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsurveysemailsendcron`
--

CREATE TABLE IF NOT EXISTS `tblsurveysemailsendcron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surveyid` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `emailid` int(11) DEFAULT NULL,
  `listid` varchar(11) DEFAULT NULL,
  `log_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsurveysendlog`
--

CREATE TABLE IF NOT EXISTS `tblsurveysendlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surveyid` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `iscronfinished` int(11) NOT NULL DEFAULT '0',
  `send_to_mail_lists` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbltags`
--

CREATE TABLE IF NOT EXISTS `tbltags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbltags_in`
--

CREATE TABLE IF NOT EXISTS `tbltags_in` (
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `tag_order` int(11) NOT NULL DEFAULT '0',
  KEY `rel_id` (`rel_id`),
  KEY `rel_type` (`rel_type`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbltaskchecklists`
--

CREATE TABLE IF NOT EXISTS `tbltaskchecklists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskid` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `finished` int(11) NOT NULL DEFAULT '0',
  `dateadded` datetime NOT NULL,
  `addedfrom` int(11) NOT NULL,
  `finished_from` int(11) DEFAULT '0',
  `list_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbltaskstimers`
--

CREATE TABLE IF NOT EXISTS `tbltaskstimers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `start_time` varchar(64) NOT NULL,
  `end_time` varchar(64) DEFAULT NULL,
  `staff_id` int(11) NOT NULL,
  `hourly_rate` decimal(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbltaxes`
--

CREATE TABLE IF NOT EXISTS `tbltaxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `taxrate` decimal(11,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblticketattachments`
--

CREATE TABLE IF NOT EXISTS `tblticketattachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) NOT NULL,
  `replyid` int(11) DEFAULT NULL,
  `file_name` mediumtext NOT NULL,
  `filetype` varchar(50) DEFAULT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblticketpipelog`
--

CREATE TABLE IF NOT EXISTS `tblticketpipelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `email_to` varchar(500) NOT NULL,
  `name` varchar(200) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `message` mediumtext NOT NULL,
  `email` varchar(300) NOT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblticketreplies`
--

CREATE TABLE IF NOT EXISTS `tblticketreplies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `contactid` int(11) NOT NULL DEFAULT '0',
  `name` text,
  `email` text,
  `date` datetime NOT NULL,
  `message` text,
  `attachment` int(11) DEFAULT NULL,
  `admin` int(11) DEFAULT NULL,
  `ip` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbltickets`
--

CREATE TABLE IF NOT EXISTS `tbltickets` (
  `ticketid` int(11) NOT NULL AUTO_INCREMENT,
  `adminreplying` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL,
  `contactid` int(11) NOT NULL DEFAULT '0',
  `email` text,
  `name` text,
  `department` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `service` int(11) DEFAULT NULL,
  `ticketkey` varchar(32) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `message` text,
  `admin` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `lastreply` datetime DEFAULT NULL,
  `clientread` int(11) NOT NULL DEFAULT '0',
  `adminread` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(40) DEFAULT NULL,
  `assigned` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketid`),
  KEY `service` (`service`),
  KEY `department` (`department`),
  KEY `status` (`status`),
  KEY `userid` (`userid`),
  KEY `priority` (`priority`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblticketsspamcontrol`
--

CREATE TABLE IF NOT EXISTS `tblticketsspamcontrol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(40) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblticketstatus`
--

CREATE TABLE IF NOT EXISTS `tblticketstatus` (
  `ticketstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `isdefault` int(11) NOT NULL DEFAULT '0',
  `statuscolor` varchar(7) DEFAULT NULL,
  `statusorder` int(11) DEFAULT NULL,
  PRIMARY KEY (`ticketstatusid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tblticketstatus`
--

INSERT INTO `tblticketstatus` (`ticketstatusid`, `name`, `isdefault`, `statuscolor`, `statusorder`) VALUES
(3, 'Answered', 1, '#0000ff', 3),
(4, 'On Hold', 1, '#c0c0c0', 4),
(2, 'In progress', 1, '#84c529', 2),
(5, 'Closed', 1, '#03a9f4', 5),
(1, 'Open', 1, '#ff2d42', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbltodoitems`
--

CREATE TABLE IF NOT EXISTS `tbltodoitems` (
  `todoid` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `staffid` int(11) NOT NULL,
  `dateadded` datetime NOT NULL,
  `finished` tinyint(1) NOT NULL,
  `datefinished` datetime DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`todoid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbluserautologin`
--

CREATE TABLE IF NOT EXISTS `tbluserautologin` (
  `key_id` char(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_agent` varchar(150) NOT NULL,
  `last_ip` varchar(40) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `staff` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblviewstracking`
--

CREATE TABLE IF NOT EXISTS `tblviewstracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rel_id` int(11) NOT NULL,
  `rel_type` varchar(40) NOT NULL,
  `date` datetime NOT NULL,
  `view_ip` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblwebtolead`
--

CREATE TABLE IF NOT EXISTS `tblwebtolead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_key` varchar(32) NOT NULL,
  `lead_source` int(11) NOT NULL,
  `lead_status` int(11) NOT NULL,
  `notify_lead_imported` int(11) NOT NULL DEFAULT '1',
  `notify_type` varchar(20) DEFAULT NULL,
  `notify_ids` mediumtext,
  `responsible` int(11) NOT NULL DEFAULT '0',
  `name` varchar(400) NOT NULL,
  `form_data` mediumtext,
  `recaptcha` int(11) NOT NULL DEFAULT '0',
  `submit_btn_name` varchar(40) DEFAULT NULL,
  `success_submit_msg` text,
  `language` varchar(40) DEFAULT NULL,
  `allow_duplicate` int(11) NOT NULL DEFAULT '1',
  `track_duplicate_field` varchar(20) DEFAULT NULL,
  `track_duplicate_field_and` varchar(20) DEFAULT NULL,
  `create_task_on_duplicate` int(11) NOT NULL DEFAULT '0',
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
