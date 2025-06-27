<?php
/**
 * 文件管理
 * 阔文展览后台管理系统
 */

$pageTitle = '文件管理 - 阔文展览后台管理';
$currentPage = 'files';
$breadcrumbs = [
    ['name' => '文件管理']
];

require_once __DIR__ . '/../../includes/header.php';

// 获取搜索参数
$search = Security::cleanInput($_GET['search'] ?? '');
$fileType = $_GET['file_type'] ?? '';
$category = $_GET['category'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// 构建查询条件
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(original_name LIKE ? OR description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($fileType)) {
    $conditions[] = "file_type = ?";
    $params[] = $fileType;
}

if (!empty($category)) {
    $conditions[] = "category = ?";
    $params[] = $category;
}

// 获取分页数据
$result = paginate('files', $conditions, $params, $page, $perPage, 'uploaded_at DESC');
$files = $result['data'];
$pagination = $result['pagination'];

// 获取统计数据
$stats = [
    'total' => fetchOne("SELECT COUNT(*) as count FROM files")['count'],
    'images' => fetchOne("SELECT COUNT(*) as count FROM files WHERE file_type = 'image'")['count'],
    'documents' => fetchOne("SELECT COUNT(*) as count FROM files WHERE file_type = 'document'")['count'],
    'total_size' => fetchOne("SELECT SUM(file_size) as size FROM files")['size'] ?? 0
];
?>

<div class="row">
    <!-- 统计卡片 -->
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-folder display-4 mb-3"></i>
                <h3><?php echo number_format($stats['total']); ?></h3>
                <p class="mb-0">总文件数</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-image display-4 mb-3"></i>
                <h3><?php echo number_format($stats['images']); ?></h3>
                <p class="mb-0">图片文件</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-file-text display-4 mb-3"></i>
                <h3><?php echo number_format($stats['documents']); ?></h3>
                <p class="mb-0">文档文件</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card danger">
            <div class="card-body text-center">
                <i class="bi bi-hdd display-4 mb-3"></i>
                <h3><?php echo number_format($stats['total_size'] / 1024 / 1024, 1); ?>MB</h3>
                <p class="mb-0">总大小</p>
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
                            <i class="bi bi-folder me-2"></i>文件管理
                        </h5>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bi bi-cloud-upload me-2"></i>上传文件
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- 搜索和筛选 -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="搜索文件名或描述">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="file_type">
                            <option value="">全部类型</option>
                            <option value="image" <?php echo $fileType === 'image' ? 'selected' : ''; ?>>图片</option>
                            <option value="document" <?php echo $fileType === 'document' ? 'selected' : ''; ?>>文档</option>
                            <option value="video" <?php echo $fileType === 'video' ? 'selected' : ''; ?>>视频</option>
                            <option value="audio" <?php echo $fileType === 'audio' ? 'selected' : ''; ?>>音频</option>
                            <option value="other" <?php echo $fileType === 'other' ? 'selected' : ''; ?>>其他</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category">
                            <option value="">全部分类</option>
                            <option value="logo" <?php echo $category === 'logo' ? 'selected' : ''; ?>>Logo</option>
                            <option value="service" <?php echo $category === 'service' ? 'selected' : ''; ?>>服务</option>
                            <option value="case" <?php echo $category === 'case' ? 'selected' : ''; ?>>案例</option>
                            <option value="news" <?php echo $category === 'news' ? 'selected' : ''; ?>>新闻</option>
                            <option value="general" <?php echo $category === 'general' ? 'selected' : ''; ?>>通用</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i>搜索
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <?php if (!empty($search) || !empty($fileType) || !empty($category)): ?>
                            <a href="/admin/modules/files/index.php" class="btn btn-outline-secondary">
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
                                data-url="/admin/modules/files/process.php">
                            <i class="bi bi-trash me-1"></i>批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 文件网格视图 -->
                <div class="row">
                    <?php if (empty($files)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">暂无文件</h4>
                            <p class="text-muted">点击上传文件按钮开始上传</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($files as $file): ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card file-card h-100">
                                    <div class="position-relative">
                                        <input type="checkbox" name="selected_ids[]" value="<?php echo $file['id']; ?>" 
                                               class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                        
                                        <?php if ($file['file_type'] === 'image'): ?>
                                            <img src="/<?php echo htmlspecialchars($file['file_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($file['original_name']); ?>"
                                                 class="card-img-top file-preview" 
                                                 style="height: 150px; object-fit: cover; cursor: pointer;"
                                                 data-bs-toggle="modal" data-bs-target="#previewModal"
                                                 data-file-id="<?php echo $file['id']; ?>">
                                        <?php else: ?>
                                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 150px;">
                                                <?php
                                                $iconMap = [
                                                    'document' => 'file-text',
                                                    'video' => 'camera-video',
                                                    'audio' => 'music-note',
                                                    'other' => 'file'
                                                ];
                                                $icon = $iconMap[$file['file_type']] ?? 'file';
                                                ?>
                                                <i class="bi bi-<?php echo $icon; ?> display-4 text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light btn-outline-secondary" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="/<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank">
                                                        <i class="bi bi-eye me-2"></i>查看
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="/<?php echo htmlspecialchars($file['file_path']); ?>" download>
                                                        <i class="bi bi-download me-2"></i>下载
                                                    </a></li>
                                                    <li><button class="dropdown-item text-primary" type="button" 
                                                                onclick="copyFileUrl('<?php echo htmlspecialchars($file['file_path']); ?>')">
                                                        <i class="bi bi-link me-2"></i>复制链接
                                                    </button></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><button class="dropdown-item text-danger" type="button"
                                                                data-action="delete" 
                                                                data-url="/admin/modules/files/process.php?id=<?php echo $file['id']; ?>&action=delete">
                                                        <i class="bi bi-trash me-2"></i>删除
                                                    </button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                            <?php echo truncateText($file['original_name'], 20); ?>
                                        </h6>
                                        <div class="small text-muted">
                                            <div><?php echo strtoupper($file['file_type']); ?></div>
                                            <div><?php echo number_format($file['file_size'] / 1024, 1); ?> KB</div>
                                            <div><?php echo timeAgo($file['uploaded_at']); ?></div>
                                            <?php if (!empty($file['category'])): ?>
                                                <span class="badge bg-secondary mt-1"><?php echo htmlspecialchars($file['category']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- 分页 -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="分页导航" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&file_type=<?php echo urlencode($fileType); ?>&category=<?php echo urlencode($category); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&file_type=<?php echo urlencode($fileType); ?>&category=<?php echo urlencode($category); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&file_type=<?php echo urlencode($fileType); ?>&category=<?php echo urlencode($category); ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="text-center text-muted">
                        共 <?php echo $pagination['total']; ?> 个文件，第 <?php echo $pagination['current_page']; ?> / <?php echo $pagination['total_pages']; ?> 页
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 上传模态框 -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">上传文件</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Security::generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">文件分类</label>
                        <select class="form-select" id="category" name="category">
                            <option value="general">通用</option>
                            <option value="logo">Logo</option>
                            <option value="service">服务</option>
                            <option value="case">案例</option>
                            <option value="news">新闻</option>
                        </select>
                    </div>
                    
                    <div class="upload-area" id="dropZone">
                        <div class="upload-icon">
                            <i class="bi bi-cloud-upload"></i>
                        </div>
                        <h5>拖拽文件到这里或点击选择</h5>
                        <p class="text-muted">支持多文件上传，单个文件最大 10MB</p>
                        <input type="file" class="d-none" id="fileInput" name="files[]" multiple>
                    </div>
                    
                    <div id="fileList" class="mt-3"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="uploadBtn">开始上传</button>
            </div>
        </div>
    </div>
</div>

<!-- 预览模态框 -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">文件预览</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent"></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    let selectedFiles = [];
    
    // 文件拖拽上传
    const dropZone = $('#dropZone');
    const fileInput = $('#fileInput');
    
    dropZone.on('click', function() {
        fileInput.click();
    });
    
    dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    dropZone.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    dropZone.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });
    
    fileInput.on('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // 处理选择的文件
    function handleFiles(files) {
        selectedFiles = Array.from(files);
        displayFileList();
    }
    
    // 显示文件列表
    function displayFileList() {
        const fileList = $('#fileList');
        fileList.empty();
        
        if (selectedFiles.length > 0) {
            const listHtml = selectedFiles.map((file, index) => `
                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file me-2"></i>
                        <div>
                            <div class="fw-semibold">${file.name}</div>
                            <small class="text-muted">${Admin.utils.formatFileSize(file.size)}</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `).join('');
            
            fileList.html(listHtml);
        }
    }
    
    // 移除文件
    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        displayFileList();
    };
    
    // 上传文件
    $('#uploadBtn').on('click', function() {
        if (selectedFiles.length === 0) {
            Admin.showAlert('请选择要上传的文件', 'warning');
            return;
        }
        
        const formData = new FormData();
        formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo Security::generateCSRFToken(); ?>');
        formData.append('category', $('#category').val());
        
        selectedFiles.forEach(file => {
            formData.append('files[]', file);
        });
        
        Admin.showLoading();
        
        $.ajax({
            url: '/admin/modules/files/upload.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(`成功上传 ${response.files.length} 个文件`, 'success');
                    $('#uploadModal').modal('hide');
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
    });
    
    // 文件预览
    $('.file-preview').on('click', function() {
        const fileId = $(this).data('file-id');
        // 这里可以加载文件详细信息进行预览
        $('#previewContent').html(`<img src="${$(this).attr('src')}" class="img-fluid" alt="预览">`);
    });
    
    // 复制文件URL
    window.copyFileUrl = function(filePath) {
        const fullUrl = window.location.origin + '/' + filePath;
        navigator.clipboard.writeText(fullUrl).then(() => {
            Admin.showAlert('链接已复制到剪贴板', 'success');
        }).catch(() => {
            Admin.showAlert('复制失败，请手动复制：' + fullUrl, 'warning');
        });
    };
});
</script>

<style>
.file-card {
    transition: transform 0.2s;
}

.file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.upload-area.dragover {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}
</style>
