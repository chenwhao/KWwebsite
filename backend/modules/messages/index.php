<?php
/**
 * 客户留言管理
 * 阔文展览后台管理系统 - 最终版本
 */

$pageTitle = '客户留言管理';
$currentPage = 'messages';
$currentModule = 'messages';

require_once '../../includes/header.php';

// 处理操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        Security::validateCSRF($_POST['csrf_token']);
        
        if ($_POST['action'] === 'reply' && isset($_POST['id']) && isset($_POST['reply'])) {
            $id = intval($_POST['id']);
            $reply = Security::sanitize($_POST['reply']);
            
            if (empty($reply)) {
                throw new Exception('请填写回复内容');
            }
            
            updateRecord(
                "UPDATE messages SET reply = ?, replied_by = ?, replied_at = NOW(), status = 'replied' WHERE id = ?",
                [$reply, $_SESSION['admin_user_id'], $id]
            );
            
            Security::logOperation('reply', 'messages', $id, '回复客户留言');
            successResponse('回复成功');
        }
        
        if ($_POST['action'] === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
            $id = intval($_POST['id']);
            $status = $_POST['status'];
            
            if (!in_array($status, ['pending', 'replied', 'archived'])) {
                throw new Exception('无效的状态');
            }
            
            updateRecord("UPDATE messages SET status = ? WHERE id = ?", [$status, $id]);
            
            Security::logOperation('update_status', 'messages', $id, "更新留言状态为: $status");
            successResponse('状态更新成功');
        }
        
        if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            updateRecord("DELETE FROM messages WHERE id = ?", [$id]);
            
            Security::logOperation('delete', 'messages', $id, '删除客户留言');
            successResponse('删除成功');
        }
        
        if ($_POST['action'] === 'batch_delete' && isset($_POST['ids'])) {
            $ids = array_map('intval', $_POST['ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            updateRecord("DELETE FROM messages WHERE id IN ($placeholders)", $ids);
            Security::logOperation('batch_delete', 'messages', null, '批量删除客户留言');
            
            successResponse('批量删除成功');
        }
        
        if ($_POST['action'] === 'batch_archive' && isset($_POST['ids'])) {
            $ids = array_map('intval', $_POST['ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            updateRecord("UPDATE messages SET status = 'archived' WHERE id IN ($placeholders)", $ids);
            Security::logOperation('batch_archive', 'messages', null, '批量归档客户留言');
            
            successResponse('批量归档成功');
        }
        
    } catch (Exception $e) {
        errorResponse($e->getMessage());
    }
}

// 获取筛选参数
$status = isset($_GET['status']) ? $_GET['status'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 15;

// 构建查询条件
$conditions = [];
$params = [];

if ($status !== '' && in_array($status, ['pending', 'replied', 'archived'])) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

if ($type !== '' && in_array($type, ['consultation', 'cooperation', 'complaint', 'suggestion'])) {
    $conditions[] = "type = ?";
    $params[] = $type;
}

if ($search !== '') {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// 获取留言列表
$sql = "SELECT m.*, u.name as replied_by_name 
        FROM messages m 
        LEFT JOIN admin_users u ON m.replied_by = u.id 
        $where_clause 
        ORDER BY m.created_at DESC 
        LIMIT $limit OFFSET " . (($page - 1) * $limit);

$messages = fetchAll($sql, $params);

// 获取总数
$count_sql = "SELECT COUNT(*) as total FROM messages $where_clause";
$total = fetchOne($count_sql, $params)['total'];
$total_pages = ceil($total / $limit);

// 获取统计数据
$stats = [
    'total' => fetchOne("SELECT COUNT(*) as count FROM messages")['count'],
    'pending' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'pending'")['count'],
    'replied' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'replied'")['count'],
    'archived' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'archived'")['count']
];

$type_names = [
    'consultation' => '咨询',
    'cooperation' => '合作',
    'complaint' => '投诉',
    'suggestion' => '建议'
];
?>

<div class="row mb-4">
    <!-- 统计卡片 -->
    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['total'] ?></h4>
                        <p class="card-text mb-0">总留言</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['pending'] ?></h4>
                        <p class="card-text mb-0">待处理</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['replied'] ?></h4>
                        <p class="card-text mb-0">已回复</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title"><?= $stats['archived'] ?></h4>
                        <p class="card-text mb-0">已归档</p>
                    </div>
                    <div class="display-6">
                        <i class="bi bi-archive"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots me-2"></i>
                        客户留言管理
                    </h5>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 筛选工具栏 -->
                <div class="row mb-3">
                    <div class="col-md-2 mb-2">
                        <select class="form-select" id="statusFilter" onchange="applyFilters()">
                            <option value="">全部状态</option>
                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>待处理</option>
                            <option value="replied" <?= $status === 'replied' ? 'selected' : '' ?>>已回复</option>
                            <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>已归档</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select class="form-select" id="typeFilter" onchange="applyFilters()">
                            <option value="">全部类型</option>
                            <option value="consultation" <?= $type === 'consultation' ? 'selected' : '' ?>>咨询</option>
                            <option value="cooperation" <?= $type === 'cooperation' ? 'selected' : '' ?>>合作</option>
                            <option value="complaint" <?= $type === 'complaint' ? 'selected' : '' ?>>投诉</option>
                            <option value="suggestion" <?= $type === 'suggestion' ? 'selected' : '' ?>>建议</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="搜索姓名、邮箱、电话、公司或内容..." value="<?= htmlspecialchars($search) ?>">
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
                        <button class="btn btn-sm btn-info me-2 batch-action" data-action="archive">
                            <i class="bi bi-archive me-1"></i>
                            批量归档
                        </button>
                        <button class="btn btn-sm btn-danger batch-action" data-action="delete">
                            <i class="bi bi-trash me-1"></i>
                            批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 留言列表 -->
                <?php if (empty($messages)): ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-muted opacity-25">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h5 class="text-muted mt-3">暂无客户留言</h5>
                        <p class="text-muted">客户留言将在这里显示</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" class="form-check-input check-all">
                                    </th>
                                    <th width="15%">客户信息</th>
                                    <th width="10%">类型</th>
                                    <th width="20%">主题</th>
                                    <th width="25%">留言内容</th>
                                    <th width="8%">状态</th>
                                    <th width="12%">留言时间</th>
                                    <th width="5%">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input" value="<?= $message['id'] ?>" 
                                                   onchange="Admin.updateBatchActions()">
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($message['name']) ?></h6>
                                                <?php if ($message['company']): ?>
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-building me-1"></i>
                                                        <?= htmlspecialchars($message['company']) ?>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($message['email']): ?>
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-envelope me-1"></i>
                                                        <?= htmlspecialchars($message['email']) ?>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($message['phone']): ?>
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-telephone me-1"></i>
                                                        <?= htmlspecialchars($message['phone']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $type_names[$message['type']] ?? $message['type'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($message['subject']): ?>
                                                <strong><?= htmlspecialchars(truncateText($message['subject'], 40)) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">无主题</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?= htmlspecialchars(truncateText($message['message'], 80)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $message['status'] === 'pending' ? 'warning' : ($message['status'] === 'replied' ? 'success' : 'secondary') ?>">
                                                <?php
                                                $status_names = [
                                                    'pending' => '待处理',
                                                    'replied' => '已回复',
                                                    'archived' => '已归档'
                                                ];
                                                echo $status_names[$message['status']] ?? $message['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= timeAgo($message['created_at']) ?>
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
                                                        <a class="dropdown-item" href="#" onclick="viewMessage(<?= $message['id'] ?>)">
                                                            <i class="bi bi-eye me-2"></i>查看详情
                                                        </a>
                                                    </li>
                                                    <?php if ($message['status'] !== 'replied'): ?>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="replyMessage(<?= $message['id'] ?>)">
                                                                <i class="bi bi-reply me-2"></i>回复
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="updateStatus(<?= $message['id'] ?>, 'archived')">
                                                            <i class="bi bi-archive me-2"></i>归档
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" 
                                                           data-action="delete" data-url="?id=<?= $message['id'] ?>"
                                                           data-message="确定要删除这条留言吗？">
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
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&status=<?= $status ?>&type=<?= $type ?>&search=<?= urlencode($search) ?>">
                                            上一页
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&status=<?= $status ?>&type=<?= $type ?>&search=<?= urlencode($search) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&status=<?= $status ?>&type=<?= $type ?>&search=<?= urlencode($search) ?>">
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

<!-- 查看留言模态框 -->
<div class="modal fade" id="viewMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">留言详情</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageDetails">
                <!-- 内容将通过AJAX加载 -->
            </div>
        </div>
    </div>
</div>

<!-- 回复留言模态框 -->
<div class="modal fade" id="replyMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">回复留言</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="id" id="replyMessageId">
                    
                    <div class="mb-3">
                        <label class="form-label">回复内容</label>
                        <textarea class="form-control" name="reply" rows="6" required placeholder="请输入回复内容..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">发送回复</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const search = document.getElementById('searchInput').value;
    
    const params = new URLSearchParams();
    if (status) params.set('status', status);
    if (type) params.set('type', type);
    if (search) params.set('search', search);
    
    window.location.href = '?' + params.toString();
}

function resetFilters() {
    window.location.href = '?';
}

function viewMessage(id) {
    // 这里可以通过AJAX加载留言详情
    $('#messageDetails').html('<div class="text-center"><i class="spinner-border"></i></div>');
    $('#viewMessageModal').modal('show');
    
    // 模拟加载内容
    setTimeout(function() {
        $('#messageDetails').html('<p>留言详情加载中...</p>');
    }, 500);
}

function replyMessage(id) {
    $('#replyMessageId').val(id);
    $('#replyMessageModal').modal('show');
}

function updateStatus(id, status) {
    if (confirm('确定要更新这条留言的状态吗？')) {
        $.ajax({
            url: '',
            method: 'POST',
            data: {
                action: 'update_status',
                id: id,
                status: status,
                csrf_token: window.AdminConfig.csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Admin.showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    Admin.showAlert('danger', response.message);
                }
            }
        });
    }
}

// 回复表单提交
$('#replyForm').on('submit', function(e) {
    e.preventDefault();
    
    Admin.submitForm($(this), function(response) {
        if (response.success) {
            $('#replyMessageModal').modal('hide');
            Admin.showAlert('success', response.message);
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        } else {
            Admin.showAlert('danger', response.message);
        }
    });
});

// 回车搜索
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
