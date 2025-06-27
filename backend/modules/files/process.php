<?php
/**
 * 文件操作处理
 * 阔文展览后台管理系统
 */

require_once __DIR__ . '/../../includes/functions.php';

// 检查权限和CSRF
requireLogin();
requireCSRF();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('请求方法错误');
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'delete':
            deleteFile();
            break;
        case 'batch_delete':
            batchDeleteFiles();
            break;
        case 'update_info':
            updateFileInfo();
            break;
        default:
            errorResponse('无效的操作');
    }
} catch (Exception $e) {
    error_log("文件操作错误: " . $e->getMessage());
    errorResponse('操作失败，请重试');
}

/**
 * 删除文件
 */
function deleteFile() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的文件ID');
    }
    
    // 获取文件信息
    $file = fetchOne("SELECT * FROM files WHERE id = ?", [$id]);
    if (!$file) {
        errorResponse('文件不存在');
    }
    
    // 删除物理文件
    $filepath = __DIR__ . '/../../' . $file['file_path'];
    if (file_exists($filepath)) {
        if (!unlink($filepath)) {
            errorResponse('物理文件删除失败');
        }
    }
    
    // 删除数据库记录
    $result = updateRecord("DELETE FROM files WHERE id = ?", [$id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('delete', 'files', $id, '删除文件: ' . $file['original_name'], $file, null);
        successResponse('文件删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 批量删除文件
 */
function batchDeleteFiles() {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要删除的文件');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 获取要删除的文件信息
    $files = fetchAll("SELECT * FROM files WHERE id IN ($placeholders)", $ids);
    
    $deletedCount = 0;
    $errors = [];
    
    foreach ($files as $file) {
        try {
            // 删除物理文件
            $filepath = __DIR__ . '/../../' . $file['file_path'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // 删除数据库记录
            $result = updateRecord("DELETE FROM files WHERE id = ?", [$file['id']]);
            
            if ($result) {
                $deletedCount++;
                Security::logOperation('delete', 'files', $file['id'], '批量删除文件: ' . $file['original_name'], $file, null);
            } else {
                $errors[] = $file['original_name'] . ': 数据库删除失败';
            }
            
        } catch (Exception $e) {
            $errors[] = $file['original_name'] . ': ' . $e->getMessage();
        }
    }
    
    if ($deletedCount > 0) {
        $message = "成功删除 $deletedCount 个文件";
        if (!empty($errors)) {
            $message .= '，' . count($errors) . ' 个文件删除失败';
        }
        successResponse($message, ['errors' => $errors]);
    } else {
        errorResponse('删除失败：' . implode('; ', $errors));
    }
}

/**
 * 更新文件信息
 */
function updateFileInfo() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的文件ID');
    }
    
    // 获取原始数据
    $oldData = fetchOne("SELECT * FROM files WHERE id = ?", [$id]);
    if (!$oldData) {
        errorResponse('文件不存在');
    }
    
    // 获取并验证数据
    $data = [
        'description' => Security::cleanInput($_POST['description'] ?? ''),
        'alt_text' => Security::cleanInput($_POST['alt_text'] ?? ''),
        'category' => Security::cleanInput($_POST['category'] ?? 'general')
    ];
    
    // 更新数据
    $sql = "UPDATE files SET description = ?, alt_text = ?, category = ? WHERE id = ?";
    $result = updateRecord($sql, [$data['description'], $data['alt_text'], $data['category'], $id]);
    
    if ($result !== false) {
        // 记录操作日志
        Security::logOperation('update', 'files', $id, '更新文件信息: ' . $oldData['original_name'], $oldData, $data);
        successResponse('文件信息更新成功');
    } else {
        errorResponse('更新失败');
    }
}
?>