                </div>
                <!-- 内容区域结束 -->
                
                <!-- 页脚 -->
                <footer class="text-center py-3 border-top bg-light">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 text-start">
                                <small class="text-muted">
                                    &copy; 2025 上海阔文展览展示服务有限公司 - 后台管理系统
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    在线时间: <span id="onlineTime">00:00:00</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    
    <!-- 加载提示模态框 -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">加载中...</span>
                    </div>
                    <div class="mt-3">
                        <span id="loadingText">处理中，请稍候...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 确认删除模态框 -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        确认删除
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteMessage">确定要删除这个项目吗？此操作不可撤销。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- 自定义JS -->
    <script src="assets/js/admin.js"></script>
    
    <script>
        // 全局配置
        window.AdminConfig = {
            baseUrl: '',
            csrfToken: '<?= Security::generateCSRFToken() ?>',
            userId: <?= $_SESSION['admin_user_id'] ?>,
            username: '<?= htmlspecialchars($_SESSION['admin_username']) ?>'
        };
        
        // 页面加载完成后初始化
        $(document).ready(function() {
            if (typeof Admin !== 'undefined') {
                Admin.init();
            }
            
            // 初始化提示信息自动隐藏
            $('.alert').each(function() {
                const alert = this;
                setTimeout(function() {
                    $(alert).fadeOut();
                }, 5000);
            });
            
            // 在线时间计时器
            let startTime = new Date().getTime();
            setInterval(function() {
                let currentTime = new Date().getTime();
                let onlineTime = Math.floor((currentTime - startTime) / 1000);
                let hours = Math.floor(onlineTime / 3600);
                let minutes = Math.floor((onlineTime % 3600) / 60);
                let seconds = onlineTime % 60;
                
                $('#onlineTime').text(
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0')
                );
            }, 1000);
        });
        
        // 修改密码功能
        function changePassword() {
            // 这里可以添加修改密码的模态框或跳转
            alert('修改密码功能开发中...');
        }
        
        // 全局AJAX错误处理
        $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
            console.error('AJAX错误:', thrownError);
            
            if (jqXHR.status === 401) {
                alert('登录已过期，请重新登录');
                window.location.href = 'index.php';
            } else if (jqXHR.status === 403) {
                alert('权限不足');
            } else if (jqXHR.status === 500) {
                alert('服务器错误，请稍后重试');
            }
        });
        
        // 全局表单提交处理
        $('form[data-ajax="true"]').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.text();
            
            // 显示加载状态
            submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>提交中...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method') || 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Admin.showAlert('success', response.message || '操作成功');
                        
                        // 如果有重定向URL，则跳转
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1500);
                        }
                    } else {
                        Admin.showAlert('danger', response.message || '操作失败');
                    }
                },
                error: function() {
                    Admin.showAlert('danger', '网络错误，请稍后重试');
                },
                complete: function() {
                    // 恢复按钮状态
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    </script>
</body>
</html>
