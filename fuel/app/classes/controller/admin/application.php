<?php

class Controller_Admin_Application extends Controller_Template
{
  public $template = 'admin/layouts/application';

  protected $user;

  //---------------------------------------------------------------------------------------
  public function before() {
    if (empty(Session::get('language'))) Session::set('language', 'en');

    Config::set('language', Session::get('language'));
    Lang::load('lang.php');

    parent::before();
    
    $this->store_back_uri();
    $this->check_csrf();
    $this->authenticate();

    $this->template->title = __('mi_com_registration');
    $this->template->user = $this->user;
  }

  //---------------------------------------------------------------------------------------
  protected function debug($variable) {
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
    exit;
  }

  //---------------------------------------------------------------------------------------
  protected function check_post_method() {
    if (Input::method() != 'POST') {
      Session::set_flash('errors', [__('the_request_method_must_be_post')] );
      Redirect::redirect_back();
    }
  }

  //---------------------------------------------------------------------------------------
  protected function response($code, $msg = null, $data = [], $custom = false) {
    if ($custom) {
      echo json_encode($data);
      exit;
    } else {
      echo json_encode(array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data
      ));
      exit;
    }
  }

  //---------------------------------------------------------------------------------------
  private function authenticate() {
    $this->user = Session::get('user');

    if (empty($this->user)) {
      Response::redirect('/authentication/auth/sign_in');
    }
  }

  //---------------------------------------------------------------------------------------
  private function store_back_uri() {
    if (empty(Session::get('back_uri')))
      Session::set('back_uri', Uri::create('/admin/users/index'));

    if (Input::method() != 'POST') {
      if (!empty(Input::referrer()))
        Session::set('back_uri', Input::referrer());
    }
  }

  //---------------------------------------------------------------------------------------
  private function check_csrf() {
    if (Input::method() == 'POST')
      if (Input::post('fuel_csrf_token') != 'test')
        if (!Security::check_token())  {
          Session::set_flash('errors', [__('csrf_error')] );
          Response::redirect_back();
        }
  }

  //---------------------------------------------------------------------------------------
  public function after($response){
    $response = parent::after($response);
    $response = $response->set_header('Cache-Control', 'no-store, no-cache, must-revalidate,  max-age=0');
    $response = $response->set_header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    $response = $response->set_header('Pragma', 'no-cache');
    return $response;
  }
}
