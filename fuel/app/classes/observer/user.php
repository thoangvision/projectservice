<?php
class Observer_User extends Orm\Observer
{
  //---------------------------------------------------------------------------------------
  public function before_insert(Orm\Model $user) {
    $user->created_id = @Session::get('user')->id;
    $user->created_at = gmdate('Y-m-d H:i:s');
    $user->updated_id = @Session::get('user')->id;
    $user->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_update(Orm\Model $user) {
    $user->updated_id = @Session::get('user')->id;
    $user->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_delete(Orm\Model $user) {
  }

}