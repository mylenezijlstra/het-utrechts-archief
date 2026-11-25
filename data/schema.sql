-- MySQL / MariaDB Schema for Het Utrechts Archief - Ontmoet de Oudegracht
-- Database: utrechtsarchief

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `route_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `video_start_sec` int(11) NOT NULL,
  `video_end_sec` int(11) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `is_bridge` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `route_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_point_id` int(11) NOT NULL,
  `lang_code` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_trans_point` (`route_point_id`),
  CONSTRAINT `fk_trans_point` FOREIGN KEY (`route_point_id`) REFERENCES `route_points` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_point_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `archive_url` varchar(255) DEFAULT '',
  `caption_nl` varchar(255) DEFAULT '',
  `caption_en` varchar(255) DEFAULT '',
  `alt_text_nl` text DEFAULT '',
  `alt_text_en` text DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_media_point` (`route_point_id`),
  CONSTRAINT `fk_media_point` FOREIGN KEY (`route_point_id`) REFERENCES `route_points` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL UNIQUE,
  `value_nl` text NOT NULL,
  `value_en` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (admin / admin123)
INSERT INTO `users` (`id`, `username`, `password_hash`, `role`) VALUES
(1, 'admin', '$2y$10$eE04vYk9Vqg0lA0jI462c.hP2xV0jF59a3iVqY9e4q34vPq9c5p8y', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;
