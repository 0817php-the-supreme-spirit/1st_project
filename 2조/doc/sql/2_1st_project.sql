-- --------------------------------------------------------
-- 호스트:                          192.168.0.85
-- 서버 버전:                        10.11.5-MariaDB - mariadb.org binary distribution
-- 서버 OS:                        Win64
-- HeidiSQL 버전:                  12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- 1st_project 데이터베이스 구조 내보내기
CREATE DATABASE IF NOT EXISTS `1st_project` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `1st_project`;

-- 테이블 1st_project.category_table 구조 내보내기
CREATE TABLE IF NOT EXISTS `category_table` (
  `category_id` char(1) NOT NULL,
  `category_name` varchar(20) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 테이블 데이터 1st_project.category_table:~2 rows (대략적) 내보내기
DELETE FROM `category_table`;
INSERT INTO `category_table` (`category_id`, `category_name`) VALUES
	('0', 'life'),
	('1', 'activity'),
	('2', 'stupid');

-- 테이블 1st_project.todolist_table 구조 내보내기
CREATE TABLE IF NOT EXISTS `todolist_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `memo` varchar(100) DEFAULT NULL,
  `amount_used` int(11) NOT NULL,
  `create_date` date NOT NULL DEFAULT current_timestamp(),
  `modify_date` date NOT NULL DEFAULT current_timestamp(),
  `delete_date` date DEFAULT NULL,
  `category_id` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 테이블 데이터 1st_project.todolist_table:~22 rows (대략적) 내보내기
DELETE FROM `todolist_table`;
INSERT INTO `todolist_table` (`id`, `title`, `memo`, `amount_used`, `create_date`, `modify_date`, `delete_date`, `category_id`) VALUES
	(25, '옷삼', NULL, 30000, '2023-09-09', '2023-09-09', NULL, '0'),
	(26, '밥삼', NULL, 6000, '2023-09-09', '2023-09-09', NULL, '0'),
	(27, '걍 돈 버림', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(28, '햄버거 먹음', NULL, 8000, '2023-09-09', '2023-09-09', NULL, '0'),
	(29, '곱도리탕 부심', NULL, 12000, '2023-09-09', '2023-09-09', NULL, '0'),
	(30, '돈 많아서 걍 씀', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(31, '거지라서 사탕 사먹음', NULL, 300, '2023-09-09', '2023-09-09', NULL, '1'),
	(32, '어쩔티비', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(33, '옷삼', NULL, 30000, '2023-09-09', '2023-09-09', NULL, '0'),
	(34, '밥삼', NULL, 6000, '2023-09-09', '2023-09-09', NULL, '0'),
	(35, '걍 돈 버림', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(36, '햄버거 먹음', NULL, 8000, '2023-09-09', '2023-09-09', NULL, '0'),
	(37, '곱도리탕 부심', NULL, 12000, '2023-09-09', '2023-09-09', NULL, '0'),
	(38, '돈 많아서 걍 씀', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(39, '거지라서 사탕 사먹음', NULL, 300, '2023-09-09', '2023-09-09', NULL, '1'),
	(41, '옷삼', NULL, 30000, '2023-09-09', '2023-09-09', NULL, '0'),
	(42, '밥삼', NULL, 6000, '2023-09-09', '2023-09-09', '2023-10-17', '0'),
	(43, '걍 돈 버림', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(44, '햄버거 먹음', NULL, 8000, '2023-09-09', '2023-09-09', NULL, '0'),
	(45, '곱도리탕 부심', NULL, 12000, '2023-09-09', '2023-09-09', NULL, '0'),
	(46, '돈 많아서 걍 씀', NULL, 10000, '2023-09-09', '2023-09-09', NULL, '2'),
	(48, '어쩔티비', NULL, 10000, '2023-09-09', '2023-09-09', '2023-10-12', '2');

-- 테이블 1st_project.user_table 구조 내보내기
CREATE TABLE IF NOT EXISTS `user_table` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `monthly_salary` int(11) NOT NULL,
  `daily_salary` int(11) NOT NULL,
  `Input_date` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 테이블 데이터 1st_project.user_table:~4 rows (대략적) 내보내기
DELETE FROM `user_table`;
INSERT INTO `user_table` (`user_id`, `monthly_salary`, `daily_salary`, `Input_date`) VALUES
	(16, 2000000, 66000, '2023-09-09'),
	(17, 3000000, 100000, '2023-08-01'),
	(18, 4000000, 133000, '2023-07-01'),
	(19, 4000000, 133000, '2023-06-01');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
