<?php
/**
 * 安全配置文件
 * 阔文展览后台管理系统
 */

// 会话安全设置
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);

// 安全常量
define('CSRF_TOKEN_NAME', '_csrf_token');
define('SESSION_TIMEOUT', 3600); // 1小时
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15分钟

/**
 * 安全工具类
 */
class Security {
    
    /**
     * 生成CSRF令牌
     */
    public static function generateCSRFToken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * 验证CSRF令牌
     */
    public static function verifyCSRFToken($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION[CSRF_TOKEN_NAME]) && 
               hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * 清理输入数据，防止XSS
     */
    public static function cleanInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'cleanInput'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
    
    /**
     * 验证邮箱格式
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * 验证密码强度
     */
    public static function validatePassword($password) {
        // 至少8位，包含字母、数字
        return strlen($password) >= 8 && 
               preg_match('/[A-Za-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
    
    /**
     * 生成安全的密码哈希
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    /**
     * 验证密码
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * 检查用户是否已登录
     */
    public static function isLoggedIn() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_user_id']) && 
               isset($_SESSION['admin_username']) &&
               self::isSessionValid();
    }
    
    /**
     * 检查会话是否有效
     */
    public static function isSessionValid() {
        if (!isset($_SESSION['last_activity'])) {
            return false;
        }
        
        // 检查会话超时
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            self::logout();
            return false;
        }
        
        // 更新最后活动时间
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * 登录用户
     */
    public static function loginUser($user) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // 重新生成会话ID防止会话固定攻击
        session_regenerate_id(true);
        
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        
        // 记录登录日志
        self::logLogin($user['id'], $user['username'], 'success');
    }
    
    /**
     * 登出用户
     */
    public static function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        // 删除会话cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * 获取当前用户信息
     */
    public static function getCurrentUser() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['admin_user_id'],
            'username' => $_SESSION['admin_username'],
            'role' => $_SESSION['admin_role'],
            'email' => $_SESSION['admin_email']
        ];
    }
    
    /**
     * 检查用户权限
     */
    public static function hasPermission($required_role = 'admin') {
        $user = self::getCurrentUser();
        if (!$user) {
            return false;
        }
        
        $roles = ['admin' => 1, 'super_admin' => 2];
        $user_level = $roles[$user['role']] ?? 0;
        $required_level = $roles[$required_role] ?? 1;
        
        return $user_level >= $required_level;
    }
    
    /**
     * 检查登录尝试次数
     */
    public static function checkLoginAttempts($username, $ip) {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                SELECT COUNT(*) as attempts 
                FROM login_logs 
                WHERE (username = ? OR ip_address = ?) 
                AND login_status = 'failed' 
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$username, $ip, LOGIN_LOCKOUT_TIME]);
            $result = $stmt->fetch();
            
            return $result['attempts'] < MAX_LOGIN_ATTEMPTS;
        } catch (Exception $e) {
            error_log("检查登录尝试次数失败: " . $e->getMessage());
            return true; // 如果查询失败，允许登录
        }
    }
    
    /**
     * 记录登录日志
     */
    public static function logLogin($user_id, $username, $status, $failure_reason = null) {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO login_logs (user_id, username, ip_address, user_agent, login_status, failure_reason) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $username,
                self::getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $status,
                $failure_reason
            ]);
        } catch (Exception $e) {
            error_log("记录登录日志失败: " . $e->getMessage());
        }
    }
    
    /**
     * 记录操作日志
     */
    public static function logOperation($action, $table_name = null, $record_id = null, $description = null, $old_data = null, $new_data = null) {
        try {
            $user = self::getCurrentUser();
            if (!$user) return;
            
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO operation_logs (user_id, action, table_name, record_id, description, old_data, new_data, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user['id'],
                $action,
                $table_name,
                $record_id,
                $description,
                $old_data ? json_encode($old_data) : null,
                $new_data ? json_encode($new_data) : null,
                self::getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log("记录操作日志失败: " . $e->getMessage());
        }
    }
    
    /**
     * 获取客户端IP地址
     */
    public static function getClientIP() {
        $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * 验证文件上传安全性
     */
    public static function validateFileUpload($file) {
        $errors = [];
        
        // 检查文件是否上传成功
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = '文件上传失败';
            return $errors;
        }
        
        // 检查文件大小
        $max_size = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $max_size) {
            $errors[] = '文件大小超过限制';
        }
        
        // 检查文件类型
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword'];
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = '不支持的文件类型';
        }
        
        // 检查文件扩展名
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = '不支持的文件扩展名';
        }
        
        return $errors;
    }
    
    /**
     * 生成安全的文件名
     */
    public static function generateSecureFilename($original_name) {
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;
        return $filename;
    }
}

/**
 * 权限检查中间件
 */
function requireLogin() {
    if (!Security::isLoggedIn()) {
        header('Location: /admin/index.php');
        exit;
    }
}

function requirePermission($role = 'admin') {
    requireLogin();
    if (!Security::hasPermission($role)) {
        header('HTTP/1.1 403 Forbidden');
        die('权限不足');
    }
}

/**
 * CSRF保护中间件
 */
function requireCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Security::verifyCSRFToken($token)) {
            header('HTTP/1.1 403 Forbidden');
            die('CSRF验证失败');
        }
    }
}
?>