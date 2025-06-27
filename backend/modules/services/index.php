<?php
/**
 * 服务项目管理
 * 阔文展览后台管理系统 - 最终版本
 */

$pageTitle = '服务项目管理';
$currentPage = 'services';
$currentModule = 'services';

require_once '../../includes/header.php';

// 处理批量操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        Security::validateCSRF($_POST['csrf_token']);
        
        if ($_POST['action'] === 'batch_delete' && isset($_POST['ids'])) {
            $ids = array_map('intval', $_POST['ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            updateRecord("DELETE FROM services WHERE id IN ($placeholders)", $ids);
            Security::logOperation('batch_delete', 'services', null, '批量删除服务项目');
            
            successResponse('批量删除成功');
        }
        
        if ($_POST['action'] === 'batch_activate' && isset($_POST['ids'])) {
            $ids = array_map('intval', $_POST['ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            updateRecord("UPDATE services SET status = 'active' WHERE id IN ($placeholders)", $ids);
            Security::logOperation('batch_activate', 'services', null, '批量激活服务项目');
            
            successResponse('批量激活成功');
        }
        
        if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            updateRecord("DELETE FROM services WHERE id = ?", [$id]);
            Security::logOperation('delete', 'services', $id, '删除服务项目');
            
            successResponse('删除成功');
        }
        
    } catch (Exception $e) {
        errorResponse($e->getMessage());
    }
}

// 获取筛选参数
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;

// 构建查询条件
$conditions = [];
$params = [];

if ($category_id > 0) {
    $conditions[] = "s.category_id = ?";
    $params[] = $category_id;
}

if ($status !== '' && in_array($status, ['active', 'inactive'])) {
    $conditions[] = "s.status = ?";
    $params[] = $status;
}

if ($search !== '') {
    $conditions[] = "(s.title LIKE ? OR s.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// 获取服务列表
$sql = "SELECT s.*, sc.name as category_name 
        FROM services s 
        LEFT JOIN service_categories sc ON s.category_id = sc.id 
        $where_clause 
        ORDER BY s.sort_order ASC, s.created_at DESC 
        LIMIT $limit OFFSET " . (($page - 1) * $limit);

$services = fetchAll($sql, $params);

// 获取总数
$count_sql = "SELECT COUNT(*) as total FROM services s $where_clause";
$total = fetchOne($count_sql, $params)['total'];
$total_pages = ceil($total / $limit);

// 获取分类列表
$categories = fetchAll("SELECT * FROM service_categories WHERE status = 'active' ORDER BY sort_order");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        服务项目管理
                    </h5>
                    <div>
                        <a href="edit.php" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>
                            添加服务
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 筛选工具栏 -->
                <div class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <select class="form-select" id="categoryFilter" onchange="applyFilters()">
                            <option value="">全部分类</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select class="form-select" id="statusFilter" onchange="applyFilters()">
                            <option value="">全部状态</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>启用</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>禁用</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="搜索服务名称或描述..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            重置
                        </button>
                    </div>
                </div>
                
                <!-- 批量操作工具栏 -->
                <div class="batch-actions mb-3" style="display: none;">
                    <div class="d-flex align-items-center">
                        <span class="me-3">
                            已选择 <span class="batch-count">0</span> 项
                        </span>
                        <button class="btn btn-sm btn-success me-2 batch-action" data-action="activate">
                            <i class="bi bi-check-circle me-1"></i>
                            批量启用
                        </button>
                        <button class="btn btn-sm btn-danger batch-action" data-action="delete">
                            <i class="bi bi-trash me-1"></i>
                            批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 服务列表 -->
                <?php if (empty($services)): ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-muted opacity-25">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h5 class="text-muted mt-3">暂无服务项目</h5>
                        <p class="text-muted">点击上方"添加服务"按钮创建第一个服务项目</p>
                        <a href="edit.php" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>
                            添加服务
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" class="form-check-input check-all">
                                    </th>
                                    <th width="15%">服务名称</th>
                                    <th width="12%">分类</th>
                                    <th width="25%">描述</th>
                                    <th width="10%">价格区间</th>
                                    <th width="8%">排序</th>
                                    <th width="8%">状态</th>
                                    <th width="12%">更新时间</th>
                                    <th width="5%">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input" value="<?= $service['id'] ?>" 
                                                   onchange="Admin.updateBatchActions()">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($service['image']): ?>
                                                    <img src="../../<?= htmlspecialchars($service['image']) ?>" 
                                                         class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($service['title']) ?></h6>
                                                    <?php if ($service['subtitle']): ?>
                                                        <small class="text-muted">
                                                            <?= htmlspecialchars(truncateText($service['subtitle'], 30)) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($service['category_name']): ?>
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($service['category_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">未分类</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?= htmlspecialchars(truncateText($service['description'], 60)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $service['price_range'] ? htmlspecialchars($service['price_range']) : '-' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $service['sort_order'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $service['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= $service['status'] === 'active' ? '启用' : '禁用' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= timeAgo($service['updated_at']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="edit.php?id=<?= $service['id'] ?>">
                                                            <i class="bi bi-pencil me-2"></i>编辑
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="view.php?id=<?= $service['id'] ?>">
                                                            <i class="bi bi-eye me-2"></i>查看
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" 
                                                           data-action="delete" data-url="?id=<?= $service['id'] ?>"
                                                           data-message="确定要删除服务"<?= htmlspecialchars($service['title']) ?>"吗？">
                                                            <i class="bi bi-trash me-2"></i>删除
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- 分页 -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="分页导航" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&category=<?= $category_id ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>">
                                            上一页
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&category=<?= $category_id ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&category=<?= $category_id ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>">
                                            下一页
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    
    const params = new URLSearchParams();
    if (category) params.set('category', category);
    if (status) params.set('status', status);
    if (search) params.set('search', search);
    
    window.location.href = '?' + params.toString();
}

function resetFilters() {
    window.location.href = '?';
}

// 回车搜索
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
