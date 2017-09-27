<?php

/**
 * PHP Class
 *
 * LICENSE
 *
 * @author Nguyen Anh Khoa-VISIONVN
 * @created Sep 05, 2014 02:55:13 PM
 */

//namespace validate;

class Validation_Group{
    private $_arrError = array();
    private $_arrData;
    private $_validate;
    private $_upload;
    public function __construct($arrParam = null, $options = null) {
		$this->_arrData = $arrParam;

        $this->_validate = \Validation::forge('validate');

        /*=======================================================
         * Start - validate company name
         *==================================
         * =====================*/
        $this->_validate->add_callable('MyRules');
        if($arrParam['action'] == 'edit'){

            $this->_validate->add_field('name', 'グループ名', 'required')
                            ->add_rule('unique', 'mt_group.seq_id.name.' . $arrParam['seq_id']);
        }else{
            $this->_validate->add_callable('MyRules');
            $this->_validate->add_field('name', 'グループ名', 'required')
                            ->add_rule('unique', 'mt_group.seq_id.name');
        }

        if($arrParam['post_params']['group_default'] == 1 && $arrParam['post_params']['status'] == 'inactive'){
            $this->_arrError['status'] = 'デフォルトにするグループは必ず「Active」のステータスのものです。';
        }


    }
    public static function isVaid_add(){

        $val = \Validation::forge('validate');

//        $val->add_field('name', 'name', 'required');
        
        $val->add_field('level', 'level', 'required|valid_string[numeric]');
        
        $_errAarray = array();
        if(!$val->run()){
            foreach ($val->error() as $field=>$er){
                  $_errAarray[] = $er->get_message();
              }
        }
        return $_errAarray;
    }
    
    private function get_err($val){
        $_errAarray = array();
        if(!$val->run()){
            foreach ($val->error() as $field=>$er){
                  $_errAarray[] = $er->get_message();
              }
        }
        return $_errAarray;
    }

    public function getData(){
        $data = \Input::param();
		$arrFile = $this->upload();
		if(count($arrFile) > 0){
			$data = array_merge($data, $arrFile);
		}
		return $data;
    }

    //return true|false
    public function isVaild(){
        if ($this->_validate->run() && empty($this->_arrError)) {
            return true;
        }else{
        	$this->_arrError = array_merge($this->_arrError, $this->_validate->error_message());
            return false;
        }
    }

    public function getMessageErrors(){
       return $this->_arrError;
    }

    public function upload(){

    }

}