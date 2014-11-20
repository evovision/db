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

    public function prepare ($statement, $driver_options=false) {
        if(!$driver_options) $driver_options=array();
        return $this->_db->prepare($statement, $driver_options);
    }

    public function query($statement) {
        return $this->_db->query($statement);
    }

    public function lastInsertId($name = null) {
        return $this->_db->lastInsertId($name);
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
        return $this->_db
            ->prepare("UPDATE $table SET $update WHERE id=$id")
            ->execute($sendValues);
    }
    
    public function getModel($table, $model, $id, $column = 'id'){
        $prepare = $this->_db->prepare("SELECT * FROM $table WHERE $column = :id LIMIT 1");
        $prepare->execute(array(':id'=>$id));
        $prepare->setFetchMode(\PDO::FETCH_CLASS, $model);
        return $prepare->fetch();
    }
}
