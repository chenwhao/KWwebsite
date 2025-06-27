<?php
/**
 * 通用函数库
 * 阔文展览后台管理系统
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

/**
 * 响应处理函数
 */
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function successResponse($message = '操作成功', $data = null) {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

function errorResponse($message = '操作失败', $code = 400) {
    jsonResponse([
        'success' => false,
        'message' => $message
    ], $code);
}

/**
 * 分页函数
 */
function paginate($table, $conditions = [], $params = [], $page = 1, $perPage = 10, $orderBy = 'id DESC') {
    try {
        $offset = ($page - 1) * $perPage;
        
        // 构建查询条件
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        // 获取总记录数
        $countSql = "SELECT COUNT(*) as total FROM {$table} {$whereClause}";
        $totalResult = fetchOne($countSql, $params);
        $total = $totalResult['total'];
        
        // 获取分页数据
        $dataSql = "SELECT * FROM {$table} {$whereClause} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $data = fetchAll($dataSql, $params);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'has_prev' => $page > 1,
                'has_next' => $page < ceil($total / $perPage)
            ]
        ];
    } catch (Exception $e) {
        error_log("分页查询失败: " . $e->getMessage());
        return [
            'data' => [],
            'pagination' => [
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'total_pages' => 0,
                'has_prev' => false,
                'has_next' => false
            ]
        ];
    }
}

/**
 * 文件上传函数
 */
function uploadFile($file, $category = 'general') {
    // 验证文件
    $errors = Security::validateFileUpload($file);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode(', ', $errors)];
    }
    
    // 确定上传目录
    $uploadDir = __DIR__ . '/../uploads/';
    if ($category === 'images') {
        $uploadDir .= 'images/';
    } elseif ($category === 'documents') {
        $uploadDir .= 'documents/';
    } else {
        $uploadDir .= 'general/';
    }
    
    // 创建目录
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // 生成安全文件名
    $filename = Security::generateSecureFilename($file['name']);
    $filepath = $uploadDir . $filename;
    
    // 移动文件
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // 获取文件信息
        $fileInfo = [
            'filename' => $filename,
            'original_name' => $file['name'],
            'file_path' => str_replace(__DIR__ . '/../', '', $filepath),
            'file_size' => $file['size'],
            'mime_type' => $file['type'],
            'file_type' => getFileType($file['type']),
            'category' => $category
        ];
        
        // 如果是图片，获取尺寸
        if (strpos($file['type'], 'image/') === 0) {
            $imageInfo = getimagesize($filepath);
            if ($imageInfo) {
                $fileInfo['width'] = $imageInfo[0];
                $fileInfo['height'] = $imageInfo[1];
            }
        }
        
        // 记录到数据库
        try {
            $user = Security::getCurrentUser();
            $sql = "INSERT INTO files (filename, original_name, file_path, file_size, mime_type, file_type, category, width, height, uploaded_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $fileId = insertRecord($sql, [
                $fileInfo['filename'],
                $fileInfo['original_name'],
                $fileInfo['file_path'],
                $fileInfo['file_size'],
                $fileInfo['mime_type'],
                $fileInfo['file_type'],
                $fileInfo['category'],
                $fileInfo['width'] ?? null,
                $fileInfo['height'] ?? null,
                $user['id'] ?? null
            ]);
            
            $fileInfo['id'] = $fileId;
            
            return ['success' => true, 'file' => $fileInfo];
        } catch (Exception $e) {
            // 删除已上传的文件
            unlink($filepath);
            error_log("文件信息保存失败: " . $e->getMessage());
            return ['success' => false, 'message' => '文件保存失败'];
        }
    }
    
    return ['success' => false, 'message' => '文件上传失败'];
}

/**
 * 获取文件类型
 */
function getFileType($mimeType) {
    $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $documentTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $videoTypes = ['video/mp4', 'video/avi', 'video/mov'];
    $audioTypes = ['audio/mp3', 'audio/wav', 'audio/ogg'];
    
    if (in_array($mimeType, $imageTypes)) {
        return 'image';
    } elseif (in_array($mimeType, $documentTypes)) {
        return 'document';
    } elseif (in_array($mimeType, $videoTypes)) {
        return 'video';
    } elseif (in_array($mimeType, $audioTypes)) {
        return 'audio';
    }
    
    return 'other';
}

/**
 * 删除文件
 */
function deleteFile($fileId) {
    try {
        // 获取文件信息
        $file = fetchOne("SELECT * FROM files WHERE id = ?", [$fileId]);
        if (!$file) {
            return ['success' => false, 'message' => '文件不存在'];
        }
        
        // 删除物理文件
        $filepath = __DIR__ . '/../' . $file['file_path'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // 删除数据库记录
        updateRecord("DELETE FROM files WHERE id = ?", [$fileId]);
        
        return ['success' => true, 'message' => '文件删除成功'];
    } catch (Exception $e) {
        error_log("删除文件失败: " . $e->getMessage());
        return ['success' => false, 'message' => '删除文件失败'];
    }
}

/**
 * 数据验证函数
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $errors[] = "{$field} 是必填字段";
        }
    }
    return $errors;
}

function validateLength($value, $min = 0, $max = 255) {
    $length = mb_strlen($value, 'UTF-8');
    return $length >= $min && $length <= $max;
}

/**
 * 字符串处理函数
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

function generateSlug($text) {
    $text = trim($text);
    $text = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $text);
    $text = preg_replace('/[\s\-_]+/', '-', $text);
    $text = strtolower($text);
    return $text;
}

/**
 * 日期时间处理函数
 */
function formatDateTime($datetime, $format = 'Y-m-d H:i:s') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return '刚刚';
    } elseif ($time < 3600) {
        return floor($time / 60) . '分钟前';
    } elseif ($time < 86400) {
        return floor($time / 3600) . '小时前';
    } elseif ($time < 2592000) {
        return floor($time / 86400) . '天前';
    } elseif ($time < 31536000) {
        return floor($time / 2592000) . '个月前';
    } else {
        return floor($time / 31536000) . '年前';
    }
}

/**
 * 统计数据函数
 */
function getDashboardStats() {
    try {
        $stats = [];
        
        // 案例数量
        $caseCount = fetchOne("SELECT COUNT(*) as count FROM cases WHERE status = 'active'");
        $stats['cases'] = $caseCount['count'];
        
        // 新闻数量
        $newsCount = fetchOne("SELECT COUNT(*) as count FROM news WHERE status = 'published'");
        $stats['news'] = $newsCount['count'];
        
        // 留言数量
        $messageCount = fetchOne("SELECT COUNT(*) as count FROM messages WHERE status = 'pending'");
        $stats['messages'] = $messageCount['count'];
        
        // 文件数量
        $fileCount = fetchOne("SELECT COUNT(*) as count FROM files");
        $stats['files'] = $fileCount['count'];
        
        // 今日访问量（这里需要集成访问统计）
        $stats['visits_today'] = 0;
        
        return $stats;
    } catch (Exception $e) {
        error_log("获取统计数据失败: " . $e->getMessage());
        return [
            'cases' => 0,
            'news' => 0,
            'messages' => 0,
            'files' => 0,
            'visits_today' => 0
        ];
    }
}

/**
 * 获取最新操作记录
 */
function getRecentOperations($limit = 10) {
    try {
        $sql = "SELECT ol.*, au.username 
                FROM operation_logs ol 
                LEFT JOIN admin_users au ON ol.user_id = au.id 
                ORDER BY ol.created_at DESC 
                LIMIT ?";
        return fetchAll($sql, [$limit]);
    } catch (Exception $e) {
        error_log("获取操作记录失败: " . $e->getMessage());
        return [];
    }
}

/**
 * 系统设置函数
 */
function getSetting($key, $default = null) {
    try {
        $setting = fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $setting ? $setting['setting_value'] : $default;
    } catch (Exception $e) {
        error_log("获取设置失败: " . $e->getMessage());
        return $default;
    }
}

function setSetting($key, $value, $description = null) {
    try {
        $user = Security::getCurrentUser();
        $existing = fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        
        if ($existing) {
            updateRecord(
                "UPDATE settings SET setting_value = ?, updated_by = ? WHERE setting_key = ?",
                [$value, $user['id'] ?? null, $key]
            );
        } else {
            insertRecord(
                "INSERT INTO settings (setting_key, setting_value, description, updated_by) VALUES (?, ?, ?, ?)",
                [$key, $value, $description, $user['id'] ?? null]
            );
        }
        
        return true;
    } catch (Exception $e) {
        error_log("设置保存失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 数据备份函数
 */
function backupDatabase() {
    try {
        $backupDir = __DIR__ . '/../uploads/backups/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filename = 'backup_' . date('YmdHis') . '.sql';
        $filepath = $backupDir . $filename;
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            DB_USER,
            DB_PASS,
            DB_HOST,
            DB_NAME,
            $filepath
        );
        
        $output = null;
        $return_var = null;
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            return ['success' => true, 'file' => $filename];
        } else {
            return ['success' => false, 'message' => '备份执行失败'];
        }
    } catch (Exception $e) {
        error_log("数据库备份失败: " . $e->getMessage());
        return ['success' => false, 'message' => '备份失败'];
    }
}

/**
 * 清理过期日志
 */
function cleanupLogs() {
    try {
        $retentionDays = getSetting('log_retention_days', 30);
        
        // 清理操作日志
        updateRecord(
            "DELETE FROM operation_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$retentionDays]
        );
        
        // 清理登录日志
        updateRecord(
            "DELETE FROM login_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$retentionDays]
        );
        
        return true;
    } catch (Exception $e) {
        error_log("清理日志失败: " . $e->getMessage());
        return false;
    }
}
?>