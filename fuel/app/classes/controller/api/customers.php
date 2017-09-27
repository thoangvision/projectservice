<?php

class Controller_Api_Customers extends Controller_Api_Application
{
  //---------------------------------------------------------------------------------------
  public function before() {
    parent::before();
  }

  //---------------------------------------------------------------------------------------
  public function get_show() {
    try {
      $customer = Model_Customer::find(Input::post('id'));

      if (empty($customer))
        throw new Exception("Can't found customer");

      $customer->privileges;

      $data = json_encode([
        'code' => Exception_Code::E_OK,
        'data' => $customer->to_array(),
      ]);

      $this->response($data);

    } catch (Exception $e) {
      $data = json_encode([
        'code' => Exception_Code::E_INTERNAL_SERVER_ERROR,
        'msg' => [$e->getMessage()],
      ]);

      $this->response($data);
    }
  }
  public function post_user(){
      try {
      $user = Model_User::find(Input::post('id'));


      $data = json_encode([
        'code' => Exception_Code::E_OK,
        'data' => $user->to_array(),
      ]);

      $this->response($data);

    } catch (Exception $e) {
      $data = json_encode([
        'code' => Exception_Code::E_INTERNAL_SERVER_ERROR,
        'msg' => [$e->getMessage()],
      ]);

      $this->response($data);
    }
  }
}