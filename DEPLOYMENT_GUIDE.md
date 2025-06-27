# 阔文展览网站部署指南

这是一份详细的部署指南，帮助您将阔文展览网站成功部署到服务器。

## 📋 部署前准备

### 服务器环境要求

| 组件 | 最低要求 | 推荐配置 |
|------|----------|----------|
| 操作系统 | CentOS 7+ / Ubuntu 18+ | Ubuntu 20.04 LTS |
| Web服务器 | Nginx 1.16+ / Apache 2.4+ | Nginx 1.20+ |
| PHP | 7.4+ | 8.0+ |
| MySQL | 5.7+ | 8.0+ |
| 内存 | 1GB | 2GB+ |
| 磁盘空间 | 5GB | 10GB+ |

### 域名准备
- 主站域名：`kuowenexpo.com`
- 后台域名：`admin.kuowenexpo.com`

## 🚀 Step 1: 服务器环境配置

### 1.1 安装基础环境 (Ubuntu)

```bash
# 更新系统包
sudo apt update && sudo apt upgrade -y

# 安装Nginx
sudo apt install nginx -y

# 安装PHP和相关扩展
sudo apt install php8.0-fpm php8.0-mysql php8.0-mbstring php8.0-xml php8.0-gd php8.0-json php8.0-curl php8.0-zip -y

# 安装MySQL
sudo apt install mysql-server -y

# 安装其他工具
sudo apt install unzip git -y
```

### 1.2 配置MySQL

```bash
# 安全配置MySQL
sudo mysql_secure_installation

# 登录MySQL创建数据库
sudo mysql -u root -p

# 在MySQL中执行
CREATE DATABASE kuowen_exhibition CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kuowen_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON kuowen_exhibition.* TO 'kuowen_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 🌐 Step 2: 前端网站部署

### 2.1 创建网站目录

```bash
# 创建网站根目录
sudo mkdir -p /var/www/kuowenexpo.com
sudo chown -R www-data:www-data /var/www/kuowenexpo.com
```

### 2.2 上传前端文件

```bash
# 方法1: 直接上传dist目录内容到网站根目录
# 将 frontend/dist/* 上传到 /var/www/kuowenexpo.com/

# 方法2: 使用git (如果有代码仓库)
cd /var/www/kuowenexpo.com
sudo git clone your_repository .
sudo chown -R www-data:www-data .
```

### 2.3 配置Nginx - 前端站点

```bash
# 创建前端站点配置
sudo nano /etc/nginx/sites-available/kuowenexpo.com
```

```nginx
server {
    listen 80;
    server_name kuowenexpo.com www.kuowenexpo.com;
    root /var/www/kuowenexpo.com;
    index index.html index.htm;

    # 启用Gzip压缩
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # 静态文件缓存
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # React Router支持
    location / {
        try_files $uri $uri/ /index.html;
    }

    # 安全头
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
}
```

```bash
# 启用站点配置
sudo ln -s /etc/nginx/sites-available/kuowenexpo.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔧 Step 3: 后台管理系统部署

### 3.1 创建后台目录

```bash
# 创建后台网站目录
sudo mkdir -p /var/www/admin.kuowenexpo.com
sudo chown -R www-data:www-data /var/www/admin.kuowenexpo.com
```

### 3.2 上传后台文件

```bash
# 上传backend目录内容到 /var/www/admin.kuowenexpo.com/
# 确保保持目录结构完整
```

### 3.3 设置目录权限

```bash
cd /var/www/admin.kuowenexpo.com

# 设置基本权限
sudo chown -R www-data:www-data .
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;

# 设置上传目录可写权限
sudo chmod 777 uploads/
sudo mkdir -p uploads/{images,documents,backups}
sudo chmod 777 uploads/{images,documents,backups}
```

### 3.4 配置数据库连接

```bash
# 编辑数据库配置
sudo nano config/database.php
```

```php
<?php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'kuowen_exhibition');
define('DB_USER', 'kuowen_user');
define('DB_PASS', 'your_strong_password');
define('DB_CHARSET', 'utf8mb4');

// 数据库连接
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>
```

### 3.5 导入数据库结构

```bash
# 导入数据库结构和初始数据
mysql -u kuowen_user -p kuowen_exhibition < /var/www/admin.kuowenexpo.com/install.sql
```

### 3.6 配置PHP-FPM

```bash
# 编辑PHP-FPM配置 (可选优化)
sudo nano /etc/php/8.0/fpm/php.ini
```

```ini
# 重要配置项
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
memory_limit = 256M
max_input_vars = 3000

# 时区设置
date.timezone = Asia/Shanghai
```

```bash
# 重启PHP-FPM
sudo systemctl restart php8.0-fpm
```

### 3.7 配置Nginx - 后台站点

```bash
# 创建后台站点配置
sudo nano /etc/nginx/sites-available/admin.kuowenexpo.com
```

```nginx
server {
    listen 80;
    server_name admin.kuowenexpo.com;
    root /var/www/admin.kuowenexpo.com;
    index index.php index.html;

    # 安全配置
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    # 禁止访问敏感文件
    location ~ /\. {
        deny all;
    }

    location ~ \.(sql|md|txt|log)$ {
        deny all;
    }

    # PHP文件处理
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # 静态文件处理
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # 上传文件保护
    location ^~ /uploads/ {
        location ~ \.php$ {
            deny all;
        }
    }

    # 默认路由
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

```bash
# 启用后台站点配置
sudo ln -s /etc/nginx/sites-available/admin.kuowenexpo.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 Step 4: SSL证书配置 (推荐)

### 4.1 安装Certbot

```bash
# 安装Certbot
sudo apt install certbot python3-certbot-nginx -y
```

### 4.2 申请SSL证书

```bash
# 为前端站点申请证书
sudo certbot --nginx -d kuowenexpo.com -d www.kuowenexpo.com

# 为后台站点申请证书
sudo certbot --nginx -d admin.kuowenexpo.com
```

### 4.3 设置自动续期

```bash
# 测试自动续期
sudo certbot renew --dry-run

# 设置定时任务
sudo crontab -e
# 添加以下行
0 12 * * * /usr/bin/certbot renew --quiet
```

## ✅ Step 5: 验证部署

### 5.1 检查前端网站

```bash
# 访问前端网站
curl -I http://kuowenexpo.com
# 或在浏览器中访问 https://kuowenexpo.com
```

### 5.2 检查后台系统

1. 访问 `https://admin.kuowenexpo.com`
2. 使用默认账户登录：
   - 用户名：`admin`
   - 密码：`admin123`
3. 立即修改默认密码！

### 5.3 功能测试清单

- [ ] 前端网站正常显示
- [ ] 后台能够正常登录
- [ ] 公司信息页面可以编辑保存
- [ ] 文件上传功能正常
- [ ] 数据库连接正常
- [ ] SSL证书配置成功

## 🛠️ Step 6: 安全强化

### 6.1 修改默认配置

```bash
# 修改默认管理员密码 (在后台界面操作)
# 删除或重命名install.sql文件
sudo mv /var/www/admin.kuowenexpo.com/install.sql /var/www/admin.kuowenexpo.com/install.sql.bak
```

### 6.2 配置防火墙

```bash
# 安装并配置UFW防火墙
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw status
```

### 6.3 设置备份策略

```bash
# 创建备份脚本
sudo nano /root/backup_kuowen.sh
```

```bash
#!/bin/bash
# 阔文展览网站备份脚本

BACKUP_DIR="/backup/kuowen"
DATE=$(date +%Y%m%d_%H%M%S)

# 创建备份目录
mkdir -p $BACKUP_DIR

# 备份数据库
mysqldump -u kuowen_user -p'your_password' kuowen_exhibition > $BACKUP_DIR/database_$DATE.sql

# 备份上传文件
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/admin.kuowenexpo.com/uploads/

# 清理7天前的备份
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# 设置执行权限
sudo chmod +x /root/backup_kuowen.sh

# 设置定时备份 (每天凌晨2点)
sudo crontab -e
# 添加：0 2 * * * /root/backup_kuowen.sh >> /var/log/kuowen_backup.log 2>&1
```

## 📊 Step 7: 性能优化

### 7.1 配置Redis缓存 (可选)

```bash
# 安装Redis
sudo apt install redis-server -y

# 配置Redis
sudo nano /etc/redis/redis.conf
# 取消注释: maxmemory 128mb
# 添加: maxmemory-policy allkeys-lru

sudo systemctl restart redis-server
```

### 7.2 Nginx性能优化

```bash
# 编辑Nginx主配置
sudo nano /etc/nginx/nginx.conf
```

```nginx
# 在http块中添加
worker_processes auto;
worker_connections 1024;

# 启用Gzip
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_proxied any;
gzip_comp_level 6;
gzip_types
    text/plain
    text/css
    text/xml
    text/javascript
    application/json
    application/javascript
    application/xml+rss
    application/atom+xml
    image/svg+xml;

# 缓冲区设置
client_body_buffer_size 128k;
client_max_body_size 20m;
client_header_buffer_size 1k;
large_client_header_buffers 4 4k;
```

## 🔍 故障排除

### 常见问题及解决方案

1. **403 Forbidden错误**
   ```bash
   # 检查文件权限
   sudo chown -R www-data:www-data /var/www/
   sudo chmod -R 755 /var/www/
   ```

2. **502 Bad Gateway错误**
   ```bash
   # 检查PHP-FPM状态
   sudo systemctl status php8.0-fpm
   sudo systemctl restart php8.0-fpm
   ```

3. **数据库连接失败**
   ```bash
   # 检查MySQL服务
   sudo systemctl status mysql
   # 测试数据库连接
   mysql -u kuowen_user -p kuowen_exhibition
   ```

4. **上传文件失败**
   ```bash
   # 检查上传目录权限
   ls -la /var/www/admin.kuowenexpo.com/uploads/
   sudo chmod 777 /var/www/admin.kuowenexpo.com/uploads/
   ```

### 日志查看

```bash
# Nginx错误日志
sudo tail -f /var/log/nginx/error.log

# PHP错误日志
sudo tail -f /var/log/php8.0-fpm.log

# MySQL错误日志
sudo tail -f /var/log/mysql/error.log
```

## 📞 部署后检查清单

- [ ] 前端网站访问正常
- [ ] 后台管理系统登录正常
- [ ] SSL证书配置成功
- [ ] 数据库连接正常
- [ ] 文件上传功能正常
- [ ] 修改了默认密码
- [ ] 配置了防火墙
- [ ] 设置了备份策略
- [ ] 性能优化完成
- [ ] 监控告警配置

## 🎉 完成部署

恭喜！您已经成功部署了阔文展览网站。现在您可以：

1. 在后台管理系统中配置公司信息
2. 添加服务项目和案例展示
3. 发布新闻资讯
4. 管理客户留言

如果遇到任何问题，请参考故障排除部分或联系技术支持。

---

**重要提醒**：
- 定期更新系统和软件包
- 监控网站性能和安全状态
- 定期检查备份文件
- 保持SSL证书有效性