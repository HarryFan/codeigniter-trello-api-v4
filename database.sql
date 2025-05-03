-- Trello Project 資料表結構

CREATE TABLE `boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `position` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 通知設定表，用於儲存使用者的通知偏好設定
CREATE TABLE `notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `browser_enabled` tinyint(1) DEFAULT 1 COMMENT '是否啟用瀏覽器通知',
  `email_enabled` tinyint(1) DEFAULT 1 COMMENT '是否啟用電子郵件通知',
  `email_lead_time` int(11) DEFAULT 60 COMMENT '電子郵件提前通知時間（分鐘）',
  `browser_lead_time` int(11) DEFAULT 30 COMMENT '瀏覽器提前通知時間（分鐘）',
  `last_email_sent` datetime DEFAULT NULL COMMENT '上次發送電子郵件的時間',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
