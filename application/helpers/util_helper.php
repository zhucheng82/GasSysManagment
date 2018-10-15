<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 常用函数
 * @version $Id: util_helper.php 3761 2013-05-24 02:10:24Z trjcn_pss $
 */


/**
 * 创建目录
 * @param $path
 * @return unknown_type
 */
function mkpath($path)
{
	!is_dir($path) && mkdir($path, 0777, TRUE);
}


/**
 * 
 * @return unknown_type
 */
function header_move($url)
{
	header( "HTTP/1.1 301 Moved Permanently");
	header('location:'.$url);
	exit;
}

function is_splider()
{
	if (empty($_SERVER['HTTP_USER_AGENT']))
	{
		return FALSE;
	}

	$searchEngineBot = array(
	'spider',
	'google',
	'msnbot',
	'yodaobot',
	'youdaobot',
	'yahoo',
	'crawler',
	);

	$spider = strtolower($_SERVER['HTTP_USER_AGENT']);
	foreach ($searchEngineBot as $item)
	{
		if (strpos($spider, $item)!== false)
		{
			return TRUE;
		}
	}
	return FALSE;
}



/**
 * 中文截取
 * @param $str
 * @param $length
 * @param $suffix
 * @return unknown_type
 */
function sub_str($str, $length, $suffix=true)
{
	$str = strip_tags(htmlspecialchars_decode($str));
	if (strlen($str) <= $length )
	{
		return $str;
	}
	else
	{
		$i = 0;
		while ($i < $length)
		{
			$StringTMP = substr($str,$i,1);
			if ( ord($StringTMP) >=224 )
			{
				$StringTMP = substr($str,$i,3);
				$i = $i + 3;
			}
			elseif( ord($StringTMP) >=192 )
			{
				$StringTMP = substr($str,$i,2);
				$i = $i + 2;
			}
			else
			{
				$i = $i + 1;
			}
			$StringLast[] = $StringTMP;
		}
		$StringLast = implode("",$StringLast);
		if($suffix)
		{
			$StringLast .= $end;
		}
		return $StringLast;
	}
}

/**
 * 部分字符串
 * */
function hiddenStr($str,$type,$length=3,$suffix="***"){
	if($str){
		$len	=	mb_strlen($str,"utf-8");
		if($type == 1){// 字**符
			$i    = ceil($len/4);
			$j    = $len-($i*2);
			$k    = $i+$j;
			return mb_substr($str,0,$i,"utf-8").$suffix.mb_substr($str,$k,$len,"utf-8");
		}else if($type ==2){// **字符
			return $suffix.mb_substr($str,3,$len,"utf-8");
		}
		else{//字符***
			return mb_substr($str,0,$len-3,"utf-8")."***";
		}
	}
}



function is_email($str)
{

	return (preg_match('/^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/',$str) ? true : false );

}

function is_mobile($str,$is_more=FALSE)
{
	if($is_more)
	{
		$_str=explode(",",$str);
		$count  =       count($_str);
		for($i=0;$i<$count;$i++)
		{
			if(!is_mobile($_str[$i]))return false;
		}
		return true;
	}
	else
	{
		return (preg_match('/1[3|5|7|8|][0-9]{9}/',$str) ? true : false );
	}
}


function is_tel($str, $is_more=FALSE)
{
	if($is_more)
	{
		$_str=explode(",",$str);
		$count = count($_str);
		for($i=0;$i<$count;$i++){
			if(!is_tel($_str[$i]))return false;
		}
		return true;
	}
	else
	{
		return (preg_match('/^\(?0?(10|2[0-57-9]|[3-9]\d{2})\)?-?\d{7,8}(-\d{0,50})?$/',$str) ? true : false );
	}

}


function alert($msg, $url=FALSE)
{
	echo '<script>alert("'.$msg.'");</script>';
	if (is_bool($url))
	{
		echo $url ? '<script>history.back();</script>' : '';
	}
	elseif($url)
	{
		echo '<script>location.href="'.$url.'";</script>';
	}
	exit;
}

/**
* 是否站内
*/
function is_from_inner()
{
	if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) return false;
	return preg_match('~http:\/\/(\w+)'.TRJ_DOMAIN.'~','http://'.$_SERVER['HTTP_REFERER']);
}

function utf_to_gbk($val)
{
	return iconv('utf-8', 'gbk', $val);
}

function gbk_to_utf($val)
{
	return iconv('gbk', 'utf-8', $val);
}

function check_pwd($pw) 
{
    if (strlen($pw) < 6) return 1;
    $c = 0;
    if (preg_match('/[a-z]+/', $pw)) $c++;
    if (preg_match('/[0-9]+/', $pw)) $c++;
    if (preg_match('/[^a-zA-Z0-9]+/', $pw)) $c++;
    if ($c < 2) {
        $s = "0123456789abcdefghigklmnopqrstuvwxyz";

        $arr = preg_split('//', strtolower($pw), -1, PREG_SPLIT_NO_EMPTY);
        $idx = strpos($s, $arr[0]);
        if ($idx > -1) {
            $arr2 = preg_split('//', $s, -1, PREG_SPLIT_NO_EMPTY);
            $len = count($arr);
            $tlen= count($arr2);
            for ($i = 0; $i < $len; $i++) {
                if ($idx + 1 >= $tlen || $arr[$i] != $arr2[$idx + $i]) {
                    $c++;
                    break;
                }
            }
        }
    }
    if ($c > 1) {
        if (strlen($pw) >= 8) $c++;
        if (preg_match('/[^a-zA-Z0-9]+/', $pw)) $c++;
    }

    if ($c > 4) $c = 4;

    return $c;
};

function search_filter($str)
{
	$str = str_replace(array('"',"'"), '', $str);
	if(strlen($str) > 100)$str = mb_substr($str, 0, 100, 'utf-8');
	return $str;
}
// ------------------------------------------------------------------------

/* End of file util_helper.php */
/* Location: ./application/frontend/heleprs/util_helper.php */
