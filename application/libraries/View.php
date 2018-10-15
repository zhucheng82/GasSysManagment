<?php
class View
{
	
	function json($data)
	{
		$callback = isset($_GET['callback']) ? $_GET['callback'] : '';
		$data = json_encode($data);
		if ($callback && preg_match('~^(jQuery)[\d\_]+$~', $callback))
		{
			echo $callback.'('.$data.')';
		}
		else
		{
			echo $data;
		}
		exit;
	}

	public function end($file){
		$this->display($file);
		$this->output();
		die;
	}
}
?>