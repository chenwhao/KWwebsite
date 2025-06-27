            </div>
            <!-- 页面内容结束 -->
            
            <!-- 页脚 -->
            <footer class="bg-light border-top py-3 mt-auto">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                &copy; <?php echo date('Y'); ?> 上海阔文展览展示服务有限公司. 保留所有权利.
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                后台管理系统 v1.0
                                <span class="mx-2">|</span>
                                在线时间: <span id="online-time"><?php echo timeAgo($_SESSION['login_time'] ?? time()); ?></span>
                            </small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- 全局加载提示 -->
    <div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary me-2" role="status">
                        <span class="visually-hidden">加载中...</span>
                    </div>
                    <span>处理中，请稍候...</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 确认删除模态框 -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">确认删除</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">您确定要删除这条记录吗？此操作无法撤销。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="/admin/assets/js/admin.js"></script>
    
    <!-- 额外的JS文件 -->
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // 全局变量
        window.adminConfig = {
            baseUrl: '/admin',
            csrfToken: '<?php echo Security::generateCSRFToken(); ?>',
            currentUser: <?php echo json_encode(Security::getCurrentUser()); ?>
        };
        
        // 菜单切换
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('toggled');
        });
        
        // 自动隐藏alert消息
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
        
        // 更新在线时间
        function updateOnlineTime() {
            const loginTime = <?php echo $_SESSION['login_time'] ?? time(); ?>;
            const now = Math.floor(Date.now() / 1000);
            const onlineSeconds = now - loginTime;
            
            let timeStr = '';
            if (onlineSeconds < 60) {
                timeStr = onlineSeconds + '秒';
            } else if (onlineSeconds < 3600) {
                timeStr = Math.floor(onlineSeconds / 60) + '分钟';
            } else {
                const hours = Math.floor(onlineSeconds / 3600);
                const minutes = Math.floor((onlineSeconds % 3600) / 60);
                timeStr = hours + '小时' + (minutes > 0 ? minutes + '分钟' : '');
            }
            
            const onlineTimeElement = document.getElementById('online-time');
            if (onlineTimeElement) {
                onlineTimeElement.textContent = timeStr;
            }
        }
        
        // 每分钟更新一次在线时间
        setInterval(updateOnlineTime, 60000);
        updateOnlineTime();
    </script>
</body>
</html>
