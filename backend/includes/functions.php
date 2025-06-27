<?php
/**
 * 通用函数库
 * 阔文展览后台管理系统 - 最终版本
 */

/**
 * JSON响应处理
 */
function jsonResponse($success, $message = '', $data = null, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function successResponse($message = '操作成功', $data = null) {
    jsonResponse(true, $message, $data);
}

function errorResponse($message = '操作失败', $code = 400) {
    jsonResponse(false, $message, null, $code);
}

/**
 * 分页处理
 */
function paginate($table, $conditions = '', $params = [], $page = 1, $limit = 10) {
    // 计算总数
    $count_sql = "SELECT COUNT(*) as total FROM {$table}";
    if ($conditions) {
        $count_sql .= " WHERE " . $conditions;
    }
    
    $total = fetchOne($count_sql, $params)['total'];
    $total_pages = ceil($total / $limit);
    $offset = ($page - 1) * $limit;
    
    // 获取数据
    $data_sql = "SELECT * FROM {$table}";
    if ($conditions) {
        $data_sql .= " WHERE " . $conditions;
    }
    $data_sql .= " LIMIT {$limit} OFFSET {$offset}";
    
    $data = fetchAll($data_sql, $params);
    
    return [
        'data' => $data,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total,
            'per_page' => $limit,
            'has_next' => $page < $total_pages,
            'has_prev' => $page > 1
        ]
    ];
}

/**
 * 文件上传处理
 */
function uploadFile($file, $upload_dir = 'uploads/images/', $allowed_types = null) {
    if (!Security::validateUpload($file)) {
        throw new Exception('文件类型不允许或文件过大');
    }
    
    $upload_path = __DIR__ . '/../' . $upload_dir;
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    $filename = Security::generateSafeFilename($file['name']);
    $file_path = $upload_path . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('文件上传失败');
    }
    
    return $upload_dir . $filename;
}

/**
 * 获取文件类型
 */
function getFileType($file_path) {
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    
    $image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $document_types = ['pdf', 'doc', 'docx'];
    
    if (in_array($extension, $image_types)) {
        return 'image';
    } elseif (in_array($extension, $document_types)) {
        return 'document';
    }
    
    return 'other';
}

/**
 * 删除文件
 */
function deleteFile($file_path) {
    $full_path = __DIR__ . '/../' . $file_path;
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    return true;
}

/**
 * 数据验证
 */
function validateRequired($data, $required_fields) {
    $missing = [];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    return $missing;
}

function validateLength($value, $min = 0, $max = 255) {
    $length = mb_strlen($value, 'UTF-8');
    return $length >= $min && $length <= $max;
}

/**
 * 字符串处理
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

function generateSlug($text) {
    $text = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $text);
    $text = trim($text, '-');
    return mb_strtolower($text, 'UTF-8');
}

/**
 * 日期时间处理
 */
function formatDateTime($datetime, $format = 'Y-m-d H:i:s') {
    if (!$datetime) return '';
    return date($format, strtotime($datetime));
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return '刚刚';
    if ($time < 3600) return floor($time / 60) . '分钟前';
    if ($time < 86400) return floor($time / 3600) . '小时前';
    if ($time < 2592000) return floor($time / 86400) . '天前';
    if ($time < 31104000) return floor($time / 2592000) . '个月前';
    
    return floor($time / 31104000) . '年前';
}

/**
 * 仪表板统计数据
 */
function getDashboardStats() {
    try {
        $stats = [];
        
        // 案例统计
        $stats['cases'] = [
            'total' => fetchOne("SELECT COUNT(*) as count FROM cases")['count'],
            'active' => fetchOne("SELECT COUNT(*) as count FROM cases WHERE status = 'active'")['count'],
            'recent' => fetchOne("SELECT COUNT(*) as count FROM cases WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['count']
        ];
        
        // 新闻统计
        $stats['news'] = [
            'total' => fetchOne("SELECT COUNT(*) as count FROM news")['count'],
            'published' => fetchOne("SELECT COUNT(*) as count FROM news WHERE status = 'published'")['count'],
            'recent' => fetchOne("SELECT COUNT(*) as count FROM news WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['count']
        ];
        
        // 留言统计
        $stats['messages'] = [
            'total' => fetchOne("SELECT COUNT(*) as count FROM messages")['count'],
            'pending' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'pending'")['count'],
            'recent' => fetchOne("SELECT COUNT(*) as count FROM messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['count']
        ];
        
        // 文件统计
        $stats['files'] = [
            'total' => fetchOne("SELECT COUNT(*) as count FROM files")['count'],
            'images' => fetchOne("SELECT COUNT(*) as count FROM files WHERE category = 'image'")['count'],
            'recent' => fetchOne("SELECT COUNT(*) as count FROM files WHERE uploaded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['count']
        ];
        
        return $stats;
    } catch (Exception $e) {
        error_log("获取统计数据失败: " . $e->getMessage());
        return [
            'cases' => ['total' => 0, 'active' => 0, 'recent' => 0],
            'news' => ['total' => 0, 'published' => 0, 'recent' => 0],
            'messages' => ['total' => 0, 'pending' => 0, 'recent' => 0],
            'files' => ['total' => 0, 'images' => 0, 'recent' => 0]
        ];
    }
}

/**
 * 获取最新操作记录
 */
function getRecentOperations($limit = 10) {
    try {
        return fetchAll(
            "SELECT * FROM operation_logs ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    } catch (Exception $e) {
        error_log("获取操作记录失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 系统设置
 */
function getSetting($key, $default = '') {
    try {
        $result = fetchOne("SELECT value FROM settings WHERE `key` = ?", [$key]);
        return $result ? $result['value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function setSetting($key, $value, $description = '') {
    try {
        $existing = fetchOne("SELECT id FROM settings WHERE `key` = ?", [$key]);
        
        if ($existing) {
            updateRecord(
                "UPDATE settings SET value = ?, updated_at = NOW() WHERE `key` = ?",
                [$value, $key]
            );
        } else {
            insertRecord(
                "INSERT INTO settings (`key`, value, description) VALUES (?, ?, ?)",
                [$key, $value, $description]
            );
        }
        
        return true;
    } catch (Exception $e) {
        error_log("设置保存失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 数据库备份
 */
function backupDatabase() {
    try {
        $backup_dir = __DIR__ . '/../uploads/backups/';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        $filename = 'backup_' . date('YmdHis') . '.sql';
        $file_path = $backup_dir . $filename;
        
        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s',
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            $file_path
        );
        
        exec($command, $output, $return_code);
        
        if ($return_code === 0) {
            return $filename;
        } else {
            throw new Exception('备份命令执行失败');
        }
    } catch (Exception $e) {
        error_log("数据库备份失败: " . $e->getMessage());
        throw $e;
    }
}

/**
 * 清理日志
 */
function cleanupLogs($days = 30) {
    try {
        $affected_rows = 0;
        
        // 清理操作日志
        $affected_rows += updateRecord(
            "DELETE FROM operation_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$days]
        );
        
        // 清理登录日志
        $affected_rows += updateRecord(
            "DELETE FROM login_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$days]
        );
        
        return $affected_rows;
    } catch (Exception $e) {
        error_log("日志清理失败: " . $e->getMessage());
        throw $e;
    }
}

/**
 * 格式化文件大小
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * 生成面包屑导航
 */
function generateBreadcrumb($current_page, $module = '') {
    $breadcrumb = [
        ['name' => '首页', 'url' => 'dashboard.php', 'active' => false]
    ];
    
    if ($module) {
        $modules = [
            'company' => '公司管理',
            'services' => '服务管理', 
            'cases' => '案例管理',
            'news' => '新闻管理',
            'messages' => '留言管理',
            'files' => '文件管理',
            'users' => '用户管理'
        ];
        
        if (isset($modules[$module])) {
            $breadcrumb[] = [
                'name' => $modules[$module],
                'url' => "modules/{$module}/index.php",
                'active' => false
            ];
        }
    }
    
    $breadcrumb[] = [
        'name' => $current_page,
        'url' => '',
        'active' => true
    ];
    
    return $breadcrumb;
}
?>
