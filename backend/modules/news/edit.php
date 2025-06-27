<?php
/**
 * 新闻编辑
 * 阔文展览后台管理系统
 */

$id = intval($_GET['id'] ?? 0);
$isEdit = $id > 0;

$pageTitle = ($isEdit ? '编辑新闻' : '添加新闻') . ' - 阔文展览后台管理';
$currentPage = 'news_edit';
$breadcrumbs = [
    ['name' => '新闻管理', 'url' => '/admin/modules/news/index.php'],
    ['name' => $isEdit ? '编辑新闻' : '添加新闻']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取新闻数据
$news = null;
if ($isEdit) {
    $news = fetchOne("SELECT * FROM news WHERE id = ?", [$id]);
    if (!$news) {
        header('Location: /admin/modules/news/index.php');
        exit;
    }
}

// 分类列表
$categories = [
    'company' => '公司新闻',
    'industry' => '行业资讯',
    'case' => '案例分享',
    'event' => '活动展会'
];
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $isEdit ? 'pencil' : 'plus'; ?> me-2"></i>
                    <?php echo $isEdit ? '编辑新闻' : '添加新闻'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form id="newsForm" data-ajax="true" action="/admin/modules/news/process.php">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">新闻标题 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($news['title'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category" class="form-label">新闻分类</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">选择分类</option>
                                    <?php foreach ($categories as $key => $name): ?>
                                        <option value="<?php echo $key; ?>" 
                                                <?php echo ($news['category'] ?? '') === $key ? 'selected' : ''; ?>>
                                            <?php echo $name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">新闻副标题</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle" 
                               value="<?php echo htmlspecialchars($news['subtitle'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">新闻摘要</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($news['excerpt'] ?? ''); ?></textarea>
                        <small class="text-muted">如果为空，将自动从正文中提取</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">新闻正文</label>
                        <textarea class="form-control rich-editor" id="content" name="content" rows="15"><?php echo htmlspecialchars($news['content'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- 缩略图上传 -->
                    <div class="mb-3">
                        <label class="form-label">缩略图</label>
                        <div class="upload-area" id="thumbnailUpload">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <h6>点击或拖拽上传缩略图</h6>
                            <p class="text-muted">建议尺寸：600x400px，支持 JPG、PNG 格式</p>
                            <input type="file" class="d-none" id="thumbnail_file" name="thumbnail_file" accept="image/*">
                        </div>
                        <?php if (!empty($news['thumbnail'])): ?>
                            <div class="mt-2">
                                <img src="/<?php echo htmlspecialchars($news['thumbnail']); ?>" 
                                     alt="缩略图预览" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" id="thumbnail" name="thumbnail" value="<?php echo htmlspecialchars($news['thumbnail'] ?? ''); ?>">
                    </div>
                    
                    <!-- 新闻信息 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="author" class="form-label">作者</label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       value="<?php echo htmlspecialchars($news['author'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="source" class="form-label">来源</label>
                                <input type="text" class="form-control" id="source" name="source" 
                                       value="<?php echo htmlspecialchars($news['source'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="published_at" class="form-label">发布时间</label>
                                <input type="datetime-local" class="form-control" id="published_at" name="published_at" 
                                       value="<?php echo $news['published_at'] ? date('Y-m-d\TH:i', strtotime($news['published_at'])) : ''; ?>">
                                <small class="text-muted">留空表示立即发布</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">状态</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?php echo ($news['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>草稿</option>
                                    <option value="published" <?php echo ($news['status'] ?? '') === 'published' ? 'selected' : ''; ?>>发布</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 关键词标签 -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">关键词标签</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               value="<?php echo htmlspecialchars($news['tags'] ?? ''); ?>"
                               placeholder="用逗号分隔多个标签，例如：展台设计,搭建服务,创意">
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
                                       value="<?php echo htmlspecialchars($news['seo_title'] ?? ''); ?>">
                                <small class="text-muted">如果为空，将使用新闻标题</small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_keywords" class="form-label">SEO关键词</label>
                                <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" 
                                       value="<?php echo htmlspecialchars($news['seo_keywords'] ?? ''); ?>"
                                       placeholder="用逗号分隔多个关键词">
                            </div>
                            <div class="mb-3">
                                <label for="seo_description" class="form-label">SEO描述</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="3"><?php echo htmlspecialchars($news['seo_description'] ?? ''); ?></textarea>
                                <small class="text-muted">如果为空，将使用新闻摘要</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end mt-4">
                        <a href="/admin/modules/news/index.php" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>返回列表
                        </a>
                        <button type="button" class="btn btn-outline-primary me-2" id="saveAsDraftBtn">
                            <i class="bi bi-save me-2"></i>保存为草稿
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i><?php echo $isEdit ? '更新' : '发布'; ?>新闻
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
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', '|',
                'imageUpload', 'mediaEmbed', '|',
                'undo', 'redo'
            ]
        }).catch(error => {
            console.error('编辑器初始化失败:', error);
        });
    }
    
    // 缩略图上传
    $('#thumbnailUpload').on('click', function() {
        $('#thumbnail_file').click();
    });
    
    $('#thumbnail_file').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('category', 'news');
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
                        $('#thumbnail').val(file.file_path);
                        
                        // 显示预览
                        const preview = `<div class="mt-2">
                            <img src="/${file.file_path}" alt="缩略图预览" class="img-thumbnail" style="max-height: 200px;">
                        </div>`;
                        $('#thumbnailUpload').after(preview);
                        
                        Admin.showAlert('缩略图上传成功', 'success');
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
    
    // 自动生成SEO信息
    $('#title').on('blur', function() {
        const title = $(this).val();
        if (title && !$('#seo_title').val()) {
            $('#seo_title').val(title);
        }
    });
    
    $('#excerpt').on('blur', function() {
        const excerpt = $(this).val();
        if (excerpt && !$('#seo_description').val()) {
            $('#seo_description').val(excerpt);
        }
    });
    
    // 保存为草稿
    $('#saveAsDraftBtn').on('click', function() {
        $('#status').val('draft');
        $('#newsForm').trigger('submit');
    });
    
    // 表单提交
    $('#newsForm').on('submit', function(e) {
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
                        window.location.href = '/admin/modules/news/index.php';
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
