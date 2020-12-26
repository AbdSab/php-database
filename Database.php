<?php

class Database {
    private $hostname = "localhost";
    private $database = "";
    private $username = "root";
    private $password = "";

    private static $instance = null;

    private $pdo;
    private $query;
    private $full_query;

    public function __construct(){
        $this->query = [];
    }

    /**
     * Singleton's instance
     * @return Database|null
     */
    public static function getInstance(){
        if(self::$instance !== null) return self::$instance;
        $class = get_called_class();
        self::$instance = new $class();
        return self::$instance;
    }

    /**
     * Set the database hostname, by default localhost
     * @param $hostname
     * @return Database|null
     */
    public static function setHostname($hostname) {
        self::getInstance()->hostname = $hostname;
        return self::getInstance();
    }

    /**
     * Set username of the database, by default root
     * @param $username
     * @return Database|null
     */
    public static function setUsername($username) {
        self::getInstance()->username = $username;
        return self::getInstance();
    }

    /**
     * Set password of the database, empty by default
     * @param $password
     * @return Database|null
     */
    public static function setPassword($password) {
        self::getInstance()->password = $password;
        return self::getInstance();
    }

    /**
     * Set database name, required
     * @param $database
     * @return Database|null
     */
    public static function setDatabase($database) {
        self::getInstance()->database = $database;
        return self::getInstance();
    }

    /**
     * Connect to the database
     * @return Database|null
     */
    public static function connect() {
        $instance = self::getInstance();
        $instance->pdo = new PDO('mysql:host='.$instance->hostname.';dbname='.$instance->database.';charset=utf8', $instance->username, $instance->password);
        return $instance;
    }

    /**
     * Specify table
     * @param $table
     * @return Database|null
     */
    public static function from($table){
        self::getInstance()->query["from"] = $table;
        return self::getInstance();
    }

    /**
     * Fields to select
     * @param $fields
     * @return $this
     */
    public static function select($fields) {
        if(is_array($fields)) $fields_parsed = implode(",", $fields);
        else $fields_parsed = $fields;
        self::getInstance()->query["select"] = $fields_parsed;
        return self::getInstance();
    }

    /**
     * Where condition
     * @param $field
     * @param string $operator
     * @param $value
     * @return $this
     */
    public static function where($field, $operator = "=", $value) {
        if(!isset(self::getInstance()->query['where'])) self::getInstance()->query["where"] = "$field $operator $value";
        return self::getInstance();
    }

    /**
     * Limit results
     * @param $limit
     */
    public static function limit($limit) {
        self::getInstance()->query["limit"] = $limit;
        return self::getInstance();
    }

    /**
     * Execute a raw sql query
     */
    public static function raw($query) {
        self::getInstance()->full_query = $query;
        return self::getInstance()->executeQuery();
    }

    /**
     * Generate query from query list
     */
    private function generateQuery(){
        $instance = self::getInstance();
        $instance->full_query = "";
        if(isset($instance->query["select"])) $instance->full_query .= "select " . $instance->query['select'];
        else $instance->full_query .= "select *";
        $instance->full_query .= " ";
        $instance->full_query .= "from " . $instance->query['from'];
        $instance->full_query .= " ";
        if(isset($instance->query["where"])) $instance->full_query .= "where " . $instance->query['where'];
        $instance->full_query .= " ";
        if(isset($instance->query["limit"])) $instance->full_query .= "limit " . $instance->query['limit'];
    }

    /**
     * Get result of the current query
     * @return array
     */
    public static function get() {
        self::getInstance()->generateQuery();
        return self::getInstance()->executeQuery();
    }

    /**
     * Execute the query
     * @return array
     */
    public function executeQuery() {
        $stmt = self::getInstance()->pdo->query($this->full_query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
//        $results = [];
//        $class = get_called_class();
//        foreach($rows as $row){
//            $instance = new $class();
//            foreach($row as $key=>$value){
//                $instance->$key = $value;
//            }
//            $results[] = $instance;
//        }
//        return $results;
    }
}