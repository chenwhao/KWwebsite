<?php
/**
 * 阔文展览后台管理系统 - 登录页面
 * 最终版本 - 2025年6月27日 (包含跳转问题调试)
 */

session_start();
require_once 'config/database.php';
require_once 'config/security.php';

// 如果已经登录，直接跳转到仪表板
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // 【调试代码】检查在跳转前是否有任何输出
    if (headers_sent($file, $line)) {
        die("调试信息：无法跳转。在文件 '{$file}' 的第 {$line} 行已有内容输出。请检查该文件及其包含的文件（如 config/*.php）的开头和结尾是否有空行或空格。");
    }
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = '请输入用户名和密码';
    } else {
        try {
            // 查询用户
            $stmt = getDB()->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // 登录成功
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_name'] = $user['name'];
                
                // 更新最后登录时间
                $stmt = getDB()->prepare("UPDATE admin_users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?");
                $stmt->execute([$_SERVER['REMOTE_ADDR'], $user['id']]);
                
                // 记录登录日志
                $stmt = getDB()->prepare("INSERT INTO login_logs (user_id, username, login_status, ip_address, user_agent) VALUES (?, ?, 'success', ?, ?)");
                $stmt->execute([$user['id'], $username, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                
                // 【调试代码】检查在跳转前是否有任何输出
                if (headers_sent($file, $line)) {
                    die("调试信息：无法跳转。在文件 '{$file}' 的第 {$line} 行已有内容输出。请检查该文件及其包含的文件（如 config/*.php）的开头和结尾是否有空行或空格。");
                }
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = '用户名或密码错误';
                // 记录失败日志
                $stmt = getDB()->prepare("INSERT INTO login_logs (username, login_status, failure_reason, ip_address, user_agent) VALUES (?, 'failed', ?, ?, ?)");
                $stmt->execute([$username, $error_message, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
            }
        } catch (Exception $e) {
            error_log("登录错误: " . $e->getMessage());
            $error_message = '系统错误，请稍后重试';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 阔文展览后台管理系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Microsoft YaHei', sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding-top: 10vh;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            border-radius: 15px 15px 0 0;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .form-floating label {
            color: #6c757d;
        }
        .btn-login {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .footer-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo">
                        <i class="bi bi-building fs-2"></i>
                    </div>
                    <h4 class="mb-0">阔文展览</h4>
                    <p class="mb-0 opacity-75">后台管理系统</p>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="用户名" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                            <label for="username">用户名</label>
                        </div>
                        
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="密码" required>
                            <label for="password">密码</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            登录系统
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            系统受到安全保护
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="footer-text">
                <p>&copy; 2025 上海阔文展览展示服务有限公司</p>
                <p>专业展览，精彩展示</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
