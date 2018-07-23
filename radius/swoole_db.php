<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/22 21:48
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace Radius;

use HuanL\Db\Driver\MySQL\MySQLDBConnect;
use PDO;

class swoole_db {

    /**
     * 连接对象
     * @var PDO
     */
    public $db;

    public $config;

    public function __construct($config) {
        $this->config = $config;
        $this->reconnect();
    }

    /**
     * 重新连接数据库
     */
    public function reconnect() {
        $this->db = null;
        $this->db = new MySQLDBConnect($this->config['user'],
            $this->config['password'], $this->config['database']
            , $this->config['prefix'], $this->config['host'], $this->config['port'], $this->config['param']
        );
    }

    /**
     * 执行sql语句
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query($sql) {
        return $this->db->query($sql);
    }

    public function prepare($sql) {
        $stmt = $this->db->stmt_init();
        if ($stmt->prepare($sql)) {
            $stmt->close();
        }
        return false;
    }


    public function select($where) {

    }
}

