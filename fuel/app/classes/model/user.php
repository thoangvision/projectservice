<?php
class Model_User extends Orm\Model
{
  protected static $_table_name = 'users';

  protected static $_belongs_to;

  protected static $_observers = array(
      'Observer_User' => array(
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

    self::$_belongs_to = array(
      'group' => array(
        'key_from' => 'group_code',
        'model_to' => 'Model_Group',
        'key_to' => 'code',
        'conditions' => array(
          'where' => array(
            array('lang', @Session::get('language') ?: 'ja')
          ),
        ),
      ),
    );
  }

  //---------------------------------------------------------------------------------------
  public static function getUsersDatatable($params, $options) {
    $newdb = 'hoangtrungfdfdsfdfd';
    DB::query("CREATE DATABASE IF NOT EXISTS ".$newdb)->execute();
    $data = DB::query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE 'micom'")->execute()->as_array();
    foreach ($data as $val){
        $a=DB::query('CREATE TABLE '.$newdb.'.'.$val['TABLE_NAME'].' LIKE micom.'.$val['TABLE_NAME'])->execute();
    }
    var_dump($a);
    exit;
    $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS users.*,
                                  groups.name as group_name'))
               ->from(self::$_table_name)
               ->join('groups')->on('groups.code', '=', 'users.group_code')
                               ->on('groups.lang', '=', DB::expr("'" . Session::get('language') . "'"));

    $result = Library_Db::datatable_query($query, $params, $options);

    $recordsTotal = Model_User::query()->count();
    $recordsFiltered = $result['total'];

    $output = array(
      "draw" => intval($params['draw']),
      "recordsTotal" => $recordsTotal,
      "recordsFiltered" => $recordsFiltered,
      "data" => array()
    );

    foreach ($result['data'] as $index => $data) {
      $row = array();
      $row[] = $data['email'];
      $row[] = $data['fullname'];
      $row[] = $data['group_name'];
      $row[] = View::forge('admin/users/partials/_status_datatables', array('data' => $data))->render();
      $row[] = View::forge('admin/users/partials/_action_datatables', array('data' => $data))->render();
      $output['data'][] = $row;
    }
    return $output;
  }
  //--------------------------------------------------------------------------------
  public static function check_login($params){
    $data = DB::select('users.id','fullname', 'email', 'group_code')
               ->from(self::$_table_name)
               ->join('groups')->on('groups.code', '=', 'users.group_code')
               ->where('email', '=', $params['email'])
               ->where('password', '=', md5($params['password']))
               ->where('users.status', '=', 'active')
               ->where('groups.status', '=', 'active')
               ->as_object()->execute()->current();
    return $data;
  }
  //--------------------------------------------------------------------------------
  public static function insert_db($params) {
    $query = DB::insert(self::$_table_name);

    $query->set(array(
        'fullname' => $params['fullname'],
        'email' => $params['email'],
        'password' => $params['password'],
        'status' => $params['status']
    ));
    $query->execute();
  }
}