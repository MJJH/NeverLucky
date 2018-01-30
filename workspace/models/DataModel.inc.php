<?php namespace datamodel;

# Autoload
spl_autoload_register(function($model) {
    $parts = explode('\\', $model);
    if ($parts[0] === __NAMESPACE__ && end($parts) !== "DataModel") {
        require "models/".end($parts).".class.php";
        
        $class = "\\datamodel\\".end($parts);
        $class::createTable();
    }
});

# Statics
$db_con = new \Medoo\Medoo([
    'database_type' => 'mysql',
    'database_name' => 'nl',
    'server'=> 'localhost',
    'username' => getenv('C9_USER'),
    'password'=> ''
    ]);


abstract class DataModel {
    private $_from_db;
    public $id;
    
    public static function db() {
        global $db_con;
        return $db_con;
    }
    
    public function __construct($object, $from_db=false) {
        $this->_from_db = $from_db;
        
        foreach ($object as $key => $value) {
            $this->$key = $value;    
        }
    }
    
    public static function from_db($row) {
        return new static($row, true);
    }
    
    public static function getAll($where=[]) {
        $data = static::db()->select(static::tablename, '*', $where);
        
        $objects = [];
        foreach ($data as $vals) {
            $objects[] = self::from_db($vals);
        }
        
        return $objects;
    }
    
    public function remove() {
        return static::db()->delete(static::tablename, ["id" => $this->id]);
    }
    
    public static function removeAll($where=[]) {
        return static::db()->delete(static::tablename, $where);
    }
    
    public static function getByID($id, $where=[]) {
        $data = static::db()->select(static::tablename, '*', array_merge(["id" => $id], $where));
        return self::from_db($data[0]);
    }
    
    public function save() {
        $this->validate();
        
        if ($this->_from_db) {
            # Update
            static::db()->update(static::tablename, $this->toArray(), ["id" => $this->id]);
            return static::getByID($this->id);
        } else {
            # Insert
            static::db()->insert(static::tablename, $this->toArray());
            
            if (!empty($error = static::db()->error()[2])) {
                throw new \Exception($error);
            } else {
                return static::getByID(static::db()->id());
            }
        }
    }
    
    public function toArray() {
        $as_array = get_object_vars($this);
        
        foreach ($as_array as $key => $value) {
            if (substr($key, 0, 1) === "_") {
                unset($as_array[$key]);
            }
        }
        
        return $as_array;
    }
    
    abstract protected function validate();
    abstract public static function createTable();
    
    
}