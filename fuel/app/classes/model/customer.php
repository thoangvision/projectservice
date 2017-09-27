<?php
class Model_Customer extends Orm\Model
{
  protected static $_table_name = 'customers';
  protected static $_properties = array('id', 'fullname', 'company_name', 'email', 'address', 'phone', 'db_name', 'status', 'created_id', 'created_at', 'updated_id', 'updated_at');

  protected static $_many_many;

  protected static $_has_many;

  protected static $_observers = array(
    'Observer_Customer' => array(
      'events' => array('before_insert', 'before_update', 'before_delete'),
    )
  );
  public static $status = array(
    'active' => 'active',
    'inactive' => 'inactive',
  );
  //---------------------------------------------------------------------------------------
  public function __construct(array $data = array(), $new = true, $view = null, $cache = true) {
    parent::__construct($data, $new, $view, $cache);

    $this->initRelationship();
  }

  //---------------------------------------------------------------------------------------
  public function initRelationship() {
    self::$_many_many = array(
      'modules' => array(
        'key_from' => 'id',
        'key_through_from' => 'customer_id',
        'table_through' => 'privileges',
        'key_through_to' => 'module_code',
        'model_to' => 'Model_Module',
        'key_to' => 'code',
        'conditions' => array(
          'where' => array(
            array('lang', @Session::get('language') ?: Config::get('default_languague'))
          ),
        ),
      ),
    );

    self::$_has_many = array(
      'privileges' => array(
        'key_from' => 'id',
        'model_to' => 'Model_Privilege',
        'key_to' => 'customer_id',
      ),
    );
  }

  //---------------------------------------------------------------------------------------
  public static function getCustomerDatatable($params, $options) {
    $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS customers.id,
                                  customers.company_name,
                                  customers.email,
                                  modules.name,
                                  modules.value,
                                  modules.expire,
                                  customers.status,
                                  customers.created_at,
                                  created_user.fullname'
                                  ))
               ->from(self::$_table_name)
               ->join(['users', 'created_user'])->on('customers.created_id', '=', 'created_user.id')
               ->join(['users', 'updated_user'])->on('customers.updated_id', '=', 'updated_user.id')
               ->join('privileges')->on('privileges.customer_id', '=', 'customers.id')
               ->join('modules')->on('modules.code', '=', 'privileges.module_code');

    $result = Library_Db::datatable_query($query, $params, $options);

    $recordsTotal = DB::select(DB::expr('count(*) as count'))
                      ->from(self::$_table_name)
                      ->execute()->as_array();
    $recordsTotal = @$recordsTotal[0]['count'] ?: 0;
    $recordsFiltered = $result['total'];

    $output = array(
      "draw" => intval($params['draw']),
      "recordsTotal" => $recordsTotal,
      "recordsFiltered" => $recordsFiltered,
      "data" => array()
    );

    foreach ($result['data'] as $index => $data) {
      $row = array();
      $row[] = $data['company_name'];
      $row[] = $data['email'];
      $row[] = $data['name'];
      $row[] = $data['value'];
      $row[] = $data['expire'];
      $row[] = View::forge('admin/customers/partials/_status_datatables', array('data' => $data))->render();
      $row[] = View::forge('admin/customers/partials/_action_datatables', array('data' => $data))->render();
      $output['data'][] = $row;
    }

    return $output;
  }
}