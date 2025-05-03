-- Trello Project 資料表結構

-- 用戶表
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 看板表
CREATE TABLE `boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `boards_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 清單表
CREATE TABLE `lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  CONSTRAINT `lists_board_id_fk` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 卡片表
CREATE TABLE `cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `deadline` datetime DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`),
  CONSTRAINT `cards_list_id_fk` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 通知設定表
CREATE TABLE `notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `browser_enabled` tinyint(1) DEFAULT 1 COMMENT '是否啟用瀏覽器通知',
  `email_enabled` tinyint(1) DEFAULT 1 COMMENT '是否啟用電子郵件通知',
  `email_lead_time` int(11) DEFAULT 60 COMMENT '電子郵件提前通知時間（分鐘）',
  `browser_lead_time` int(11) DEFAULT 30 COMMENT '瀏覽器提前通知時間（分鐘）',
  `last_email_sent` datetime DEFAULT NULL COMMENT '上次發送電子郵件的時間',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notification_settings_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 預設用戶資料
INSERT INTO `users` (`name`, `email`, `password`) VALUES 
('范綱栓', 'harry750110@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: 12345678

-- 預設看板
INSERT INTO `boards` (`title`, `user_id`) VALUES 
('我的任務清單', 1);

-- 預設清單
INSERT INTO `lists` (`board_id`, `title`, `position`) VALUES 
(1, '待處理', 0),
(1, '進行中', 1),
(1, '已完成', 2);

-- 預設卡片
INSERT INTO `cards` (`list_id`, `title`, `description`, `deadline`, `position`) VALUES 
(1, '研究 Vue 3 新功能', '了解 Composition API 和 Script Setup 的使用方式', '2025-05-10 23:59:59', 0),
(1, '學習 CodeIgniter 4', '熟悉 RESTful API 開發和資料庫操作', '2025-05-15 23:59:59', 1),
(2, '開發任務清單功能', '實作拖曳排序和狀態更新', '2025-05-20 23:59:59', 0),
(3, '完成用戶認證', '實作登入和權限控制', '2025-05-01 23:59:59', 0);

-- 預設通知設定
INSERT INTO `notification_settings` (`user_id`, `browser_enabled`, `email_enabled`, `email_lead_time`, `browser_lead_time`) VALUES 
(1, 1, 1, 60, 30);
