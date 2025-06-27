<?php
/**
 * 用户登出
 * 阔文展览后台管理系统
 */

require_once __DIR__ . '/includes/functions.php';

// 记录登出操作
if (Security::isLoggedIn()) {
    $user = Security::getCurrentUser();
    Security::logOperation('logout', 'admin_users', $user['id'], '用户登出');
}

// 执行登出
Security::logout();

// 跳转到登录页面
header('Location: /admin/index.php');
exit;
?>