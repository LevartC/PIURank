<?php
class DBConn {
    private $host;
    private $user;
    private $pw;
    private $dbName;
    private $mysqli;
    
    function __construct() {
        $this->host = 'piurank.com';
        $this->user = 'piurank';
        $this->pw = 'fodzld';
        $this->dbName = 'piurank';
        $this->port = 3307;
        $this->mysqli = new mysqli($this->host, $this->user, $this->pw, $this->dbName, $this->port);

        $this->mysqli->query("set session character_set_connection=utf8;");
        $this->mysqli->query("set session character_set_results=utf8;");
        $this->mysqli->query("set session character_set_client=utf8;");
    }

    function __destruct() {
        $this->mysqli->close();
    }

    function connect($host, $user, $pw, $dbName){
        $this->mysqli->close();
        $this->host = $host;
        $this->user = $user;
        $this->pw = $pw;
        $this->dbName = $dbName;
        $this->mysqli = new mysqli($this->host, $this->user, $this->pw, $this->dbName);
    }

    function query($sql) {
        return $this->mysqli->query($sql);
    }


    function fetch_array($res) {
        $row = mysqli_fetch_array($res);
        return $row;
    }

    function fetch_array_all($res, $attr = null) {
        $arr = array();
        while ($row = mysqli_fetch_array($res)) {
            array_push($arr, ($attr === null) ? $row : $row[$attr]);
        }
        return $arr;
    }
}
?>