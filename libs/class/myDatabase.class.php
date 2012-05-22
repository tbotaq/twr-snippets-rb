<?php

/* 
 * my php class for mysql
 */

class myDatabase{
    
    protected $url = 'localhost';
    protected $user = DB_USER_NAME;
    protected $pass = DB_PASSWORD;
    protected $db = DB_NAME;
    protected $connection = "";

    function __construct(){
        $this->connection = mysql_connect($this->url,$this->user,$this->pass) or die("connection failed");
        mysql_set_charset("utf8", $this->connection);       
        mysql_select_db($this->db,$this->connection) or die("selection failed");
        return $this->connection;
    }

    public function query($myQuery){
        $result = mysql_query($myQuery,$this->connection) or die("query failed");
        return $result;
    }
    
    public function fetchArray($result){
        $ret = array();
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
            $ret[] = $row;
        }
        return $ret;
    } 
     
    public function getRowsNum($result){
        return mysql_num_rows($result);
    }

    public function freeResult($result){
        return mysql_free_result($result);
    }

    public function closeConnection(){
        return mysql_close($this->connection);
    }
}