<?php
class Controller_Api_Auths extends Controller_Rest {

  //---------------------------------------------------------------------------------------
  public function post_login() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

    $params = Input::post();

    $private_key = Config::get('security.private_key');
    $user = Model_User::check_login($params);

    if (empty($user)) {
      $this->response([
          'status' => 'Error',
          'code' => Exception_Code::LOGIN_ER,
          'message' => Exception_Code::getMessage(Exception_Code::LOGIN_ER),
      ]);
    }
    else {
      $payload = array(
          'iss' => 'Vision-VietNam',
          'issuedAt'=> time(),
          'exp' => time() + 360,
          'data' => $user,
      );
      $this->response([
          'status' => 'success',
          'code' => Exception_Code::LOGIN_OK,
          'message' => Exception_Code::getMessage(Exception_Code::LOGIN_OK),
          'token' => Library_Jwt::encode($payload, $private_key)
      ]);
    }
  }
}
