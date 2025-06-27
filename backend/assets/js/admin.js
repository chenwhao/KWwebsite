/**
 * 阔文展览后台管理系统JavaScript
 */

// 全局管理对象
window.Admin = {
    // 配置
    config: {
        baseUrl: '/admin',
        csrfToken: '',
        debug: false
    },
    
    // 初始化
    init: function() {
        this.initConfig();
        this.initGlobalEvents();
        this.initComponents();
        this.initTooltips();
    },
    
    // 初始化配置
    initConfig: function() {
        if (window.adminConfig) {
            this.config = { ...this.config, ...window.adminConfig };
        }
    },
    
    // 初始化全局事件
    initGlobalEvents: function() {
        // 确认删除
        $(document).on('click', '[data-action="delete"]', this.handleDelete.bind(this));
        
        // 批量操作
        $(document).on('click', '[data-action="batch-delete"]', this.handleBatchDelete.bind(this));
        
        // 状态切换
        $(document).on('click', '[data-action="toggle-status"]', this.handleToggleStatus.bind(this));
        
        // 表单提交
        $(document).on('submit', 'form[data-ajax="true"]', this.handleAjaxForm.bind(this));
        
        // 搜索表单
        $(document).on('submit', '.search-form', this.handleSearch.bind(this));
        
        // 全选/取消全选
        $(document).on('change', '[data-action="select-all"]', this.handleSelectAll.bind(this));
        
        // 文件上传
        $(document).on('change', '[data-action="upload-file"]', this.handleFileUpload.bind(this));
    },
    
    // 初始化组件
    initComponents: function() {
        // 初始化富文本编辑器
        this.initEditor();
        
        // 初始化图片上传
        this.initImageUpload();
        
        // 初始化排序
        this.initSortable();
    },
    
    // 初始化工具提示
    initTooltips: function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    },
    
    // 处理删除操作
    handleDelete: function(e) {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const url = $btn.data('url');
        const message = $btn.data('message') || '您确定要删除这条记录吗？';
        
        this.showConfirmDialog(message, function() {
            Admin.deleteRecord(url);
        });
    },
    
    // 处理批量删除
    handleBatchDelete: function(e) {
        e.preventDefault();
        const selectedItems = $('input[name="selected_ids[]"]:checked');
        
        if (selectedItems.length === 0) {
            this.showAlert('请选择要删除的项目', 'warning');
            return;
        }
        
        const ids = selectedItems.map(function() { return $(this).val(); }).get();
        const url = $(e.currentTarget).data('url');
        
        this.showConfirmDialog(`您确定要删除选中的 ${ids.length} 项记录吗？`, function() {
            Admin.batchDelete(url, ids);
        });
    },
    
    // 处理状态切换
    handleToggleStatus: function(e) {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const url = $btn.data('url');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                [this.config.csrfToken.name]: this.config.csrfToken,
                action: 'toggle_status'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.showAlert('操作失败，请重试', 'error');
            }
        });
    },
    
    // 处理AJAX表单提交
    handleAjaxForm: function(e) {
        e.preventDefault();
        const $form = $(e.currentTarget);
        const url = $form.attr('action');
        const data = new FormData($form[0]);
        
        this.showLoading();
        
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(response.message, 'success');
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    }
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('提交失败，请重试', 'error');
            }
        });
    },
    
    // 处理搜索
    handleSearch: function(e) {
        e.preventDefault();
        const $form = $(e.currentTarget);
        const url = $form.attr('action');
        const data = $form.serialize();
        
        window.location.href = url + '?' + data;
    },
    
    // 处理全选
    handleSelectAll: function(e) {
        const isChecked = $(e.currentTarget).prop('checked');
        $('input[name="selected_ids[]"]').prop('checked', isChecked);
        this.updateBatchActions();
    },
    
    // 处理文件上传
    handleFileUpload: function(e) {
        const $input = $(e.currentTarget);
        const files = e.target.files;
        
        if (files.length > 0) {
            this.uploadFiles(files, $input);
        }
    },
    
    // 删除记录
    deleteRecord: function(url) {
        this.showLoading();
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                [this.config.csrfToken.name]: this.config.csrfToken,
                action: 'delete'
            },
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('删除失败，请重试', 'error');
            }
        });
    },
    
    // 批量删除
    batchDelete: function(url, ids) {
        this.showLoading();
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                [this.config.csrfToken.name]: this.config.csrfToken,
                action: 'batch_delete',
                ids: ids
            },
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('批量删除失败，请重试', 'error');
            }
        });
    },
    
    // 上传文件
    uploadFiles: function(files, $input) {
        const formData = new FormData();
        
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        formData.append(this.config.csrfToken.name, this.config.csrfToken);
        
        this.showLoading();
        
        $.ajax({
            url: this.config.baseUrl + '/modules/files/upload.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert('文件上传成功', 'success');
                    // 触发自定义事件
                    $input.trigger('upload.success', [response.files]);
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('文件上传失败', 'error');
            }
        });
    },
    
    // 显示确认对话框
    showConfirmDialog: function(message, callback) {
        const $modal = $('#confirmDeleteModal');
        $modal.find('.modal-body p').text(message);
        
        $('#confirmDeleteBtn').off('click').on('click', function() {
            $modal.modal('hide');
            if (typeof callback === 'function') {
                callback();
            }
        });
        
        $modal.modal('show');
    },
    
    // 显示加载状态
    showLoading: function() {
        $('#loadingModal').modal('show');
    },
    
    // 隐藏加载状态
    hideLoading: function() {
        $('#loadingModal').modal('hide');
    },
    
    // 显示提示消息
    showAlert: function(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const $alert = $(`
            <div class="alert ${alertClass[type]} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.container-fluid').first().prepend($alert);
        
        // 自动隐藏
        setTimeout(function() {
            $alert.fadeOut();
        }, 5000);
    },
    
    // 更新批量操作按钮状态
    updateBatchActions: function() {
        const selectedCount = $('input[name="selected_ids[]"]:checked').length;
        const $batchActions = $('.batch-actions');
        
        if (selectedCount > 0) {
            $batchActions.removeClass('d-none');
            $batchActions.find('.selected-count').text(selectedCount);
        } else {
            $batchActions.addClass('d-none');
        }
    },
    
    // 初始化富文本编辑器
    initEditor: function() {
        if (typeof ClassicEditor !== 'undefined') {
            $('.rich-editor').each(function() {
                const element = this;
                ClassicEditor
                    .create(element, {
                        language: 'zh-cn',
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'blockQuote', 'insertTable', '|',
                            'undo', 'redo'
                        ]
                    })
                    .catch(error => {
                        console.error('编辑器初始化失败:', error);
                    });
            });
        }
    },
    
    // 初始化图片上传
    initImageUpload: function() {
        // 拖拽上传
        $('.upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        }).on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        }).on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                Admin.uploadFiles(files, $(this));
            }
        });
    },
    
    // 初始化排序
    initSortable: function() {
        if (typeof Sortable !== 'undefined') {
            $('.sortable').each(function() {
                new Sortable(this, {
                    animation: 150,
                    handle: '.sort-handle',
                    onEnd: function(evt) {
                        // 处理排序更新
                        Admin.updateSortOrder(evt);
                    }
                });
            });
        }
    },
    
    // 更新排序
    updateSortOrder: function(evt) {
        const $table = $(evt.to);
        const url = $table.data('sort-url');
        
        if (!url) return;
        
        const sortData = [];
        $table.find('tr[data-id]').each(function(index) {
            sortData.push({
                id: $(this).data('id'),
                sort_order: index + 1
            });
        });
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                [this.config.csrfToken.name]: this.config.csrfToken,
                action: 'update_sort',
                sort_data: sortData
            },
            success: function(response) {
                if (response.success) {
                    Admin.showAlert('排序更新成功', 'success');
                } else {
                    Admin.showAlert(response.message, 'error');
                }
            },
            error: function() {
                Admin.showAlert('排序更新失败', 'error');
            }
        });
    },
    
    // 图片预览
    previewImage: function(input, container) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const $preview = $(`
                    <div class="image-preview">
                        <img src="${e.target.result}" alt="预览">
                        <button type="button" class="remove-image" onclick="Admin.removeImagePreview(this)">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `);
                $(container).append($preview);
            };
            reader.readAsDataURL(input.files[0]);
        }
    },
    
    // 移除图片预览
    removeImagePreview: function(btn) {
        $(btn).closest('.image-preview').remove();
    },
    
    // 工具函数
    utils: {
        // 格式化文件大小
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        // 获取文件扩展名
        getFileExtension: function(filename) {
            return filename.split('.').pop().toLowerCase();
        },
        
        // 验证邮箱
        validateEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },
        
        // 防抖函数
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }
};

// 文档就绪后初始化
$(document).ready(function() {
    Admin.init();
    
    // 监听复选框变化，更新批量操作
    $(document).on('change', 'input[name="selected_ids[]"]', function() {
        Admin.updateBatchActions();
    });
});

// 全局错误处理
window.addEventListener('error', function(e) {
    if (Admin.config.debug) {
        console.error('JavaScript错误:', e.error);
    }
});

// 导出到全局
window.Admin = Admin;
