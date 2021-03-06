<?php
class MY_Form_validation extends CI_Form_validation
{
    
    function set_ci_service(&$ci)
    {
    	$this->CI_SERVICE = $ci;
    }


    /**
	 * Executes the Validation routines
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @param	integer
	 * @return	mixed
	 */
	protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
	{
		// If the $_POST data is an array we will run a recursive call
		if (is_array($postdata))
		{
			foreach ($postdata as $key => $val)
			{
				$this->_execute($row, $rules, $val, $cycles);
				$cycles++;
			}

			return;
		}

		// --------------------------------------------------------------------

		// If the field is blank, but NOT required, no further tests are necessary
		$callback = FALSE;
		if ( ! in_array('required', $rules) AND is_null($postdata))
		{
			// Before we bail out, does the rule contain a callback?
			if (preg_match("/(callback_\w+(\[.*?\])?)/", implode(' ', $rules), $match))
			{
				$callback = TRUE;
				$rules = (array('1' => $match[1]));
			}
			else
			{
				return;
			}
		}

		// --------------------------------------------------------------------

		// Isset Test. Typically this rule will only apply to checkboxes.
		if (is_null($postdata) AND $callback == FALSE)
		{
			if (in_array('isset', $rules, TRUE) OR in_array('required', $rules))
			{
				// Set the message type
				$type = (in_array('required', $rules)) ? 'required' : 'isset';

				if ( ! isset($this->_error_messages[$type]))
				{
					if (FALSE === ($line = $this->CI->lang->line($type)))
					{
						$line = 'The field was not set';
					}
				}
				else
				{
					$line = $this->_error_messages[$type];
				}

				// Build the error message
				$message = sprintf($line, $this->_translate_fieldname($row['label']));

				// Save the error message
				$this->_field_data[$row['field']]['error'] = $message;

				if ( ! isset($this->_error_array[$row['field']]))
				{
					$this->_error_array[$row['field']] = $message;
				}
			}

			return;
		}

		// --------------------------------------------------------------------

		// Cycle through each rule and run it
		foreach ($rules As $rule)
		{
			$_in_array = FALSE;

			// We set the $postdata variable with the current data in our master array so that
			// each cycle of the loop is dealing with the processed data from the last cycle
			if ($row['is_array'] == TRUE AND is_array($this->_field_data[$row['field']]['postdata']))
			{
				// We shouldn't need this safety, but just in case there isn't an array index
				// associated with this cycle we'll bail out
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
				{
					continue;
				}

				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			}
			else
			{
				$postdata = $this->_field_data[$row['field']]['postdata'];
			}

			// --------------------------------------------------------------------

			// Is the rule a callback?
			$callback = FALSE;
			if (substr($rule, 0, 9) == 'callback_')
			{
				$rule = substr($rule, 9);
				$callback = TRUE;
			}

			// Strip the parameter (if exists) from the rule
			// Rules can contain a parameter: max_length[5]
			$param = FALSE;
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match))
			{
				$rule	= $match[1];
				$param	= $match[2];
			}

			// Call the function that corresponds to the rule
			if ($callback === TRUE)
			{
				if ( method_exists($this->CI, $rule))
				{
					$result = $this->CI->$rule($postdata, $param);
				}
				elseif ( isset($this->CI_SERVICE) && method_exists($this->CI_SERVICE, $rule))
				{
					$result = $this->CI_SERVICE->$rule($postdata, $param);
				}
				else
				{
					continue;
				}

				// Run the function and grab the result

				// Re-assign the result to the master data array
				if ($_in_array == TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}

				// If the field isn't required and we just processed a callback we'll move on...
				if ( ! in_array('required', $rules, TRUE) AND $result !== FALSE)
				{
					continue;
				}
			}
			else
			{
				if ( ! method_exists($this, $rule))
				{
					// If our own wrapper function doesn't exist we see if a native PHP function does.
					// Users can use any native PHP function call that has one param.
					if (function_exists($rule))
					{
						$result = $rule($postdata);

						if ($_in_array == TRUE)
						{
							$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
						}
						else
						{
							$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
						}
					}
					else
					{
						log_message('debug', "Unable to find validation rule: ".$rule);
					}

					continue;
				}

				$result = $this->$rule($postdata, $param);

				if ($_in_array == TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}
			}

			// Did the rule test negatively?  If so, grab the error.
			if ($result === FALSE)
			{
				if ( ! isset($this->_error_messages[$rule]))
				{
					if (FALSE === ($line = $this->CI->lang->line($rule)))
					{
						$line = 'Unable to access an error message corresponding to your field name.';
					}
				}
				else
				{
					$line = $this->_error_messages[$rule];
				}

				// Is the parameter we are inserting into the error message the name
				// of another field?  If so we need to grab its "field label"
				if (isset($this->_field_data[$param]) AND isset($this->_field_data[$param]['label']))
				{
					$param = $this->_translate_fieldname($this->_field_data[$param]['label']);
				}
				
				//start  south 非法词汇 
				if ($rule=='vaild_black_keyword' && isset($this->black_keyword))
				{
					$param = $this->black_keyword;
					
				}
				//south end
				
				
				// Build the error message
				$message = sprintf($line, $this->_translate_fieldname($row['label']), $param);

				// Save the error message
				$this->_field_data[$row['field']]['error'] = $message;

				if ( ! isset($this->_error_array[$row['field']]))
				{
					$this->_error_array[$row['field']] = $message;
				}

				return;
			}
		}
	}

    function getErrors(){
    	$errors = array();
    	foreach($this->_field_data as $k=>$v){
    		if ($v['error'])$errors[$k]=$v['error'];
    	}
    	return $errors;
    }
    
    //黑名单关键字
    function vaild_black_keyword($val)
    {
    	if ($val && $black = check_black_keyword($val))
    	{
    		$this->black_keyword = $black;
    		return FALSE;
    	}
    	return TRUE;
    }
    
    //验证时间格式
	function vaild_date($date) 
	{
		if ($date) 
		{
			$arr = explode('-', $date);
			if (checkdate($arr['1'], $arr['2'], $arr['0'])) 
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
    
    //验证时间格式
	function greater_date($date, $min) 
	{
		return $date > $min;
	}
    
    
/*******用户账号相关***********/
    
    //验证注册邮箱是否已存在
    function exist_user_account($val)
    {
    	$CI =& get_instance();
		if ($val && $CI->User_model->count(array('username'=>$val)) == 0)
		{
			return TRUE;
		}
    	
    	return FALSE;
    }
    
    //验证注册邮箱是否已存在
    function exist_user_email($val)
    {
    	$CI =& get_instance();
		if ($val && $CI->User_model->count(array('email'=>$val))== 0  && M('user_contact')->count(array('email'=>$val)) == 0)
		{
			return TRUE;
		}
    	
    	return FALSE;
    }
    

    //验证手机是否合法
    function valid_mobile($val)
    {
		if (is_mobile($val))
		{
			return TRUE;
		}
    	
    	return FALSE;
    }
    
    //验证手机是否已存在
    function exist_user_mobile($val)
    {
    	$CI =& get_instance();
		if ($val && $CI->User_model->count(array('username'=>$val)) == 0 && $CI->User_model->count(array('mobile'=>$val)) == 0)
		{
			return TRUE;
		}
    	return FALSE;
    }

    
}

?>