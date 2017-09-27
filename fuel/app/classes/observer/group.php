<?php
class Observer_Group extends Orm\Observer
{
  //---------------------------------------------------------------------------------------
  public function before_insert(Orm\Model $group) {
    $group->created_id = @Session::get('user')->id;
    $group->created_at = gmdate('Y-m-d H:i:s');
    $group->updated_id = @Session::get('user')->id;
    $group->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_update(Orm\Model $group) {
    $group->updated_id = @Session::get('user')->id;
    $group->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_delete(Orm\Model $group) {
    // delete relationship
    DB::delete('users')->where('group_code', $group->code)->execute();
  }

}