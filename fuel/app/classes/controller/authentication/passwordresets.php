<?php

class Controller_Authentication_Passwordresets extends Controller_Authentication_Application
{
  //---------------------------------------------------------------------------------------
  public function action_new() {
    $this->template->title = __('forgot_password');
    $this->template->content = View::forge('authentication/passwordresets/new');
  }

  //---------------------------------------------------------------------------------------
  public function action_create() {
    $email_reset = Input::post('email');

    $user = Model_User::find('first', array(
      'where' => array(
        array('email' => $email_reset)
      )
    ));

    if (!empty($user)) {
      $user->reset_token = md5($user->email . gmdate('Y-m-d H:i:s'));
      $user->reset_sent_at = gmdate('Y-m-d H:i:s');
      $user->save();

      $body = View::forge('authentication/emails/password_resets');
      $body->user = $user;
      $body = $body->render();

      // SEND MAIL
      Library_Algorithm::send_email([
        'to' => $email_reset,
        'subject' => __('reset_password'),
        'body' => $body,
      ]);

      Session::set_flash('successes', [__('send_forgot_email_success_please_check_your_email')] );
      Response::redirect('/authentication/auth/sign_in');
    } else {
      Session::set_flash('errors', [__('email_does_not_exist')] );
      Response::redirect_back();
    }
  }

  //---------------------------------------------------------------------------------------
  public function action_edit() {
    $reset_token = Input::get('reset_token');

    $user = Model_User::find('first', array(
      'where' => array(
        array('reset_token' => $reset_token)
      )
    ));

    if (!empty($user) && strtotime($user->reset_sent_at . ' +2 hours') > strtotime('now') ) {
      $this->template->title = __('password_recovery');
      $this->template->content = View::forge('authentication/passwordresets/edit');
      $this->template->content->user = $user;
    } else {
      Session::set_flash('errors', [__('reset_token_expired')] );
      Response::redirect('/authentication/auth/sign_in');
    }
  }

  //---------------------------------------------------------------------------------------
  public function action_update() {
    $user = Model_User::find(Input::post('id'));

    if (empty($user)) Response::redirect('/authentication/auth/sign_in');

    $password = Input::post('password');
    $confirm_password = Input::post('confirm_password');

    if ($password == '') {
      Session::set_flash('errors', __('password_is_invalid'));
      Response::redirect_back();
    }

    if ($password == $confirm_password) {
      $user->password = md5($password);
      $user->reset_token = null;
      $user->reset_sent_at = null;
      $user->save();

      Session::set_flash('successes', __('password_has_been_reset'));
      Response::redirect('/authentication/auth/sign_in');
    } else {
      Session::set_flash('errors', __('password_does_not_match_the_confirm_password'));
      Response::redirect_back();
    }
  }
}
