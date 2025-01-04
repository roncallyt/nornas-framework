<?php

namespace Nornas;

use Nornas\Database;

class Model
{
    public function __construct()
    {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }
    
    public function getAll($order = "")
    {
        return $this->db->fetchAll($this->table, $order);
    }
    
    public function getById($id = 0, $selectableColumns = array("*"), $allRecords = false)
    {
        $referenceColumns = array(
            "id" => (int) $id
        );
        
        return $this->db->retrieve($this->table, (array) $referenceColumns, (array) $selectableColumns, (bool) $allRecords);
    }

    public function get($referenceColumns = array(), $selectableColumns = array("*"), $allRecords = false)
    {
        return $this->db->retrieve($this->table, (array) $referenceColumns, (array) $selectableColumns, (bool) $allRecords);
    }
    
    public function create($addedUpColumns = array(), $lastId = true)
    {
        return $this->db->create($this->table, (array) $addedUpColumns, (bool) $lastId);
    }
    
    public function update($referenceColumns = array(), $setableColumns = array())
    {
        return $this->db->update($this->table, (array) $referenceColumns, (array) $setableColumns);
    }
    
    public function delete($referenceColumns)
    {
        return $this->db->delete($this->table, (array) $referenceColumns);
    }

    public function queryJoin($referenceColumns, $query, $allRecords = false)
    {
        return $this->db->queryJoin((array) $referenceColumns, $query, (bool) $allRecords);
    }
}
