<?php
namespace Model;

use Database\Database;

class Model {
	
	public $table = null;
	
	public $createdTimestampField = null;
	public $timestampField = null;
	
	function __construct(Database $db, $table = null) {
		if($table != null) {
			$this->table = $table;
		}
		$this->db = $db;
	}
	
	public function findAll() {
		return $this->db->select($this->table);
	}
	
	public function addNew($data) {
		$this->checkCreatedTimestamp($data);
		return $this->db->insert($this->table, $data);
	}
	
	public function update($id, $data) {
        if(isset($data['id'])) {
            unset($data['id']);
        }
		$this->checkTimestamp($data);
		return $this->db->update($this->table, $id, $data);
	}
	
	protected function checkCreatedTimestamp(&$data) {
		$this->checkTimestamp($data, $this->createdTimestampField);
		$this->checkTimestamp($data);
	}
	
	protected function checkTimestamp(&$data, $field = null) {
		if($field == null) {
			$field = $this->timestampField;
		}
		if($field != null && !isset($data[$field])) {
			$data[$field] = $this->db->timestamp();
		}
	}
}