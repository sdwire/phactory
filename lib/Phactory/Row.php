<?

class Phactory_Row {
    protected $_table;
    protected $_storage = array();

    public function __construct($table, $data) {
        $this->_table = $table;
        foreach($data as $key => $value) {
            $this->_storage[$key] = $value;
        }
    }

    public function getId() {
        $pk = $this->_getPrimaryKey();
        return $this->_storage[$pk];
    }

    public function save() {
        $pdo = Phactory::getConnection();
        $sql = "INSERT INTO {$this->_table} (";

        $data = array();
        $params = array();
        foreach($this->_storage as $key => $value) {
            $data["`$key`"] = ":$key";
            $params[":$key"] = $value;
        }

        $keys = array_keys($data);
        $values = array_values($data);

        $sql .= join(',', $keys);
        $sql .= ") VALUES (";
        $sql .= join(',', $values);
        $sql .= ")";

        $stmt = $pdo->prepare($sql);
        $r = $stmt->execute($params);

        $id = $pdo->lastInsertId();

        if($pk = $this->_getPrimaryKey()) { 
            $this->_storage[$pk] = $id;
        }

        return $r;
    }

    public function __get($key) {
        return $this->_storage[$key];
    }

    public function __set($key, $value) {
        $this->_storage[$key] = $value;
    }

    protected function _getPrimaryKey() {
        $db_util = Phactory_DbUtilFactory::getDbUtil();
        return $db_util->getPrimaryKey($this->_table);
    }
}
