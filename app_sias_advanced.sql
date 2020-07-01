-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2018 at 09:16 AM
-- Server version: 10.1.24-MariaDB
-- PHP Version: 7.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_sias_premium_send`
--

-- --------------------------------------------------------

--
-- Table structure for table `arsip_file`
--

CREATE TABLE `arsip_file` (
  `id_arsip` int(11) NOT NULL,
  `id_user` varchar(25) NOT NULL,
  `id_klasifikasi` varchar(25) NOT NULL,
  `no_arsip` varchar(100) NOT NULL,
  `tgl_arsip` date NOT NULL,
  `keamanan` varchar(100) NOT NULL,
  `ket` tinytext NOT NULL,
  `file_arsip` tinytext NOT NULL,
  `tgl_upload` date NOT NULL,
  `created` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arsip_sk`
--

CREATE TABLE `arsip_sk` (
  `id_sk` int(11) NOT NULL,
  `no_agenda` varchar(100) NOT NULL,
  `custom_noagenda` varchar(100) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `no_sk` varchar(150) NOT NULL,
  `tgl_surat` date NOT NULL,
  `klasifikasi` varchar(10) NOT NULL,
  `pengolah` varchar(100) NOT NULL,
  `tujuan_surat` tinytext NOT NULL,
  `perihal` text NOT NULL,
  `ket` text NOT NULL,
  `file` varchar(200) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arsip_sm`
--

CREATE TABLE `arsip_sm` (
  `id_sm` int(11) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `no_sm` varchar(150) NOT NULL,
  `tgl_terima` date NOT NULL,
  `no_agenda` varchar(50) NOT NULL,
  `custom_noagenda` varchar(100) NOT NULL,
  `klasifikasi` varchar(10) NOT NULL,
  `tgl_surat` date NOT NULL,
  `pengirim` varchar(200) NOT NULL,
  `tujuan_surat` varchar(200) NOT NULL,
  `perihal` text NOT NULL,
  `ket` text NOT NULL,
  `file` tinytext NOT NULL,
  `view` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bagian`
--

CREATE TABLE `bagian` (
  `id_bag` int(11) NOT NULL,
  `nama_bagian` tinytext NOT NULL,
  `kepala` varchar(100) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bagian`
--

INSERT INTO `bagian` (`id_bag`, `nama_bagian`, `kepala`, `created`) VALUES
(1, 'Administrasi', 'Ka. Administrasi', '2017-07-08 17:35:18'),
(2, 'Kepegawain', 'Ka. Kepegawaian', '2017-07-08 17:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `email_setting`
--

CREATE TABLE `email_setting` (
  `id` int(11) NOT NULL,
  `id_kop` varchar(15) NOT NULL,
  `layout` text NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'N',
  `ket` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_setting`
--

INSERT INTO `email_setting` (`id`, `id_kop`, `layout`, `status`, `ket`) VALUES
(11, '1', '<p>Yth Bpk/Ibu :</p>\r\n<p><strong>=TembusanV=</strong></p>\r\n<p>Anda mendapat tembusan surat masuk baru dari <strong>=DisposisiOleh=</strong> dengan ket :<br /><br />Nomor agenda : <strong>=NoAgenda=</strong><br />Nomor surat : <strong>=NoSurat=</strong><br />Perihal surat : <strong>=Perihal=</strong><br />Surat Dari : <strong>=AsalSurat=</strong><br />Keterangan : <strong>=Keterangan=</strong><br />Tanggal surat : <strong>=TglSurat=</strong><br />Tanggal terima : <strong>=TglTerima=</strong><br />Tanggal ditembuskan: <strong>=TglDisposisi=</strong></p>\r\n<p>Terimakasih.</p>', 'Y', 'Format Email Penerima Tembusan Surat Masuk'),
(12, '2', '<p>Yth Bapak/Ibu :<br /><strong>=TujuanSurat=</strong></p>\r\n<p>Anda mendapat surat masuk baru dari <strong>=AsalSurat=</strong> perihal <strong>=Perihal=</strong>. Surat diterima pada tanggal <strong>=TglTerima= </strong>yang diterima oleh <strong>=Penerima=</strong>&nbsp;dengan ket:</p>\r\n<p>Nomor agenda : <strong>=NoAgenda=</strong><br />Nomor surat : <strong>=NoSurat=</strong><br />Tanggal surat : <strong>=TglSurat=<br /></strong>keterangan&nbsp;:<strong> =Keterangan=<br /></strong></p>\r\n<p>Terimakasih.</p>', 'Y', 'Format Email Penerima Surat Masuk'),
(13, '3', '<p>Yth Bapak/Ibu :<br /><strong>=Disposisi=</strong></p>\r\n<p>Anda mendapat disposisi surat masuk baru dari <strong>=DisposisiOleh=</strong> dengan ket :<br /><br />Nomor agenda : <strong>=NoAgenda=</strong><br />Nomor surat : <strong>=NoSurat=</strong><br />Perihal surat : <strong>=Perihal=</strong><br />Surat Dari : <strong>=AsalSurat=</strong><br />Keterangan : <strong>=Keterangan=</strong><br />Tanggal surat : <strong>=TglSurat=</strong><br />Tanggal terima : <strong>=TglTerima=</strong><br />Catatan disposisi : <br /><strong>=NoteDisposisi=</strong><br />Tanggal disposisi : <strong>=TglDisposisi=<br /></strong>Tembusan : <br /><strong>=TembusanV=<br /></strong>Surat Diteruskan ke:<br /><strong>=TujuanSurat=</strong><strong><br /></strong></p>\r\n<p>Terimakasih.</p>', 'Y', 'Format Email Penerima Disposisi Surat Masuk'),
(26, '4', '<p>Yth Bpk/Ibu :</p>\r\n<p><strong>=TujuanMemo=</strong></p>\r\n<p>Anda mendapat memo baru perihal <strong>=PerihalMemo=</strong> pada tanggal <strong>=TglMemo=</strong> dengan ket sebagai berikut :<br /><br /><em>=IsiMemo=</em></p>\r\n<p>Terimakasih.</p>', 'Y', 'Format Email Penerima Memo');

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `id_info` int(11) NOT NULL,
  `pengirim_info` varchar(50) NOT NULL,
  `tujuan_info` varchar(200) NOT NULL,
  `judul_info` varchar(150) NOT NULL,
  `ket_info` text NOT NULL,
  `tgl_info` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `klasifikasi`
--

CREATE TABLE `klasifikasi` (
  `id_klas` int(11) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `klasifikasi`
--

INSERT INTO `klasifikasi` (`id_klas`, `kode`, `nama`, `updated`, `created`) VALUES
(1, 'PR.001', 'Undangan', '2017-05-11 08:56:39', '2017-03-25'),
(2, 'PR.002', 'Permohonan', '2017-10-03 02:23:54', '2017-03-25'),
(3, 'PP.001', 'Surat Keputusan', '2018-05-04 00:42:15', '2017-05-06');

-- --------------------------------------------------------

--
-- Table structure for table `klasifikasi_arsip`
--

CREATE TABLE `klasifikasi_arsip` (
  `id_klasifikasi` int(11) NOT NULL,
  `nama_klasifikasi` varchar(150) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `klasifikasi_arsip`
--

INSERT INTO `klasifikasi_arsip` (`id_klasifikasi`, `nama_klasifikasi`, `updated`, `created`) VALUES
(1, 'Umum', '2017-06-12 13:12:43', '2017-03-25'),
(2, 'Keuangan', '2017-06-12 13:12:27', '2017-03-25'),
(10, 'Kepegawaian', '2017-07-08 10:28:38', '2017-07-08');

-- --------------------------------------------------------

--
-- Table structure for table `klasifikasi_sk`
--

CREATE TABLE `klasifikasi_sk` (
  `id_klas` int(11) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `klasifikasi_sk`
--

INSERT INTO `klasifikasi_sk` (`id_klas`, `kode`, `nama`, `updated`, `created`) VALUES
(1, 'PR.001', 'Undangan', '2017-05-11 01:56:39', '2017-03-25'),
(3, 'PP.001', 'Perintah', '2017-11-08 08:22:42', '2017-05-06'),
(11, 'PR.003', 'Permohonan', '2017-11-14 14:07:13', '2017-11-14');

-- --------------------------------------------------------

--
-- Table structure for table `kop_setting`
--

CREATE TABLE `kop_setting` (
  `idkop` int(11) NOT NULL,
  `kopdefault` enum('Y','N') NOT NULL DEFAULT 'Y',
  `layout` text NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'N',
  `ket` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kop_setting`
--

INSERT INTO `kop_setting` (`idkop`, `kopdefault`, `layout`, `status`, `ket`) VALUES
(1, 'Y', '<p style=\"color: #000000; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: normal; letter-spacing: normal; text-align: center; text-indent: 0px; text-transform: none; white-space: normal; word-spacing: 0px; -webkit-text-stroke-width: 0px; text-decoration-style: initial; text-decoration-color: initial;\">&nbsp;<span style=\"font-size: x-large;\"><strong><u>TANDA TERIMA SURAT CUSTOM <br /></u></strong></span></p>\r\n<table style=\"border-collapse: collapse; width: 699px;\" border=\"1\" align=\"center\">\r\n<tbody>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Telah terima dari</td>\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.224px;\" nowrap=\"nowrap\">=AsalSurat=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Nomor Surat</td>\r\n<td style=\"padding: 5px; width: 338.224px;\">=NoSurat=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Nomor Agenda</td>\r\n<td style=\"padding: 5px; width: 338.224px;\">=NoAgenda=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Tanggal Surat</td>\r\n<td style=\"padding: 5px; width: 338.224px;\">=TglSurat=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Tujuan Surat</td>\r\n<td style=\"padding: 5px; width: 338.224px;\">=TujuanSurat=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Tanggal Terima</td>\r\n<td style=\"padding: 5px; width: 338.224px;\">=TglTerima=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.776px;\" nowrap=\"nowrap\">Perihal</td>\r\n<td style=\"padding: 5px; vertical-align: top; width: 338.224px;\">=Perihal=</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<table style=\"border-collapse: collapse; width: 699px;\" border=\"1\" align=\"center\">\r\n<tbody>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; width: 340.622px; text-align: center;\" nowrap=\"nowrap\">Yang Menyerahkan<br /><br /><br /><br /><br /><br /><strong>=AsalSurat=</strong></td>\r\n<td style=\"padding: 5px; width: 336.378px; text-align: center;\" nowrap=\"nowrap\">Yang Menerima<br /><br /><br /><br /><br /><br /><strong>=Penerima=</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"color: #000000; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: normal; letter-spacing: normal; text-align: center; text-indent: 0px; text-transform: none; white-space: normal; word-spacing: 0px; -webkit-text-stroke-width: 0px; text-decoration-style: initial; text-decoration-color: initial;\">&nbsp;</p>\r\n<div id=\"container\" style=\"color: #000000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; text-decoration-style: initial; text-decoration-color: initial;\">\r\n<div id=\"row\">\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n</div>\r\n</div>', 'N', 'Tanda Terima Surat'),
(2, 'Y', '<h3 style=\"text-align: center;\">SURAT MASUK CUSTOM</h3>\r\n<table style=\"border-collapse: collapse;\" border=\"1\" width=\"700\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Surat Dari</td>\r\n<td style=\"padding: 5px; vertical-align: top; width: 250;\" nowrap=\"nowrap\">=AsalSurat=</td>\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Diterima Tanggal</td>\r\n<td style=\"padding: 5px; vertical-align: top; width: 225;\" nowrap=\"nowrap\">=TglTerima=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Tanggal Surat</td>\r\n<td style=\"padding: 5px; vertical-align: top;\">=TglSurat=</td>\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Nomor Agenda</td>\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">=NoAgenda=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Nomor Surat</td>\r\n<td style=\"padding: 5px;\">=NoSurat=</td>\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Tujuan Surat</td>\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">=TujuanSurat=</td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Perihal</td>\r\n<td style=\"padding: 5px; vertical-align: top;\">=Perihal=</td>\r\n<td style=\"padding: 5px; vertical-align: top;\" nowrap=\"nowrap\">Ket</td>\r\n<td style=\"padding: 5px; vertical-align: top;\">=Keterangan=</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>', 'N', 'Detail Surat'),
(3, 'Y', '<h2 id=\"mcetoc_1bd3u8rgs1\" style=\"text-align: center;\"><strong><u>Disposisi Surat Custom<br /></u></strong></h2>\r\n<table style=\"border-collapse: collapse; width: 697px;\" border=\"1\" cellspacing=\"5\" cellpadding=\"5\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 180px;\">Surat Dari</td>\r\n<td style=\"width: 142.173px;\">=AsalSurat=</td>\r\n<td style=\"width: 178.827px;\">Nomor Agenda</td>\r\n<td style=\"width: 168px;\">=NoAgenda=</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width: 180px;\">Tanggal Surat</td>\r\n<td style=\"width: 142.173px;\">=TglSurat=</td>\r\n<td style=\"width: 178.827px;\">Tujuan Surat</td>\r\n<td style=\"width: 168px;\">=TujuanSurat=</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width: 180px;\">Nomor Surat</td>\r\n<td style=\"width: 142.173px;\">=NoSurat=</td>\r\n<td style=\"width: 178.827px;\">Disposisi dari/ke</td>\r\n<td style=\"width: 168px;\">\r\n<p><strong>=DisposisiOleh=</strong> ke :<br />=Disposisi=</p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width: 180px;\">Diterima Tanggal</td>\r\n<td style=\"width: 142.173px;\">=TglTerima=</td>\r\n<td style=\"width: 178.827px;\">Tanggal Disposisi</td>\r\n<td style=\"width: 168px;\">=TglDisposisi=</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width: 180px;\">Perihal</td>\r\n<td style=\"width: 142.173px;\">=Perihal=</td>\r\n<td style=\"width: 178.827px;\">Keterangan</td>\r\n<td style=\"width: 168px;\">=Keterangan=</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<table style=\"border-collapse: collapse; width: 700px;\" border=\"1\" cellspacing=\"5\" cellpadding=\"5\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 330.026px;\" nowrap=\"nowrap\">Tembusan:&nbsp;</td>\r\n<td style=\"width: 355.974px;\" nowrap=\"nowrap\">Catatan Disposisi:</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width: 330.026px;\">=TembusanV=</td>\r\n<td style=\"width: 355.974px;\">=NoteDisposisi=</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width: 686px;\" colspan=\"2\">Ditindak lanjuti oleh Kasie/Kasubbag, TU kepada Kasubsi/kaur:</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>', 'N', 'Disposisi');

-- --------------------------------------------------------

--
-- Table structure for table `kop_variabel`
--

CREATE TABLE `kop_variabel` (
  `variabel` varchar(100) NOT NULL,
  `ket` tinytext NOT NULL,
  `id_kop` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kop_variabel`
--

INSERT INTO `kop_variabel` (`variabel`, `ket`, `id_kop`) VALUES
('=AsalSurat=', 'Asal surat/pengirim surat ', '1,2,3'),
('=Disposisi=', 'Surat didisposisikan ke', '3'),
('=DisposisiOleh=', 'Yang Memberikan Disposisi', '3'),
('=IsiMemo=', 'Isi Memo/Pesan Singkat', '4'),
('=Keterangan=', 'Keterangan surat', '1,2,3'),
('=KetSuratEdaran=', 'Keterangan Surat Edaran', '5'),
('=NoAgenda=', 'Nomor agenda surat masuk', '1,2,3'),
('=NoSurat=', 'Nomor surat masuk', '1,2,3'),
('=NoSuratEdaran=', 'Nomor Surat Edaran', '5'),
('=NoteDisposisi=', 'Catatan disposisi', '3'),
('=Penerima=', 'Konseptor Surat', '1,2'),
('=PengirimEdaran=', 'Pengirim Surat Edaran', '5'),
('=Perihal=', 'Perihal surat', '1,2,3'),
('=PerihalEdaran=', 'Perihal Surat Edaran', '5'),
('=PerihalMemo=', 'Perihal Memo/Pesna Singkat', '4'),
('=TembusanH=', 'Tembusan surat (tampil tampil sebaris)', '3'),
('=TembusanV=', 'Tembusan surat (tampil per baris)', '3'),
('=TglDisposisi=', 'Tanggal surat didisposisi', '3'),
('=TglMemo=', 'Tanggal memo', '4'),
('=TglSurat=', 'Tanggal surat Masuk', '1,2,3'),
('=TglSuratEdaran=', 'Tanggal Surat Edaran', '5'),
('=TglTerima=', 'Tanggal terima Surat', '1,2,3'),
('=TujuanEdaran=', 'Tujuan Surat Edaran', '5'),
('=TujuanMemo=', 'Tujuan Memo', '4'),
('=TujuanSurat=', 'Tujuan Surat Masuk', '1,2,3');

-- --------------------------------------------------------

--
-- Table structure for table `memo`
--

CREATE TABLE `memo` (
  `id_status` int(11) NOT NULL,
  `id_user` varchar(50) NOT NULL,
  `id_sm` varchar(200) NOT NULL,
  `disposisi` varchar(100) NOT NULL,
  `tembusan` varchar(200) NOT NULL,
  `note` text NOT NULL,
  `tgl` datetime NOT NULL,
  `ref` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `title` tinytext NOT NULL,
  `deskripsi` tinytext NOT NULL,
  `logo` varchar(200) NOT NULL,
  `no_agenda_sm_start` varchar(100) NOT NULL,
  `no_agenda_sm` varchar(100) NOT NULL,
  `no_agenda_sk_start` varchar(100) NOT NULL,
  `no_agenda_sk` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass_email` varchar(100) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `title`, `deskripsi`, `logo`, `no_agenda_sm_start`, `no_agenda_sm`, `no_agenda_sk_start`, `no_agenda_sk`, `email`, `pass_email`, `updated`) VALUES
(1, 'SIAS - Sistem Informasi Arsip Surat', 'SIAS merupakan aplikasi pengelolaan arsip surat', 'KOP_22-04-2017_10-03-01.jpg', '1', '=KodeSurat=/SM/=Tahun=', '1', '=KodeSurat=/SK/=Tahun=', '', '', '2018-05-04 03:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `status_surat`
--

CREATE TABLE `status_surat` (
  `id_status` int(15) NOT NULL,
  `id_sm` varchar(15) NOT NULL,
  `statsurat` char(1) NOT NULL,
  `id_user` varchar(15) NOT NULL,
  `ket` tinytext NOT NULL,
  `file_progress` varchar(100) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `surat_read`
--

CREATE TABLE `surat_read` (
  `id_sm` varchar(15) NOT NULL,
  `id_user` varchar(11) NOT NULL,
  `kode` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `uname` varchar(150) NOT NULL,
  `upass` varchar(150) NOT NULL,
  `rule_disposisi` tinytext NOT NULL,
  `level` varchar(100) NOT NULL,
  `jabatan` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `picture` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nik`, `nama`, `uname`, `upass`, `rule_disposisi`, `level`, `jabatan`, `email`, `picture`) VALUES
(1, '123', 'Administrator', 'admin', '25e4ee4e9229397b6b17776bfceaf8e7', '[\"2\",\"9\",\"10\",\"3\"]', 'Admin', 1, '', 'picture.jpg'),
(2, '231', 'Heranto', 'manager', '3fd7488b6fd40f33c5a8e857b6a944aa', '[\"1\",\"9\",\"10\",\"11\",\"12\"]', 'Manager', 2, '', 'picture.jpg'),
(3, '345', 'Sekretaris', 'sekretaris', '32412aecc88b9b652ba420a6ae5ff42f', '[\"11\"]', 'Sekretaris', 6, '', 'picture.jpg'),
(9, '112233', 'Pegawai1', 'pegawai1', '0b96cb1d0dfbcc85f6b57041656abc49', '[\"1\",\"2\",\"9\",\"10\",\"11\",\"12\",\"3\"]', '', 2, '', 'picture.jpg'),
(10, '223311', 'Pegawai2', 'pegawai2', '7849816e52e7d1596c51f3e36f21c498', '[\"11\",\"9\"]', '', 3, '', 'picture.jpg'),
(11, '332211', 'Pegawai3', 'pegawai3', 'a09cc4cccc0885f6aaa35c3a181174b4', '[\"10\"]', '', 5, '', 'picture.jpg'),
(12, '443322', 'Pegawai4', 'pegawai4', '651fe7b9c6578dee129b7c7b9d484119', 'null', '', 4, '', 'picture.jpg'),
(17, '12345', 'Pegawai5', 'pegawai5', 'b2aaae58aef41072cf421b520c2def51', 'null', '', 4, '', 'sekretaris.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_jabatan`
--

CREATE TABLE `user_jabatan` (
  `id_jab` int(11) NOT NULL,
  `nama_jabatan` varchar(200) NOT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_jabatan`
--

INSERT INTO `user_jabatan` (`id_jab`, `nama_jabatan`, `created`, `updated`) VALUES
(1, 'Ketua ICT', '2017-09-16 06:25:39', '2017-09-16 06:29:30'),
(2, 'Ketua', '2017-09-16 10:35:40', '2017-12-13 08:34:57'),
(3, 'Sekretaris', '2017-09-16 13:40:42', '2017-12-13 08:34:49'),
(4, 'Staf', '2017-09-27 05:41:22', '2017-09-26 22:41:22'),
(5, 'Kasubag Kepegawaian', '2017-11-29 04:56:37', '2017-12-13 08:34:36'),
(6, 'Pengelola Surat', '2017-11-29 04:58:16', '2017-12-13 08:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_level`
--

CREATE TABLE `user_level` (
  `id_user` int(11) NOT NULL,
  `sm` char(1) NOT NULL DEFAULT 'N',
  `sk` char(1) NOT NULL DEFAULT 'N',
  `arsip` char(1) NOT NULL DEFAULT 'N',
  `cari_surat_masuk` char(1) NOT NULL DEFAULT 'N',
  `cari_surat_keluar` char(1) NOT NULL DEFAULT 'N',
  `template_surat` char(1) NOT NULL DEFAULT 'N',
  `atur_noagenda` char(1) NOT NULL DEFAULT 'N',
  `atur_layout` char(1) NOT NULL DEFAULT 'N',
  `report_dispo` char(1) NOT NULL DEFAULT 'N',
  `atur_klasifikasi_sm` char(1) NOT NULL DEFAULT 'N',
  `atur_klasifikasi_sk` char(1) NOT NULL DEFAULT 'N',
  `atur_klasifikasi_arsip` char(1) NOT NULL DEFAULT 'N',
  `atur_user` char(1) NOT NULL DEFAULT 'N',
  `atur_infoapp` char(1) NOT NULL DEFAULT 'N',
  `report_sm` char(1) DEFAULT 'N',
  `report_sk` char(1) NOT NULL DEFAULT 'N',
  `report_arsip` char(1) NOT NULL DEFAULT 'N',
  `report_progress` char(1) NOT NULL DEFAULT 'N',
  `info` char(1) NOT NULL DEFAULT 'N',
  `atur_tujuan_sk` char(1) NOT NULL DEFAULT 'N',
  `edaran` char(1) NOT NULL,
  `report_edaran` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_level`
--

INSERT INTO `user_level` (`id_user`, `sm`, `sk`, `arsip`, `cari_surat_masuk`, `cari_surat_keluar`, `template_surat`, `atur_noagenda`, `atur_layout`, `report_dispo`, `atur_klasifikasi_sm`, `atur_klasifikasi_sk`, `atur_klasifikasi_arsip`, `atur_user`, `atur_infoapp`, `report_sm`, `report_sk`, `report_arsip`, `report_progress`, `info`, `atur_tujuan_sk`, `edaran`, `report_edaran`) VALUES
(1, 'W', 'W', 'W', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'W', ''),
(2, 'R', 'R', 'R', 'N', 'N', 'N', 'N', '', 'Y', '', 'N', '', '', 'N', 'Y', 'Y', 'Y', '', 'N', '', '', ''),
(3, 'W', 'W', 'W', 'Y', 'Y', 'W', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', '', 'W', 'Y'),
(9, 'R', 'R', 'R', 'N', 'N', 'N', 'N', '', 'Y', 'N', 'N', 'N', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'N', '', '', ''),
(10, 'R', 'R', 'R', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', '', '', ''),
(11, 'R', 'R', 'R', 'N', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', '', '', ''),
(12, '', '', '', 'N', 'N', 'N', 'N', '', '', '', 'N', '', '', 'N', '', '', '', '', 'N', '', '', ''),
(17, 'W', 'W', '', 'N', 'N', 'N', 'N', '', '', '', '', '', '', 'N', '', '', '', '', '', 'N', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arsip_file`
--
ALTER TABLE `arsip_file`
  ADD PRIMARY KEY (`id_arsip`);

--
-- Indexes for table `arsip_sk`
--
ALTER TABLE `arsip_sk`
  ADD PRIMARY KEY (`id_sk`);

--
-- Indexes for table `arsip_sm`
--
ALTER TABLE `arsip_sm`
  ADD PRIMARY KEY (`id_sm`);

--
-- Indexes for table `bagian`
--
ALTER TABLE `bagian`
  ADD PRIMARY KEY (`id_bag`);

--
-- Indexes for table `email_setting`
--
ALTER TABLE `email_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`id_info`);

--
-- Indexes for table `klasifikasi`
--
ALTER TABLE `klasifikasi`
  ADD PRIMARY KEY (`id_klas`);

--
-- Indexes for table `klasifikasi_arsip`
--
ALTER TABLE `klasifikasi_arsip`
  ADD PRIMARY KEY (`id_klasifikasi`);

--
-- Indexes for table `klasifikasi_sk`
--
ALTER TABLE `klasifikasi_sk`
  ADD PRIMARY KEY (`id_klas`);

--
-- Indexes for table `kop_setting`
--
ALTER TABLE `kop_setting`
  ADD PRIMARY KEY (`idkop`);

--
-- Indexes for table `kop_variabel`
--
ALTER TABLE `kop_variabel`
  ADD PRIMARY KEY (`variabel`);

--
-- Indexes for table `memo`
--
ALTER TABLE `memo`
  ADD PRIMARY KEY (`id_status`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status_surat`
--
ALTER TABLE `status_surat`
  ADD PRIMARY KEY (`id_status`);

--
-- Indexes for table `surat_read`
--
ALTER TABLE `surat_read`
  ADD PRIMARY KEY (`id_sm`,`id_user`,`kode`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_jabatan`
--
ALTER TABLE `user_jabatan`
  ADD PRIMARY KEY (`id_jab`);

--
-- Indexes for table `user_level`
--
ALTER TABLE `user_level`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip_file`
--
ALTER TABLE `arsip_file`
  MODIFY `id_arsip` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `arsip_sk`
--
ALTER TABLE `arsip_sk`
  MODIFY `id_sk` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `arsip_sm`
--
ALTER TABLE `arsip_sm`
  MODIFY `id_sm` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bagian`
--
ALTER TABLE `bagian`
  MODIFY `id_bag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `email_setting`
--
ALTER TABLE `email_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `info`
--
ALTER TABLE `info`
  MODIFY `id_info` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `klasifikasi`
--
ALTER TABLE `klasifikasi`
  MODIFY `id_klas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `klasifikasi_arsip`
--
ALTER TABLE `klasifikasi_arsip`
  MODIFY `id_klasifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `klasifikasi_sk`
--
ALTER TABLE `klasifikasi_sk`
  MODIFY `id_klas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `memo`
--
ALTER TABLE `memo`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `status_surat`
--
ALTER TABLE `status_surat`
  MODIFY `id_status` int(15) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `user_jabatan`
--
ALTER TABLE `user_jabatan`
  MODIFY `id_jab` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
