

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<meta http-equiv="MSThemeCompatible" content="Yes" />
<link rel="stylesheet" type="text/css" href="/res/admin/css/localupload/style_2_common.css" />
<link href="/res/admin/css/localupload/style.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="/res/admin/css/localupload/cymain.css" />
<script src="/res/lib/kindeditor/common.js" type="text/javascript"></script>

<script src="/res/admin/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="/res/lib/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="/res/lib/artDialog/plugins/iframeTools.js"></script>


</head>
<body style="background:#fff">
<?php if(empty($error)){?>
<div></div>
<!-- <div style="background:#fefbe4;border:1px solid #f3ecb9;color:#993300;padding:10px;width:90%;margin:40px auto 5px auto;">选中文件后点击上传按钮或者点击“从素材库选择”直接从已上传文件中选择</div> -->
<form enctype="multipart/form-data" action="" id="thumbForm" method="POST" style="font-size:14px;padding:30px 20px 10px 20px;">
<p id="picsize" style="margin-bottom:20px"></p>
<p><div><div style="font-size:14px;">选择本地文件：<br><br><input type="file" style="width:80%;border:1px solid #ddd" name="file"></input></div><div style="padding:20px 0;text-align:center;"><input id="submitbtn" name="doSubmit" type="submit" class="btnGreen" value="上传" onclick="this.value='上传中...'"></input> </div></p>
<input type="hidden" value="" id="width" name="width" /><input type="hidden" value="" id="height" name="height" />
</form>
<script>
if (art.dialog.data('width')) {
	document.getElementById('width').value = art.dialog.data('width');// 获取由主页面传递过来的数据
	document.getElementById('height').value = art.dialog.data('height');
	if(document.getElementById('height').value){
		$('#picsize').html('<span style="color:#930; font-size:14px;margin-bottom:20px;">图片最佳尺寸：宽'+document.getElementById('width').value+'px 高'+document.getElementById('height').value+'px</span>');
	}else{
		$('#picsize').html('<span style="color:red; font-size:14px;margin-bottom:20px;">图片宽高不限</span>');
	}
};
</script>
<?php }else{ ?>
<div style="text-align:center;line-height:140px;font-size:14px;"> <img src="/res/admin/images/export.png" align="absmiddle" /> 

<?php if($error==200){ echo '上传成功';}else{ echo "上传失败" ;} ?>
</div>
<script>
var domid=art.dialog.data('domid');
// 返回数据到主页面
function returnHomepage(url){
	var origin = artDialog.open.origin;
	var dom = origin.document.getElementById(domid);
	var domsrcid=domid+'_src';

	if(origin.document.getElementById(domsrcid)){
	//origin.document.getElementById(domsrcid).src=url;
	}
	
	//dom.value=url;
	setTimeout("art.dialog.close()", 1500 )
}
<?php if($error==200){ ?>
returnHomepage('<?php echo BASE_SITE_URL.$url;?>');
<?php } ?>
</script>
<?php } ?>
</body>
</html>