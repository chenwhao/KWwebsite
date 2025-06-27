<?php
/**
 * 案例分类管理
 * 阔文展览后台管理系统
 */

$pageTitle = '案例分类管理 - 阔文展览后台管理';
$currentPage = 'case_categories';
$breadcrumbs = [
    ['name' => '案例作品管理', 'url' => '/admin/modules/cases/index.php'],
    ['name' => '分类管理']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取分类列表
$categories = fetchAll("SELECT *, (SELECT COUNT(*) FROM cases WHERE category_id = case_categories.id) as case_count FROM case_categories ORDER BY sort_order ASC, id ASC");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="bi bi-tags me-2"></i>案例分类管理
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="/admin/modules/cases/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>返回案例列表
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="bi bi-plus me-2"></i>添加分类
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 分类列表 -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">排序</th>
                                <th>分类名称</th>
                                <th>分类描述</th>
                                <th width="80">案例数量</th>
                                <th width="80">状态</th>
                                <th width="120">创建时间</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody class="sortable" data-sort-url="/admin/modules/cases/category_process.php">
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        暂无分类
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr data-id="<?php echo $category['id']; ?>">
                                        <td>
                                            <span class="sort-handle text-muted" style="cursor: move;">
                                                <i class="bi bi-grip-vertical"></i>
                                            </span>
                                            <span class="small text-muted"><?php echo $category['sort_order']; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                            <?php if (!empty($category['slug'])): ?>
                                                <div class="small text-muted">别名: <?php echo htmlspecialchars($category['slug']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($category['description'])): ?>
                                                <?php echo truncateText($category['description'], 80); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $category['case_count']; ?></span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $category['status']; ?>">
                                                <?php echo $category['status'] === 'active' ? '启用' : '禁用'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDateTime($category['created_at'], 'Y-m-d'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-action="edit-category" 
                                                        data-id="<?php echo $category['id']; ?>"
                                                        data-bs-toggle="tooltip" title="编辑">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info" 
                                                        data-action="toggle-status" 
                                                        data-url="/admin/modules/cases/category_process.php?id=<?php echo $category['id']; ?>&action=toggle_status"
                                                        data-bs-toggle="tooltip" title="切换状态">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <?php if ($category['case_count'] == 0): ?>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            data-action="delete" 
                                                            data-url="/admin/modules/cases/category_process.php?id=<?php echo $category['id']; ?>&action=delete"
                                                            data-bs-toggle="tooltip" title="删除">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-outline-secondary" disabled
                                                            data-bs-toggle="tooltip" title="有案例的分类不能删除">
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 分类编辑模态框 -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加分类</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">分类名称 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">分类别名</label>
                        <input type="text" class="form-control" id="slug" name="slug" 
                               placeholder="用于URL，如: booth-design">
                        <small class="text-muted">留空将自动生成，只能包含字母、数字和连字符</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">分类描述</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">排序</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                                <small class="text-muted">数字越小排序越靠前</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">状态</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active">启用</option>
                                    <option value="inactive">禁用</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">保存分类</button>
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

<script>
$(document).ready(function() {
    // 编辑分类
    $('[data-action="edit-category"]').on('click', function() {
        const categoryId = $(this).data('id');
        
        // 获取分类数据
        $.ajax({
            url: '/admin/modules/cases/category_process.php',
            method: 'POST',
            data: {
                action: 'get',
                id: categoryId,
                '<?php echo CSRF_TOKEN_NAME; ?>': '<?php echo Security::generateCSRFToken(); ?>'
            },
            success: function(response) {
                if (response.success && response.data) {
                    const category = response.data;
                    
                    // 填充表单
                    $('#categoryForm input[name="id"]').val(category.id);
                    $('#categoryForm input[name="action"]').val('update');
                    $('#name').val(category.name);
                    $('#slug').val(category.slug);
                    $('#description').val(category.description);
                    $('#sort_order').val(category.sort_order);
                    $('#status').val(category.status);
                    
                    // 更新模态框标题
                    $('#categoryModal .modal-title').text('编辑分类');
                    
                    // 显示模态框
                    $('#categoryModal').modal('show');
                } else {
                    Admin.showAlert(response.message || '获取分类信息失败', 'error');
                }
            },
            error: function() {
                Admin.showAlert('获取分类信息失败', 'error');
            }
        });
    });
    
    // 重置模态框
    $('#categoryModal').on('hidden.bs.modal', function() {
        $('#categoryForm')[0].reset();
        $('#categoryForm input[name="id"]').val('');
        $('#categoryForm input[name="action"]').val('create');
        $('#categoryModal .modal-title').text('添加分类');
    });
    
    // 自动生成别名
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\u4e00-\u9fa5]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    });
    
    // 保存分类
    $('#saveCategoryBtn').on('click', function() {
        const formData = $('#categoryForm').serialize();
        
        Admin.showLoading();
        
        $.ajax({
            url: '/admin/modules/cases/category_process.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(response.message, 'success');
                    $('#categoryModal').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('操作失败，请重试', 'error');
            }
        });
    });
    
    // 初始化拖拽排序
    if (typeof Sortable !== 'undefined') {
        const sortableEl = document.querySelector('.sortable');
        if (sortableEl) {
            Sortable.create(sortableEl, {
                handle: '.sort-handle',
                animation: 150,
                onEnd: function(evt) {
                    const sortData = [];
                    $('.sortable tr[data-id]').each(function(index) {
                        const id = $(this).data('id');
                        if (id) {
                            sortData.push({
                                id: id,
                                sort_order: index
                            });
                        }
                    });
                    
                    if (sortData.length > 0) {
                        // 更新排序
                        $.ajax({
                            url: '/admin/modules/cases/category_process.php',
                            method: 'POST',
                            data: {
                                action: 'update_sort',
                                sort_data: sortData,
                                '<?php echo CSRF_TOKEN_NAME; ?>': '<?php echo Security::generateCSRFToken(); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Admin.showAlert('排序更新成功', 'success', 2000);
                                } else {
                                    Admin.showAlert('排序更新失败', 'error');
                                }
                            },
                            error: function() {
                                Admin.showAlert('排序更新失败', 'error');
                            }
                        });
                    }
                }
            });
        }
    }
});
</script>