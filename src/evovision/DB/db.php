<?php
/**
 * Created by PhpStorm.
 * User: bogdanandrei
 * Date: 9/25/14
 * Time: 12:26 PM
 */
namespace evovision\DB;
use PDO;

class db {

    public static $config;

    private $_db = null;
    protected static $_instance;
    private function __construct(){
        if(!isset(self::$config)) throw new \Exception('Database $config is not defined');
	$this->_db = new PDO(self::$config['connections'][self::$config['default']]['driver'] . ":host=" . self::$config['connections'][self::$config['default']]['host'] . ";dbname=" . self::$config['connections'][self::$config['default']]['database'], self::$config['connections'][self::$config['default']]['username'], self::$config['connections'][self::$config['default']]['password']);

    }
    private function __clone(){}
    private function __wakeup(){}

    public static function getInstance(){
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * Initiates a transaction
     *
     * @return bool
     */
    public function beginTransaction() {
        return $this->_db->beginTransaction();
    }

    /**
     * Commits a transaction
     *
     * @return bool
     */
    public function commit() {
        return $this->_db->commit();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle
     *
     * @return string
     */
    public function errorCode() {
        return $this->_db->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @return array
     */
    public function errorInfo() {
        return $this->_db->errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement
     */
    public function exec($statement) {
        return $this->_db->exec($statement);
    }

    /**
     * Retrieve a database connection attribute
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute($attribute) {
        return $this->_db->getAttribute($attribute);
    }

    /**
     * Return an array of available PDO drivers
     *
     * @return array
     */
    public function getAvailableDrivers(){
        return $this->_db->getAvailableDrivers();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function lastInsertId($name = null) {
        return $this->_db->lastInsertId($name);
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @param string $statement A valid SQL statement for the target database server
     * @param array $driver_options Array of one or more key=>value pairs to set attribute values for the PDOStatement obj
    returned
     * @return PDOStatement
     */
    public function prepare ($statement, $driver_options=false) {
        if(!$driver_options) $driver_options=array();
        return $this->_db->prepare($statement, $driver_options);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function query($statement) {
        return $this->_db->query($statement);
    }

    public function insert($table, $array){
        $buildFields = '';
        $buildValues = '';
        $sendValues = array();
        if (is_array($array)) {
            foreach($array as $key => $value) {
                $buildFields .= ($buildFields=='') ? $key : ", ".$key;
                $buildValues .= ($buildValues=='') ? ":".$key : ", :".$key;
                $sendValues = array_merge($sendValues, array(":".$key=>$value));
            }
        }
        return $this->_db->prepare("INSERT INTO $table ($buildFields) VALUES($buildValues)")->execute($sendValues);
    }


    public function update($table, $id, $array){
        $update = '';
        $sendValues = array();
        if (is_array($array)) {
            foreach($array as $key => $value) {
                $update .= ($update=='') ? $key."= :".$key : ", ".$key."= :".$key ;
                $sendValues = array_merge($sendValues, array(":".$key=>$value));
            }
        }
        return $this->_db->prepare("UPDATE $table SET $update WHERE id=$id")->execute($sendValues);
    }
    
    public function getModel($table, $model, $id, $column = 'id'){
        $prepare = $this->_db->prepare("SELECT * FROM $table WHERE $column = :id LIMIT 1");
        $prepare->execute(array(':id'=>$id));
        $prepare->setFetchMode(\PDO::FETCH_CLASS, $model);
        return $prepare->fetch();
    }

     /**
     * Execute query and select one column only
     *
     * @param string $statement
     * @return mixed
     */
    public function queryFetchColAssoc($statement) {
        return $this->_db->query($statement)->fetchColumn();
    }

    /**
     * Quotes a string for use in a query
     *
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function quote ($input, $parameter_type=0) {
        return $this->_db->quote($input, $parameter_type);
    }

    /**
     * Rolls back a transaction
     *
     * @return bool
     */
    public function rollBack() {
        return $this->_db->rollBack();
    }

    /**
     * Set an attribute
     *
     * @param int $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value  ) {
        return $this->_db->setAttribute($attribute, $value);
    }


}
