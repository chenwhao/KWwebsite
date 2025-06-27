/**
 * 阔文展览后台管理系统脚本
 * 最终版本 - 2025年6月27日
 */

const Admin = {
    // 初始化
    init: function() {
        this.bindEvents();
        this.initComponents();
        console.log('阔文展览后台管理系统已初始化');
    },

    // 绑定事件
    bindEvents: function() {
        // 确认删除
        $(document).on('click', '[data-action="delete"]', this.confirmDelete);
        
        // 批量操作
        $(document).on('change', '.check-all', this.toggleCheckAll);
        $(document).on('click', '.batch-action', this.batchAction);
        
        // 文件上传
        $(document).on('change', 'input[type="file"]', this.handleFileUpload);
        
        // 表单验证
        $(document).on('submit', 'form[data-validate="true"]', this.validateForm);
        
        // 快捷键
        $(document).on('keydown', this.handleShortcuts);
        
        // 图片预览
        $(document).on('change', 'input[type="file"][accept*="image"]', this.previewImages);
        
        // 富文本编辑器
        this.initRichEditor();
    },

    // 初始化组件
    initComponents: function() {
        // 初始化工具提示
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        // 初始化下拉菜单
        $('.dropdown-toggle').dropdown();
        
        // 自动隐藏提示信息
        setTimeout(function() {
            $('.alert[data-auto-dismiss="true"]').fadeOut();
        }, 5000);
    },

    // 显示提示信息
    showAlert: function(type, message, autoHide = true) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${this.getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // 插入到页面顶部
        $('.content-area').prepend(alertHtml);
        
        // 自动隐藏
        if (autoHide) {
            setTimeout(function() {
                $('.alert').first().fadeOut();
            }, 5000);
        }
    },

    // 获取提示图标
    getAlertIcon: function(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    },

    // 确认删除
    confirmDelete: function(e) {
        e.preventDefault();
        
        const target = $(this);
        const url = target.data('url') || target.attr('href');
        const message = target.data('message') || '确定要删除这个项目吗？此操作不可撤销。';
        
        $('#deleteMessage').text(message);
        $('#confirmDeleteModal').modal('show');
        
        $('#confirmDeleteBtn').off('click').on('click', function() {
            Admin.performDelete(url);
        });
    },

    // 执行删除
    performDelete: function(url) {
        $('#confirmDeleteModal').modal('hide');
        this.showLoading('删除中...');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                action: 'delete',
                csrf_token: window.AdminConfig.csrfToken
            },
            dataType: 'json',
            success: function(response) {
                Admin.hideLoading();
                if (response.success) {
                    Admin.showAlert('success', response.message || '删除成功');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    Admin.showAlert('danger', response.message || '删除失败');
                }
            },
            error: function() {
                Admin.hideLoading();
                Admin.showAlert('danger', '网络错误，删除失败');
            }
        });
    },

    // 批量选择
    toggleCheckAll: function() {
        const checked = $(this).prop('checked');
        $(this).closest('table').find('input[type="checkbox"]:not(.check-all)').prop('checked', checked);
        Admin.updateBatchActions();
    },

    // 更新批量操作状态
    updateBatchActions: function() {
        const checkedCount = $('input[type="checkbox"]:checked:not(.check-all)').length;
        if (checkedCount > 0) {
            $('.batch-actions').show();
            $('.batch-count').text(checkedCount);
        } else {
            $('.batch-actions').hide();
        }
    },

    // 批量操作
    batchAction: function(e) {
        e.preventDefault();
        
        const action = $(this).data('action');
        const checkedIds = [];
        
        $('input[type="checkbox"]:checked:not(.check-all)').each(function() {
            checkedIds.push($(this).val());
        });
        
        if (checkedIds.length === 0) {
            Admin.showAlert('warning', '请选择要操作的项目');
            return;
        }
        
        const confirmMessage = `确定要对选中的 ${checkedIds.length} 个项目执行"${$(this).text()}"操作吗？`;
        
        if (confirm(confirmMessage)) {
            Admin.showLoading('处理中...');
            
            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    action: 'batch_' + action,
                    ids: checkedIds,
                    csrf_token: window.AdminConfig.csrfToken
                },
                dataType: 'json',
                success: function(response) {
                    Admin.hideLoading();
                    if (response.success) {
                        Admin.showAlert('success', response.message || '操作完成');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        Admin.showAlert('danger', response.message || '操作失败');
                    }
                },
                error: function() {
                    Admin.hideLoading();
                    Admin.showAlert('danger', '网络错误，操作失败');
                }
            });
        }
    },

    // 文件上传处理
    handleFileUpload: function() {
        const files = this.files;
        const maxSize = $(this).data('max-size') || 5 * 1024 * 1024; // 5MB
        const allowedTypes = $(this).data('allowed-types') || 'image/*';
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // 检查文件大小
            if (file.size > maxSize) {
                Admin.showAlert('warning', `文件 ${file.name} 超过大小限制`);
                $(this).val('');
                return;
            }
            
            // 检查文件类型
            if (allowedTypes !== '*' && !file.type.match(allowedTypes)) {
                Admin.showAlert('warning', `文件 ${file.name} 类型不支持`);
                $(this).val('');
                return;
            }
        }
    },

    // 图片预览
    previewImages: function() {
        const files = this.files;
        const previewContainer = $(this).data('preview') || '.image-preview-container';
        
        $(previewContainer).empty();
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewHtml = `
                    <div class="image-preview">
                        <img src="${e.target.result}" alt="预览">
                        <button type="button" class="remove-btn" onclick="Admin.removePreview(this)">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                $(previewContainer).append(previewHtml);
            };
            
            reader.readAsDataURL(file);
        }
    },

    // 移除预览
    removePreview: function(btn) {
        $(btn).closest('.image-preview').remove();
    },

    // 表单验证
    validateForm: function(e) {
        const form = $(this);
        let isValid = true;
        
        // 清除之前的错误提示
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // 验证必填字段
        form.find('[required]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                Admin.showFieldError(field, '此字段为必填项');
                isValid = false;
            }
        });
        
        // 验证邮箱格式
        form.find('input[type="email"]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (value && !Admin.isValidEmail(value)) {
                Admin.showFieldError(field, '请输入有效的邮箱地址');
                isValid = false;
            }
        });
        
        // 验证手机号格式
        form.find('input[data-type="phone"]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (value && !Admin.isValidPhone(value)) {
                Admin.showFieldError(field, '请输入有效的手机号码');
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Admin.showAlert('warning', '请检查表单中的错误信息');
        }
        
        return isValid;
    },

    // 显示字段错误
    showFieldError: function(field, message) {
        field.addClass('is-invalid');
        field.after(`<div class="invalid-feedback">${message}</div>`);
    },

    // 验证邮箱
    isValidEmail: function(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },

    // 验证手机号
    isValidPhone: function(phone) {
        const regex = /^1[3-9]\d{9}$/;
        return regex.test(phone);
    },

    // 显示加载
    showLoading: function(text = '加载中...') {
        $('#loadingText').text(text);
        $('#loadingModal').modal('show');
    },

    // 隐藏加载
    hideLoading: function() {
        $('#loadingModal').modal('hide');
    },

    // 快捷键处理
    handleShortcuts: function(e) {
        // Ctrl+S 保存
        if (e.ctrlKey && e.keyCode === 83) {
            e.preventDefault();
            const form = $('form').first();
            if (form.length) {
                form.submit();
            }
        }
        
        // Escape 取消
        if (e.keyCode === 27) {
            $('.modal').modal('hide');
        }
    },

    // 初始化富文本编辑器
    initRichEditor: function() {
        // 这里可以集成富文本编辑器，如 TinyMCE 或 Quill
        $('.rich-editor').each(function() {
            // 示例：可以在这里初始化富文本编辑器
            console.log('初始化富文本编辑器:', this);
        });
    },

    // 数据表格增强
    enhanceDataTable: function() {
        $('.data-table').each(function() {
            const table = $(this);
            
            // 添加搜索功能
            if (table.data('search') !== false) {
                Admin.addTableSearch(table);
            }
            
            // 添加排序功能
            if (table.data('sort') !== false) {
                Admin.addTableSort(table);
            }
        });
    },

    // 添加表格搜索
    addTableSearch: function(table) {
        const searchHtml = `
            <div class="table-search mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="搜索...">
                </div>
            </div>
        `;
        
        table.before(searchHtml);
        
        // 绑定搜索事件
        table.prev('.table-search').find('input').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            table.find('tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    },

    // 添加表格排序
    addTableSort: function(table) {
        table.find('thead th[data-sort]').each(function() {
            $(this).addClass('sortable').append(' <i class="bi bi-arrow-down-up"></i>');
        });
        
        table.on('click', 'thead th.sortable', function() {
            const column = $(this).data('sort');
            Admin.sortTable(table, column, $(this));
        });
    },

    // 表格排序
    sortTable: function(table, column, header) {
        const rows = table.find('tbody tr').get();
        const isAsc = header.hasClass('asc');
        
        rows.sort(function(a, b) {
            const aVal = $(a).children().eq(header.index()).text();
            const bVal = $(b).children().eq(header.index()).text();
            
            if (isAsc) {
                return aVal > bVal ? -1 : 1;
            } else {
                return aVal < bVal ? -1 : 1;
            }
        });
        
        // 更新排序状态
        table.find('thead th').removeClass('asc desc');
        header.addClass(isAsc ? 'desc' : 'asc');
        
        // 重新排列行
        $.each(rows, function(index, row) {
            table.children('tbody').append(row);
        });
    },

    // AJAX表单提交
    submitForm: function(form, callback) {
        const formData = new FormData(form[0]);
        formData.append('csrf_token', window.AdminConfig.csrfToken);
        
        $.ajax({
            url: form.attr('action') || window.location.href,
            method: form.attr('method') || 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (callback) {
                    callback(response);
                } else {
                    if (response.success) {
                        Admin.showAlert('success', response.message || '操作成功');
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1500);
                        }
                    } else {
                        Admin.showAlert('danger', response.message || '操作失败');
                    }
                }
            },
            error: function() {
                if (callback) {
                    callback({ success: false, message: '网络错误' });
                } else {
                    Admin.showAlert('danger', '网络错误，请稍后重试');
                }
            }
        });
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
        
        // 格式化日期
        formatDate: function(dateString) {
            const date = new Date(dateString);
            return date.getFullYear() + '-' + 
                   String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                   String(date.getDate()).padStart(2, '0');
        },
        
        // 生成随机字符串
        randomString: function(length = 8) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
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

// 页面加载完成后自动初始化
$(document).ready(function() {
    Admin.init();
    Admin.enhanceDataTable();
});

// 导出到全局作用域
window.Admin = Admin;