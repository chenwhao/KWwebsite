<?php
/**
 * 公共头部文件
 * 阔文展览后台管理系统 - 最终版本
 */

// 检查登录状态
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/functions.php';

requireLogin();

// 定义网站根URL，方便管理
// 如果您的后台不在根目录，请修改这里。例如：define('BASE_URL', '/admin');
define('BASE_URL', '');

// 设置默认值
$pageTitle = $pageTitle ?? '阔文展览后台管理系统';
$currentPage = $currentPage ?? '';
$currentModule = $currentModule ?? '';

// 生成面包屑
$breadcrumb = generateBreadcrumb($pageTitle, $currentModule, BASE_URL);

/**
 * 一个辅助函数，用于生成带BASE_URL的链接
 */
function url(string $path): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- 自定义CSS -->
    <link href="<?= url('assets/css/admin.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --sidebar-bg: #34495e;
            --sidebar-text: #ecf0f1;
        }
        
        body { 
            font-family: 'Microsoft YaHei', sans-serif; 
            background: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(52, 152, 219, 0.2);
            color: #ffffff;
        }
        
        .sidebar .nav-link.active {
            background: var(--secondary-color);
            color: #ffffff;
        }
        
        .main-content {
            min-height: 100vh;
            padding: 0;
        }
        
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .content-area {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px 25px;
        }
        
        .btn {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--sidebar-text);
            margin-bottom: 30px;
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- 侧边栏 -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="logo-text">
                    <i class="bi bi-building me-2"></i>
                    阔文展览
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('dashboard.php') ?>">
                        <i class="bi bi-speedometer2 me-2"></i>
                        仪表板
                    </a>
                    
                    <div class="nav-item">
                        <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                            内容管理
                        </h6>
                        
                        <a class="nav-link <?= $currentModule === 'company' ? 'active' : '' ?>" href="<?= url('modules/company/index.php') ?>">
                            <i class="bi bi-building me-2"></i>
                            公司信息
                        </a>
                        
                        <a class="nav-link <?= $currentModule === 'services' ? 'active' : '' ?>" href="<?= url('modules/services/index.php') ?>">
                            <i class="bi bi-gear me-2"></i>
                            服务项目
                        </a>
                        
                        <a class="nav-link <?= $currentModule === 'cases' ? 'active' : '' ?>" href="<?= url('modules/cases/index.php') ?>">
                            <i class="bi bi-images me-2"></i>
                            案例管理
                        </a>
                        
                        <a class="nav-link <?= $currentModule === 'news' ? 'active' : '' ?>" href="<?= url('modules/news/index.php') ?>">
                            <i class="bi bi-newspaper me-2"></i>
                            新闻管理
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                            互动管理
                        </h6>
                        
                        <a class="nav-link <?= $currentModule === 'messages' ? 'active' : '' ?>" href="<?= url('modules/messages/index.php') ?>">
                            <i class="bi bi-chat-dots me-2"></i>
                            留言管理
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase">
                            系统管理
                        </h6>
                        
                        <a class="nav-link <?= $currentModule === 'files' ? 'active' : '' ?>" href="<?= url('modules/files/index.php') ?>">
                            <i class="bi bi-folder me-2"></i>
                            文件管理
                        </a>
                        
                        <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                        <a class="nav-link <?= $currentModule === 'users' ? 'active' : '' ?>" href="<?= url('modules/users/index.php') ?>">
                            <i class="bi bi-people me-2"></i>
                            用户管理
                        </a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
            
            <!-- 主内容区域 -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- 顶部导航栏 -->
                <div class="top-navbar d-flex justify-content-between align-items-center">
                    <div>
                        <!-- 面包屑导航 -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <?php foreach ($breadcrumb as $item): ?>
                                    <li class="breadcrumb-item <?= $item['active'] ? 'active' : '' ?>">
                                        <?php if ($item['active']): ?>
                                            <?= htmlspecialchars($item['name']) ?>
                                        <?php else: ?>
                                            <a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['name']) ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <!-- 快捷操作 -->
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-plus-lg me-1"></i>
                                快捷操作
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= url('modules/cases/edit.php') ?>"><i class="bi bi-images me-2"></i>添加案例</a></li>
                                <li><a class="dropdown-item" href="<?= url('modules/news/edit.php') ?>"><i class="bi bi-newspaper me-2"></i>发布新闻</a></li>
                                <li><a class="dropdown-item" href="<?= url('modules/services/edit.php') ?>"><i class="bi bi-gear me-2"></i>添加服务</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('modules/files/index.php') ?>"><i class="bi bi-folder me-2"></i>文件管理</a></li>
                            </ul>
                        </div>
                        
                        <!-- 通知 -->
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <?php 
                                $pending_messages = fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'pending'")['count'];
                                if ($pending_messages > 0):
                                ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $pending_messages ?>
                                </span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">消息通知</h6></li>
                                <?php if ($pending_messages > 0): ?>
                                <li><a class="dropdown-item" href="<?= url('modules/messages/index.php') ?>">
                                    <i class="bi bi-chat-dots me-2"></i>
                                    <?= $pending_messages ?> 条新留言
                                </a></li>
                                <?php else: ?>
                                <li><span class="dropdown-item-text text-muted">暂无新消息</span></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <!-- 用户菜单 -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['admin_name']) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">
                                    <?= htmlspecialchars($_SESSION['admin_username']) ?>
                                    <small class="text-muted d-block"><?= htmlspecialchars($_SESSION['admin_role']) ?></small>
                                </h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="changePassword()">
                                    <i class="bi bi-key me-2"></i>修改密码
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= url('logout.php') ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i>退出登录
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- 内容区域 -->
                <div class="content-area">
