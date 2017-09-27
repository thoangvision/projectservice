<?php

class Controller_Authentication_Application extends Controller_Template
{
  public $template = 'authentication/layouts/application';

  //---------------------------------------------------------------------------------------
  public function before() {
    parent::before();

    if (empty(Cookie::get('language'))) {
      Session::set('language', 'en');
      Cookie::set('language', 'en');
    } else {
      Session::set('language', Cookie::get('language'));
    }

    Config::set('language', Session::get('language'));
    Lang::load('lang.php');

    $this->check_csrf();
  }

  //---------------------------------------------------------------------------------------
  protected function check_post_method() {
    if (Input::method() != 'POST') {
      Session::set_flash('errors', [__('the_request_method_must_be_post')] );
      Redirect::redirect_back();
    }
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

}
