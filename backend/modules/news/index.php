<?php
/**
 * 新闻管理
 * 阔文展览后台管理系统
 */

$pageTitle = '新闻管理 - 阔文展览后台管理';
$currentPage = 'news';
$breadcrumbs = [
    ['name' => '新闻管理']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取搜索参数
$search = Security::cleanInput($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// 构建查询条件
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(title LIKE ? OR content LIKE ? OR author LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($category)) {
    $conditions[] = "category = ?";
    $params[] = $category;
}

if (!empty($status)) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

// 获取分页数据
$result = paginate('news', $conditions, $params, $page, $perPage, 'created_at DESC');
$news = $result['data'];
$pagination = $result['pagination'];

// 获取统计数据
$stats = [
    'total' => fetchOne("SELECT COUNT(*) as count FROM news")['count'],
    'published' => fetchOne("SELECT COUNT(*) as count FROM news WHERE status = 'published'")['count'],
    'draft' => fetchOne("SELECT COUNT(*) as count FROM news WHERE status = 'draft'")['count'],
    'today' => fetchOne("SELECT COUNT(*) as count FROM news WHERE DATE(created_at) = CURDATE()")['count']
];

// 分类列表
$categories = [
    'company' => '公司新闻',
    'industry' => '行业资讯',
    'case' => '案例分享',
    'event' => '活动展会'
];
?>

<div class="row">
    <!-- 统计卡片 -->
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-newspaper display-4 mb-3"></i>
                <h3><?php echo number_format($stats['total']); ?></h3>
                <p class="mb-0">总新闻数</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4 mb-3"></i>
                <h3><?php echo number_format($stats['published']); ?></h3>
                <p class="mb-0">已发布</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-clock display-4 mb-3"></i>
                <h3><?php echo number_format($stats['draft']); ?></h3>
                <p class="mb-0">草稿</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <i class="bi bi-calendar-day display-4 mb-3"></i>
                <h3><?php echo number_format($stats['today']); ?></h3>
                <p class="mb-0">今日新增</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="bi bi-newspaper me-2"></i>新闻管理
                        </h5>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/modules/news/edit.php" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>添加新闻
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 搜索表单 -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="搜索标题、内容或作者">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category">
                            <option value="">全部分类</option>
                            <?php foreach ($categories as $key => $name): ?>
                                <option value="<?php echo $key; ?>" <?php echo $category === $key ? 'selected' : ''; ?>>
                                    <?php echo $name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">全部状态</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>已发布</option>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>草稿</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i>搜索
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <?php if (!empty($search) || !empty($category) || !empty($status)): ?>
                            <a href="/admin/modules/news/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>重置
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <!-- 批量操作 -->
                <div class="batch-actions d-none mb-3">
                    <div class="d-flex align-items-center">
                        <span class="me-3">已选择 <span class="selected-count">0</span> 项</span>
                        <button type="button" class="btn btn-sm btn-success me-2" 
                                data-action="batch-publish" 
                                data-url="/admin/modules/news/process.php">
                            <i class="bi bi-check-circle me-1"></i>批量发布
                        </button>
                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                data-action="batch-draft" 
                                data-url="/admin/modules/news/process.php">
                            <i class="bi bi-clock me-1"></i>批量草稿
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                data-action="batch-delete" 
                                data-url="/admin/modules/news/process.php">
                            <i class="bi bi-trash me-1"></i>批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 新闻列表 -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" data-action="select-all">
                                </th>
                                <th width="100">缩略图</th>
                                <th>新闻信息</th>
                                <th width="100">分类</th>
                                <th width="80">状态</th>
                                <th width="60">浏览</th>
                                <th width="120">发布时间</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($news)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        暂无新闻
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($news as $item): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $item['id']; ?>">
                                        </td>
                                        <td>
                                            <?php if (!empty($item['thumbnail'])): ?>
                                                <img src="/<?php echo htmlspecialchars($item['thumbnail']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                     class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 60px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                <?php if (!empty($item['subtitle'])): ?>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($item['subtitle']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <?php if (!empty($item['author'])): ?>
                                                    <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($item['author']); ?>
                                                <?php endif; ?>
                                                <?php if (!empty($item['source'])): ?>
                                                    <span class="ms-2">
                                                        <i class="bi bi-link me-1"></i><?php echo htmlspecialchars($item['source']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php echo truncateText($item['excerpt'] ?? strip_tags($item['content']), 80); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($item['category'])): ?>
                                                <span class="badge bg-info"><?php echo $categories[$item['category']] ?? $item['category']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $item['status']; ?>">
                                                <?php echo $item['status'] === 'published' ? '已发布' : '草稿'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="small text-muted"><?php echo number_format($item['views']); ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDateTime($item['published_at'] ?? $item['created_at'], 'Y-m-d'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/admin/modules/news/edit.php?id=<?php echo $item['id']; ?>" 
                                                   class="btn btn-outline-primary" data-bs-toggle="tooltip" title="编辑">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-info" 
                                                        data-action="toggle-status" 
                                                        data-url="/admin/modules/news/process.php?id=<?php echo $item['id']; ?>&action=toggle_status"
                                                        data-bs-toggle="tooltip" title="切换状态">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        data-action="delete" 
                                                        data-url="/admin/modules/news/process.php?id=<?php echo $item['id']; ?>&action=delete"
                                                        data-bs-toggle="tooltip" title="删除">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- 分页 -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="分页导航">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="text-center text-muted">
                        共 <?php echo $pagination['total']; ?> 条记录，第 <?php echo $pagination['current_page']; ?> / <?php echo $pagination['total_pages']; ?> 页
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
