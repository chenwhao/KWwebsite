-- 阔文展览后台管理系统数据库结构
-- 创建时间: 2025-06-26
-- 注意：运行此脚本前请确保已经创建了数据库

-- 设置字符集
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 用户表
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `name` varchar(50) DEFAULT NULL COMMENT '姓名',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `role` enum('super_admin','admin','editor') DEFAULT 'editor' COMMENT '角色',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT '状态',
  `last_login_at` datetime DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(45) DEFAULT NULL COMMENT '最后登录IP',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- 公司信息表
-- ----------------------------
DROP TABLE IF EXISTS `company_info`;
CREATE TABLE `company_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '公司名称',
  `short_name` varchar(50) DEFAULT NULL COMMENT '简称',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Logo',
  `description` text COMMENT '公司描述',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `phone` varchar(50) DEFAULT NULL COMMENT '电话',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `website` varchar(255) DEFAULT NULL COMMENT '网站',
  `established_year` year DEFAULT NULL COMMENT '成立年份',
  `business_scope` text COMMENT '业务范围',
  `slogan` varchar(200) DEFAULT NULL COMMENT '企业口号',
  `vision` text COMMENT '企业愿景',
  `mission` text COMMENT '企业使命',
  `culture` text COMMENT '企业文化',
  `history` text COMMENT '发展历程',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信号',
  `business_hours` varchar(100) DEFAULT NULL COMMENT '营业时间',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公司信息表';

-- ----------------------------
-- 服务项目表
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '服务标题',
  `subtitle` varchar(200) DEFAULT NULL COMMENT '服务副标题',
  `description` text COMMENT '服务描述',
  `content` longtext COMMENT '详细内容',
  `icon` varchar(50) DEFAULT NULL COMMENT '图标',
  `featured_image` varchar(255) DEFAULT NULL COMMENT '特色图片',
  `features` json DEFAULT NULL COMMENT '服务特色',
  `process` json DEFAULT NULL COMMENT '服务流程',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT '状态',
  `seo_title` varchar(200) DEFAULT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(500) DEFAULT NULL COMMENT 'SEO关键词',
  `seo_description` text COMMENT 'SEO描述',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务项目表';

-- ----------------------------
-- 案例分类表
-- ----------------------------
DROP TABLE IF EXISTS `case_categories`;
CREATE TABLE `case_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `slug` varchar(50) DEFAULT NULL COMMENT '分类别名',
  `description` text COMMENT '分类描述',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT '状态',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='案例分类表';

-- ----------------------------
-- 案例作品表
-- ----------------------------
DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '案例标题',
  `subtitle` varchar(200) DEFAULT NULL COMMENT '案例副标题',
  `description` text COMMENT '案例描述',
  `content` longtext COMMENT '详细内容',
  `client` varchar(100) DEFAULT NULL COMMENT '客户名称',
  `project_date` date DEFAULT NULL COMMENT '项目日期',
  `location` varchar(100) DEFAULT NULL COMMENT '项目地点',
  `featured_image` varchar(255) DEFAULT NULL COMMENT '主要图片',
  `project_features` json DEFAULT NULL COMMENT '项目特色',
  `technologies` json DEFAULT NULL COMMENT '使用技术',
  `area` varchar(50) DEFAULT NULL COMMENT '展示面积',
  `duration` varchar(50) DEFAULT NULL COMMENT '项目周期',
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT '状态',
  `views` int(11) DEFAULT '0' COMMENT '浏览次数',
  `seo_title` varchar(200) DEFAULT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(500) DEFAULT NULL COMMENT 'SEO关键词',
  `seo_description` text COMMENT 'SEO描述',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_project_date` (`project_date`),
  CONSTRAINT `fk_cases_category` FOREIGN KEY (`category_id`) REFERENCES `case_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='案例作品表';

-- ----------------------------
-- 案例图片表
-- ----------------------------
DROP TABLE IF EXISTS `case_images`;
CREATE TABLE `case_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL COMMENT '案例ID',
  `image_path` varchar(255) NOT NULL COMMENT '图片路径',
  `title` varchar(100) DEFAULT NULL COMMENT '图片标题',
  `description` text COMMENT '图片描述',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_case_id` (`case_id`),
  KEY `idx_sort_order` (`sort_order`),
  CONSTRAINT `fk_case_images_case` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='案例图片表';

-- ----------------------------
-- 新闻文章表
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT '新闻标题',
  `subtitle` varchar(300) DEFAULT NULL COMMENT '副标题',
  `excerpt` text COMMENT '摘要',
  `content` longtext COMMENT '正文内容',
  `category` varchar(50) DEFAULT NULL COMMENT '分类',
  `author` varchar(50) DEFAULT NULL COMMENT '作者',
  `source` varchar(100) DEFAULT NULL COMMENT '来源',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `tags` varchar(500) DEFAULT NULL COMMENT '标签',
  `status` enum('draft','published') DEFAULT 'draft' COMMENT '状态',
  `published_at` datetime DEFAULT NULL COMMENT '发布时间',
  `views` int(11) DEFAULT '0' COMMENT '浏览次数',
  `seo_title` varchar(200) DEFAULT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(500) DEFAULT NULL COMMENT 'SEO关键词',
  `seo_description` text COMMENT 'SEO描述',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='新闻文章表';

-- ----------------------------
-- 文件管理表
-- ----------------------------
DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL COMMENT '文件名',
  `original_name` varchar(255) NOT NULL COMMENT '原始文件名',
  `file_path` varchar(500) NOT NULL COMMENT '文件路径',
  `file_size` bigint(20) NOT NULL COMMENT '文件大小',
  `mime_type` varchar(100) NOT NULL COMMENT 'MIME类型',
  `file_type` enum('image','document','video','audio','other') DEFAULT 'other' COMMENT '文件类型',
  `category` varchar(50) DEFAULT 'general' COMMENT '文件分类',
  `description` text COMMENT '文件描述',
  `alt_text` varchar(255) DEFAULT NULL COMMENT '替代文本',
  `width` int(11) DEFAULT NULL COMMENT '图片宽度',
  `height` int(11) DEFAULT NULL COMMENT '图片高度',
  `uploaded_by` int(11) DEFAULT NULL COMMENT '上传者ID',
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '上传时间',
  PRIMARY KEY (`id`),
  KEY `idx_file_type` (`file_type`),
  KEY `idx_category` (`category`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_uploaded_at` (`uploaded_at`),
  CONSTRAINT `fk_files_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件管理表';

-- ----------------------------
-- 留言表
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '电话',
  `company` varchar(100) DEFAULT NULL COMMENT '公司',
  `subject` varchar(200) DEFAULT NULL COMMENT '主题',
  `message` text NOT NULL COMMENT '留言内容',
  `status` enum('pending','replied','archived') DEFAULT 'pending' COMMENT '状态',
  `reply_content` text COMMENT '回复内容',
  `replied_at` datetime DEFAULT NULL COMMENT '回复时间',
  `replied_by` int(11) DEFAULT NULL COMMENT '回复人ID',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text COMMENT '用户代理',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_replied_by` (`replied_by`),
  CONSTRAINT `fk_messages_user` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='留言表';

-- ----------------------------
-- 操作日志表
-- ----------------------------
DROP TABLE IF EXISTS `operation_logs`;
CREATE TABLE `operation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '操作用户ID',
  `action` varchar(50) NOT NULL COMMENT '操作类型',
  `table_name` varchar(50) DEFAULT NULL COMMENT '表名',
  `record_id` int(11) DEFAULT NULL COMMENT '记录ID',
  `description` varchar(255) DEFAULT NULL COMMENT '操作描述',
  `old_data` json DEFAULT NULL COMMENT '原数据',
  `new_data` json DEFAULT NULL COMMENT '新数据',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text COMMENT '用户代理',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_table_record` (`table_name`,`record_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_operation_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作日志表';

-- ----------------------------
-- 插入默认管理员账户
-- ----------------------------
INSERT INTO `users` (`username`, `email`, `password`, `name`, `role`, `status`) VALUES
('admin', 'admin@kuowen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '系统管理员', 'super_admin', 'active');

-- ----------------------------
-- 插入默认公司信息
-- ----------------------------
INSERT INTO `company_info` (`name`, `short_name`, `description`, `phone`, `email`, `established_year`, `slogan`, `business_hours`) VALUES
('上海阔文展览展示服务有限公司', '阔文展览', '专业的展台设计搭建服务提供商，致力于为客户提供一站式展览展示解决方案。我们拥有经验丰富的设计团队和专业的施工队伍，为每一位客户量身定制最适合的展示方案。', '021-XXXXXXXX', 'info@kuowen.com', 2020, '专业展台设计搭建 创造展会价值', '周一至周五 9:00-18:00');

-- ----------------------------
-- 插入默认服务项目
-- ----------------------------
INSERT INTO `services` (`title`, `subtitle`, `description`, `content`, `icon`, `features`, `process`, `sort_order`, `status`) VALUES
('展台设计', '创意展台设计服务', '为您量身定制独特的展台设计方案，彰显企业形象', '<p>我们拥有专业的设计团队，为客户提供创意独特、实用性强的展台设计服务。从概念设计到施工图纸，全程为您服务。</p><p>设计团队具有丰富的行业经验，能够根据不同行业特点和品牌定位，创造出富有创意和吸引力的展台设计方案。</p>', 'palette', '["创意设计", "专业团队", "一对一服务", "效果图制作", "3D建模", "材料选择"]', '["需求沟通", "概念设计", "方案确认", "施工图制作", "材料清单", "效果验收"]', 1, 'active'),
('展台搭建', '专业展台搭建服务', '专业的搭建团队，确保展台按时高质量完成', '<p>拥有丰富经验的搭建团队，严格按照设计方案进行施工，确保展台安全稳固，效果完美。</p><p>施工团队具备专业技能和丰富经验，能够处理各种复杂的搭建要求，确保展台的安全性和美观性。</p>', 'tools', '["专业施工", "质量保证", "按时交付", "安全规范", "现场管理", "售后服务"]', '["材料采购", "现场准备", "结构搭建", "装饰安装", "质量检验", "完工交付"]', 2, 'active'),
('展会策划', '全方位展会策划服务', '提供展会策划、组织、执行等一站式服务', '<p>从展会前期策划到现场执行，为客户提供全方位的展会服务，确保展会成功举办。</p><p>策划团队具有丰富的展会组织经验，能够协调各方资源，确保展会活动的顺利进行。</p>', 'calendar', '["策划方案", "活动组织", "现场执行", "效果评估", "媒体宣传", "嘉宾邀请"]', '["前期调研", "方案策划", "资源协调", "活动执行", "现场管理", "总结反馈"]', 3, 'active'),
('设备租赁', '展览设备租赁服务', '提供各类展览设备租赁，满足不同需求', '<p>拥有丰富的展览设备资源，包括音响、灯光、显示设备等，为客户提供便捷的设备租赁服务。</p><p>设备齐全且维护良好，能够满足各种展览展示的技术需求。</p>', 'briefcase', '["设备齐全", "质量可靠", "价格优惠", "服务周到", "技术支持", "快速响应"]', '["需求确认", "设备预订", "运输配送", "现场安装", "技术支持", "撤展回收"]', 4, 'active');

-- ----------------------------
-- 插入默认案例分类
-- ----------------------------
INSERT INTO `case_categories` (`name`, `slug`, `description`, `sort_order`, `status`) VALUES
('展台设计', 'booth-design', '各类展台设计案例展示', 1, 'active'),
('活动策划', 'event-planning', '活动策划执行案例展示', 2, 'active'),
('展览搭建', 'exhibition-construction', '展览搭建施工案例展示', 3, 'active'),
('品牌展示', 'brand-display', '品牌形象展示案例展示', 4, 'active');

-- ----------------------------
-- 插入示例案例作品
-- ----------------------------
INSERT INTO `cases` (`title`, `subtitle`, `description`, `content`, `client`, `project_date`, `location`, `project_features`, `technologies`, `area`, `duration`, `category_id`, `sort_order`, `status`) VALUES
('2024年中国国际工业博览会展台', '科技企业展台设计搭建', '为某科技企业设计搭建的现代化展台，充分体现了企业的科技创新实力', '<p>该项目是我们为某知名科技企业在2024年中国国际工业博览会上设计搭建的展台。展台采用现代简约风格，以蓝白色调为主，营造出科技感十足的氛围。</p><p>展台分为产品展示区、洽谈区和演示区三个功能区域，合理的空间布局确保了参观者的良好体验。</p>', '某科技有限公司', '2024-09-15', '上海国家会展中心', '["现代设计", "科技感强", "互动体验", "多媒体展示"]', '["LED显示屏", "智能照明系统", "音响设备", "互动触控设备"]', '100平方米', '7天', 1, 1, 'active'),
('汽车展览会品牌展示', '豪华汽车品牌展台', '为国际知名汽车品牌打造的豪华展示空间', '<p>此项目为国际知名汽车品牌在上海车展期间设计的展台，展现了品牌的豪华定位和创新理念。</p><p>展台采用流线型设计，营造出动感十足的视觉效果，完美契合汽车品牌的特质。</p>', '某汽车品牌', '2024-04-20', '上海汽车展览中心', '["豪华设计", "品牌突出", "产品聚焦", "体验优先"]', '["高端材料", "特殊照明", "展车平台", "VR体验设备"]', '200平方米', '10天', 4, 2, 'active');

-- ----------------------------
-- 插入示例新闻
-- ----------------------------
INSERT INTO `news` (`title`, `subtitle`, `excerpt`, `content`, `category`, `author`, `status`, `published_at`) VALUES
('阔文展览成功参与2024年进博会展台搭建', '专业团队高质量完成多个展台项目', '在刚刚结束的第七届中国国际进口博览会上，阔文展览凭借专业的设计和搭建能力，成功完成了多个重要展台项目。', '<p>第七届中国国际进口博览会于2024年11月在上海国家会展中心成功举办，阔文展览作为专业的展台设计搭建服务商，承接了多个重要客户的展台项目。</p><p>在为期6天的展会期间，我们的专业团队展现了出色的执行能力和服务水平，得到了客户的一致好评。</p><p>此次进博会的成功参与，进一步提升了阔文展览在行业内的知名度和影响力，为公司未来的发展奠定了坚实基础。</p>', 'company', '阔文展览', 'published', '2024-11-15 10:00:00'),
('展览行业数字化转型趋势分析', '科技赋能展览展示新发展', '随着数字技术的快速发展，展览行业正在经历深刻的变革，数字化展示成为新的发展趋势。', '<p>近年来，随着虚拟现实、增强现实、人工智能等技术的快速发展，展览行业正在经历一场深刻的数字化变革。</p><p>传统的展示方式正在与现代科技深度融合，为参展商和观众带来全新的体验。数字化技术不仅提升了展示效果，还大大提高了展览的互动性和参与度。</p><p>阔文展览积极拥抱这一变革趋势，不断学习和应用新技术，为客户提供更加创新和有效的展示解决方案。</p>', 'industry', '阔文展览', 'published', '2024-12-01 14:30:00');

SET FOREIGN_KEY_CHECKS = 1;