<?php
/**
 * 数据库配置文件
 * 阔文展览后台管理系统 - 最终版本
 */

// 数据库配置 - 请根据您的实际配置修改
define('DB_HOST', 'localhost');
define('DB_NAME', 'kuowen_exhibition');
define('DB_USER', 'kuowen_admin');              // 请修改为您的数据库用户名
define('DB_PASS', 'your_password_here');       // 请修改为您的数据库密码
define('DB_CHARSET', 'utf8mb4');

/**
 * 数据库连接类
 */
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            error_log("数据库连接错误: " . $e->getMessage());
            throw new Exception("数据库连接失败");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}

/**
 * 获取数据库连接
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * 执行查询
 */
function executeQuery($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("SQL执行错误: " . $e->getMessage() . " SQL: " . $sql);
        throw new Exception("数据库操作失败");
    }
}

/**
 * 获取单条记录
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * 获取多条记录
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * 插入记录并返回ID
 */
function insertRecord($sql, $params = []) {
    executeQuery($sql, $params);
    return getDB()->lastInsertId();
}

/**
 * 更新/删除记录并返回影响行数
 */
function updateRecord($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}
?>
