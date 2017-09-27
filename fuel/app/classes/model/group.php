<?php
class Model_Group extends Orm\Model
{
  protected static $_table_name = 'groups';

  protected static $_has_many;

  protected static $_observers = array(
    'Observer_Group' => array(
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

    self::$_has_many = array(
      'users' => array(
        'key_from' => 'code',
        'model_to' => 'Model_User',
        'key_to' => 'group_code',
      ),
    );
  }

  //---------------------------------------------------------------------------------------
  public static function getGroupDatatable($params, $options) {
    $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS groups.*'))
               ->from(self::$_table_name)
               ->where('lang', Session::get('language'));

    $result = Library_Db::datatable_query($query, $params, $options);

    $recordsTotal = DB::select(DB::expr('count(*) as count'))
                      ->from(self::$_table_name)
                      ->where('lang', Session::get('language'))
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
      $row[] = $data['name'];
      $row[] = $data['status'];
      $row[] = $data['code'];
      $output['data'][] = $row;
    }

    return $output;
  }

  //---------------------------------------------------------------------------------------
  public static function insert_db($params) {
      //en
      $group = new Model_Group();
      $group->name = @$params['name_en'] ?: null;
      $group->status = $params['status'];
      $group->lang = 'en';
      $group->save();

      $code = $group->id;

      $group->code = $code;
      $group->save();

      //vn
      $group = new Model_Group();
      $group->name = @$params['name_vn'] ?: null;
      $group->status = $params['status'];
      $group->lang = 'vn';
      $group->code = $code;
      $group->save();
  }

  public static function update_db($params) {

      // JA
      $group = Model_Group::find('first', array(
        'where' => array(
          array('code' => $params['code'], 'lang' => 'vn')
        )
      ));

     $group->name = @$params['name_en'] ?: null;
     $group->status = $params['status'];
     $group->save();

      // EN
     $group = Model_Group::find('first', array(
       'where' => array(
         array('code' => $params['code'], 'lang' => 'en')
       )
     ));

     $group->name = @$params['name_vn'] ?: null;
     $group->status = $params['status'];
     $group->save();
  }

}