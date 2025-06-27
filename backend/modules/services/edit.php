<?php
/**
 * 服务项目编辑
 * 阔文展览后台管理系统
 */

$id = intval($_GET['id'] ?? 0);
$isEdit = $id > 0;

$pageTitle = ($isEdit ? '编辑服务项目' : '添加服务项目') . ' - 阔文展览后台管理';
$currentPage = 'service_edit';
$breadcrumbs = [
    ['name' => '服务项目管理', 'url' => '/admin/modules/services/index.php'],
    ['name' => $isEdit ? '编辑服务项目' : '添加服务项目']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取服务数据
$service = null;
if ($isEdit) {
    $service = fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
    if (!$service) {
        header('Location: /admin/modules/services/index.php');
        exit;
    }
}

// Bootstrap图标列表
$icons = [
    'gear' => '齿轮',
    'lightbulb' => '灯泡',
    'wrench' => '扳手',
    'tools' => '工具',
    'briefcase' => '公文包',
    'building' => '建筑',
    'paint-bucket' => '油漆桶',
    'palette' => '调色板',
    'pencil-square' => '编辑',
    'camera' => '相机',
    'image' => '图片',
    'graph-up' => '图表',
    'award' => '奖杯',
    'star' => '星星',
    'heart' => '心形',
    'shield-check' => '盾牌',
    'clock' => '时钟',
    'calendar' => '日历'
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $isEdit ? 'pencil' : 'plus'; ?> me-2"></i>
                    <?php echo $isEdit ? '编辑服务项目' : '添加服务项目'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form id="serviceForm" data-ajax="true" action="/admin/modules/services/process.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">服务标题 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($service['title'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="icon" class="form-label">图标</label>
                                <select class="form-select" id="icon" name="icon">
                                    <option value="">选择图标</option>
                                    <?php foreach ($icons as $iconName => $iconLabel): ?>
                                        <option value="<?php echo $iconName; ?>" 
                                                <?php echo ($service['icon'] ?? '') === $iconName ? 'selected' : ''; ?>>
                                            <?php echo $iconLabel; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2">
                                    <small class="text-muted">图标预览：</small>
                                    <i id="iconPreview" class="bi bi-<?php echo $service['icon'] ?? 'gear'; ?> ms-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">服务副标题</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle" 
                               value="<?php echo htmlspecialchars($service['subtitle'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">服务描述</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">详细内容</label>
                        <textarea class="form-control rich-editor" id="content" name="content" rows="8"><?php echo htmlspecialchars($service['content'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">特色图片</label>
                        <div class="upload-area" id="imageUpload">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <h6>点击或拖拽上传图片</h6>
                            <p class="text-muted">支持 JPG、PNG 格式，文件大小不超过 5MB</p>
                            <input type="file" class="d-none" id="featured_image_file" name="featured_image_file" accept="image/*">
                        </div>
                        <?php if (!empty($service['featured_image'])): ?>
                            <div class="mt-2">
                                <img src="<?php echo htmlspecialchars($service['featured_image']); ?>" 
                                     alt="当前图片" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" id="featured_image" name="featured_image" value="<?php echo htmlspecialchars($service['featured_image'] ?? ''); ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="features" class="form-label">服务特色</label>
                                <textarea class="form-control" id="features" name="features" rows="6" 
                                          placeholder="每行一个特色，例如：&#10;创意设计&#10;专业团队&#10;优质服务"><?php echo htmlspecialchars($service['features'] ?? ''); ?></textarea>
                                <small class="text-muted">每行一个特色点</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="process" class="form-label">服务流程</label>
                                <textarea class="form-control" id="process" name="process" rows="6" 
                                          placeholder="每行一个流程步骤，例如：&#10;需求分析&#10;方案设计&#10;项目实施"><?php echo htmlspecialchars($service['process'] ?? ''); ?></textarea>
                                <small class="text-muted">每行一个流程步骤</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">排序</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       value="<?php echo $service['sort_order'] ?? 0; ?>" min="0">
                                <small class="text-muted">数字越小排序越靠前</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">状态</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($service['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>启用</option>
                                    <option value="inactive" <?php echo ($service['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>禁用</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO设置 -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">SEO设置</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="seo_title" class="form-label">SEO标题</label>
                                <input type="text" class="form-control" id="seo_title" name="seo_title" 
                                       value="<?php echo htmlspecialchars($service['seo_title'] ?? ''); ?>">
                                <small class="text-muted">如果为空，将使用服务标题</small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_keywords" class="form-label">SEO关键词</label>
                                <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" 
                                       value="<?php echo htmlspecialchars($service['seo_keywords'] ?? ''); ?>"
                                       placeholder="用逗号分隔多个关键词">
                            </div>
                            <div class="mb-3">
                                <label for="seo_description" class="form-label">SEO描述</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="3"><?php echo htmlspecialchars($service['seo_description'] ?? ''); ?></textarea>
                                <small class="text-muted">如果为空，将使用服务描述</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end mt-4">
                        <a href="/admin/modules/services/index.php" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>返回列表
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i><?php echo $isEdit ? '更新' : '添加'; ?>服务
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$extraJS = [
    'https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js'
];
require_once __DIR__ . '/../../includes/footer.php'; 
?>

<script>
$(document).ready(function() {
    // 初始化富文本编辑器
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor.create(document.querySelector('#content'), {
            language: 'zh-cn',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', '|', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
        }).catch(error => {
            console.error('编辑器初始化失败:', error);
        });
    }
    
    // 图标预览更新
    $('#icon').on('change', function() {
        const iconName = $(this).val();
        $('#iconPreview').attr('class', 'bi bi-' + (iconName || 'gear') + ' ms-2');
    });
    
    // 图片上传
    $('#imageUpload').on('click', function() {
        $('#featured_image_file').click();
    });
    
    $('#featured_image_file').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('category', 'service');
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo Security::generateCSRFToken(); ?>');
            
            Admin.showLoading();
            
            $.ajax({
                url: '/admin/modules/files/upload.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Admin.hideLoading();
                    if (response.success && response.files && response.files[0]) {
                        const file = response.files[0];
                        $('#featured_image').val(file.file_path);
                        
                        // 显示预览
                        const preview = `<div class="mt-2">
                            <img src="/${file.file_path}" alt="图片预览" class="img-thumbnail" style="max-height: 200px;">
                        </div>`;
                        $('#imageUpload').after(preview);
                        
                        Admin.showAlert('图片上传成功', 'success');
                    } else {
                        Admin.showAlert(response.message || '上传失败', 'error');
                    }
                },
                error: function() {
                    Admin.hideLoading();
                    Admin.showAlert('上传失败，请重试', 'error');
                }
            });
        }
    });
    
    // 表单提交
    $('#serviceForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // 获取富文本编辑器内容
        if (window.contentEditor) {
            formData.set('content', window.contentEditor.getData());
        }
        
        Admin.showLoading();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(response.message, 'success');
                    setTimeout(function() {
                        window.location.href = '/admin/modules/services/index.php';
                    }, 1500);
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('提交失败，请重试', 'error');
            }
        });
    });
});
</script>
