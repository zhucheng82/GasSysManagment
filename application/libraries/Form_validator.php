<?php

/**
 * 表单验证异常
 */
class FormException extends MY_Exception{}

/**
 * 表单验证
 */
class Form_validator{
	private $_validator_rules, $_errors;
	protected $form_data, $rules;

	/**
	 * 构造
	 */
	public function __construct(){
		$this->_validator_rules=array(
			'email'=>array(
				'type'=>'regexp',
				'pattern'=>"/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",
				'err_code'=>9901
			),
			'url'=>array(
				'type'=>'regexp',
				'pattern'=>"/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/",
				'err_code'=>9902
			),
			'qq'=>array(
				'type'=>'regexp',
				'pattern'=>"/^[1-9]\d{4,13}$/",
				'err_code'=>9903
			),
			'zip'=>array(
				'type'=>'regexp',
				'pattern'=>"/^[1-9]\d{5}$/",
				'err_code'=>9904
			),
			'idcard'=>array(
				'type'=>'regexp',
				'pattern'=>"/^\d{15}(\d{2}[A-Za-z0-9])?$/",
				'err_code'=>9905
			),
			'english'=>array(
				'type'=>'regexp',
				'pattern'=>"/^[A-Za-z]+$/",
				'err_code'=>9906
			),
			'mobile'=>array(
				'type'=>'regexp',
				'pattern'=>"/^((\(\d{3}\))|(\d{3}\-))?13\d{9}$/",
				'err_code'=>9907
			),
			'phone'=>array(
				'type'=>'regexp',
				'pattern'=>"/^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/",
				'err_code'=>9908
			),
			'number'=>array(
				'type'=>'func',
				'func'=>function($value){
					if(!is_numeric($value)){
						throw new FormException(9909);
					}
				},
			),
			'required'=>array(
				'type'=>'func',
				'func'=>function($value){
					$value=trim($value);
					if(strlen($value)<1){
						throw new FormException(9910);
					}
				}
			),
			'length'=>array(
				'type'=>'func',
				'func'=>function($value,$max_len,$min_len=null){
					$value=trim($value);
					$len=strlen($value);
					if($len>$max_len){
						throw new FormException(9911);
					}
					if($min_len && $len<$min_len&&$min_len){
						throw new FormException(9912);
					}
				}
			)
		);
	}

	/**
	 * 增加验证规则
	 *
	 * @param string $name 规则名称
	 * @param array $rule  规则数据
 *                     'type'=>'regexp|func'
 *                     'pattern'=>'当类型为regexp时，设置正则表达式',
 *                     'func'=>'当类型为func时，设置匿名处理函数'
	 * @param bool $is_overwrite 是否覆盖已有验证规则
	 *
	 * @return bool
	 */
	public function addValidatorRule($name,$rule,$is_overwrite=FALSE){
		if(!$is_overwrite && isset($this->_validator_rules[$name])){
			return FALSE;
		}
		$this->_validator_rules[$name]=$rule;
		return TRUE;
	}

	/**
	 * 获取表单验证错误信息
	 *
	 * @return mixed
	 */
	public function getErrors(){
		return $this->_errors;
	}

	/**
	 * 设置表单验证规则
	 *
	 * @param array      $rules 验证规则
	 * @param array|null $form_data 表单数据
	 *
	 * @return bool
	 */
	public function setRules($rules, $form_data=NULL){
		if(!$form_data){
			$form_data=$_POST;
		}
		$this->form_data=$form_data;
		foreach($rules as $name=>$rule){
			if(is_array($rule)) {
				$rule_list=$rule;
			}else{
				$rule_list=array($rule);
			}
			$this->rules[$name]=$rule_list;
		}
	}

	/**
	 * 表单验证
	 **
	 * @return bool
	 */
	public function validate(){
		$result=TRUE;
		foreach($this->rules as $name=>$rules){
			if(!$this->_isValidate($name,$rules)){
				$result=FALSE;
			}
		}
		return $result;
	}

	/**
	 * 是否验证通过
	 *
	 * @param string $element 表单元素名称
	 * @param array $rules 验证规则列表
	 *
	 * @return bool
	 */
	private function _isValidate($element,$rules){
		$result=TRUE;
		try{
			$form_data=$this->form_data;
			$value=isset($form_data[$element])?$form_data[$element]:null;
			foreach($rules as $name){
				$rule_info=$this->_parseRule($name);
				$rule=isset($this->_validator_rules[$rule_info['name']])?$this->_validator_rules[$rule_info['name']]:array();
				if(!$rule||!is_array($rule)){
					continue;
				}
				$type=$rule['type'];
				if($type=='regexp'){
					$result=preg_match($rule['pattern'],$value)!==FALSE;
					if(!$result){
						throw new FormException($rule['err_code']);
					}
				}
				elseif($type=='func'){
					$paras=array();
					if(isset($rule_info['paras'])&&is_array($rule_info['paras'])){
						$paras=$rule_info['paras'];
					}
					array_unshift($paras,$value);
					call_user_func_array($rule['func'],$paras);
				}
			}
		}
		catch(FormException $e){
			$this->_errors[$name]=$e->getMessage();
			$result=FALSE;
		}
		return $result;
	}

	/**
	 * 处理规则名称
	 *
	 * @param string $rule_name 规则名
	 *
	 * @return array
	 */
	private function _parseRule($rule_name){
		$rule=explode('(',$rule_name);
		$rule_info=array(
			'name'=>isset($rule[0])?$rule[0]:$rule_name,
			'paras'=>array()
		);
		if(isset($rule[1])){
			$para=str_replace(')','',$rule[1]);
			$rule_info['paras']=explode(',',$para);
		}
		return $rule_info;
	}
}
