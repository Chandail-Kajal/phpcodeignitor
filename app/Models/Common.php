<?php

namespace App\Models;

use CodeIgniter\Model;

class Common extends Model
{
      public function insertData($table, $data)
      {
            $builder = $this->db->table($table);
            $builder->insert($data);
            return true;
      }

      public function updateData($table, $where, $data)
      {
            $builder = $this->db->table($table);
            $builder->where($where);
            return $builder->update($data);
            
      }

      public function deleteData($table, $where)
      {
            $builder = $this->db->table($table);
            $builder->where($where);
            return $builder->delete();
      }

      public function getRecords($table){
            $builder = $this->db->table($table)->where(array())->get();
            return $builder->getResult();
      }

}