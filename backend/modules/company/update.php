<?php
/**
 * 公司信息更新处理
 * 阔文展览后台管理系统
 */

require_once __DIR__ . '/../../includes/functions.php';

// 检查权限和CSRF
requireLogin();
requireCSRF();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('请求方法错误');
}

try {
    // 获取并清理输入数据
    $data = [
        'company_name' => Security::cleanInput($_POST['company_name'] ?? ''),
        'company_slogan' => Security::cleanInput($_POST['company_slogan'] ?? ''),
        'description' => Security::cleanInput($_POST['description'] ?? ''),
        'address' => Security::cleanInput($_POST['address'] ?? ''),
        'phone' => Security::cleanInput($_POST['phone'] ?? ''),
        'mobile' => Security::cleanInput($_POST['mobile'] ?? ''),
        'email' => Security::cleanInput($_POST['email'] ?? ''),
        'wechat' => Security::cleanInput($_POST['wechat'] ?? ''),
        'website' => Security::cleanInput($_POST['website'] ?? ''),
        'business_hours' => Security::cleanInput($_POST['business_hours'] ?? ''),
        'history' => $_POST['history'] ?? '',
        'culture' => $_POST['culture'] ?? '',
        'vision' => Security::cleanInput($_POST['vision'] ?? ''),
        'mission' => Security::cleanInput($_POST['mission'] ?? ''),
        'logo' => Security::cleanInput($_POST['logo'] ?? '')
    ];
    
    // 数据验证
    if (empty($data['company_name'])) {
        errorResponse('公司名称不能为空');
    }
    
    if (!empty($data['email']) && !Security::validateEmail($data['email'])) {
        errorResponse('邮箱格式不正确');
    }
    
    if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
        errorResponse('网站地址格式不正确');
    }
    
    // 获取原始数据用于日志记录
    $oldData = fetchOne("SELECT * FROM company_info WHERE id = 1");
    
    // 更新数据
    $sql = "UPDATE company_info SET 
                company_name = ?, 
                company_slogan = ?, 
                description = ?, 
                address = ?, 
                phone = ?, 
                mobile = ?, 
                email = ?, 
                wechat = ?, 
                website = ?, 
                business_hours = ?, 
                history = ?, 
                culture = ?, 
                vision = ?, 
                mission = ?, 
                logo = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = 1";
    
    $params = [
        $data['company_name'],
        $data['company_slogan'],
        $data['description'],
        $data['address'],
        $data['phone'],
        $data['mobile'],
        $data['email'],
        $data['wechat'],
        $data['website'],
        $data['business_hours'],
        $data['history'],
        $data['culture'],
        $data['vision'],
        $data['mission'],
        $data['logo']
    ];
    
    $result = updateRecord($sql, $params);
    
    if ($result !== false) {
        // 记录操作日志
        Security::logOperation(
            'update',
            'company_info',
            1,
            '更新公司信息',
            $oldData,
            $data
        );
        
        successResponse('公司信息更新成功');
    } else {
        errorResponse('更新失败，请重试');
    }
    
} catch (Exception $e) {
    error_log("公司信息更新错误: " . $e->getMessage());
    errorResponse('系统错误，请重试');
}
?>