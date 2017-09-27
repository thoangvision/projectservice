<?php
class Model_Module extends Orm\Model
{
  protected static $_table_name = 'modules';
  protected static $_properties = array('id', 'name', 'type', 'value', 'code', 'lang', 'status', 'created_id', 'created_at', 'updated_id', 'updated_at');

  protected static $_many_many;

  protected static $_has_many;

  protected static $_observers = array(
    'Observer_Module' => array(
      'events' => array('before_insert', 'before_update', 'before_delete'),
    )
  );

  //---------------------------------------------------------------------------------------
  public function __construct(array $data = array(), $new = true, $view = null, $cache = true) {
    parent::__construct($data, $new, $view, $cache);

    $this->initRelationship();
  }

  //---------------------------------------------------------------------------------------
  public function initRelationship() {
    self::$_many_many = array(
      'customers' => array(
        'key_from' => 'code',
        'key_through_from' => 'module_code',
        'table_through' => 'privileges',
        'key_through_to' => 'customer_id',
        'model_to' => 'Model_Customer',
        'key_to' => 'id',
      ),
    );

    self::$_has_many = array(
      'privileges' => array(
        'key_from' => 'code',
        'model_to' => 'Model_Privilege',
        'key_to' => 'module_code',
      ),
    );
  }
}