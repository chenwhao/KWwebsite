<?php
/**
 * 新闻处理
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
        case 'create':
            createNews();
            break;
        case 'update':
            updateNews();
            break;
        case 'delete':
            deleteNews();
            break;
        case 'batch_delete':
            batchDeleteNews();
            break;
        case 'batch_publish':
            batchUpdateStatus('published');
            break;
        case 'batch_draft':
            batchUpdateStatus('draft');
            break;
        case 'toggle_status':
            toggleNewsStatus();
            break;
        default:
            errorResponse('无效的操作');
    }
} catch (Exception $e) {
    error_log("新闻操作错误: " . $e->getMessage());
    errorResponse('操作失败，请重试');
}

/**
 * 创建新闻
 */
function createNews() {
    // 获取并验证数据
    $data = getNewsData();
    
    // 插入数据
    $sql = "INSERT INTO news (title, subtitle, excerpt, content, category, author, source, 
                              thumbnail, tags, published_at, status, seo_title, seo_keywords, seo_description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $data['title'],
        $data['subtitle'],
        $data['excerpt'],
        $data['content'],
        $data['category'],
        $data['author'],
        $data['source'],
        $data['thumbnail'],
        $data['tags'],
        $data['published_at'],
        $data['status'],
        $data['seo_title'],
        $data['seo_keywords'],
        $data['seo_description']
    ];
    
    $id = insertRecord($sql, $params);
    
    if ($id) {
        // 记录操作日志
        Security::logOperation('create', 'news', $id, '创建新闻: ' . $data['title'], null, $data);
        successResponse('新闻创建成功');
    } else {
        errorResponse('创建失败');
    }
}

/**
 * 更新新闻
 */
function updateNews() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的新闻ID');
    }
    
    // 获取原始数据
    $oldData = fetchOne("SELECT * FROM news WHERE id = ?", [$id]);
    if (!$oldData) {
        errorResponse('新闻不存在');
    }
    
    // 获取并验证数据
    $data = getNewsData();
    
    // 更新数据
    $sql = "UPDATE news SET 
                title = ?, subtitle = ?, excerpt = ?, content = ?, category = ?, 
                author = ?, source = ?, thumbnail = ?, tags = ?, published_at = ?, 
                status = ?, seo_title = ?, seo_keywords = ?, seo_description = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?";
    
    $params = [
        $data['title'],
        $data['subtitle'],
        $data['excerpt'],
        $data['content'],
        $data['category'],
        $data['author'],
        $data['source'],
        $data['thumbnail'],
        $data['tags'],
        $data['published_at'],
        $data['status'],
        $data['seo_title'],
        $data['seo_keywords'],
        $data['seo_description'],
        $id
    ];
    
    $result = updateRecord($sql, $params);
    
    if ($result !== false) {
        // 记录操作日志
        Security::logOperation('update', 'news', $id, '更新新闻: ' . $data['title'], $oldData, $data);
        successResponse('新闻更新成功');
    } else {
        errorResponse('更新失败');
    }
}

/**
 * 删除新闻
 */
function deleteNews() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的新闻ID');
    }
    
    // 获取原始数据
    $news = fetchOne("SELECT * FROM news WHERE id = ?", [$id]);
    if (!$news) {
        errorResponse('新闻不存在');
    }
    
    // 删除缩略图
    if (!empty($news['thumbnail'])) {
        deleteFile($news['thumbnail']);
    }
    
    // 删除新闻
    $result = updateRecord("DELETE FROM news WHERE id = ?", [$id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('delete', 'news', $id, '删除新闻: ' . $news['title'], $news, null);
        successResponse('新闻删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 批量删除新闻
 */
function batchDeleteNews() {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要删除的新闻');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 获取要删除的新闻信息
    $newsList = fetchAll("SELECT * FROM news WHERE id IN ($placeholders)", $ids);
    
    $deletedCount = 0;
    $errors = [];
    
    foreach ($newsList as $news) {
        try {
            // 删除缩略图
            if (!empty($news['thumbnail'])) {
                deleteFile($news['thumbnail']);
            }
            
            // 删除数据库记录
            $result = updateRecord("DELETE FROM news WHERE id = ?", [$news['id']]);
            
            if ($result) {
                $deletedCount++;
                Security::logOperation('delete', 'news', $news['id'], '批量删除新闻: ' . $news['title'], $news, null);
            } else {
                $errors[] = $news['title'] . ': 数据库删除失败';
            }
            
        } catch (Exception $e) {
            $errors[] = $news['title'] . ': ' . $e->getMessage();
        }
    }
    
    if ($deletedCount > 0) {
        $message = "成功删除 $deletedCount 条新闻";
        if (!empty($errors)) {
            $message .= '，' . count($errors) . ' 条新闻删除失败';
        }
        successResponse($message, ['errors' => $errors]);
    } else {
        errorResponse('删除失败：' . implode('; ', $errors));
    }
}

/**
 * 批量更新状态
 */
function batchUpdateStatus($status) {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要操作的新闻');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 更新状态
    $sql = "UPDATE news SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id IN ($placeholders)";
    $params = array_merge([$status], $ids);
    $result = updateRecord($sql, $params);
    
    if ($result) {
        $action = $status === 'published' ? '发布' : '设为草稿';
        // 记录操作日志
        Security::logOperation('batch_update', 'news', null, "批量{$action}新闻，数量: " . count($ids));
        successResponse("成功{$action} " . count($ids) . ' 条新闻');
    } else {
        errorResponse('批量操作失败');
    }
}

/**
 * 切换新闻状态
 */
function toggleNewsStatus() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的新闻ID');
    }
    
    // 获取当前状态
    $news = fetchOne("SELECT id, title, status FROM news WHERE id = ?", [$id]);
    if (!$news) {
        errorResponse('新闻不存在');
    }
    
    // 切换状态
    $newStatus = $news['status'] === 'published' ? 'draft' : 'published';
    $result = updateRecord("UPDATE news SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$newStatus, $id]);
    
    if ($result) {
        // 如果是发布状态，设置发布时间
        if ($newStatus === 'published') {
            updateRecord("UPDATE news SET published_at = CURRENT_TIMESTAMP WHERE id = ? AND published_at IS NULL", [$id]);
        }
        
        // 记录操作日志
        $action = $newStatus === 'published' ? '发布' : '设为草稿';
        Security::logOperation('update', 'news', $id, "{$action}新闻: {$news['title']}");
        successResponse('状态切换成功');
    } else {
        errorResponse('状态切换失败');
    }
}

/**
 * 获取并验证新闻数据
 */
function getNewsData() {
    $data = [
        'title' => Security::cleanInput($_POST['title'] ?? ''),
        'subtitle' => Security::cleanInput($_POST['subtitle'] ?? ''),
        'excerpt' => Security::cleanInput($_POST['excerpt'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'category' => Security::cleanInput($_POST['category'] ?? ''),
        'author' => Security::cleanInput($_POST['author'] ?? ''),
        'source' => Security::cleanInput($_POST['source'] ?? ''),
        'thumbnail' => Security::cleanInput($_POST['thumbnail'] ?? ''),
        'tags' => Security::cleanInput($_POST['tags'] ?? ''),
        'published_at' => $_POST['published_at'] ?? null,
        'status' => $_POST['status'] ?? 'draft',
        'seo_title' => Security::cleanInput($_POST['seo_title'] ?? ''),
        'seo_keywords' => Security::cleanInput($_POST['seo_keywords'] ?? ''),
        'seo_description' => Security::cleanInput($_POST['seo_description'] ?? '')
    ];
    
    // 数据验证
    if (empty($data['title'])) {
        errorResponse('新闻标题不能为空');
    }
    
    if (!in_array($data['status'], ['draft', 'published'])) {
        errorResponse('状态值无效');
    }
    
    // 处理发布时间
    if (!empty($data['published_at'])) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $data['published_at']);
        if ($date) {
            $data['published_at'] = $date->format('Y-m-d H:i:s');
        } else {
            $data['published_at'] = null;
        }
    } else {
        // 如果是发布状态且没有指定发布时间，使用当前时间
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        } else {
            $data['published_at'] = null;
        }
    }
    
    // 自动生成摘要
    if (empty($data['excerpt']) && !empty($data['content'])) {
        $data['excerpt'] = truncateText(strip_tags($data['content']), 200);
    }
    
    // 自动生成SEO信息
    if (empty($data['seo_title'])) {
        $data['seo_title'] = $data['title'];
    }
    
    if (empty($data['seo_description'])) {
        $data['seo_description'] = $data['excerpt'];
    }
    
    return $data;
}
?>