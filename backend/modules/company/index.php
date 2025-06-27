<?php
/**
 * 公司信息管理
 * 阔文展览后台管理系统 - 最终版本
 */

$pageTitle = '公司信息管理';
$currentPage = 'company';
$currentModule = 'company';

require_once '../../includes/header.php';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF验证
        Security::validateCSRF($_POST['csrf_token']);
        
        $data = [
            'name' => Security::sanitize($_POST['name']),
            'short_name' => Security::sanitize($_POST['short_name']),
            'description' => Security::sanitize($_POST['description']),
            'founded_year' => intval($_POST['founded_year']),
            'business_scope' => Security::sanitize($_POST['business_scope']),
            'address' => Security::sanitize($_POST['address']),
            'phone' => Security::sanitize($_POST['phone']),
            'fax' => Security::sanitize($_POST['fax']),
            'email' => Security::sanitize($_POST['email']),
            'website' => Security::sanitize($_POST['website']),
            'business_hours' => Security::sanitize($_POST['business_hours'])
        ];
        
        // 验证必填字段
        $required = ['name', 'short_name', 'description'];
        $missing = validateRequired($data, $required);
        
        if (!empty($missing)) {
            throw new Exception('请填写所有必填字段');
        }
        
        // 处理社交媒体JSON数据
        $social_media = [];
        if (!empty($_POST['wechat'])) {
            $social_media['wechat'] = Security::sanitize($_POST['wechat']);
        }
        if (!empty($_POST['weibo'])) {
            $social_media['weibo'] = Security::sanitize($_POST['weibo']);
        }
        if (!empty($_POST['linkedin'])) {
            $social_media['linkedin'] = Security::sanitize($_POST['linkedin']);
        }
        $data['social_media'] = json_encode($social_media, JSON_UNESCAPED_UNICODE);
        
        // 检查是否已存在公司信息
        $existing = fetchOne("SELECT id FROM company_info LIMIT 1");
        
        if ($existing) {
            // 更新
            $sql = "UPDATE company_info SET 
                    name = ?, short_name = ?, description = ?, founded_year = ?,
                    business_scope = ?, address = ?, phone = ?, fax = ?, 
                    email = ?, website = ?, social_media = ?, business_hours = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            $params = array_values($data);
            $params[] = $existing['id'];
            updateRecord($sql, $params);
            
            Security::logOperation('update', 'company', $existing['id'], '更新公司信息');
            $message = '公司信息更新成功';
        } else {
            // 新增
            $sql = "INSERT INTO company_info 
                    (name, short_name, description, founded_year, business_scope, 
                     address, phone, fax, email, website, social_media, business_hours) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $id = insertRecord($sql, array_values($data));
            
            Security::logOperation('create', 'company', $id, '添加公司信息');
            $message = '公司信息添加成功';
        }
        
        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            successResponse($message);
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

// 获取公司信息
$company = fetchOne("SELECT * FROM company_info LIMIT 1");
if (!$company) {
    $company = [
        'name' => '',
        'short_name' => '',
        'description' => '',
        'founded_year' => date('Y'),
        'business_scope' => '',
        'address' => '',
        'phone' => '',
        'fax' => '',
        'email' => '',
        'website' => '',
        'social_media' => '{}',
        'business_hours' => ''
    ];
}

// 解析社交媒体数据
$social_media = json_decode($company['social_media'], true) ?: [];
?>

<!-- 成功/错误提示 -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" data-auto-dismiss="true">
        <i class="bi bi-check-circle me-2"></i>
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" data-auto-dismiss="true">
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
                        <i class="bi bi-building me-2"></i>
                        公司信息设置
                    </h5>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="resetForm()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            重置
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="saveCompany()">
                            <i class="bi bi-check-lg me-1"></i>
                            保存设置
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form id="companyForm" method="POST" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    
                    <!-- 基本信息 -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                基本信息
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">
                                公司全称 <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($company['name']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="short_name">
                                公司简称 <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="short_name" name="short_name" 
                                   value="<?= htmlspecialchars($company['short_name']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="founded_year">成立年份</label>
                            <select class="form-select" id="founded_year" name="founded_year">
                                <?php for($year = date('Y'); $year >= 1990; $year--): ?>
                                    <option value="<?= $year ?>" <?= $company['founded_year'] == $year ? 'selected' : '' ?>>
                                        <?= $year ?>年
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label" for="description">
                                公司描述 <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($company['description']) ?></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label" for="business_scope">经营范围</label>
                            <textarea class="form-control" id="business_scope" name="business_scope" rows="3"><?= htmlspecialchars($company['business_scope']) ?></textarea>
                        </div>
                    </div>
                    
                    <!-- 联系信息 -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-telephone me-2"></i>
                                联系信息
                            </h6>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label" for="address">公司地址</label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   value="<?= htmlspecialchars($company['address']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone">联系电话</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($company['phone']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fax">传真号码</label>
                            <input type="text" class="form-control" id="fax" name="fax" 
                                   value="<?= htmlspecialchars($company['fax']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="email">邮箱地址</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($company['email']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="website">官网地址</label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   value="<?= htmlspecialchars($company['website']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="business_hours">营业时间</label>
                            <input type="text" class="form-control" id="business_hours" name="business_hours" 
                                   value="<?= htmlspecialchars($company['business_hours']) ?>"
                                   placeholder="例如：周一至周五 9:00-18:00">
                        </div>
                    </div>
                    
                    <!-- 社交媒体 -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-share me-2"></i>
                                社交媒体
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="wechat">
                                <i class="bi bi-wechat me-1"></i>
                                微信
                            </label>
                            <input type="text" class="form-control" id="wechat" name="wechat" 
                                   value="<?= htmlspecialchars($social_media['wechat'] ?? '') ?>"
                                   placeholder="微信号或二维码链接">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="weibo">
                                <i class="bi bi-person-circle me-1"></i>
                                微博
                            </label>
                            <input type="url" class="form-control" id="weibo" name="weibo" 
                                   value="<?= htmlspecialchars($social_media['weibo'] ?? '') ?>"
                                   placeholder="微博主页链接">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="linkedin">
                                <i class="bi bi-linkedin me-1"></i>
                                LinkedIn
                            </label>
                            <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                   value="<?= htmlspecialchars($social_media['linkedin'] ?? '') ?>"
                                   placeholder="LinkedIn主页链接">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                    重置
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    保存设置
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
function saveCompany() {
    $('#companyForm').submit();
}

function resetForm() {
    if (confirm('确定要重置表单吗？未保存的修改将丢失。')) {
        document.getElementById('companyForm').reset();
    }
}

// AJAX表单提交
$('#companyForm').on('submit', function(e) {
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
