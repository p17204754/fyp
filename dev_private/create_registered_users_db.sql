SET NAMES utf8mb4;
SET TIME_ZONE='+00:00';
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;



DROP DATABASE IF EXISTS `dev_db`;

CREATE DATABASE IF NOT EXISTS dev_db COLLATE utf8_unicode_ci;

GRANT SELECT, INSERT, UPDATE,DELETE ON dev_db.* TO 'dev_user'@localhost IDENTIFIED BY 'dev_pass';

USE m2m_db;

DROP TABLE IF EXISTS `user_info`;

CREATE TABLE `user_info` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `password` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastLoggedIn`timestamp NOT NULL,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO user_info(user_name, email, role, password, lastLoggedIn)
VALUES('user', 'user@gmail.com', 'admin', '$2y$12$pgWV.Fsa.NS6eqydME60duvw7YTsmekFgEXYeyxrvuklzV2U6NUKi', current_timestamp);



FLUSH PRIVILEGES;
