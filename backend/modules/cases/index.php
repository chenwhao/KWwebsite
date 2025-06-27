<?php
/**
 * 案例作品管理
 * 阔文展览后台管理系统
 */

$pageTitle = '案例作品管理 - 阔文展览后台管理';
$currentPage = 'cases';
$breadcrumbs = [
    ['name' => '案例作品管理']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取搜索参数
$search = Security::cleanInput($_GET['search'] ?? '');
$category_id = intval($_GET['category_id'] ?? 0);
$status = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// 构建查询条件
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(c.title LIKE ? OR c.description LIKE ? OR c.client LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($category_id > 0) {
    $conditions[] = "c.category_id = ?";
    $params[] = $category_id;
}

if (!empty($status)) {
    $conditions[] = "c.status = ?";
    $params[] = $status;
}

// 构建查询SQL
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$offset = ($page - 1) * $perPage;

// 获取案例数据
$sql = "SELECT c.*, cc.name as category_name 
        FROM cases c 
        LEFT JOIN case_categories cc ON c.category_id = cc.id 
        $whereClause 
        ORDER BY c.sort_order ASC, c.id DESC 
        LIMIT $perPage OFFSET $offset";

$cases = fetchAll($sql, $params);

// 获取总记录数
$countSql = "SELECT COUNT(*) as total FROM cases c $whereClause";
$totalResult = fetchOne($countSql, $params);
$total = $totalResult['total'];

$pagination = [
    'current_page' => $page,
    'per_page' => $perPage,
    'total' => $total,
    'total_pages' => ceil($total / $perPage),
    'has_prev' => $page > 1,
    'has_next' => $page < ceil($total / $perPage)
];

// 获取分类列表
$categories = fetchAll("SELECT * FROM case_categories ORDER BY sort_order ASC, id ASC");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="bi bi-briefcase me-2"></i>案例作品管理
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="/admin/modules/cases/categories.php" class="btn btn-outline-primary">
                                <i class="bi bi-tags me-2"></i>分类管理
                            </a>
                            <a href="/admin/modules/cases/edit.php" class="btn btn-primary">
                                <i class="bi bi-plus me-2"></i>添加案例
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 搜索表单 -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="搜索案例名称、描述或客户">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category_id">
                            <option value="">全部分类</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_id === intval($category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">全部状态</option>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>启用</option>
                            <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>禁用</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i>搜索
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <?php if (!empty($search) || $category_id > 0 || !empty($status)): ?>
                            <a href="/admin/modules/cases/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>重置
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <!-- 批量操作 -->
                <div class="batch-actions d-none mb-3">
                    <div class="d-flex align-items-center">
                        <span class="me-3">已选择 <span class="selected-count">0</span> 项</span>
                        <button type="button" class="btn btn-sm btn-danger" 
                                data-action="batch-delete" 
                                data-url="/admin/modules/cases/process.php">
                            <i class="bi bi-trash me-1"></i>批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 案例列表 -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" data-action="select-all">
                                </th>
                                <th width="60">排序</th>
                                <th width="100">主图</th>
                                <th>案例信息</th>
                                <th width="100">分类</th>
                                <th width="80">状态</th>
                                <th width="60">浏览</th>
                                <th width="120">创建时间</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody class="sortable" data-sort-url="/admin/modules/cases/process.php">
                            <?php if (empty($cases)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        暂无案例作品
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cases as $case): ?>
                                    <tr data-id="<?php echo $case['id']; ?>">
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $case['id']; ?>">
                                        </td>
                                        <td>
                                            <span class="sort-handle text-muted" style="cursor: move;">
                                                <i class="bi bi-grip-vertical"></i>
                                            </span>
                                            <span class="small text-muted"><?php echo $case['sort_order']; ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($case['featured_image'])): ?>
                                                <img src="/<?php echo htmlspecialchars($case['featured_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($case['title']); ?>"
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
                                                <strong><?php echo htmlspecialchars($case['title']); ?></strong>
                                                <?php if (!empty($case['subtitle'])): ?>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($case['subtitle']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <?php if (!empty($case['client'])): ?>
                                                    <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($case['client']); ?>
                                                <?php endif; ?>
                                                <?php if (!empty($case['project_date'])): ?>
                                                    <span class="ms-2">
                                                        <i class="bi bi-calendar me-1"></i><?php echo $case['project_date']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php echo truncateText($case['description'] ?? '', 80); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($case['category_name'])): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($case['category_name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $case['status']; ?>">
                                                <?php echo $case['status'] === 'active' ? '启用' : '禁用'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="small text-muted"><?php echo number_format($case['views']); ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDateTime($case['created_at'], 'Y-m-d'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/admin/modules/cases/edit.php?id=<?php echo $case['id']; ?>" 
                                                   class="btn btn-outline-primary" data-bs-toggle="tooltip" title="编辑">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-info" 
                                                        data-action="toggle-status" 
                                                        data-url="/admin/modules/cases/process.php?id=<?php echo $case['id']; ?>&action=toggle_status"
                                                        data-bs-toggle="tooltip" title="切换状态">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        data-action="delete" 
                                                        data-url="/admin/modules/cases/process.php?id=<?php echo $case['id']; ?>&action=delete"
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
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>">
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

<?php 
$extraJS = [
    'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js'
];
require_once __DIR__ . '/../../includes/footer.php'; 
?>
