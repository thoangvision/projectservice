<?php
class Observer_Module extends Orm\Observer
{
  //---------------------------------------------------------------------------------------
  public function before_insert(Orm\Model $module) {
    $module->created_id = Session::get('user')->id;
    $module->created_at = gmdate('Y-m-d H:i:s');
    $module->updated_id = Session::get('user')->id;
    $module->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_update(Orm\Model $module) {
    $module->updated_id = Session::get('user')->id;
    $module->updated_at = gmdate('Y-m-d H:i:s');
  }
}