-- Database dump: document_share (designed from original bookstore sample)
-- Charset: utf8mb4, Engine: InnoDB
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,            -- lưu hash (bcrypt/argon2...)
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample admins
INSERT INTO `admins` (`username`,`password`,`full_name`,`email`,`phone`) VALUES
('admin','21232f297a57a5a743894a0e4a801fc3','Trần Văn Hùng','admin@example.com','0900000001'),
('moderator','e10adc3949ba59abbe56e057f20f883e','Lên Văn An','mod@example.com','0900000002');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,            -- lưu hash
  `name` VARCHAR(150) NOT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('user','uploader') NOT NULL DEFAULT 'user',
  `status` TINYINT NOT NULL DEFAULT 1,         -- 1: active, 0: locked
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample users
INSERT INTO `users` (`email`,`password`,`name`,`address`,`phone`,`role`) VALUES
('ngminh@example.com','e10adc3949ba59abbe56e057f20f883e','Nguyễn Minh Triết','Quận 1','099999999','user'),
('hung.stu@gmail.com','e10adc3949ba59abbe56e057f20f883e','Đại Ca - Trần văn Hùng','Quận 3','090090999','uploader');

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample categories
INSERT INTO `categories` (`code`,`name`,`description`) VALUES
('gk','Giáo khoa','Tài liệu giáo khoa các lớp'),
('it','Tin học','Lập trình, cơ sở dữ liệu, mạng'),
('nn','Ngoại ngữ','Tiếng Anh, Tiếng Nhật, Từ điển'),
('kt','Kinh tế','Kinh tế, Marketing'),
('th','Thủ thuật','Thủ thuật máy tính và văn phòng');

-- ----------------------------
-- Table structure for documents
-- ----------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,            -- path trong thư mục uploads/
  `file_type` VARCHAR(20) NOT NULL,            -- pdf|docx|pptx...
  `filesize` INT UNSIGNED NOT NULL DEFAULT 0,  -- bytes
  `category_id` SMALLINT UNSIGNED NOT NULL,
  `uploader_id` INT UNSIGNED NOT NULL,
  `downloads` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_uploader` (`uploader_id`),
  CONSTRAINT `fk_documents_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_documents_user` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample documents
INSERT INTO `documents` (`title`,`slug`,`description`,`filename`,`file_type`,`filesize`,`category_id`,`uploader_id`,`status`,`is_featured`) VALUES
('Từ Điển mẫu câu tiếng Nhật','tu-dien-mau-cau-tieng-nhat','Tập hợp các mẫu câu tiếng Nhật đầy đủ','uploads/td01.pdf','pdf',452000,3,2,'approved',1),
('Lập trình web bằng PHP 5.3','lap-trinh-web-php-5-3','Hướng dẫn lập trình web và MySQL','uploads/th02.pdf','pdf',760000,2,2,'approved',0),
('Giáo trình SQL Server 2008','giao-trinh-sqlserver-2008','Giáo trình SQL Server (tập 1)','uploads/th08.pdf','pdf',810000,2,2,'approved',0);

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_doc` (`document_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_comments_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample comments
INSERT INTO `comments` (`document_id`,`user_id`,`content`) VALUES
(1,1,'Tài liệu rất hữu ích, cảm ơn!'),
(2,1,'Có file bài tập kèm theo không?');

-- ----------------------------
-- Table structure for ratings
-- ----------------------------
DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `stars` TINYINT UNSIGNED NOT NULL CHECK (stars BETWEEN 1 AND 5),
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rating_doc` (`document_id`),
  CONSTRAINT `fk_ratings_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample ratings
INSERT INTO `ratings` (`document_id`,`user_id`,`stars`) VALUES
(1,1,5),
(2,1,4);

-- ----------------------------
-- Table structure for news (site announcements)
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `img` VARCHAR(255) DEFAULT NULL,
  `short_content` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `is_hot` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample news
INSERT INTO `news` (`title`,`img`,`short_content`,`content`,`is_hot`) VALUES
('Bảo trì hệ thống','/assets/img/maintain.png','Hệ thống sẽ bảo trì vào CN','Hệ thống chia sẻ tài liệu sẽ tạm dừng ...',1);

-- ----------------------------
-- Table structure for uploads_log (optional)
-- ----------------------------
DROP TABLE IF EXISTS `uploads_log`;
CREATE TABLE `uploads_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` INT UNSIGNED NOT NULL,
  `action` ENUM('uploaded','approved','rejected','deleted') NOT NULL,
  `admin_id` INT UNSIGNED DEFAULT NULL,
  `note` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_uploadslog_doc` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_uploadslog_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- end
SET FOREIGN_KEY_CHECKS = 1;
