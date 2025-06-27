<?php
/**
 * 留言处理
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
        case 'get':
            getMessage();
            break;
        case 'reply':
            replyMessage();
            break;
        case 'delete':
            deleteMessage();
            break;
        case 'batch_delete':
            batchDeleteMessages();
            break;
        case 'batch_reply':
            batchUpdateStatus('replied');
            break;
        case 'batch_archive':
            batchUpdateStatus('archived');
            break;
        case 'update_status':
            updateMessageStatus();
            break;
        default:
            errorResponse('无效的操作');
    }
} catch (Exception $e) {
    error_log("留言操作错误: " . $e->getMessage());
    errorResponse('操作失败，请重试');
}

/**
 * 获取留言详情
 */
function getMessage() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的留言ID');
    }
    
    $message = fetchOne("SELECT * FROM messages WHERE id = ?", [$id]);
    if (!$message) {
        errorResponse('留言不存在');
    }
    
    successResponse('获取成功', ['data' => $message]);
}

/**
 * 回复留言
 */
function replyMessage() {
    $messageId = intval($_POST['message_id'] ?? 0);
    $replyContent = Security::cleanInput($_POST['reply_content'] ?? '');
    $sendEmail = isset($_POST['send_email']) && $_POST['send_email'] == '1';
    
    if ($messageId <= 0) {
        errorResponse('无效的留言ID');
    }
    
    if (empty($replyContent)) {
        errorResponse('回复内容不能为空');
    }
    
    // 获取留言信息
    $message = fetchOne("SELECT * FROM messages WHERE id = ?", [$messageId]);
    if (!$message) {
        errorResponse('留言不存在');
    }
    
    if ($message['status'] === 'replied') {
        errorResponse('该留言已经回复过了');
    }
    
    // 更新留言状态和回复内容
    $user = Security::getCurrentUser();
    $sql = "UPDATE messages SET 
                status = 'replied', 
                reply_content = ?, 
                replied_at = CURRENT_TIMESTAMP,
                replied_by = ?
            WHERE id = ?";
    
    $result = updateRecord($sql, [$replyContent, $user['id'] ?? null, $messageId]);
    
    if ($result) {
        // 发送邮件通知（如果选择了发送邮件且有邮箱地址）
        if ($sendEmail && !empty($message['email'])) {
            try {
                sendReplyEmail($message, $replyContent);
            } catch (Exception $e) {
                error_log("发送回复邮件失败: " . $e->getMessage());
                // 邮件发送失败不影响回复操作
            }
        }
        
        // 记录操作日志
        Security::logOperation('reply', 'messages', $messageId, '回复留言: ' . $message['name'], null, ['reply_content' => $replyContent]);
        
        $message = $sendEmail ? '回复成功并已发送邮件通知' : '回复成功';
        successResponse($message);
    } else {
        errorResponse('回复失败');
    }
}

/**
 * 删除留言
 */
function deleteMessage() {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        errorResponse('无效的留言ID');
    }
    
    // 获取原始数据
    $message = fetchOne("SELECT * FROM messages WHERE id = ?", [$id]);
    if (!$message) {
        errorResponse('留言不存在');
    }
    
    // 删除留言
    $result = updateRecord("DELETE FROM messages WHERE id = ?", [$id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('delete', 'messages', $id, '删除留言: ' . $message['name'], $message, null);
        successResponse('留言删除成功');
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 批量删除留言
 */
function batchDeleteMessages() {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要删除的留言');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 获取要删除的留言信息
    $messages = fetchAll("SELECT * FROM messages WHERE id IN ($placeholders)", $ids);
    
    // 删除留言
    $result = updateRecord("DELETE FROM messages WHERE id IN ($placeholders)", $ids);
    
    if ($result) {
        // 记录操作日志
        foreach ($messages as $message) {
            Security::logOperation('delete', 'messages', $message['id'], '批量删除留言: ' . $message['name'], $message, null);
        }
        
        successResponse("成功删除 $result 条留言");
    } else {
        errorResponse('删除失败');
    }
}

/**
 * 批量更新状态
 */
function batchUpdateStatus($status) {
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        errorResponse('请选择要操作的留言');
    }
    
    $ids = array_map('intval', $ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // 更新状态
    $sql = "UPDATE messages SET status = ? WHERE id IN ($placeholders)";
    $params = array_merge([$status], $ids);
    $result = updateRecord($sql, $params);
    
    if ($result) {
        $actionMap = [
            'replied' => '标记为已回复',
            'archived' => '归档'
        ];
        $action = $actionMap[$status] ?? '更新状态';
        
        // 记录操作日志
        Security::logOperation('batch_update', 'messages', null, "批量{$action}留言，数量: " . count($ids));
        successResponse("成功{$action} " . count($ids) . ' 条留言');
    } else {
        errorResponse('批量操作失败');
    }
}

/**
 * 更新留言状态
 */
function updateMessageStatus() {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if ($id <= 0) {
        errorResponse('无效的留言ID');
    }
    
    if (!in_array($status, ['pending', 'replied', 'archived'])) {
        errorResponse('无效的状态值');
    }
    
    // 获取留言信息
    $message = fetchOne("SELECT * FROM messages WHERE id = ?", [$id]);
    if (!$message) {
        errorResponse('留言不存在');
    }
    
    // 更新状态
    $result = updateRecord("UPDATE messages SET status = ? WHERE id = ?", [$status, $id]);
    
    if ($result) {
        // 记录操作日志
        Security::logOperation('update', 'messages', $id, "更新留言状态: {$message['name']} -> $status");
        successResponse('状态更新成功');
    } else {
        errorResponse('状态更新失败');
    }
}

/**
 * 发送回复邮件
 */
function sendReplyEmail($message, $replyContent) {
    // 这里可以集成邮件发送功能
    // 例如使用 PHPMailer 或其他邮件库
    
    // 简单的邮件发送示例（需要配置邮件服务器）
    $to = $message['email'];
    $subject = '阔文展览 - 您的留言回复';
    
    $emailContent = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>留言回复</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;'>
                <h2 style='color: #0066cc; margin: 0;'>阔文展览展示服务有限公司</h2>
                <p style='margin: 5px 0 0 0; color: #666;'>专业展台设计搭建服务</p>
            </div>
            
            <p>尊敬的 {$message['name']}，您好！</p>
            
            <p>感谢您对我们的关注和留言。针对您的咨询，我们的回复如下：</p>
            
            <div style='background: #e8f4fd; padding: 15px; border-left: 4px solid #0066cc; margin: 20px 0;'>
                <h4 style='margin: 0 0 10px 0; color: #0066cc;'>您的留言：</h4>
                <p style='margin: 0; font-style: italic;'>" . (isset($message['subject']) ? $message['subject'] . '<br>' : '') . $message['message'] . "</p>
            </div>
            
            <div style='background: #f0f9ff; padding: 15px; border-left: 4px solid #22c55e; margin: 20px 0;'>
                <h4 style='margin: 0 0 10px 0; color: #22c55e;'>我们的回复：</h4>
                <p style='margin: 0;'>" . nl2br(htmlspecialchars($replyContent)) . "</p>
            </div>
            
            <p>如果您还有其他问题，欢迎随时与我们联系：</p>
            <ul>
                <li>电话：021-XXXXXXXX</li>
                <li>邮箱：info@kuowen.com</li>
                <li>地址：上海市XXXXXX</li>
            </ul>
            
            <p>再次感谢您的信任与支持！</p>
            
            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 14px;'>
                <p>此邮件由系统自动发送，请勿直接回复。</p>
                <p>&copy; " . date('Y') . " 上海阔文展览展示服务有限公司 版权所有</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // 设置邮件头
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: 阔文展览 <noreply@kuowen.com>" . "\r\n";
    
    // 发送邮件
    if (!mail($to, $subject, $emailContent, $headers)) {
        throw new Exception('邮件发送失败');
    }
    
    return true;
}
?>