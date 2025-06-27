<?php
/**
 * 公司信息管理
 * 阔文展览后台管理系统
 */

$pageTitle = '公司信息管理 - 阔文展览后台管理';
$currentPage = 'company_info';
$breadcrumbs = [
    ['name' => '公司信息管理']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取公司信息
$companyInfo = fetchOne("SELECT * FROM company_info WHERE id = 1");
if (!$companyInfo) {
    // 如果没有记录，插入默认记录
    insertRecord("INSERT INTO company_info (id) VALUES (1)");
    $companyInfo = fetchOne("SELECT * FROM company_info WHERE id = 1");
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-building me-2"></i>公司基本信息
                </h5>
            </div>
            <div class="card-body">
                <form id="companyInfoForm" data-ajax="true" action="/admin/modules/company/update.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">公司名称</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($companyInfo['company_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_slogan" class="form-label">公司口号</label>
                                <input type="text" class="form-control" id="company_slogan" name="company_slogan" 
                                       value="<?php echo htmlspecialchars($companyInfo['company_slogan'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">公司描述</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($companyInfo['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">公司地址</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($companyInfo['address'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="phone" class="form-label">固定电话</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($companyInfo['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mobile" class="form-label">手机号码</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" 
                                       value="<?php echo htmlspecialchars($companyInfo['mobile'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">邮箱地址</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($companyInfo['email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="wechat" class="form-label">微信号</label>
                                <input type="text" class="form-control" id="wechat" name="wechat" 
                                       value="<?php echo htmlspecialchars($companyInfo['wechat'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="website" class="form-label">官方网站</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?php echo htmlspecialchars($companyInfo['website'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="business_hours" class="form-label">营业时间</label>
                        <input type="text" class="form-control" id="business_hours" name="business_hours" 
                               value="<?php echo htmlspecialchars($companyInfo['business_hours'] ?? ''); ?>"
                               placeholder="例如：周一至周五 9:00-18:00">
                    </div>
                    
                    <div class="mb-3">
                        <label for="history" class="form-label">公司历程</label>
                        <textarea class="form-control rich-editor" id="history" name="history" rows="6"><?php echo htmlspecialchars($companyInfo['history'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="culture" class="form-label">企业文化</label>
                        <textarea class="form-control rich-editor" id="culture" name="culture" rows="6"><?php echo htmlspecialchars($companyInfo['culture'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vision" class="form-label">企业愿景</label>
                        <textarea class="form-control" id="vision" name="vision" rows="3"><?php echo htmlspecialchars($companyInfo['vision'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mission" class="form-label">企业使命</label>
                        <textarea class="form-control" id="mission" name="mission" rows="3"><?php echo htmlspecialchars($companyInfo['mission'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">公司Logo</label>
                        <div class="upload-area" id="logoUpload">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <h6>点击或拖拽上传Logo</h6>
                            <p class="text-muted">支持 JPG、PNG 格式，文件大小不超过 2MB</p>
                            <input type="file" class="d-none" id="logo_file" name="logo_file" accept="image/*">
                        </div>
                        <?php if (!empty($companyInfo['logo'])): ?>
                            <div class="mt-2">
                                <img src="<?php echo htmlspecialchars($companyInfo['logo']); ?>" 
                                     alt="当前Logo" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" id="logo" name="logo" value="<?php echo htmlspecialchars($companyInfo['logo'] ?? ''); ?>">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>保存信息
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
        ClassicEditor.create(document.querySelector('#history'), {
            language: 'zh-cn',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', '|', 'bulletedList', 'numberedList', '|', 'blockQuote', '|', 'undo', 'redo']
        }).catch(error => {
            console.error('编辑器初始化失败:', error);
        });
        
        ClassicEditor.create(document.querySelector('#culture'), {
            language: 'zh-cn',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', '|', 'bulletedList', 'numberedList', '|', 'blockQuote', '|', 'undo', 'redo']
        }).catch(error => {
            console.error('编辑器初始化失败:', error);
        });
    }
    
    // Logo上传
    $('#logoUpload').on('click', function() {
        $('#logo_file').click();
    });
    
    $('#logo_file').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('category', 'logo');
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
                        $('#logo').val(file.file_path);
                        
                        // 显示预览
                        const preview = `<div class="mt-2">
                            <img src="/${file.file_path}" alt="Logo预览" class="img-thumbnail" style="max-height: 100px;">
                        </div>`;
                        $('#logoUpload').after(preview);
                        
                        Admin.showAlert('Logo上传成功', 'success');
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
    
    // 表单提交成功后的处理
    $('#companyInfoForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // 获取富文本编辑器内容
        if (window.historyEditor) {
            formData.set('history', window.historyEditor.getData());
        }
        if (window.cultureEditor) {
            formData.set('culture', window.cultureEditor.getData());
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
