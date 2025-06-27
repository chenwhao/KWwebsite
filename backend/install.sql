-- 阔文展览后台管理系统数据库结构
-- 最终版本 - 2025年6月27日

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- 数据库编码设置
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- 管理员用户表
-- --------------------------------------------------------

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `name` varchar(100) NOT NULL COMMENT '姓名',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `role` enum('super_admin','admin','editor') NOT NULL DEFAULT 'admin' COMMENT '角色',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '状态',
  `last_login_at` datetime DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(45) DEFAULT NULL COMMENT '最后登录IP',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员用户表';

-- --------------------------------------------------------
-- 公司信息表
-- --------------------------------------------------------

CREATE TABLE `company_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT '公司名称',
  `short_name` varchar(100) DEFAULT NULL COMMENT '公司简称',
  `description` text COMMENT '公司描述',
  `founded_year` year DEFAULT NULL COMMENT '成立年份',
  `business_scope` text COMMENT '经营范围',
  `address` varchar(500) DEFAULT NULL COMMENT '公司地址',
  `phone` varchar(50) DEFAULT NULL COMMENT '联系电话',
  `fax` varchar(50) DEFAULT NULL COMMENT '传真',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `website` varchar(200) DEFAULT NULL COMMENT '官网地址',
  `logo` varchar(500) DEFAULT NULL COMMENT 'Logo图片',
  `banner_images` text COMMENT '轮播图片(JSON)',
  `social_media` text COMMENT '社交媒体(JSON)',
  `business_hours` varchar(200) DEFAULT NULL COMMENT '营业时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公司信息表';

-- --------------------------------------------------------
-- 服务分类表
-- --------------------------------------------------------

CREATE TABLE `service_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '分类名称',
  `description` text COMMENT '分类描述',
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '状态',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务分类表';

-- --------------------------------------------------------
-- 服务项目表
-- --------------------------------------------------------

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `title` varchar(200) NOT NULL COMMENT '服务标题',
  `subtitle` varchar(300) DEFAULT NULL COMMENT '副标题',
  `description` text COMMENT '服务描述',
  `content` longtext COMMENT '详细内容',
  `features` text COMMENT '服务特色(JSON)',
  `price_range` varchar(100) DEFAULT NULL COMMENT '价格区间',
  `duration` varchar(100) DEFAULT NULL COMMENT '服务周期',
  `image` varchar(500) DEFAULT NULL COMMENT '主图',
  `gallery` text COMMENT '图片集(JSON)',
  `tags` varchar(500) DEFAULT NULL COMMENT '标签',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '状态',
  `views` int(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务项目表';

-- --------------------------------------------------------
-- 案例分类表
-- --------------------------------------------------------

CREATE TABLE `case_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '分类名称',
  `description` text COMMENT '分类描述',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '状态',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='案例分类表';

-- --------------------------------------------------------
-- 案例项目表
-- --------------------------------------------------------

CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `title` varchar(200) NOT NULL COMMENT '案例标题',
  `subtitle` varchar(300) DEFAULT NULL COMMENT '副标题',
  `client` varchar(200) DEFAULT NULL COMMENT '客户名称',
  `project_date` date DEFAULT NULL COMMENT '项目日期',
  `project_location` varchar(300) DEFAULT NULL COMMENT '项目地点',
  `project_area` varchar(100) DEFAULT NULL COMMENT '项目面积',
  `project_budget` varchar(100) DEFAULT NULL COMMENT '项目预算',
  `description` text COMMENT '项目描述',
  `content` longtext COMMENT '详细内容',
  `challenge` text COMMENT '项目挑战',
  `solution` text COMMENT '解决方案',
  `result` text COMMENT '项目成果',
  `cover_image` varchar(500) DEFAULT NULL COMMENT '封面图片',
  `gallery` text COMMENT '图片集(JSON)',
  `video_url` varchar(500) DEFAULT NULL COMMENT '视频链接',
  `tags` varchar(500) DEFAULT NULL COMMENT '标签',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '状态',
  `views` int(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  KEY `is_featured` (`is_featured`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='案例项目表';

-- --------------------------------------------------------
-- 新闻分类表
-- --------------------------------------------------------

CREATE TABLE `news_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '分类名称',
  `description` text COMMENT '分类描述',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '状态',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻分类表';

-- --------------------------------------------------------
-- 新闻文章表
-- --------------------------------------------------------

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `title` varchar(300) NOT NULL COMMENT '文章标题',
  `subtitle` varchar(500) DEFAULT NULL COMMENT '副标题',
  `summary` text COMMENT '文章摘要',
  `content` longtext COMMENT '文章内容',
  `author` varchar(100) DEFAULT NULL COMMENT '作者',
  `source` varchar(200) DEFAULT NULL COMMENT '来源',
  `cover_image` varchar(500) DEFAULT NULL COMMENT '封面图片',
  `gallery` text COMMENT '图片集(JSON)',
  `tags` varchar(500) DEFAULT NULL COMMENT '标签',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐',
  `is_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否置顶',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft' COMMENT '状态',
  `published_at` datetime DEFAULT NULL COMMENT '发布时间',
  `views` int(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `likes` int(11) NOT NULL DEFAULT 0 COMMENT '点赞数',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  KEY `is_featured` (`is_featured`),
  KEY `is_top` (`is_top`),
  KEY `published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻文章表';

-- --------------------------------------------------------
-- 客户留言表
-- --------------------------------------------------------

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '姓名',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `company` varchar(200) DEFAULT NULL COMMENT '公司名称',
  `subject` varchar(300) DEFAULT NULL COMMENT '主题',
  `message` text NOT NULL COMMENT '留言内容',
  `type` enum('consultation','cooperation','complaint','suggestion') DEFAULT 'consultation' COMMENT '留言类型',
  `status` enum('pending','replied','archived') NOT NULL DEFAULT 'pending' COMMENT '处理状态',
  `reply` text COMMENT '回复内容',
  `replied_by` int(11) DEFAULT NULL COMMENT '回复人ID',
  `replied_at` datetime DEFAULT NULL COMMENT '回复时间',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text COMMENT '用户代理',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  KEY `replied_by` (`replied_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户留言表';

-- --------------------------------------------------------
-- 文件管理表
-- --------------------------------------------------------

CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_name` varchar(255) NOT NULL COMMENT '原始文件名',
  `filename` varchar(255) NOT NULL COMMENT '存储文件名',
  `file_path` varchar(500) NOT NULL COMMENT '文件路径',
  `file_size` bigint(20) NOT NULL COMMENT '文件大小(字节)',
  `mime_type` varchar(100) NOT NULL COMMENT 'MIME类型',
  `category` enum('image','document','video','audio','other') NOT NULL DEFAULT 'other' COMMENT '文件分类',
  `description` text COMMENT '文件描述',
  `alt_text` varchar(300) DEFAULT NULL COMMENT '替代文本',
  `uploaded_by` int(11) NOT NULL COMMENT '上传人ID',
  `download_count` int(11) NOT NULL DEFAULT 0 COMMENT '下载次数',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开',
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `is_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文件管理表';

-- --------------------------------------------------------
-- 系统设置表
-- --------------------------------------------------------

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL COMMENT '设置键',
  `value` text COMMENT '设置值',
  `description` varchar(500) DEFAULT NULL COMMENT '设置描述',
  `type` enum('string','number','boolean','json') NOT NULL DEFAULT 'string' COMMENT '数据类型',
  `group` varchar(50) DEFAULT 'general' COMMENT '设置分组',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统设置表';

-- --------------------------------------------------------
-- 操作日志表
-- --------------------------------------------------------

CREATE TABLE `operation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '操作用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `action` varchar(50) NOT NULL COMMENT '操作类型',
  `module` varchar(50) NOT NULL COMMENT '操作模块',
  `target_id` int(11) DEFAULT NULL COMMENT '目标ID',
  `description` text COMMENT '操作描述',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text COMMENT '用户代理',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `module` (`module`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';

-- --------------------------------------------------------
-- 登录日志表
-- --------------------------------------------------------

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `ip_address` varchar(45) NOT NULL COMMENT 'IP地址',
  `user_agent` text COMMENT '用户代理',
  `status` enum('success','failed') NOT NULL COMMENT '登录状态',
  `failure_reason` varchar(200) DEFAULT NULL COMMENT '失败原因',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志表';

-- --------------------------------------------------------
-- 插入初始数据
-- --------------------------------------------------------

-- 默认管理员用户 (用户名: admin, 密码: admin123)
INSERT INTO `admin_users` (`username`, `password`, `name`, `email`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '系统管理员', 'admin@kuowenexpo.com', 'super_admin', 'active');

-- 默认公司信息
INSERT INTO `company_info` (`name`, `short_name`, `description`, `founded_year`, `address`, `phone`, `email`, `website`) VALUES
('上海阔文展览展示服务有限公司', '阔文展览', '专业的展台设计搭建服务公司，致力于为客户提供一站式展览展示解决方案。', 2020, '上海市浦东新区', '021-12345678', 'info@kuowenexpo.com', 'https://kuowenexpo.com');

-- 默认服务分类
INSERT INTO `service_categories` (`name`, `description`, `icon`, `sort_order`, `status`) VALUES
('展台设计', '专业的展台设计服务', 'bi-palette', 1, 'active'),
('展台搭建', '专业的展台搭建服务', 'bi-hammer', 2, 'active'),
('活动策划', '专业的活动策划服务', 'bi-calendar-event', 3, 'active'),
('物料制作', '专业的物料制作服务', 'bi-printer', 4, 'active');

-- 默认案例分类
INSERT INTO `case_categories` (`name`, `description`, `sort_order`, `status`) VALUES
('科技展览', '科技类展览案例', 1, 'active'),
('汽车展览', '汽车类展览案例', 2, 'active'),
('食品展览', '食品类展览案例', 3, 'active'),
('医疗展览', '医疗类展览案例', 4, 'active');

-- 默认新闻分类
INSERT INTO `news_categories` (`name`, `description`, `sort_order`, `status`) VALUES
('公司动态', '公司最新动态和资讯', 1, 'active'),
('行业资讯', '展览行业相关资讯', 2, 'active'),
('技术分享', '展台设计和搭建技术分享', 3, 'active'),
('案例分析', '经典案例深度分析', 4, 'active');

-- 默认系统设置
INSERT INTO `settings` (`key`, `value`, `description`, `type`, `group`) VALUES
('site_name', '阔文展览后台管理系统', '网站名称', 'string', 'general'),
('site_description', '专业的展台设计搭建服务', '网站描述', 'string', 'general'),
('contact_email', 'info@kuowenexpo.com', '联系邮箱', 'string', 'contact'),
('contact_phone', '021-12345678', '联系电话', 'string', 'contact'),
('upload_max_size', '5242880', '文件上传最大尺寸(字节)', 'number', 'upload'),
('allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx', '允许上传的文件类型', 'string', 'upload');

-- --------------------------------------------------------
-- 外键约束
-- --------------------------------------------------------

ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `case_categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`replied_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `operation_logs`
  ADD CONSTRAINT `operation_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;