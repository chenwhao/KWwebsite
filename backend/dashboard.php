<?php
/**
 * 仪表板
 * 阔文展览后台管理系统
 */

$pageTitle = '仪表板 - 阔文展览后台管理';
$currentPage = 'dashboard';

require_once __DIR__ . '/includes/header.php';

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

// 获取系统信息
$systemInfo = [
    'php_version' => PHP_VERSION,
    'mysql_version' => fetchOne("SELECT VERSION() as version")['version'] ?? 'Unknown',
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'upload_max_size' => ini_get('upload_max_filesize'),
    'memory_limit' => ini_get('memory_limit'),
    'disk_free_space' => function_exists('disk_free_space') ? disk_free_space('.') : 0
];
?>

<div class="row">
    <!-- 欢迎信息 -->
    <div class="col-12 mb-4">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-1">欢迎回来，<?php echo htmlspecialchars($currentUser['username']); ?>！</h4>
                        <p class="mb-0">今天是 <?php echo date('Y年m月d日 H:i'); ?>，祝您工作愉快。</p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-circle" style="font-size: 3rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 统计卡片 -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="stats-number"><?php echo number_format($stats['cases']); ?></h2>
                        <p class="stats-label mb-0">展览案例</p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-briefcase stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0">
                <a href="modules/cases/index.php" class="text-white text-decoration-none">
                    <small>查看详情 <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="stats-number"><?php echo number_format($stats['news']); ?></h2>
                        <p class="stats-label mb-0">新闻文章</p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-newspaper stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0">
                <a href="modules/news/index.php" class="text-white text-decoration-none">
                    <small>查看详情 <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="stats-number"><?php echo number_format($stats['messages']); ?></h2>
                        <p class="stats-label mb-0">待处理留言</p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-chat-dots stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0">
                <a href="modules/messages/index.php" class="text-white text-decoration-none">
                    <small>查看详情 <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card danger">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="stats-number"><?php echo number_format($stats['files']); ?></h2>
                        <p class="stats-label mb-0">媒体文件</p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-folder stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0">
                <a href="modules/files/index.php" class="text-white text-decoration-none">
                    <small>查看详情 <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 快捷操作 -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>快捷操作
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="modules/cases/edit.php" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>添加案例
                    </a>
                    <a href="modules/news/edit.php" class="btn btn-outline-success">
                        <i class="bi bi-pencil me-2"></i>发布新闻
                    </a>
                    <a href="modules/services/edit.php" class="btn btn-outline-info">
                        <i class="bi bi-gear me-2"></i>添加服务
                    </a>
                    <a href="modules/files/index.php" class="btn btn-outline-warning">
                        <i class="bi bi-upload me-2"></i>上传文件
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 最新留言 -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-chat-dots me-2"></i>最新留言
                </h5>
                <a href="modules/messages/index.php" class="btn btn-sm btn-outline-primary">查看全部</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMessages)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                        暂无新留言
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMessages as $message): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($message['name']); ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo truncateText($message['message'], 60); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo timeAgo($message['created_at']); ?>
                                        </small>
                                    </div>
                                    <div class="col-auto">
                                        <span class="status-badge status-<?php echo $message['status']; ?>">
                                            <?php 
                                            $statusText = [
                                                'pending' => '待处理',
                                                'processing' => '处理中',
                                                'processed' => '已处理',
                                                'closed' => '已关闭'
                                            ];
                                            echo $statusText[$message['status']];
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 最新新闻 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-newspaper me-2"></i>最新新闻
                </h5>
                <a href="modules/news/index.php" class="btn btn-sm btn-outline-primary">查看全部</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentNews)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-newspaper display-4 d-block mb-2"></i>
                        暂无新闻文章
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentNews as $news): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="modules/news/edit.php?id=<?php echo $news['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($news['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo truncateText($news['summary'] ?? '', 50); ?>
                                        </p>
                                        <small class="text-muted">
                                            <span class="badge bg-light text-dark me-2"><?php echo $news['category_name']; ?></span>
                                            <?php echo timeAgo($news['created_at']); ?>
                                        </small>
                                    </div>
                                    <span class="status-badge status-<?php echo $news['status']; ?>">
                                        <?php 
                                        $statusText = [
                                            'draft' => '草稿',
                                            'published' => '已发布',
                                            'archived' => '已归档'
                                        ];
                                        echo $statusText[$news['status']];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 操作日志 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>最近操作
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentOperations)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-activity display-4 d-block mb-2"></i>
                        暂无操作记录
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentOperations as $log): ?>
                            <div class="list-group-item operation-log <?php echo $log['action']; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($log['username'] ?? '系统'); ?>
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo htmlspecialchars($log['description'] ?? $log['action']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo timeAgo($log['created_at']); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-secondary"><?php echo $log['action']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 系统信息 -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>系统信息
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-2">服务器环境</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>PHP版本:</strong> <?php echo $systemInfo['php_version']; ?></li>
                                <li><strong>MySQL版本:</strong> <?php echo $systemInfo['mysql_version']; ?></li>
                                <li><strong>Web服务器:</strong> <?php echo $systemInfo['server_software']; ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-2">系统配置</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>上传限制:</strong> <?php echo $systemInfo['upload_max_size']; ?></li>
                                <li><strong>内存限制:</strong> <?php echo $systemInfo['memory_limit']; ?></li>
                                <li><strong>时区:</strong> <?php echo date_default_timezone_get(); ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-2">运行状态</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>磁盘可用:</strong> 
                                    <?php 
                                    if ($systemInfo['disk_free_space']) {
                                        echo number_format($systemInfo['disk_free_space'] / 1024 / 1024 / 1024, 2) . ' GB';
                                    } else {
                                        echo '未知';
                                    }
                                    ?>
                                </li>
                                <li><strong>运行时间:</strong> <?php echo timeAgo($_SESSION['login_time']); ?></li>
                                <li><strong>当前用户:</strong> <?php echo $currentUser['username']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
