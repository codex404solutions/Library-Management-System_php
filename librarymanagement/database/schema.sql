-- CodeX404 Solutions Schema Dump
-- Generated: 2026-11-04 08:55:10
-- Database: lms

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `add_books`;
CREATE TABLE `add_books` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `books_name` varchar(50) NOT NULL,
  `books_image` varchar(500) NOT NULL,
  `books_author_name` varchar(50) NOT NULL,
  `books_publication_name` varchar(50) NOT NULL,
  `books_purchase_date` varchar(50) NOT NULL,
  `books_price` varchar(50) NOT NULL,
  `books_qty` varchar(50) NOT NULL,
  `available_qty` varchar(50) NOT NULL,
  `librarian_username` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `cname` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `cphone` varchar(15) NOT NULL,
  `cemail` varchar(200) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `issue_books`;
CREATE TABLE `issue_books` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `student_enrollment` varchar(50) NOT NULL,
  `student_name` varchar(50) NOT NULL,
  `student_sem` varchar(50) NOT NULL,
  `student_contact` varchar(50) NOT NULL,
  `student_email` varchar(50) NOT NULL,
  `books_name` varchar(50) NOT NULL,
  `books_issue_date` varchar(50) NOT NULL,
  `books_return_date` varchar(50) NOT NULL,
  `student_username` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `librarian_registration`;
CREATE TABLE `librarian_registration` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `susername` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `read1` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration` (`migration`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `noticedate` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `page_title` text NOT NULL,
  `description` text NOT NULL,
  `met_tags` text NOT NULL,
  `meta_description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `webtitle` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contactnum` varchar(15) NOT NULL,
  `whyus1` varchar(255) NOT NULL,
  `whyus2` varchar(255) NOT NULL,
  `whyus3` varchar(255) NOT NULL,
  `whyus4` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `googleplus` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `student_registration`;
CREATE TABLE `student_registration` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `sem` varchar(50) NOT NULL,
  `enrollmentno` varchar(50) NOT NULL,
  `status` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

SET FOREIGN_KEY_CHECKS=1;
