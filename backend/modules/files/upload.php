<?php
/**
 * 文件上传处理
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
    $uploadedFiles = [];
    $errors = [];
    
    // 获取分类
    $category = Security::cleanInput($_POST['category'] ?? 'general');
    
    // 处理单个文件上传
    if (isset($_FILES['file'])) {
        $result = uploadSingleFile($_FILES['file'], $category);
        if ($result['success']) {
            $uploadedFiles[] = $result['file'];
        } else {
            $errors[] = $result['message'];
        }
    }
    
    // 处理多文件上传
    if (isset($_FILES['files'])) {
        $fileCount = count($_FILES['files']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['files']['name'][$i],
                    'type' => $_FILES['files']['type'][$i],
                    'tmp_name' => $_FILES['files']['tmp_name'][$i],
                    'error' => $_FILES['files']['error'][$i],
                    'size' => $_FILES['files']['size'][$i]
                ];
                
                $result = uploadSingleFile($file, $category);
                if ($result['success']) {
                    $uploadedFiles[] = $result['file'];
                } else {
                    $errors[] = $_FILES['files']['name'][$i] . ': ' . $result['message'];
                }
            }
        }
    }
    
    // 返回结果
    if (!empty($uploadedFiles)) {
        $message = count($uploadedFiles) . ' 个文件上传成功';
        if (!empty($errors)) {
            $message .= '，' . count($errors) . ' 个文件失败';
        }
        
        successResponse($message, [
            'files' => $uploadedFiles,
            'errors' => $errors
        ]);
    } else {
        errorResponse(!empty($errors) ? implode('; ', $errors) : '上传失败');
    }
    
} catch (Exception $e) {
    error_log("文件上传错误: " . $e->getMessage());
    errorResponse('系统错误，请重试');
}

/**
 * 上传单个文件
 */
function uploadSingleFile($file, $category) {
    try {
        // 验证文件
        $errors = Security::validateFileUpload($file);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(', ', $errors)];
        }
        
        // 确定上传目录
        $uploadBaseDir = __DIR__ . '/../../uploads/';
        
        switch ($category) {
            case 'images':
            case 'logo':
            case 'service':
            case 'case':
            case 'news':
                $uploadDir = $uploadBaseDir . 'images/';
                break;
            case 'documents':
                $uploadDir = $uploadBaseDir . 'documents/';
                break;
            default:
                $uploadDir = $uploadBaseDir . 'general/';
        }
        
        // 创建目录
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // 生成安全文件名
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'message' => '文件移动失败'];
        }
        
        // 处理图片（压缩、生成缩略图等）
        if (strpos($file['type'], 'image/') === 0) {
            optimizeImage($filepath, $file['type']);
        }
        
        // 获取文件信息
        $fileInfo = [
            'filename' => $filename,
            'original_name' => $file['name'],
            'file_path' => str_replace(__DIR__ . '/../../', '', $filepath),
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
        
        if ($fileId) {
            $fileInfo['id'] = $fileId;
            
            // 记录操作日志
            Security::logOperation('upload', 'files', $fileId, '上传文件: ' . $fileInfo['original_name']);
            
            return ['success' => true, 'file' => $fileInfo];
        } else {
            // 删除已上传的文件
            unlink($filepath);
            return ['success' => false, 'message' => '文件信息保存失败'];
        }
        
    } catch (Exception $e) {
        error_log("单文件上传错误: " . $e->getMessage());
        return ['success' => false, 'message' => '上传失败'];
    }
}

/**
 * 优化图片（压缩、调整大小等）
 */
function optimizeImage($filepath, $mimeType) {
    try {
        // 获取图片信息
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            return false;
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        // 如果图片太大，进行压缩
        $maxWidth = 1920;
        $maxHeight = 1080;
        $quality = 85;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            // 计算新尺寸
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);
            
            // 创建图片资源
            switch ($mimeType) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($filepath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($filepath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($filepath);
                    break;
                default:
                    return false;
            }
            
            if ($source) {
                // 创建新图片
                $destination = imagecreatetruecolor($newWidth, $newHeight);
                
                // 保持透明度（PNG/GIF）
                if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                    imagealphablending($destination, false);
                    imagesavealpha($destination, true);
                    $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
                    imagefill($destination, 0, 0, $transparent);
                }
                
                // 缩放图片
                imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                
                // 保存图片
                switch ($mimeType) {
                    case 'image/jpeg':
                        imagejpeg($destination, $filepath, $quality);
                        break;
                    case 'image/png':
                        imagepng($destination, $filepath, 9);
                        break;
                    case 'image/gif':
                        imagegif($destination, $filepath);
                        break;
                }
                
                // 释放资源
                imagedestroy($source);
                imagedestroy($destination);
                
                return true;
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("图片优化错误: " . $e->getMessage());
        return false;
    }
}
?>