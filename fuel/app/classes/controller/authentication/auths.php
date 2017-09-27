<?php

class Controller_Authentication_Auths extends Controller_Authentication_Application
{
  //---------------------------------------------------------------------------------------
  public function action_sign_in() {
    $user = Session::get('user');

    if (!empty($user)) Response::redirect_back('/admin/users/index');

    $this->template->title = __('mi_com_registration');
    $this->template->content = View::forge('authentication/auths/sign_in');
  }

  //---------------------------------------------------------------------------------------
  public function action_login() {
    $email = Input::post('email');
    $password = Input::post('password');

    $user = Model_User::find('first', array(
      'where' => array(
        array('email' => $email, 'password' => md5($password), 'status' => 'active')
      )
    ));
    
    if (empty($user)) {
      Session::set_flash('errors', ['ádsadsadsa'] );
      Response::redirect_back();
    } else {
      Session::destroy();
      Session::set('user', $user);
      Response::redirect('/admin/users/index');
    }
  }

  //---------------------------------------------------------------------------------------
  public function action_logout() {
    Session::destroy();
    Response::redirect('/authentication/auths/sign_in');
  }
}
