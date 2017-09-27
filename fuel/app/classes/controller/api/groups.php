<?php

class Controller_Api_Groups extends Controller_Api_Application {

  //---------------------------------------------------------------------------------------
  public function before() {
    parent::before();
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
  }

  //---------------------------------------------------------------------------------------
  public function get_list() {
    $options = [
        'columns' => array('groups.name', 'groups.status', 'groups.level'),
    ];

    $params = Input::get();

    $output = Model_Group::getGroupDatatable($params, $options);

    $this->response($output, Exception_Code::E_OK);
  }

  //---------------------------------------------------------------------------------------
  public function post_add() {
    $params = Input::param();
    var_dump($params);
    exit;
    try {

      DB::start_transaction();

      Model_Group::insert_db($params);

      $this->response(['message' => __('created_successfully')], Exception_Code::E_OK);

      DB::commit_transaction();
    } catch (Exception $e) {

      DB::rollback_transaction();

      $this->response(['message' => $e->getMessage(), 'code' => Exception_Code::E_INTERNAL_SERVER_ERROR]);
    }
  }

  //---------------------------------------------------------------------------------------
  public function put_update($code = '') {

    try {
      $params = Input::param();
//      $params['code'] = $code;
      var_dump($params);
      exit;

      DB::start_transaction();

      Model_Group::update_db($params);

      $this->response(['message' => __('created_successfully')], Exception_Code::E_OK);

      DB::commit_transaction();
    } catch (Exception $e) {
      DB::rollback_transaction();
      $this->response(['message' => $e->getMessage()], Exception_Code::E_INTERNAL_SERVER_ERROR);
    }
  }

}
