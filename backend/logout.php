<?php
/**
 * 退出登录
 * 阔文展览后台管理系统 - 最终版本
 */

session_start();
require_once 'config/database.php';
require_once 'config/security.php';

// 记录登出日志
if (isset($_SESSION['admin_user_id']) && isset($_SESSION['admin_username'])) {
    Security::logOperation('logout', 'system', null, '用户退出登录');
}

// 清除所有会话数据
session_unset();
session_destroy();

// 清除cookie（如果有的话）
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 重定向到登录页面
header('Location: index.php?logout=1');
exit;
?>
