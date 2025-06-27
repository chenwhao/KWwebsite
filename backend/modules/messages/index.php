<?php
/**
 * 留言管理
 * 阔文展览后台管理系统
 */

$pageTitle = '留言管理 - 阔文展览后台管理';
$currentPage = 'messages';
$breadcrumbs = [
    ['name' => '留言管理']
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
    $conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($status)) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

// 获取分页数据
$result = paginate('messages', $conditions, $params, $page, $perPage, 'created_at DESC');
$messages = $result['data'];
$pagination = $result['pagination'];

// 获取统计数据
$stats = [
    'total' => fetchOne("SELECT COUNT(*) as count FROM messages")['count'],
    'pending' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'pending'")['count'],
    'replied' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'replied'")['count'],
    'today' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE DATE(created_at) = CURDATE()")['count']
];
?>

<div class="row">
    <!-- 统计卡片 -->
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-envelope display-4 mb-3"></i>
                <h3><?php echo number_format($stats['total']); ?></h3>
                <p class="mb-0">总留言数</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-clock display-4 mb-3"></i>
                <h3><?php echo number_format($stats['pending']); ?></h3>
                <p class="mb-0">待回复</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle display-4 mb-3"></i>
                <h3><?php echo number_format($stats['replied']); ?></h3>
                <p class="mb-0">已回复</p>
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
                            <i class="bi bi-envelope me-2"></i>留言管理
                        </h5>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise me-2"></i>刷新
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 搜索表单 -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="搜索姓名、邮箱、电话、主题或内容">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">全部状态</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>待回复</option>
                            <option value="replied" <?php echo $status === 'replied' ? 'selected' : ''; ?>>已回复</option>
                            <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>已归档</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i>搜索
                        </button>
                    </div>
                    <div class="col-md-2 text-end">
                        <?php if (!empty($search) || !empty($status)): ?>
                            <a href="/admin/modules/messages/index.php" class="btn btn-outline-secondary">
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
                                data-action="batch-reply" 
                                data-url="/admin/modules/messages/process.php">
                            <i class="bi bi-check-circle me-1"></i>标记已回复
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary me-2" 
                                data-action="batch-archive" 
                                data-url="/admin/modules/messages/process.php">
                            <i class="bi bi-archive me-1"></i>批量归档
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                data-action="batch-delete" 
                                data-url="/admin/modules/messages/process.php">
                            <i class="bi bi-trash me-1"></i>批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 留言列表 -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" data-action="select-all">
                                </th>
                                <th>联系人信息</th>
                                <th>留言内容</th>
                                <th width="80">状态</th>
                                <th width="120">留言时间</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        暂无留言
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($messages as $message): ?>
                                    <tr class="<?php echo $message['status'] === 'pending' ? 'table-warning' : ''; ?>">
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $message['id']; ?>">
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                                                <?php if (!empty($message['company'])): ?>
                                                    <span class="small text-muted">（<?php echo htmlspecialchars($message['company']); ?>）</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php if (!empty($message['email'])): ?>
                                                    <i class="bi bi-envelope me-1"></i>
                                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="text-decoration-none">
                                                        <?php echo htmlspecialchars($message['email']); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php if (!empty($message['phone'])): ?>
                                                    <i class="bi bi-telephone me-1"></i>
                                                    <a href="tel:<?php echo htmlspecialchars($message['phone']); ?>" class="text-decoration-none">
                                                        <?php echo htmlspecialchars($message['phone']); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($message['subject'])): ?>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($message['subject']); ?></div>
                                            <?php endif; ?>
                                            <div class="text-muted">
                                                <?php echo truncateText($message['message'], 100); ?>
                                            </div>
                                            <?php if (!empty($message['reply_content'])): ?>
                                                <div class="mt-2 p-2 bg-light rounded small">
                                                    <strong>回复:</strong> <?php echo truncateText($message['reply_content'], 80); ?>
                                                    <div class="text-muted">
                                                        回复时间: <?php echo formatDateTime($message['replied_at'], 'Y-m-d H:i'); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $message['status']; ?>">
                                                <?php 
                                                $statusMap = [
                                                    'pending' => '待回复',
                                                    'replied' => '已回复',
                                                    'archived' => '已归档'
                                                ];
                                                echo $statusMap[$message['status']] ?? $message['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDateTime($message['created_at'], 'Y-m-d H:i'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-action="view-message" 
                                                        data-id="<?php echo $message['id']; ?>"
                                                        data-bs-toggle="tooltip" title="查看详情">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php if ($message['status'] === 'pending'): ?>
                                                    <button type="button" class="btn btn-outline-success" 
                                                            data-action="reply-message" 
                                                            data-id="<?php echo $message['id']; ?>"
                                                            data-bs-toggle="tooltip" title="回复">
                                                        <i class="bi bi-reply"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        data-action="delete" 
                                                        data-url="/admin/modules/messages/process.php?id=<?php echo $message['id']; ?>&action=delete"
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

<!-- 查看留言详情模态框 -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">留言详情</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- 动态加载内容 -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="replyMessageBtn" style="display:none;">回复留言</button>
            </div>
        </div>
    </div>
</div>

<!-- 回复留言模态框 -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">回复留言</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="message_id" value="">
                    
                    <div class="mb-3">
                        <label for="reply_content" class="form-label">回复内容 <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply_content" name="reply_content" rows="6" required></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
                        <label class="form-check-label" for="send_email">
                            发送邮件通知给留言者
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="sendReplyBtn">发送回复</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // 查看留言详情
    $('[data-action="view-message"]').on('click', function() {
        const messageId = $(this).data('id');
        
        // 获取留言详情
        $.ajax({
            url: '/admin/modules/messages/process.php',
            method: 'POST',
            data: {
                action: 'get',
                id: messageId,
                '<?php echo CSRF_TOKEN_NAME; ?>': '<?php echo Security::generateCSRFToken(); ?>'
            },
            success: function(response) {
                if (response.success && response.data) {
                    const message = response.data;
                    displayMessageDetail(message);
                } else {
                    Admin.showAlert(response.message || '获取留言详情失败', 'error');
                }
            },
            error: function() {
                Admin.showAlert('获取留言详情失败', 'error');
            }
        });
    });
    
    // 回复留言
    $('[data-action="reply-message"]').on('click', function() {
        const messageId = $(this).data('id');
        $('#replyForm input[name="message_id"]').val(messageId);
        $('#replyModal').modal('show');
    });
    
    // 发送回复
    $('#sendReplyBtn').on('click', function() {
        const formData = $('#replyForm').serialize();
        
        Admin.showLoading();
        
        $.ajax({
            url: '/admin/modules/messages/process.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(response.message, 'success');
                    $('#replyModal').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('回复失败，请重试', 'error');
            }
        });
    });
});

// 显示留言详情
function displayMessageDetail(message) {
    const statusMap = {
        'pending': '待回复',
        'replied': '已回复',
        'archived': '已归档'
    };
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>联系人信息</h6>
                <table class="table table-sm">
                    <tr><td><strong>姓名:</strong></td><td>${message.name}</td></tr>
                    ${message.company ? `<tr><td><strong>公司:</strong></td><td>${message.company}</td></tr>` : ''}
                    <tr><td><strong>邮箱:</strong></td><td><a href="mailto:${message.email}">${message.email}</a></td></tr>
                    ${message.phone ? `<tr><td><strong>电话:</strong></td><td><a href="tel:${message.phone}">${message.phone}</a></td></tr>` : ''}
                </table>
            </div>
            <div class="col-md-6">
                <h6>留言信息</h6>
                <table class="table table-sm">
                    <tr><td><strong>状态:</strong></td><td><span class="status-badge status-${message.status}">${statusMap[message.status] || message.status}</span></td></tr>
                    <tr><td><strong>留言时间:</strong></td><td>${message.created_at}</td></tr>
                    ${message.ip_address ? `<tr><td><strong>IP地址:</strong></td><td>${message.ip_address}</td></tr>` : ''}
                </table>
            </div>
        </div>
        
        ${message.subject ? `
        <div class="mt-3">
            <h6>主题</h6>
            <p class="border rounded p-3 bg-light">${message.subject}</p>
        </div>
        ` : ''}
        
        <div class="mt-3">
            <h6>留言内容</h6>
            <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">${message.message}</div>
        </div>
        
        ${message.reply_content ? `
        <div class="mt-3">
            <h6>回复内容</h6>
            <div class="border rounded p-3 bg-success bg-opacity-10" style="white-space: pre-wrap;">${message.reply_content}</div>
            <small class="text-muted">回复时间: ${message.replied_at}</small>
        </div>
        ` : ''}
    `;
    
    $('#messageContent').html(html);
    
    // 显示回复按钮
    if (message.status === 'pending') {
        $('#replyMessageBtn').show().off('click').on('click', function() {
            $('#messageModal').modal('hide');
            $('#replyForm input[name="message_id"]').val(message.id);
            $('#replyModal').modal('show');
        });
    } else {
        $('#replyMessageBtn').hide();
    }
    
    $('#messageModal').modal('show');
}
</script>
