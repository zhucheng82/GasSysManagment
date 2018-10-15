<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
  *毛卫华导出类
  */
class Excel {
	//$filename 文件名称
	//$head 头部信息
	//list  导出内容数组
	public function createExcel($filename='',$head=array(),$list=array()){
		$filename=iconv("UTF-8", "GB2312//IGNORE",$filename.".xls");
		ob_end_clean();
		header("Content-type:application/vnd.ms-excel");
		Header("Accept-Ranges:bytes");
		Header("Content-Disposition:attachment;filename=".$filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
		                xmlns:x="urn:schemas-microsoft-com:office:excel"
		                xmlns="http://www.w3.org/TR/REC-html40">
		             <head>
		                <meta http-equiv="expires" content="Mon, 06 Jan 1999 00:00:01 GMT">
		                <meta http-equiv=Content-Type content="text/html; charset=gb2312">
		                <!--[if gte mso 9]><xml>
		                <x:ExcelWorkbook>
		                <x:ExcelWorksheets>
		                  <x:ExcelWorksheet>
		                  <x:Name></x:Name>
		                  <x:WorksheetOptions>
		                    <x:DisplayGridlines/>
		                  </x:WorksheetOptions>
		                  </x:ExcelWorksheet>
		                </x:ExcelWorksheets>
		                </x:ExcelWorkbook>
		                </xml><![endif]-->
		            </head>';
			echo "<table><tr>";
				foreach ($head as $val) {
					echo "<th>".iconv("UTF-8", "GB2312//IGNORE",$val)."</th>";
				}
		    echo "</tr>";
		    //判断数组的维度
		    function getmaxdim($arr){

		    	if(!is_array($arr)){
		    		return 0;
		    	}else{
		    		$dimension = 0;
		   		foreach($arr as $item1){
		    		$t1 = getmaxdim($item1);
		    		if($t1>$dimension){$dimension = $t1;}
		    	}
		    	return $dimension+1;
			    }
		    }
		    $dimension = getmaxdim($list);
		    if($dimension == 1){//一维数组
		        echo "<tr>";
		    	foreach ($list as $k=>$v){
		    		echo "<td>".iconv("UTF-8", "GB2312//IGNORE",$v)."</td>";
		    	}
		    	echo "</tr>";
		    }
		    if($dimension == 2){//二维数组
		        foreach ($list as $item){
				echo "<tr>";
					foreach($item as $k=>$val){
						echo "<td>".iconv("UTF-8", "GB2312//IGNORE",$val)."</td>";
					}
				echo "</tr>";
				}
		    }
		echo "</table>";
	} 
}