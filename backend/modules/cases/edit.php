<?php
/**
 * 案例作品编辑
 * 阔文展览后台管理系统
 */

$id = intval($_GET['id'] ?? 0);
$isEdit = $id > 0;

$pageTitle = ($isEdit ? '编辑案例作品' : '添加案例作品') . ' - 阔文展览后台管理';
$currentPage = 'case_edit';
$breadcrumbs = [
    ['name' => '案例作品管理', 'url' => '/admin/modules/cases/index.php'],
    ['name' => $isEdit ? '编辑案例作品' : '添加案例作品']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取案例数据
$case = null;
if ($isEdit) {
    $case = fetchOne("SELECT * FROM cases WHERE id = ?", [$id]);
    if (!$case) {
        header('Location: /admin/modules/cases/index.php');
        exit;
    }
}

// 获取分类列表
$categories = fetchAll("SELECT * FROM case_categories ORDER BY sort_order ASC, id ASC");

// 如果是编辑模式，获取案例图片
$caseImages = [];
if ($isEdit) {
    $caseImages = fetchAll("SELECT * FROM case_images WHERE case_id = ? ORDER BY sort_order ASC", [$id]);
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $isEdit ? 'pencil' : 'plus'; ?> me-2"></i>
                    <?php echo $isEdit ? '编辑案例作品' : '添加案例作品'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form id="caseForm" data-ajax="true" action="/admin/modules/cases/process.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $case['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">案例标题 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($case['title'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">案例分类</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">选择分类</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($case['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">案例副标题</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle" 
                               value="<?php echo htmlspecialchars($case['subtitle'] ?? ''); ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client" class="form-label">客户名称</label>
                                <input type="text" class="form-control" id="client" name="client" 
                                       value="<?php echo htmlspecialchars($case['client'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="project_date" class="form-label">项目日期</label>
                                <input type="date" class="form-control" id="project_date" name="project_date" 
                                       value="<?php echo $case['project_date'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="location" class="form-label">项目地点</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($case['location'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">案例描述</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($case['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">详细内容</label>
                        <textarea class="form-control rich-editor" id="content" name="content" rows="12"><?php echo htmlspecialchars($case['content'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- 主要特色图片 -->
                    <div class="mb-3">
                        <label class="form-label">主要特色图片</label>
                        <div class="upload-area" id="featuredImageUpload">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <h6>点击或拖拽上传主图</h6>
                            <p class="text-muted">建议尺寸：800x600px，支持 JPG、PNG 格式</p>
                            <input type="file" class="d-none" id="featured_image_file" name="featured_image_file" accept="image/*">
                        </div>
                        <?php if (!empty($case['featured_image'])): ?>
                            <div class="mt-2">
                                <img src="/<?php echo htmlspecialchars($case['featured_image']); ?>" 
                                     alt="主图预览" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" id="featured_image" name="featured_image" value="<?php echo htmlspecialchars($case['featured_image'] ?? ''); ?>">
                    </div>
                    
                    <!-- 项目特色 -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_features" class="form-label">项目特色</label>
                                <textarea class="form-control" id="project_features" name="project_features" rows="6" 
                                          placeholder="每行一个特色，例如：&#10;创意设计&#10;专业搭建&#10;优质服务"><?php echo htmlspecialchars($case['project_features'] ?? ''); ?></textarea>
                                <small class="text-muted">每行一个特色点</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="technologies" class="form-label">使用技术/材料</label>
                                <textarea class="form-control" id="technologies" name="technologies" rows="6" 
                                          placeholder="每行一个技术或材料，例如：&#10;LED显示屏&#10;木质结构&#10;灯光设计"><?php echo htmlspecialchars($case['technologies'] ?? ''); ?></textarea>
                                <small class="text-muted">每行一个技术或材料</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 项目参数 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="area" class="form-label">展示面积</label>
                                <input type="text" class="form-control" id="area" name="area" 
                                       value="<?php echo htmlspecialchars($case['area'] ?? ''); ?>"
                                       placeholder="例如：100平方米">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="duration" class="form-label">项目周期</label>
                                <input type="text" class="form-control" id="duration" name="duration" 
                                       value="<?php echo htmlspecialchars($case['duration'] ?? ''); ?>"
                                       placeholder="例如：15天">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">排序</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       value="<?php echo $case['sort_order'] ?? 0; ?>" min="0">
                                <small class="text-muted">数字越小排序越靠前</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">状态</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($case['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>启用</option>
                                    <option value="inactive" <?php echo ($case['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>禁用</option>
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
                                       value="<?php echo htmlspecialchars($case['seo_title'] ?? ''); ?>">
                                <small class="text-muted">如果为空，将使用案例标题</small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_keywords" class="form-label">SEO关键词</label>
                                <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" 
                                       value="<?php echo htmlspecialchars($case['seo_keywords'] ?? ''); ?>"
                                       placeholder="用逗号分隔多个关键词">
                            </div>
                            <div class="mb-3">
                                <label for="seo_description" class="form-label">SEO描述</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="3"><?php echo htmlspecialchars($case['seo_description'] ?? ''); ?></textarea>
                                <small class="text-muted">如果为空，将使用案例描述</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end mt-4">
                        <a href="/admin/modules/cases/index.php" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>返回列表
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i><?php echo $isEdit ? '更新' : '添加'; ?>案例
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($isEdit && !empty($caseImages)): ?>
<!-- 案例图片管理 -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">案例图片管理</h6>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-primary" id="addMoreImages">
                            <i class="bi bi-plus me-1"></i>添加图片
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="imageGallery">
                    <?php foreach ($caseImages as $image): ?>
                        <div class="col-md-3 mb-3" data-image-id="<?php echo $image['id']; ?>">
                            <div class="card">
                                <div class="position-relative">
                                    <img src="/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($image['title'] ?? ''); ?>"
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteImage(<?php echo $image['id']; ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <input type="text" class="form-control form-control-sm" 
                                           value="<?php echo htmlspecialchars($image['title'] ?? ''); ?>"
                                           placeholder="图片标题" 
                                           onchange="updateImageTitle(<?php echo $image['id']; ?>, this.value)">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
    
    // 主图上传
    $('#featuredImageUpload').on('click', function() {
        $('#featured_image_file').click();
    });
    
    $('#featured_image_file').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('category', 'case');
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
                            <img src="/${file.file_path}" alt="主图预览" class="img-thumbnail" style="max-height: 200px;">
                        </div>`;
                        $('#featuredImageUpload').after(preview);
                        
                        Admin.showAlert('主图上传成功', 'success');
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
    $('#caseForm').on('submit', function(e) {
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
                        window.location.href = '/admin/modules/cases/index.php';
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
    
    // 添加更多图片
    $('#addMoreImages').on('click', function() {
        const input = $('<input type="file" accept="image/*" multiple style="display:none;">');
        $('body').append(input);
        input.click();
        
        input.on('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                uploadCaseImages(files);
            }
            input.remove();
        });
    });
});

// 上传案例图片
function uploadCaseImages(files) {
    const formData = new FormData();
    formData.append('case_id', '<?php echo $id; ?>');
    formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo Security::generateCSRFToken(); ?>');
    
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }
    
    Admin.showLoading();
    
    $.ajax({
        url: '/admin/modules/cases/upload_images.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Admin.hideLoading();
            if (response.success) {
                Admin.showAlert(`成功上传 ${response.uploaded} 张图片`, 'success');
                setTimeout(() => location.reload(), 1500);
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

// 删除图片
function deleteImage(imageId) {
    if (confirm('确定要删除这张图片吗？')) {
        $.ajax({
            url: '/admin/modules/cases/process.php',
            method: 'POST',
            data: {
                action: 'delete_image',
                image_id: imageId,
                '<?php echo CSRF_TOKEN_NAME; ?>': '<?php echo Security::generateCSRFToken(); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $(`[data-image-id="${imageId}"]`).fadeOut(300, function() {
                        $(this).remove();
                    });
                    Admin.showAlert('图片删除成功', 'success');
                } else {
                    Admin.showAlert(response.message || '删除失败', 'error');
                }
            },
            error: function() {
                Admin.showAlert('删除失败，请重试', 'error');
            }
        });
    }
}

// 更新图片标题
function updateImageTitle(imageId, title) {
    $.ajax({
        url: '/admin/modules/cases/process.php',
        method: 'POST',
        data: {
            action: 'update_image_title',
            image_id: imageId,
            title: title,
            '<?php echo CSRF_TOKEN_NAME; ?>': '<?php echo Security::generateCSRFToken(); ?>'
        },
        success: function(response) {
            if (response.success) {
                Admin.showAlert('标题更新成功', 'success', 1000);
            }
        }
    });
}
</script>