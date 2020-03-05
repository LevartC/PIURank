<?php
class DBConn {
    private $host;
    private $user;
    private $pw;
    private $dbName;
    private $port;
    public $mysqli;
    
    function __construct() {
        $this->host = null;
        $this->user = null;
        $this->pw = null;
        $this->dbName = null;
        $this->mysqli = null;
    }

    function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function connect_default() {
        $host = 'piurank.com';
        $user = 'piurank';
        $pw = 'fodzld';
        $dbName = 'piurank';
        $port = 3307;
        $this->connect($host, $user, $pw, $dbName, $port);
    }

    public function connect($host, $user, $pw, $dbName, $port="3306"){
        if ($this->mysqli !== null) {
            $this->mysqli->close();
        }
        $this->host = $host;
        $this->user = $user;
        $this->pw = $pw;
        $this->dbName = $dbName;
        $this->port = $port;
        $this->mysqli = new mysqli($this->host, $this->user, $this->pw, $this->dbName, $this->port);
        if (mysqli_connect_errno()) {
            echo "mysqli connect failed: %s\n", mysqli_connect_error();
            $this->mysqli = null;
            return false;
        } else {
            $this->_setUTF8();
            return true;
        }
    }

    public function prepare($sql) {
        return ($this->mysqli === null) ? null : $this->mysqli->prepare($sql);
    }

    public function query($sql) {
        if ($this->mysqli === null) {
            $this->connect_default();
        }
        return $this->mysqli->query($sql);
    }

    public function last_insert_id() {
        return ($this->mysqli === null) ? null : mysqli_insert_id($this->mysqli);
    }

    public function fetch_array($res) {
        $row = mysqli_fetch_array($res);
        return $row;
    }

    public function fetch_array_all($res, $attr = null) {
        $arr = array();
        while ($row = mysqli_fetch_array($res)) {
            array_push($arr, ($attr === null) ? $row : $row[$attr]);
        }
        return $arr;
    }
    public function version() {
        mysqli_get_server_info();
    }

    public function commit() {
        $this->mysqli->commit();
    }

    public function rollback() {
        $this->mysqli->rollback();
    }

    public function begin_transaction($flag = 0) {
        $this->mysqli->begin_transaction($flag);
    }
    
    private function _setUTF8() {
        $this->mysqli->query("set session character_set_connection=utf8mb4;");
        $this->mysqli->query("set session character_set_results=utf8mb4;");
        $this->mysqli->query("set session character_set_client=utf8mb4;");
    }
}

?>