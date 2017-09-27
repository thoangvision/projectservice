<?php
class Observer_Customer extends Orm\Observer
{
  //---------------------------------------------------------------------------------------
  public function before_insert(Orm\Model $customer) {
    $customer->created_id = Session::get('user')->id;
    $customer->created_at = gmdate('Y-m-d H:i:s');
    $customer->updated_id = Session::get('user')->id;
    $customer->updated_at = gmdate('Y-m-d H:i:s');
  }

  //---------------------------------------------------------------------------------------
  public function before_update(Orm\Model $customer) {
    $customer->updated_id = Session::get('user')->id;
    $customer->updated_at = gmdate('Y-m-d H:i:s');
  }
}