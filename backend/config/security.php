<?php
/**
 * 安全配置文件
 * 阔文展览后台管理系统 - 最终版本
 */

// 安全设置
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // 如果使用HTTPS，设置为1

// 安全常量
define('CSRF_TOKEN_NAME', '_token');
define('SESSION_TIMEOUT', 3600); // 1小时
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15分钟

/**
 * 安全类
 */
class Security {
    
    /**
     * 生成CSRF令牌
     */
    public static function generateCSRFToken() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_NAME] = $token;
        
        return $token;
    }
    
    /**
     * 验证CSRF令牌
     */
    public static function verifyCSRFToken($token) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        return isset($_SESSION[CSRF_TOKEN_NAME]) && 
               hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * 清理输入数据
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
        // 至少8位，包含大小写字母和数字
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
    
    /**
     * 密码哈希
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * 验证密码
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * 检查登录状态
     */
    public static function checkLogin() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: index.php');
            exit;
        }
        
        // 检查会话超时
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            session_destroy();
            header('Location: index.php?timeout=1');
            exit;
        }
        
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * 记录操作日志
     */
    public static function logOperation($action, $module = '', $target_id = null, $description = '') {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            
            $user_id = $_SESSION['admin_user_id'] ?? null;
            $username = $_SESSION['admin_username'] ?? '';
            
            $sql = "INSERT INTO operation_logs (user_id, username, action, module, target_id, description, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            executeQuery($sql, [
                $user_id,
                $username, 
                $action,
                $module,
                $target_id,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log("日志记录失败: " . $e->getMessage());
        }
    }
    
    /**
     * 记录登录日志
     */
    public static function logLogin($user_id, $username, $status, $reason = '') {
        try {
            $sql = "INSERT INTO login_logs (user_id, username, login_status, failure_reason, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            executeQuery($sql, [
                $user_id,
                $username,
                $status,
                $reason,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log("登录日志记录失败: " . $e->getMessage());
        }
    }
    
    /**
     * 获取客户端IP
     */
    public static function getClientIP() {
        $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * 文件上传安全检查
     */
    public static function validateUpload($file) {
        // 允许的文件类型
        $allowed_types = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        // 允许的文件扩展名
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'];
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // 检查文件类型
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // 检查文件扩展名
        if (!in_array($file_extension, $allowed_extensions)) {
            return false;
        }
        
        // 检查文件大小 (最大10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 生成安全的文件名
     */
    public static function generateSafeFilename($original_name) {
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $filename = pathinfo($original_name, PATHINFO_FILENAME);
        
        // 清理文件名
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $filename);
        $filename = substr($filename, 0, 50); // 限制长度
        
        // 添加时间戳
        $timestamp = date('YmdHis');
        $random = bin2hex(random_bytes(4));
        
        return $filename . '_' . $timestamp . '_' . $random . '.' . $extension;
    }
}

/**
 * 权限检查中间件
 */
function requireLogin() {
    Security::checkLogin();
}

function requirePermission($permission) {
    Security::checkLogin();
    
    // 这里可以添加更细粒度的权限检查
    // 暂时所有登录用户都有权限
}

function requireCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Security::verifyCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token verification failed');
        }
    }
}
?>
