<?php

namespace App\Traits;

trait DataTablesTrait
{
    /**
     * Helper to process DataTables Server-Side request
     */
    protected function processDataTables($model, array $searchFields = [], $customCondition = null, $joins = [])
    {
        $request = service('request');
        
        $limit = (int) ($request->getPost('length') ?? 10);
        $offset = (int) ($request->getPost('start') ?? 0);
        $search = $request->getPost('search')['value'] ?? '';
        
        $builder = $model->builder();
        
        // Handle Joins
        if (!empty($joins)) {
            foreach ($joins as $join) {
                // e.g., ['table' => 'anggota', 'cond' => 'anggota.id = simpanan.anggota_id', 'type' => 'left']
                $builder->join($join['table'], $join['cond'], $join['type'] ?? 'left');
            }
        }
        
        if (is_callable($customCondition)) {
            $customCondition($builder);
        }
        
        $totalData = $builder->countAllResults(false);
        
        if (!empty($search) && !empty($searchFields)) {
            $builder->groupStart();
            foreach ($searchFields as $i => $field) {
                if ($i === 0) {
                    $builder->like($field, $search);
                } else {
                    $builder->orLike($field, $search);
                }
            }
            $builder->groupEnd();
        }
        
        $totalFiltered = $builder->countAllResults(false);
        
        // Default ordering logic (can be extended if needed)
        $orderInfo = $request->getPost('order');
        $columnsInfo = $request->getPost('columns');
        if (!empty($orderInfo) && !empty($columnsInfo)) {
            $colIdx = $orderInfo[0]['column'];
            $colName = $columnsInfo[$colIdx]['data'] ?? null;
            $dir = $orderInfo[0]['dir'];
            // If colName corresponds to a real db column, order by it. But it's usually just index, so we stick to latest first for now
            // To keep it simple, we just order by ID DESC unless specified
        }
        $builder->orderBy($model->table . '.id', 'DESC');
        
        $data = $builder->limit($limit, $offset)->get()->getResultArray();
        
        return [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
            'offset' => $offset
        ];
    }
}
