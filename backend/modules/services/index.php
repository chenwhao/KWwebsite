<?php
/**
 * 服务项目管理
 * 阔文展览后台管理系统
 */

$pageTitle = '服务项目管理 - 阔文展览后台管理';
$currentPage = 'services';
$breadcrumbs = [
    ['name' => '服务项目管理']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取搜索参数
$search = Security::cleanInput($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// 构建查询条件
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($status)) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

// 获取分页数据
$result = paginate('services', $conditions, $params, $page, $perPage, 'sort_order ASC, id DESC');
$services = $result['data'];
$pagination = $result['pagination'];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>服务项目管理
                        </h5>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/modules/services/edit.php" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>添加服务
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 搜索表单 -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="搜索服务名称或描述">
                    </div>
                    <div class="col-md-3">
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
                        <?php if (!empty($search) || !empty($status)): ?>
                            <a href="/admin/modules/services/index.php" class="btn btn-outline-secondary">
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
                                data-url="/admin/modules/services/process.php">
                            <i class="bi bi-trash me-1"></i>批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 服务列表 -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" data-action="select-all">
                                </th>
                                <th width="60">排序</th>
                                <th width="80">图标</th>
                                <th>服务名称</th>
                                <th>描述</th>
                                <th width="100">状态</th>
                                <th width="120">创建时间</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody class="sortable" data-sort-url="/admin/modules/services/process.php">
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        暂无服务项目
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($services as $service): ?>
                                    <tr data-id="<?php echo $service['id']; ?>">
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $service['id']; ?>">
                                        </td>
                                        <td>
                                            <span class="sort-handle text-muted" style="cursor: move;">
                                                <i class="bi bi-grip-vertical"></i>
                                            </span>
                                            <span class="small text-muted"><?php echo $service['sort_order']; ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($service['icon'])): ?>
                                                <i class="bi bi-<?php echo htmlspecialchars($service['icon']); ?> text-primary"></i>
                                            <?php else: ?>
                                                <i class="bi bi-gear text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                            <?php if (!empty($service['subtitle'])): ?>
                                                <div class="small text-muted"><?php echo htmlspecialchars($service['subtitle']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <?php echo truncateText($service['description'] ?? '', 100); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $service['status']; ?>">
                                                <?php echo $service['status'] === 'active' ? '启用' : '禁用'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDateTime($service['created_at'], 'Y-m-d'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/admin/modules/services/edit.php?id=<?php echo $service['id']; ?>" 
                                                   class="btn btn-outline-primary" data-bs-toggle="tooltip" title="编辑">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-info" 
                                                        data-action="toggle-status" 
                                                        data-url="/admin/modules/services/process.php?id=<?php echo $service['id']; ?>&action=toggle_status"
                                                        data-bs-toggle="tooltip" title="切换状态">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        data-action="delete" 
                                                        data-url="/admin/modules/services/process.php?id=<?php echo $service['id']; ?>&action=delete"
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
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
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
