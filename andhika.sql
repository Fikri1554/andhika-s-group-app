-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 06, 2017 at 09:18 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `andhika`
--

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE IF NOT EXISTS `karyawan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `alamat` text,
  `jenis_kelamin` varchar(10) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama`, `alamat`, `jenis_kelamin`, `tanggal_lahir`) VALUES
(1, 'Ahmad Maulana', 'Jl Margonda Raya Gg kapuk no 2', 'pria', '1985-11-30'),
(2, 'Diana', 'Jl Mawar raya No 23', 'wanita', '1992-08-17'),
(3, 'Juliana Y', 'Jl Anggrek  No 25', 'wanita', '1990-10-10'),
(5, 'risma', 'Jl Angsana Raya no 3', 'wanita', '1976-11-19'),
(6, 'Yuliana', 'Jl Jati Raya No 301', 'wanita', '1991-06-19'),
(7, 'budiono', 'Jl Kramat Raya no 201A', 'pria', '1966-05-19'),
(8, 'Achmad Sutomo', 'Jl Merdeka Raya Blok A No 4', 'pria', '1992-05-20'),
(9, 'kiki fitri', 'Jl Nusantara Raya Blok C No 23', 'wanita', '1989-12-19'),
(10, 'Sutrisno', 'Jl Lontar Raya No 30', 'pria', '1982-11-01'),
(11, 'riris dewi', 'Jl Menteng atas no 109', 'wanita', '1991-10-20'),
(12, 'Agung', 'Jl Fatimah no 34', 'pria', '1982-08-29'),
(13, 'juni akbar', 'Jl Ragunan Raya No 34', 'pria', '1991-10-09'),
(14, 'wahid', 'Jl Dunia Baru blok B No 34', 'pria', '1988-04-03'),
(15, 'juni akbar', 'Jl Ragunan Raya No 34', 'pria', '1991-10-09'),
(16, 'wahid', 'Jl Dunia Baru blok B No 34', 'pria', '1988-04-03'),
(17, 'Dewi', 'Jl Kelapa tiga No 34', 'wanita', '1978-10-19'),
(18, 'dessy', 'Jl Kramat Raya No 36', 'wanita', '1987-10-10'),
(19, 'fitri angsana', 'Jl Durian No 23', 'wanita', '1993-09-09'),
(20, 'giant', 'Jl jeruk no 3', 'pria', '1991-01-01'),
(21, 'dodi', 'Jl menteng bawah', 'pria', '1988-12-12'),
(22, 'rudianto', 'Jl akses ui no 302', 'pria', '1975-04-09'),
(23, 'windri', 'Jl Jembatan Tiga No 45', 'wanita', '1980-06-06'),
(24, 'trisna', 'Jl Nawar No 45', 'pria', '1981-03-09'),
(25, 'sinta', 'Jl Putri Indah no 8', 'wanita', '1994-10-12'),
(26, 'cintia', 'Jl Angsana No 34', 'wanita', '1992-02-02'),
(27, 'wahid hidayat', 'Jl Sawah Baru blok A No 45', 'pria', '1989-09-08'),
(28, 'kinjay', 'Jl Kelapa dua No 21', 'pria', '1972-10-11'),
(29, 'mia hidayat', 'Jl Sawah Besar No 51', 'wanita', '1983-02-05'),
(30, 'hindun', 'Jl Gugus Baru No 89', 'wanita', '1986-10-11'),
(31, 'juniarto', 'Jl Ragunan Raya No 34', 'pria', '1991-10-09'),
(32, 'warnada', 'Jl Dunia Baru blok B No 34', 'pria', '1988-04-03'),
(33, 'dwi', 'Jl Kelapa tiga No 34', 'wanita', '1978-10-19'),
(34, 'desi', 'Jl Kramat Raya No 36', 'wanita', '1987-10-10'),
(35, 'dian', 'Jl Durian No 23', 'wanita', '1993-09-09'),
(36, 'pohan', 'Jl jeruk no 3', 'pria', '1991-01-01'),
(37, 'budiono', 'Jl menteng bawah', 'pria', '1988-12-12'),
(38, 'titis', 'Jl akses ui no 302', 'pria', '1975-04-09'),
(39, 'fifin', 'Jl Jembatan Tiga No 45', 'wanita', '1980-06-06'),
(40, 'lucky', 'Jl Nawar No 45', 'pria', '1981-03-09'),
(41, 'nina', 'Jl Putri Indah no 8', 'wanita', '1994-10-12'),
(42, 'nia', 'Jl Angsana No 34', 'wanita', '1992-02-02'),
(43, 'tiono', 'Jl Sawah Baru blok A No 45', 'pria', '1989-09-08'),
(44, 'rudi', 'Jl Kelapa dua No 21', 'pria', '1972-10-11'),
(45, 'yuli', 'Jl Sawah Besar No 51', 'wanita', '1983-02-05'),
(46, 'yeni', 'Jl Gugus Baru No 89', 'wanita', '1986-10-11');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
