<?php
/**
 * 服务项目编辑
 * 阔文展览后台管理系统 - 最终版本
 */

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pageTitle = $id > 0 ? '编辑服务项目' : '添加服务项目';
$currentPage = 'services';
$currentModule = 'services';

require_once '../../includes/header.php';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Security::validateCSRF($_POST['csrf_token']);
        
        $data = [
            'category_id' => $_POST['category_id'] ? intval($_POST['category_id']) : null,
            'title' => Security::sanitize($_POST['title']),
            'subtitle' => Security::sanitize($_POST['subtitle']),
            'description' => Security::sanitize($_POST['description']),
            'content' => $_POST['content'], // 富文本内容不过度清理
            'price_range' => Security::sanitize($_POST['price_range']),
            'duration' => Security::sanitize($_POST['duration']),
            'tags' => Security::sanitize($_POST['tags']),
            'sort_order' => intval($_POST['sort_order']),
            'status' => in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active'
        ];
        
        // 验证必填字段
        if (empty($data['title'])) {
            throw new Exception('请填写服务标题');
        }
        
        // 处理特色功能
        $features = [];
        if (!empty($_POST['features'])) {
            $feature_lines = explode("\n", $_POST['features']);
            foreach ($feature_lines as $line) {
                $line = trim($line);
                if ($line) {
                    $features[] = $line;
                }
            }
        }
        $data['features'] = json_encode($features, JSON_UNESCAPED_UNICODE);
        
        if ($id > 0) {
            // 更新
            $sql = "UPDATE services SET 
                    category_id = ?, title = ?, subtitle = ?, description = ?, content = ?,
                    features = ?, price_range = ?, duration = ?, tags = ?, 
                    sort_order = ?, status = ?, updated_at = NOW()
                    WHERE id = ?";
            $params = array_values($data);
            $params[] = $id;
            updateRecord($sql, $params);
            
            Security::logOperation('update', 'services', $id, '更新服务项目: ' . $data['title']);
            $message = '服务项目更新成功';
        } else {
            // 新增
            $sql = "INSERT INTO services 
                    (category_id, title, subtitle, description, content, features, 
                     price_range, duration, tags, sort_order, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $id = insertRecord($sql, array_values($data));
            
            Security::logOperation('create', 'services', $id, '添加服务项目: ' . $data['title']);
            $message = '服务项目添加成功';
        }
        
        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            successResponse($message, ['id' => $id, 'redirect' => 'index.php']);
        } else {
            $_SESSION['success_message'] = $message;
            header('Location: index.php');
            exit;
        }
        
    } catch (Exception $e) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            errorResponse($e->getMessage());
        } else {
            $_SESSION['error_message'] = $e->getMessage();
        }
    }
}

// 获取服务信息
if ($id > 0) {
    $service = fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
    if (!$service) {
        $_SESSION['error_message'] = '服务项目不存在';
        header('Location: index.php');
        exit;
    }
    
    // 解析特色功能
    $features = json_decode($service['features'], true) ?: [];
} else {
    $service = [
        'category_id' => '',
        'title' => '',
        'subtitle' => '',
        'description' => '',
        'content' => '',
        'price_range' => '',
        'duration' => '',
        'tags' => '',
        'sort_order' => 0,
        'status' => 'active'
    ];
    $features = [];
}

// 获取分类列表
$categories = fetchAll("SELECT * FROM service_categories WHERE status = 'active' ORDER BY sort_order");
?>

<!-- 成功/错误提示 -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        <?= $pageTitle ?>
                    </h5>
                    <div>
                        <a href="index.php" class="btn btn-outline-secondary btn-sm me-2">
                            <i class="bi bi-arrow-left me-1"></i>
                            返回列表
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="saveService()">
                            <i class="bi bi-check-lg me-1"></i>
                            保存
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form id="serviceForm" method="POST" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    
                    <div class="row">
                        <!-- 基本信息 -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">基本信息</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="title">
                                            服务标题 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= htmlspecialchars($service['title']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="subtitle">副标题</label>
                                        <input type="text" class="form-control" id="subtitle" name="subtitle" 
                                               value="<?= htmlspecialchars($service['subtitle']) ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="description">服务描述</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($service['description']) ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="content">详细内容</label>
                                        <textarea class="form-control rich-editor" id="content" name="content" rows="8"><?= htmlspecialchars($service['content']) ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="features">服务特色（每行一个）</label>
                                        <textarea class="form-control" id="features" name="features" rows="5" 
                                                  placeholder="专业设计团队&#10;一站式服务&#10;品质保证"><?= implode("\n", $features) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 设置信息 -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">设置</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="category_id">服务分类</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">请选择分类</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" 
                                                        <?= $service['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="price_range">价格区间</label>
                                        <input type="text" class="form-control" id="price_range" name="price_range" 
                                               value="<?= htmlspecialchars($service['price_range']) ?>"
                                               placeholder="例如：5000-20000元">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="duration">服务周期</label>
                                        <input type="text" class="form-control" id="duration" name="duration" 
                                               value="<?= htmlspecialchars($service['duration']) ?>"
                                               placeholder="例如：7-15个工作日">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="tags">标签</label>
                                        <input type="text" class="form-control" id="tags" name="tags" 
                                               value="<?= htmlspecialchars($service['tags']) ?>"
                                               placeholder="用逗号分隔多个标签">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="sort_order">排序</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                               value="<?= $service['sort_order'] ?>" min="0">
                                        <small class="form-text text-muted">数字越小排序越靠前</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="status">状态</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" <?= $service['status'] === 'active' ? 'selected' : '' ?>>启用</option>
                                            <option value="inactive" <?= $service['status'] === 'inactive' ? 'selected' : '' ?>>禁用</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="index.php" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    返回列表
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    保存
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function saveService() {
    $('#serviceForm').submit();
}

// AJAX表单提交
$('#serviceForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // 显示加载状态
    submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>保存中...');
    
    // 添加AJAX标识
    const formData = new FormData(this);
    formData.append('ajax', '1');
    
    $.ajax({
        url: '',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Admin.showAlert('success', response.message || '保存成功');
                if (response.data && response.data.redirect) {
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 1500);
                }
            } else {
                Admin.showAlert('danger', response.message || '保存失败');
            }
        },
        error: function() {
            Admin.showAlert('danger', '网络错误，保存失败');
        },
        complete: function() {
            // 恢复按钮状态
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
