<?php
/**
 * 仪表板
 * 阔文展览后台管理系统 - 最终版本
 */

$pageTitle = '仪表板';
$currentPage = 'dashboard';

require_once 'includes/header.php';

// 获取统计数据
$stats = getDashboardStats();

// 获取最新操作记录
$recentOperations = getRecentOperations(8);

// 获取最新留言
$recentMessages = fetchAll(
    "SELECT * FROM messages ORDER BY created_at DESC LIMIT 5"
);

// 获取最新新闻
$recentNews = fetchAll(
    "SELECT n.*, nc.name as category_name 
     FROM news n 
     LEFT JOIN news_categories nc ON n.category_id = nc.id 
     ORDER BY n.created_at DESC 
     LIMIT 5"
);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-speedometer2 me-2"></i>
                欢迎回来，<?= htmlspecialchars($_SESSION['admin_name']) ?>！
            </h2>
            <div class="text-muted">
                <i class="bi bi-clock me-1"></i>
                <?= date('Y年m月d日 H:i') ?>
            </div>
        </div>
    </div>
</div>

<!-- 统计卡片 -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['cases']['total'] ?></h4>
                        <p class="card-text mb-0">案例项目</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-images"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="opacity-75">
                        <i class="bi bi-arrow-up me-1"></i>
                        本月新增 <?= $stats['cases']['recent'] ?> 个
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/cases/index.php" class="text-white text-decoration-none">
                    查看详情 <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['news']['total'] ?></h4>
                        <p class="card-text mb-0">新闻文章</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-newspaper"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="opacity-75">
                        <i class="bi bi-arrow-up me-1"></i>
                        本月发布 <?= $stats['news']['recent'] ?> 篇
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/news/index.php" class="text-white text-decoration-none">
                    查看详情 <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['messages']['total'] ?></h4>
                        <p class="card-text mb-0">客户留言</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="opacity-75">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        待处理 <?= $stats['messages']['pending'] ?> 条
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/messages/index.php" class="text-white text-decoration-none">
                    查看详情 <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['files']['total'] ?></h4>
                        <p class="card-text mb-0">管理文件</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-folder"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="opacity-75">
                        <i class="bi bi-image me-1"></i>
                        图片 <?= $stats['files']['images'] ?> 张
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/files/index.php" class="text-white text-decoration-none">
                    查看详情 <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 快捷操作 -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    快捷操作
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="modules/cases/edit.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-lg me-2"></i>
                            添加案例
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="modules/news/edit.php" class="btn btn-outline-success w-100">
                            <i class="bi bi-plus-lg me-2"></i>
                            发布新闻
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="modules/services/edit.php" class="btn btn-outline-info w-100">
                            <i class="bi bi-plus-lg me-2"></i>
                            添加服务
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="modules/files/index.php" class="btn btn-outline-warning w-100">
                            <i class="bi bi-upload me-2"></i>
                            上传文件
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 内容概览 -->
<div class="row g-4">
    <!-- 最新留言 -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat-dots me-2"></i>
                    最新留言
                </h5>
                <a href="modules/messages/index.php" class="btn btn-sm btn-outline-primary">查看全部</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentMessages)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat-dots display-4 opacity-25"></i>
                        <p class="mt-2">暂无留言</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMessages as $message): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($message['name']) ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <?= htmlspecialchars(truncateText($message['message'], 50)) ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= timeAgo($message['created_at']) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $message['status'] === 'pending' ? 'warning' : 'success' ?>">
                                        <?= $message['status'] === 'pending' ? '待处理' : '已处理' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 最新新闻 -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-newspaper me-2"></i>
                    最新新闻
                </h5>
                <a href="modules/news/index.php" class="btn btn-sm btn-outline-primary">查看全部</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentNews)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-newspaper display-4 opacity-25"></i>
                        <p class="mt-2">暂无新闻</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentNews as $news): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($news['title']) ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <?= htmlspecialchars($news['category_name'] ?? '未分类') ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= timeAgo($news['created_at']) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $news['status'] === 'published' ? 'success' : 'secondary' ?>">
                                        <?= $news['status'] === 'published' ? '已发布' : '草稿' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 最近操作日志 -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-activity me-2"></i>
                    最近操作
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentOperations)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-activity display-4 opacity-25"></i>
                        <p class="mt-2">暂无操作记录</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="15%">操作者</th>
                                    <th width="15%">操作类型</th>
                                    <th width="15%">模块</th>
                                    <th width="35%">描述</th>
                                    <th width="20%">时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOperations as $log): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-person-circle me-1"></i>
                                            <?= htmlspecialchars($log['username']) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= htmlspecialchars($log['action']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($log['module']) ?></td>
                                        <td class="text-muted">
                                            <?= htmlspecialchars(truncateText($log['description'], 60)) ?>
                                        </td>
                                        <td class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= timeAgo($log['created_at']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 系统信息 -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    系统信息
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="30%"><strong>PHP版本</strong></td>
                                <td><?= phpversion() ?></td>
                            </tr>
                            <tr>
                                <td><strong>数据库</strong></td>
                                <td>MySQL <?= fetchOne("SELECT VERSION() as version")['version'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>服务器时间</strong></td>
                                <td><?= date('Y-m-d H:i:s') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="30%"><strong>登录时间</strong></td>
                                <td><?= formatDateTime($_SESSION['admin_login_time'] ?? date('Y-m-d H:i:s')) ?></td>
                            </tr>
                            <tr>
                                <td><strong>用户角色</strong></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($_SESSION['admin_role']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>登录IP</strong></td>
                                <td><?= htmlspecialchars($_SERVER['REMOTE_ADDR']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
