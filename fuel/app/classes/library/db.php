<?php
class Library_Db {

  //---------------------------------------------------------------------------------------
  public static function insert_on_duplicate_update($table_name, $columns, $values){
    $query = DB::insert($table_name);

    $query->columns($columns);

    foreach ($values as $value)
      $query->values($value);

    $str = '';

    foreach ($columns as $column) {
      $str .= $column . ' = VALUES(' . $column . '), ';
    }

    $query = $query->compile() . ' ON DUPLICATE KEY UPDATE ' . substr($str, 0, -2);
    DB::query($query)->execute();
  }

  //---------------------------------------------------------------------------------------
  public static function datatable_query($query, $params = null, $options = null) {
    //======================= SEARCH =================
    if (!empty($params['search']['value'])) $query = $query->where_open();

    foreach ($options['columns'] as $index => $column_name) {
      if (!empty($params['search']['value'])) {
        if ($params['columns'][$index]['searchable'] === 'true') {
          $query = $query->or_where($column_name, 'like', "%{$params['search']['value']}%");
        }
      }
    }

    if (!empty($params['search']['value'])) $query = $query->where_close();

    //============== SEARCH BY FIELD =================
    foreach ($options['columns'] as $index => $column_name) {
      if (!empty($params['columns'][$index]['search']['value'])) {
        if ($params['columns'][$index]['searchable'] === 'true') {

          if (!empty($options['columns_equal']) && in_array($index, $options['columns_equal']))
            $query = $query->where($column_name, $params['columns'][$index]['search']['value']);
          elseif (!empty($options['columns_range']) && in_array($index, $options['columns_range'])) {
            $date_range = explode(',', $params['columns'][$index]['search']['value']);
            if (!empty($date_range[0])) $query = $query->where($column_name, '>=', $date_range[0]);
            if (!empty($date_range[1])) $query = $query->where($column_name, '<=', $date_range[1]);
          }
          elseif (!empty($column_name))
            $query = $query->where($column_name, 'like', "%{$params['columns'][$index]['search']['value']}%");

        }
      }
    }

    //======================= Paging =================

    $query = $query->limit($params['length'])
                   ->offset($params['start']);

    //======================= Ordering =================

    foreach ($params['order'] as $index => $order) {
      $column_order = $options['columns'][$order['column']];
      $type_order = $order['dir'];

      $query = $query->order_by($column_order, $type_order);
    }

    $result['data'] = DB::query($query)->execute()->as_array();

    $total = DB::select(DB::expr("FOUND_ROWS() as count"))->execute()->as_array();
    $result['total'] = @$total[0]['count'] ?: 0;
    return $result;
  }
}
