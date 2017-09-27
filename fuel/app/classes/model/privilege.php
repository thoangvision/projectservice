<?php
class Model_Privilege extends Orm\Model
{
  protected static $_table_name = 'privileges';
  protected static $_properties = array('id', 'customer_id', 'module_code', 'status', 'created_id', 'created_at', 'updated_id', 'updated_at');

  protected static $_belongs_to;

  protected static $_observers = array(
    'Observer_Privilege' => array(
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
    self::$_belongs_to = array(
      'customer' => array(
        'key_from' => 'customer_id',
        'model_to' => 'Model_Customer',
        'key_to' => 'id',
      ),
      'module' => array(
        'key_from' => 'module_code',
        'model_to' => 'Model_Module',
        'key_to' => 'code',
        'conditions' => array(
          'where' => array(
            array('lang', @Session::get('language') ?: Config::get('default_languague'))
          ),
        ),
      ),
    );
  }
}