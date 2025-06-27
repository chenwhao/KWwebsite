<?php
/**
 * 案例作品处理
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
            createCase();
            break;
        case 'update':
            updateCase();
            break;
        case 'delete':
            deleteCase();
            break;
        case 'batch_delete':
            batchDeleteCases();
            break;
        case 'toggle_status':
            toggleCaseStatus();
            break;
        case 'update_sort':
            updateCaseSort();
            break;
        case 'delete_image':
            deleteCaseImage();
            break;
        case 'update_image_title':
            updateImageTitle();
            break;
        default:
            errorResponse('无效的操作');
    }
} catch (Exception $e) {
    error_log("案例操作错误: " . $e->getMessage());
    errorResponse('操作失败，请重试');
}

/**
 * 创建案例
 */
function createCase() {
    // 获取并验证数据
    $data = getCaseData();
    
    // 插入数据
    $sql = "INSERT INTO cases (title, subtitle, description, content, client, project_date, location, 
                               featured_image, project_features, technologies, area, duration, category_id, 
                               sort_order, status, seo_title, seo_keywords, seo_description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $data['title'],
        $data['subtitle'],
        $data['description'],
        $data['content'],
        $data['client'],
        $data['project_date'],
        $data['location'],
        $data['featured_image'],
        $data['project_features'],
        $data['technologies'],
        $data['area'],
        $data['duration'],
        $data['category_id'],
        $data['sort_order'],
        $data['status'],
        $data['seo_title'],
        $data['seo_keywords'],
        $data['seo_description']
    ];
    
    $id = insertRecord($sql, $params);
    
    if ($id) {
        // 记录操作日志
        Security::logOperation('create', 'cases', $id, '创建案例: ' . $data['title'], null, $data);
        successResponse('案例创建成功');
    } else {
        errorResponse('创建失败');
    }
}

/**
 * 更新案例
 */
function updateCase() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的案例ID');
    }
    
    // 获取原始数据
    $oldData = fetchOne("SELECT * FROM cases WHERE id = ?", [$id]);
    if (!$oldData) {
        errorResponse('案例不存在');
    }
    
    // 获取并验证数据
    $data = getCaseData();
    
    // 更新数据
    $sql = "UPDATE cases SET 
                title = ?, subtitle = ?, description = ?, content = ?, client = ?, 
                project_date = ?, location = ?, featured_image = ?, project_features = ?, 
                technologies = ?, area = ?, duration = ?, category_id = ?, sort_order = ?, 
                status = ?, seo_title = ?, seo_keywords = ?, seo_description = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?";
    
    $params = [
        $data['title'],
        $data['subtitle'],
        $data['description'],
        $data['content'],
        $data['client'],
        $data['project_date'],
        $data['location'],
        $data['featured_image'],
        $data['project_features'],
        $data['technologies'],
        $data['area'],
        $data['duration'],
        $data['category_id'],
        $data['sort_order'],
        $data['status'],
        $data['seo_title'],
        $data['seo_keywords'],
        $data['seo_description'],
        $id
    ];
    
    $result = updateRecord($sql, $params);
    
    if ($result !== false) {
        // 记录操作日志
        Security::logOperation('update', 'cases', $id, '更新案例: ' . $data['title'], $oldData, $data);
        successResponse('案例更新成功');
    } else {
        errorResponse('更新失败');
    }
}

/**
 * 删除案例
 */
function deleteCase() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的案例ID');
    }
    
    // 获取原始数据
    $case = fetchOne("SELECT * FROM cases WHERE id = ?", [$id]);
    if (!$case) {
        errorResponse('案例不存在');
    }
    
    // 删除相关图片
    $caseImages = fetchAll("SELECT * FROM case_images WHERE case_id = ?", [$id]);
    foreach ($caseImages as $image) {
        deleteFile($image['image_path']);
        updateRecord("DELETE FROM case_images WHERE id = ?", [$image['id']]);
    }
    
    // 删除主图
    if (!empty($case['featured_image'])) {
        deleteFile($case['featured_image']);
    }
    
    // 删除案例
    $result = updateRecord("DELETE FROM cases WHERE id = ?", [$id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('delete', 'cases', $id, '删除案例: ' . $case['title'], $case, null);
        successResponse('案例删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 批量删除案例
 */
function batchDeleteCases() {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要删除的案例');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 获取要删除的案例信息
    $cases = fetchAll("SELECT * FROM cases WHERE id IN ($placeholders)", $ids);
    
    $deletedCount = 0;
    $errors = [];
    
    foreach ($cases as $case) {
        try {
            // 删除相关图片
            $caseImages = fetchAll("SELECT * FROM case_images WHERE case_id = ?", [$case['id']]);
            foreach ($caseImages as $image) {
                deleteFile($image['image_path']);
                updateRecord("DELETE FROM case_images WHERE id = ?", [$image['id']]);
            }
            
            // 删除主图
            if (!empty($case['featured_image'])) {
                deleteFile($case['featured_image']);
            }
            
            // 删除数据库记录
            $result = updateRecord("DELETE FROM cases WHERE id = ?", [$case['id']]);
            
            if ($result) {
                $deletedCount++;
                Security::logOperation('delete', 'cases', $case['id'], '批量删除案例: ' . $case['title'], $case, null);
            } else {
                $errors[] = $case['title'] . ': 数据库删除失败';
            }
            
        } catch (Exception $e) {
            $errors[] = $case['title'] . ': ' . $e->getMessage();
        }
    }
    
    if ($deletedCount > 0) {
        $message = "成功删除 $deletedCount 个案例";
        if (!empty($errors)) {
            $message .= '，' . count($errors) . ' 个案例删除失败';
        }
        successResponse($message, ['errors' => $errors]);
    } else {
        errorResponse('删除失败：' . implode('; ', $errors));
    }
}

/**
 * 切换案例状态
 */
function toggleCaseStatus() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的案例ID');
    }
    
    // 获取当前状态
    $case = fetchOne("SELECT id, title, status FROM cases WHERE id = ?", [$id]);
    if (!$case) {
        errorResponse('案例不存在');
    }
    
    // 切换状态
    $newStatus = $case['status'] === 'active' ? 'inactive' : 'active';
    $result = updateRecord("UPDATE cases SET status = ? WHERE id = ?", [$newStatus, $id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('update', 'cases', $id, "切换案例状态: {$case['title']} -> $newStatus");
        successResponse('状态切换成功');
    } else {
        errorResponse('状态切换失败');
    }
}

/**
 * 更新排序
 */
function updateCaseSort() {
    $sortData = $_POST['sort_data'] ?? [];
    if (empty($sortData) || !is_array($sortData)) {
        errorResponse('排序数据无效');
    }
    
    $db = getDB();
    $db->beginTransaction();
    
    try {
        foreach ($sortData as $item) {
            if (isset($item['id']) && isset($item['sort_order'])) {
                updateRecord(
                    "UPDATE cases SET sort_order = ? WHERE id = ?",
                    [intval($item['sort_order']), intval($item['id'])]
                );
            }
        }
        
        $db->commit();
        
        // 记录操作日志
        Security::logOperation('update', 'cases', 0, '更新案例排序');
        successResponse('排序更新成功');
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * 删除案例图片
 */
function deleteCaseImage() {
    $imageId = intval($_POST['image_id'] ?? 0);
    if ($imageId <= 0) {
        errorResponse('无效的图片ID');
    }
    
    // 获取图片信息
    $image = fetchOne("SELECT * FROM case_images WHERE id = ?", [$imageId]);
    if (!$image) {
        errorResponse('图片不存在');
    }
    
    // 删除物理文件
    deleteFile($image['image_path']);
    
    // 删除数据库记录
    $result = updateRecord("DELETE FROM case_images WHERE id = ?", [$imageId]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('delete', 'case_images', $imageId, '删除案例图片: ' . $image['title'], $image, null);
        successResponse('图片删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 更新图片标题
 */
function updateImageTitle() {
    $imageId = intval($_POST['image_id'] ?? 0);
    $title = Security::cleanInput($_POST['title'] ?? '');
    
    if ($imageId <= 0) {
        errorResponse('无效的图片ID');
    }
    
    // 更新图片标题
    $result = updateRecord("UPDATE case_images SET title = ? WHERE id = ?", [$title, $imageId]);
    
    if ($result !== false) {
        // 记录操作日志
        Security::logOperation('update', 'case_images', $imageId, '更新图片标题: ' . $title);
        successResponse('标题更新成功');
    } else {
        errorResponse('更新失败');
    }
}

/**
 * 获取并验证案例数据
 */
function getCaseData() {
    $data = [
        'title' => Security::cleanInput($_POST['title'] ?? ''),
        'subtitle' => Security::cleanInput($_POST['subtitle'] ?? ''),
        'description' => Security::cleanInput($_POST['description'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'client' => Security::cleanInput($_POST['client'] ?? ''),
        'project_date' => $_POST['project_date'] ?? null,
        'location' => Security::cleanInput($_POST['location'] ?? ''),
        'featured_image' => Security::cleanInput($_POST['featured_image'] ?? ''),
        'project_features' => Security::cleanInput($_POST['project_features'] ?? ''),
        'technologies' => Security::cleanInput($_POST['technologies'] ?? ''),
        'area' => Security::cleanInput($_POST['area'] ?? ''),
        'duration' => Security::cleanInput($_POST['duration'] ?? ''),
        'category_id' => intval($_POST['category_id'] ?? 0),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
        'seo_title' => Security::cleanInput($_POST['seo_title'] ?? ''),
        'seo_keywords' => Security::cleanInput($_POST['seo_keywords'] ?? ''),
        'seo_description' => Security::cleanInput($_POST['seo_description'] ?? '')
    ];
    
    // 数据验证
    if (empty($data['title'])) {
        errorResponse('案例标题不能为空');
    }
    
    if (!in_array($data['status'], ['active', 'inactive'])) {
        errorResponse('状态值无效');
    }
    
    // 处理日期
    if (!empty($data['project_date'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['project_date']);
        if (!$date) {
            $data['project_date'] = null;
        }
    } else {
        $data['project_date'] = null;
    }
    
    // 处理特色和技术数据（转换为JSON）
    if (!empty($data['project_features'])) {
        $features = array_filter(array_map('trim', explode("\n", $data['project_features'])));
        $data['project_features'] = json_encode($features, JSON_UNESCAPED_UNICODE);
    }
    
    if (!empty($data['technologies'])) {
        $technologies = array_filter(array_map('trim', explode("\n", $data['technologies'])));
        $data['technologies'] = json_encode($technologies, JSON_UNESCAPED_UNICODE);
    }
    
    // 处理分类ID
    if ($data['category_id'] <= 0) {
        $data['category_id'] = null;
    }
    
    return $data;
}
?>