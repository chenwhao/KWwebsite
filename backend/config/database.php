<?php
/**
 * 数据库配置文件
 * 阔文展览后台管理系统
 */

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'kuowen_exhibition');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 数据库连接类
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $conn;

    public function connect() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            error_log("数据库连接错误: " . $e->getMessage());
            throw new Exception("数据库连接失败");
        }
        
        return $this->conn;
    }
    
    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
        }
        return $this->conn;
    }
}

// 全局数据库连接函数
function getDB() {
    static $database = null;
    if ($database === null) {
        $database = new Database();
    }
    return $database->getConnection();
}

// 执行查询的辅助函数
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

// 获取单条记录
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

// 获取多条记录
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

// 插入记录并返回ID
function insertRecord($sql, $params = []) {
    executeQuery($sql, $params);
    return getDB()->lastInsertId();
}

// 更新/删除记录并返回影响行数
function updateRecord($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}
?>