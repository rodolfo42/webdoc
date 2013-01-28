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
		$this->applyCreatedTimestamp($data);
		return $this->db->insert($this->table, $data);
	}
	
	public function update($id, $data) {
        if(isset($data['id'])) {
            unset($data['id']);
        }
		$this->applyModifiedTimestamp($data);
		return $this->db->update($this->table, $id, $data);
	}

    public function delete($id) {
        return $this->db->delete($this->table, $id);
    }
	
	protected function applyCreatedTimestamp(&$data) {
		$this->applyTimestamp($data, $this->createdTimestampField);
		$this->applyModifiedTimestamp($data);
	}

    protected function applyModifiedTimestamp(&$data) {
        $this->applyTimestamp($data, $this->timestampField);
    }
	
	private function applyTimestamp(&$data, $field) {
        if($field != null && !isset($data[$field])) {
            $data[$field] = $this->db->timestamp();
        }
	}
}