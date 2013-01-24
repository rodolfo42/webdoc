<?php
namespace Database;

class Database {

	private $_connection = NULL;

	public function __construct($hostname, $username, $password, $dbname, $charset = "utf8") {
		$this->_connection = mysql_connect($hostname, $username, $password);
		mysql_select_db($dbname, $this->_connection);
		mysql_set_charset($charset, $this->_connection);
	}

	public function getResults($sqlQuery) {
		$resultSet = $this->query($sqlQuery);
		$rows = array();
		if($resultSet != NULL) {
			while($row = mysql_fetch_array($resultSet, MYSQL_ASSOC)) {
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function query($sqlQuery) {
		$sqlQuery = trim($sqlQuery, ' ;').';';
		//logar($sqlQuery);
		$query = mysql_query($sqlQuery, $this->_connection);
		if(!$query) {
			$this->error("erro SQL: [%6d] => %s".PHP_EOL."Query: [%s]", mysql_errno($this->_connection), mysql_error($this->_connection), $sqlQuery);
		}
		return $query;
	}

	public function getLastInsertId() {
		return mysql_insert_id();
	}

    public function getNumAffectedRows() {
        return mysql_affected_rows();
    }

	public function getConnection() {
		return $this->_connection;
	}

	public function error($format) {
		if(func_num_args() > 1) {
			$args = func_get_args();
			array_shift($args);
			$message = vsprintf($format, $args);
		} else if(is_string($format)) {
			$message = $format;
		} else if(!is_resource($format)) {
			$message = "var dump:".PHP_EOL.var_export($format, true);
		} else {
			$message = "Resource => " . get_resource_type($handle);
		}

		die($message);
	}

	// DML
	
	public function select($table, $fields = "*") {
		if(is_array($fields)) {
			$fields = implode(', ', $fields);
		}
		$sqlQuery = "SELECT {$fields} FROM {$table}";
		return $this->getResults($sqlQuery);
	}

	public function insert($table, $data) {
		$sqlQuery = "INSERT INTO {$table} (";
		$sqlQuery .= $this->enumerateFields($data);
		$sqlQuery .= ") VALUES (";
		$sqlQuery .= $this->enumerateValues($data);
		$sqlQuery .= ")";

		$success = $this->query($sqlQuery);
		
		if($success) {
			$result = $this->getLastInsertId();
		} else {
			$result = FALSE;
		}
		
		return $result;
	}

    public function update($table, $id, $data) {
		$sqlQuery = "UPDATE {$table} SET ";
		$sqlQuery .= $this->enumerateAssignments($data);
		$sqlQuery .= " WHERE id = {$id}";

		$success = $this->query($sqlQuery);
		return !!$success;
	}

	// helper functions
	
	private function enumerateFields($data) {
		return implode(', ', array_keys($data));
	}
	
	private function enumerateValues($data) {
		$values = array();
		foreach($data as $value) {
			$values[] = $this->escape($value);
		}
		return implode(', ', $values);
	}

	private function enumerateAssignments($data) {
		$assign = array();
		foreach($data as $field => $value) {
			$assign[] = ($field.' = '.$this->escape($value));
		}
		return implode(', ', $assign);
	}

	public function escape($value) {
		if(is_string($value)) {
			$value = '"'.addslashes($value).'"';
		} else if(is_array($value)) {
			// TODO remover suporte a arrays, ma' pratica
			$value = '"'.serialize($value).'"';
		}
		return $value;
	}
	
	public function timestamp() {
		return date('Y-m-d H:i:s');
	}
}