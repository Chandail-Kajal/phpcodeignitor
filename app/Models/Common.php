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

      public function getRecords($table, $where = array())
      {
            $builder = $this->db->table($table)->where($where)->get();
            return $builder->getResult();
      }

      public function getRecord($table, $where = array())
      {
            return $this->db->table($table)->where($where)->get()->getRowArray();
      }

      public function countFiltered($table, $searchColumns = [], $search = '')
      {
            $builder = $this->db->table($table);

            if (!empty($search) && !empty($searchColumns)) {
                  $builder->groupStart();
                  foreach ($searchColumns as $col) {
                        $builder->orLike($col, $search);
                  }
                  $builder->groupEnd();
            }

            return $builder->countAllResults();
      }

      public function getPaginatedRecords($table, $limit, $offset, $searchColumns = [], $search = '', $orderBy = null, $orderDir = 'asc')
      {
            $builder = $this->db->table($table);
            if (!empty($search) && !empty($searchColumns)) {
                  $builder->groupStart();
                  foreach ($searchColumns as $col) {
                        $builder->orLike($col, $search);
                  }
                  $builder->groupEnd();
            }
            if ($orderBy) {
                  $builder->orderBy($orderBy, $orderDir);
            }
            if ((int) $limit !== -1) {
                  $builder->limit($limit, $offset);
            }

            return $builder->get()->getResultArray();
      }

      public function countAllRows($table)
      {
            return $this->db->table($table)->countAll();
      }


}