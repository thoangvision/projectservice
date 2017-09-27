<?php
class Observer_Privilege extends Orm\Observer
{
  //---------------------------------------------------------------------------------------
  public function before_insert(Orm\Model $privilege) {
    $privilege->created_id = Session::get('user')->id;
    $privilege->created_at = gmdate('Y-m-d H:i:s');
    $privilege->updated_id = Session::get('user')->id;
    $privilege->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_update(Orm\Model $privilege) {
    $privilege->updated_id = Session::get('user')->id;
    $privilege->updated_at = gmdate('Y-m-d H:i:s');
  }
}