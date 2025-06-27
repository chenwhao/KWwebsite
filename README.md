# 阔文展览企业官网 - 最终版本

这是为上海阔文展览展示服务有限公司开发的完整企业官网解决方案，包含前端展示网站和后台管理系统。

## 📋 项目概述

### 前端网站特点
- **现代化设计**: 采用React 18 + TypeScript + TailwindCSS技术栈
- **响应式布局**: 完美适配桌面端、平板和移动设备
- **优化性能**: 图片懒加载、代码分割、SEO优化
- **交互体验**: 平滑滚动、动画效果、在线留言表单

### 后台管理系统特点
- **安全可靠**: CSRF保护、XSS防护、密码哈希、操作日志
- **功能完整**: 内容管理、文件管理、用户权限、数据统计
- **界面友好**: Bootstrap 5响应式设计、直观操作界面
- **易于维护**: 模块化架构、标准化代码、详细注释

## 🗂️ 项目结构

```
kuowen-final/
├── frontend/                 # 前端网站
│   ├── dist/                # 编译后的静态文件(可直接部署)
│   ├── src/                 # 源代码
│   ├── public/              # 公共资源
│   └── package.json         # 依赖配置
│
├── backend/                  # 后台管理系统
│   ├── config/              # 配置文件
│   │   ├── database.php     # 数据库配置
│   │   └── security.php     # 安全配置
│   ├── includes/            # 公共文件
│   │   ├── header.php       # 页面头部
│   │   ├── footer.php       # 页面底部
│   │   └── functions.php    # 通用函数
│   ├── modules/             # 功能模块
│   │   ├── company/         # 公司信息管理
│   │   ├── services/        # 服务项目管理
│   │   ├── cases/           # 案例管理
│   │   ├── news/            # 新闻管理
│   │   ├── messages/        # 留言管理
│   │   ├── files/           # 文件管理
│   │   └── users/           # 用户管理
│   ├── assets/              # 静态资源
│   │   ├── css/             # 样式文件
│   │   └── js/              # 脚本文件
│   ├── uploads/             # 上传文件目录
│   ├── index.php            # 登录页面
│   ├── dashboard.php        # 控制台首页
│   ├── logout.php           # 退出登录
│   └── install.sql          # 数据库结构
│
└── README.md                # 项目说明文档
```

## 🚀 快速部署

### 1. 前端网站部署

前端网站已经编译完成，可以直接部署：

```bash
# 将frontend/dist目录上传到网站根目录
# 确保web服务器指向dist目录
```

### 2. 后台管理系统部署

#### 环境要求
- PHP 7.4+ (推荐 PHP 8.0+)
- MySQL 5.7+ 或 MariaDB 10.3+
- Web服务器 (Nginx/Apache)
- 支持URL重写

#### 部署步骤

1. **上传文件**
   ```bash
   # 将backend目录上传到服务器
   # 例如：/var/www/admin.kuowenexpo.com/
   ```

2. **配置数据库**
   ```bash
   # 导入数据库结构
   mysql -u username -p database_name < install.sql
   ```

3. **配置数据库连接**
   ```php
   // 编辑 config/database.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **设置目录权限**
   ```bash
   chmod 755 -R backend/
   chmod 777 backend/uploads/
   ```

5. **配置Web服务器**
   
   **Nginx 配置示例：**
   ```nginx
   server {
       listen 80;
       server_name admin.kuowenexpo.com;
       root /var/www/admin.kuowenexpo.com;
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

## 🔐 默认登录信息

- **用户名**: admin
- **密码**: admin123

⚠️ **重要**: 首次登录后请立即修改默认密码！

## 📊 功能模块

### 1. 仪表板 (Dashboard)
- 系统概览和统计数据
- 快捷操作入口
- 最新动态展示
- 系统信息监控

### 2. 公司信息管理
- 基本信息设置
- 联系方式管理
- 社交媒体配置
- 营业时间设置

### 3. 服务项目管理
- 服务分类管理
- 服务项目增删改查
- 价格区间设置
- 服务特色描述

### 4. 案例管理
- 案例分类管理
- 项目信息录入
- 图片画廊管理
- 客户信息管理

### 5. 新闻管理
- 文章分类管理
- 内容发布系统
- 富文本编辑器
- 发布状态控制

### 6. 留言管理
- 客户留言查看
- 留言回复功能
- 状态跟踪管理
- 批量操作支持

### 7. 文件管理
- 文件上传功能
- 图片预览功能
- 文件分类整理
- 存储空间管理

### 8. 用户管理
- 管理员账户管理
- 角色权限控制
- 登录日志记录
- 安全设置配置

## 🛡️ 安全特性

- **CSRF保护**: 所有表单都包含CSRF令牌验证
- **XSS防护**: 输入数据自动过滤和转义
- **SQL注入防护**: 使用预处理语句
- **密码加密**: BCrypt哈希算法
- **操作日志**: 详细记录所有管理操作
- **登录日志**: 记录登录尝试和IP地址
- **文件上传安全**: 类型和大小限制

## 🔧 技术栈

### 前端技术
- React 18
- TypeScript
- TailwindCSS
- Vite
- React Router
- Framer Motion

### 后端技术
- PHP 8.0+
- MySQL 8.0+
- Bootstrap 5
- jQuery
- PDO数据库抽象层

## 📝 开发指南

### 添加新模块

1. **创建模块目录**
   ```bash
   mkdir backend/modules/your_module
   ```

2. **创建基础文件**
   ```php
   // index.php - 列表页面
   // edit.php - 编辑页面
   // view.php - 详情页面（可选）
   ```

3. **更新导航菜单**
   ```php
   // 编辑 includes/header.php 添加菜单项
   ```

4. **添加数据库表**
   ```sql
   -- 在 install.sql 中添加表结构
   ```

### 自定义样式

```css
/* 编辑 assets/css/admin.css */
/* 使用CSS变量进行主题定制 */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    /* 更多变量... */
}
```

### 扩展功能

```php
// 在 includes/functions.php 中添加通用函数
// 在各模块中实现具体业务逻辑
```

## 🐛 故障排除

### 常见问题

1. **数据库连接失败**
   - 检查数据库配置信息
   - 确认数据库服务是否启动
   - 验证用户权限

2. **文件上传失败**
   - 检查uploads目录权限
   - 确认PHP上传限制设置
   - 查看错误日志

3. **页面无法访问**
   - 检查Web服务器配置
   - 确认URL重写规则
   - 验证文件路径

### 日志查看

```bash
# PHP错误日志
tail -f /var/log/php_errors.log

# Nginx访问日志
tail -f /var/log/nginx/access.log

# 系统操作日志
# 登录后台 -> 查看操作日志模块
```

## 📞 技术支持

如果您在使用过程中遇到问题，可以：

1. 查看本文档的故障排除部分
2. 检查系统操作日志
3. 查看服务器错误日志
4. 联系技术支持

## 📄 版本信息

- **版本**: v1.0.0
- **发布日期**: 2025年6月27日
- **开发者**: MiniMax Agent
- **技术栈**: React + PHP + MySQL

## 📋 更新日志

### v1.0.0 (2025-06-27)
- 初始版本发布
- 完整的前端网站功能
- 完整的后台管理系统
- 安全特性实现
- 文档编写完成

---

**注意**: 这是一个生产就绪的系统，但在实际部署前请：
1. 修改默认密码
2. 配置SSL证书
3. 设置定期备份
4. 更新安全设置
5. 进行性能优化

祝您使用愉快！ 🎉