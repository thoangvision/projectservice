<?php

class Controller_Admin_Customers extends Controller_Admin_Application
{
  public function before() {
    parent::before();
  }

  //---------------------------------------------------------------------------------------
  public function action_index() {
    $this->template->content = View::forge('admin/customers/index');
    $this->template->content->title = __('list_customers');
    $this->template->content->user = $this->user;
    $this->template->content->status = Model_Customer::$status;
  }

  //---------------------------------------------------------------------------------------
  public function action_load_customers_datatables() {
    $options = [
      'columns' => array('customers.company_name', 'customers.email', 'modules.name', 'modules.value')
    ];

    $params = Input::get();

    $output = Model_Customer::getCustomerDatatable($params, $options);

    $this->response('200', 'success', $output, true);
  }


}