<?php

class Controller_Admin_Users extends Controller_Admin_Application
{
  public function before() {
    parent::before();
  }

  //---------------------------------------------------------------------------------------
  public function action_index() {
    $this->template->content = View::forge('admin/users/index');
    $this->template->content->title = __('list_users');
    $this->template->content->user = $this->user;
    $this->template->content->status = Model_User::$status;
    $this->template->content->groups = Model_Group::find('all', array(
      'where' => array(
        array('lang' => Session::get('language'))
      )
    ));
  }

  //---------------------------------------------------------------------------------------
  public function action_load_users_datatables() {
    $options = [
      'columns' => array('users.fullname', 'users.email', 'groups.name')
    ];

    $params = Input::get();

    $output = Model_User::getUsersDatatable($params, $options);
    $this->response('200', 'success', $output, true);
  }

  //---------------------------------------------------------------------------------------
  public function action_add() {
    $groups = Model_Group::find('all', array(
      'where' => array(
        array('lang' => Session::get('language'))
      )
    ));
    $output = View::forge('admin/users/add', array('groups' =>$groups, 'status' => Model_User::$status, 'title' => __('add_a_user')))->render();
    
    echo json_encode($output);
    
    exit;

  }

  //---------------------------------------------------------------------------------------
  public function action_create() {

    $this->check_post_method();
    $val = \Validation::forge('user-form');
    $val->add('repassword', 'パスワード確認')->add_rule('match_field','password')->add_rule('min_length',6);
    $val->add('password', 'パスワード')->add_rule('match_field','repassword')->add_rule('min_length',6);
    $_errAarray=array();
    $fields = array();
    if(!$val->run()){
            foreach ($val->error() as $field=>$er){
                  $_errAarray['err'][] = $er->get_message();
                  $_errAarray['field'][] = $field;
                  
              }
        }
//        var_dump(json_encode($_errAarray));
        $this->response('500',$_errAarray);
    try {
      DB::start_transaction();

      $user = new Model_User();

      $user->email = Input::post('email');
      $user->password = md5(Input::post('password'));
      $user->fullname = Input::post('fullname');
      $user->group_code = Input::post('group_code');
      $user->status = Input::post('status');

      $user->save();

      DB::commit_transaction();
      $this->response('200', [__('user_has_been_created_successfully')]);

    } catch (Exception $e) {
      DB::rollback_transaction();
      $this->response('500', [$e->getMessage()]);
      
    }
  }

  //---------------------------------------------------------------------------------------
  public function action_edit() {
      
    $groups = Model_Group::find('all', array(
      'where' => array(
        array('lang' => Session::get('language'))
      )
    ));
    $user = Model_User::find(Input::get('id'));
    $output = View::forge('admin/users/edit', array('groups' => $groups, 'user' => $user, 'status' => Model_User::$status, 'title' => __('edit_a_user')))->render();
    
    echo json_encode($output);
    
    exit;
  }

  //---------------------------------------------------------------------------------------
  public function action_update() {
    $this->check_post_method();

    try {
      DB::start_transaction();

      $user = Model_User::find(Input::post('id'));

      if (!empty(Input::post('password')))
        $user->password = md5(Input::post('password'));
      
      $user->email = Input::post('email');
      $user->fullname = Input::post('fullname');
      $user->group_code = Input::post('group_code');
      $user->status = Input::post('status');

      $user->save();

      if ($user->id == $this->user->id)
        Session::set('user', $user);

      DB::commit_transaction();
      
      $this->response('200', [__('user_has_been_updated_successfully')]);
      
    } catch (Exception $e) {
      DB::rollback_transaction();

      $this->response('500', [$e->getMessage()]);
    }
  }

  //---------------------------------------------------------------------------------------
  public function action_delete() {
    $this->check_post_method();

    try {
      DB::start_transaction();

      $user = Model_User::find(Input::post('id'));
      $user->delete();

      DB::commit_transaction();

      $this->response('200', [__('user_has_been_deleted_successfully')] );

    } catch (Exception $e) {
      DB::rollback_transaction();
      $this->response('500', [$e->getMessage()] );
    }
  }
}