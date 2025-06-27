<?php
/**
 * 服务项目处理
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
            createService();
            break;
        case 'update':
            updateService();
            break;
        case 'delete':
            deleteService();
            break;
        case 'batch_delete':
            batchDeleteServices();
            break;
        case 'toggle_status':
            toggleServiceStatus();
            break;
        case 'update_sort':
            updateServiceSort();
            break;
        default:
            errorResponse('无效的操作');
    }
} catch (Exception $e) {
    error_log("服务项目操作错误: " . $e->getMessage());
    errorResponse('操作失败，请重试');
}

/**
 * 创建服务项目
 */
function createService() {
    // 获取并验证数据
    $data = getServiceData();
    
    // 插入数据
    $sql = "INSERT INTO services (title, subtitle, description, content, icon, featured_image, features, process, sort_order, status, seo_title, seo_keywords, seo_description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $data['title'],
        $data['subtitle'],
        $data['description'],
        $data['content'],
        $data['icon'],
        $data['featured_image'],
        $data['features'],
        $data['process'],
        $data['sort_order'],
        $data['status'],
        $data['seo_title'],
        $data['seo_keywords'],
        $data['seo_description']
    ];
    
    $id = insertRecord($sql, $params);
    
    if ($id) {
        // 记录操作日志
        Security::logOperation('create', 'services', $id, '创建服务项目: ' . $data['title'], null, $data);
        successResponse('服务项目创建成功');
    } else {
        errorResponse('创建失败');
    }
}

/**
 * 更新服务项目
 */
function updateService() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的服务ID');
    }
    
    // 获取原始数据
    $oldData = fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
    if (!$oldData) {
        errorResponse('服务项目不存在');
    }
    
    // 获取并验证数据
    $data = getServiceData();
    
    // 更新数据
    $sql = "UPDATE services SET 
                title = ?, subtitle = ?, description = ?, content = ?, icon = ?, 
                featured_image = ?, features = ?, process = ?, sort_order = ?, 
                status = ?, seo_title = ?, seo_keywords = ?, seo_description = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?";
    
    $params = [
        $data['title'],
        $data['subtitle'],
        $data['description'],
        $data['content'],
        $data['icon'],
        $data['featured_image'],
        $data['features'],
        $data['process'],
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
        Security::logOperation('update', 'services', $id, '更新服务项目: ' . $data['title'], $oldData, $data);
        successResponse('服务项目更新成功');
    } else {
        errorResponse('更新失败');
    }
}

/**
 * 删除服务项目
 */
function deleteService() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的服务ID');
    }
    
    // 获取原始数据
    $service = fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
    if (!$service) {
        errorResponse('服务项目不存在');
    }
    
    // 删除服务
    $result = updateRecord("DELETE FROM services WHERE id = ?", [$id]);
    
    if ($result) {
        // 删除相关文件
        if (!empty($service['featured_image'])) {
            deleteFile($service['featured_image']);
        }
        
        // 记录操作日志
        Security::logOperation('delete', 'services', $id, '删除服务项目: ' . $service['title'], $service, null);
        successResponse('服务项目删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 批量删除服务项目
 */
function batchDeleteServices() {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要删除的服务项目');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 获取要删除的服务信息
    $services = fetchAll("SELECT * FROM services WHERE id IN ($placeholders)", $ids);
    
    // 删除服务
    $result = updateRecord("DELETE FROM services WHERE id IN ($placeholders)", $ids);
    
    if ($result) {
        // 删除相关文件并记录日志
        foreach ($services as $service) {
            if (!empty($service['featured_image'])) {
                deleteFile($service['featured_image']);
            }
            
            Security::logOperation('delete', 'services', $service['id'], '批量删除服务项目: ' . $service['title'], $service, null);
        }
        
        successResponse("成功删除 $result 个服务项目");
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 切换服务状态
 */
function toggleServiceStatus() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的服务ID');
    }
    
    // 获取当前状态
    $service = fetchOne("SELECT id, title, status FROM services WHERE id = ?", [$id]);
    if (!$service) {
        errorResponse('服务项目不存在');
    }
    
    // 切换状态
    $newStatus = $service['status'] === 'active' ? 'inactive' : 'active';
    $result = updateRecord("UPDATE services SET status = ? WHERE id = ?", [$newStatus, $id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('update', 'services', $id, "切换服务状态: {$service['title']} -> $newStatus");
        successResponse('状态切换成功');
    } else {
        errorResponse('状态切换失败');
    }
}

/**
 * 更新排序
 */
function updateServiceSort() {
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
                    "UPDATE services SET sort_order = ? WHERE id = ?",
                    [intval($item['sort_order']), intval($item['id'])]
                );
            }
        }
        
        $db->commit();
        
        // 记录操作日志
        Security::logOperation('update', 'services', 0, '更新服务项目排序');
        successResponse('排序更新成功');
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * 获取并验证服务数据
 */
function getServiceData() {
    $data = [
        'title' => Security::cleanInput($_POST['title'] ?? ''),
        'subtitle' => Security::cleanInput($_POST['subtitle'] ?? ''),
        'description' => Security::cleanInput($_POST['description'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'icon' => Security::cleanInput($_POST['icon'] ?? ''),
        'featured_image' => Security::cleanInput($_POST['featured_image'] ?? ''),
        'features' => Security::cleanInput($_POST['features'] ?? ''),
        'process' => Security::cleanInput($_POST['process'] ?? ''),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
        'seo_title' => Security::cleanInput($_POST['seo_title'] ?? ''),
        'seo_keywords' => Security::cleanInput($_POST['seo_keywords'] ?? ''),
        'seo_description' => Security::cleanInput($_POST['seo_description'] ?? '')
    ];
    
    // 数据验证
    if (empty($data['title'])) {
        errorResponse('服务标题不能为空');
    }
    
    if (!in_array($data['status'], ['active', 'inactive'])) {
        errorResponse('状态值无效');
    }
    
    // 处理特色和流程数据（转换为JSON）
    if (!empty($data['features'])) {
        $features = array_filter(array_map('trim', explode("\n", $data['features'])));
        $data['features'] = json_encode($features, JSON_UNESCAPED_UNICODE);
    }
    
    if (!empty($data['process'])) {
        $process = array_filter(array_map('trim', explode("\n", $data['process'])));
        $data['process'] = json_encode($process, JSON_UNESCAPED_UNICODE);
    }
    
    return $data;
}
?>