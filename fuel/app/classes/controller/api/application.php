<?php

class Controller_Api_Application extends Controller_Rest
{
  //---------------------------------------------------------------------------------------
  public function before() {
    parent::before();
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With,If-Modified-Since, X-File-Name, Cache-Control,Authorization');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    if (empty(Cookie::get('language'))) {
      Session::set('language', 'en');
      Cookie::set('language', 'en');
    } else {
      Session::set('language', Cookie::get('language'));
    }

    Config::set('language', Session::get('language'));
    Lang::load('lang.php');
//    $requestHeader = Input::headers();
//
    $this->check_token();
  }

  //---------------------------------------------------------------------------------------
  private function check_csrf() {
    if (Input::method() == 'POST')
      switch (Input::post('fuel_csrf_token')) {
        case 'test':
          // do nothing
          break;

        default:
          if (!Security::check_token())  {
            Session::set_flash('errors', [__('csrf_error')] );
            Response::redirect_back();
          }
          break;
      }
  }
  public function check_token() {
    $requestHeader = Input::headers();
    $token = $requestHeader['Authorization'];
//    var_dump($token);
//    exit;
    $private_key = Config::get('security.private_key');
    $payload = Library_Jwt::decode($token, $private_key);
    if($payload == false){
      $this->response(['status' => 'error', 'code' => Exception_Code::TOKEN_ERROR, 'message' => 'token err']);
    }
    else {
      $this->response(['status' => 'success', 'code' => Exception_Code::E_OK, 'message' => 'success']);
    }
  }
}
