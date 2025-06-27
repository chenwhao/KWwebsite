<?php
/**
 * 案例分类处理
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
            createCategory();
            break;
        case 'update':
            updateCategory();
            break;
        case 'get':
            getCategory();
            break;
        case 'delete':
            deleteCategory();
            break;
        case 'toggle_status':
            toggleCategoryStatus();
            break;
        case 'update_sort':
            updateCategorySort();
            break;
        default:
            errorResponse('无效的操作');
    }
} catch (Exception $e) {
    error_log("案例分类操作错误: " . $e->getMessage());
    errorResponse('操作失败，请重试');
}

/**
 * 创建分类
 */
function createCategory() {
    // 获取并验证数据
    $data = getCategoryData();
    
    // 检查分类名称是否重复
    $exists = fetchOne("SELECT id FROM case_categories WHERE name = ?", [$data['name']]);
    if ($exists) {
        errorResponse('分类名称已存在');
    }
    
    // 检查别名是否重复
    if (!empty($data['slug'])) {
        $slugExists = fetchOne("SELECT id FROM case_categories WHERE slug = ?", [$data['slug']]);
        if ($slugExists) {
            errorResponse('分类别名已存在');
        }
    }
    
    // 插入数据
    $sql = "INSERT INTO case_categories (name, slug, description, sort_order, status) 
            VALUES (?, ?, ?, ?, ?)";
    
    $params = [
        $data['name'],
        $data['slug'],
        $data['description'],
        $data['sort_order'],
        $data['status']
    ];
    
    $id = insertRecord($sql, $params);
    
    if ($id) {
        // 记录操作日志
        Security::logOperation('create', 'case_categories', $id, '创建案例分类: ' . $data['name'], null, $data);
        successResponse('分类创建成功');
    } else {
        errorResponse('创建失败');
    }
}

/**
 * 更新分类
 */
function updateCategory() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的分类ID');
    }
    
    // 获取原始数据
    $oldData = fetchOne("SELECT * FROM case_categories WHERE id = ?", [$id]);
    if (!$oldData) {
        errorResponse('分类不存在');
    }
    
    // 获取并验证数据
    $data = getCategoryData();
    
    // 检查分类名称是否重复（排除自己）
    $exists = fetchOne("SELECT id FROM case_categories WHERE name = ? AND id != ?", [$data['name'], $id]);
    if ($exists) {
        errorResponse('分类名称已存在');
    }
    
    // 检查别名是否重复（排除自己）
    if (!empty($data['slug'])) {
        $slugExists = fetchOne("SELECT id FROM case_categories WHERE slug = ? AND id != ?", [$data['slug'], $id]);
        if ($slugExists) {
            errorResponse('分类别名已存在');
        }
    }
    
    // 更新数据
    $sql = "UPDATE case_categories SET 
                name = ?, slug = ?, description = ?, sort_order = ?, status = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?";
    
    $params = [
        $data['name'],
        $data['slug'],
        $data['description'],
        $data['sort_order'],
        $data['status'],
        $id
    ];
    
    $result = updateRecord($sql, $params);
    
    if ($result !== false) {
        // 记录操作日志
        Security::logOperation('update', 'case_categories', $id, '更新案例分类: ' . $data['name'], $oldData, $data);
        successResponse('分类更新成功');
    } else {
        errorResponse('更新失败');
    }
}

/**
 * 获取分类信息
 */
function getCategory() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的分类ID');
    }
    
    $category = fetchOne("SELECT * FROM case_categories WHERE id = ?", [$id]);
    if (!$category) {
        errorResponse('分类不存在');
    }
    
    successResponse('获取成功', ['data' => $category]);
}

/**
 * 删除分类
 */
function deleteCategory() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的分类ID');
    }
    
    // 获取原始数据
    $category = fetchOne("SELECT * FROM case_categories WHERE id = ?", [$id]);
    if (!$category) {
        errorResponse('分类不存在');
    }
    
    // 检查是否有案例使用此分类
    $caseCount = fetchOne("SELECT COUNT(*) as count FROM cases WHERE category_id = ?", [$id])['count'];
    if ($caseCount > 0) {
        errorResponse('该分类下还有案例，不能删除');
    }
    
    // 删除分类
    $result = updateRecord("DELETE FROM case_categories WHERE id = ?", [$id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('delete', 'case_categories', $id, '删除案例分类: ' . $category['name'], $category, null);
        successResponse('分类删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 切换分类状态
 */
function toggleCategoryStatus() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的分类ID');
    }
    
    // 获取当前状态
    $category = fetchOne("SELECT id, name, status FROM case_categories WHERE id = ?", [$id]);
    if (!$category) {
        errorResponse('分类不存在');
    }
    
    // 切换状态
    $newStatus = $category['status'] === 'active' ? 'inactive' : 'active';
    $result = updateRecord("UPDATE case_categories SET status = ? WHERE id = ?", [$newStatus, $id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('update', 'case_categories', $id, "切换分类状态: {$category['name']} -> $newStatus");
        successResponse('状态切换成功');
    } else {
        errorResponse('状态切换失败');
    }
}

/**
 * 更新排序
 */
function updateCategorySort() {
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
                    "UPDATE case_categories SET sort_order = ? WHERE id = ?",
                    [intval($item['sort_order']), intval($item['id'])]
                );
            }
        }
        
        $db->commit();
        
        // 记录操作日志
        Security::logOperation('update', 'case_categories', 0, '更新案例分类排序');
        successResponse('排序更新成功');
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * 获取并验证分类数据
 */
function getCategoryData() {
    $data = [
        'name' => Security::cleanInput($_POST['name'] ?? ''),
        'slug' => Security::cleanInput($_POST['slug'] ?? ''),
        'description' => Security::cleanInput($_POST['description'] ?? ''),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active'
    ];
    
    // 数据验证
    if (empty($data['name'])) {
        errorResponse('分类名称不能为空');
    }
    
    if (!in_array($data['status'], ['active', 'inactive'])) {
        errorResponse('状态值无效');
    }
    
    // 处理别名
    if (empty($data['slug'])) {
        // 自动生成别名
        $data['slug'] = generateSlug($data['name']);
    } else {
        // 验证别名格式
        if (!preg_match('/^[a-z0-9\-]+$/', $data['slug'])) {
            errorResponse('别名只能包含小写字母、数字和连字符');
        }
    }
    
    return $data;
}

/**
 * 生成别名
 */
function generateSlug($name) {
    // 简单的拼音转换（可以扩展为更完善的拼音库）
    $pinyinMap = [
        '展台' => 'zhantai',
        '设计' => 'sheji',
        '搭建' => 'dajian',
        '活动' => 'huodong',
        '会议' => 'huiyi',
        '展览' => 'zhanlan',
        '展示' => 'zhanshi'
    ];
    
    $slug = $name;
    foreach ($pinyinMap as $chinese => $pinyin) {
        $slug = str_replace($chinese, $pinyin, $slug);
    }
    
    // 转换为小写并替换特殊字符
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9\u4e00-\u9fa5]+/', '-', $slug);
    $slug = trim($slug, '-');
    
    return $slug;
}
?>