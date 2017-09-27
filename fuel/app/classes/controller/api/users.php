<?php
class Controller_Api_Users extends Controller_Api_Groups {


  //------------------------------------------------------------------------------
  public function get_listUser() {

  }

  //---------------------------------------------------------------------------------------
  public function post_add() {
    $params = Input::post();
    try {

      DB::start_transaction();

      Model_User::insert_db($params);

      $this->response(['message' => __('created_successfully')], Exception_Code::E_OK);

      DB::commit_transaction();
    } catch (Exception $e) {

      DB::rollback_transaction();

      $this->response(['message' => $e->getMessage(), 'code' => Exception_Code::E_INTERNAL_SERVER_ERROR]);
    }
  }
}