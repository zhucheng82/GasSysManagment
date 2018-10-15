<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('admin_css','perfect-scrollbar.min.css','css');?>
<?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>

<?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('admin_js','perfect-scrollbar.min.js','js');?>

</head>
<body>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>首页管理</h3>
      <ul class="tab-base">
        <li><a href="<?php echo ADMIN_SITE_URL.'/operation/images_list'?>"><span>轮播图</span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo isset($info['id'])?'编辑轮播图':'新增轮播图';?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" >
    <input type="hidden" id="id" name="id" value="<?php if(isset($info['id'])){echo $info['id'];}?>" />
    <table class="table tb-type2 nobdb">
      <tbody>

        <tr>
          <td colspan="2" class="required"><label class="validation" for="result">上传图片:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <div class="upload_block">
              <span class="type-file-show"><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                <div class="type-file-preview"><img id="preview_picurl" src="<?php if(isset($info['picurl'])){ echo BASE_SITE_URL.'/'.$info['picurl'];}?>" onload="javascript:DrawImage(this,500,500);"></div>
              </span>
              <div class="f_note">
                  <input type="hidden"  name="picurl" id="picurl" value="<?php if(isset($info['picurl'])){echo $info['picurl'];}?>">
                  <em><i class="icoPro16"></i></em>
                  <div class="file_but">
                      <input id="picurl_upload" name="picurl_upload" value="<?php echo lang('adv_upload_img')?>" type="file" >
                  </div>
              </div>
            </div>
          </td>
          <td class="vatop tips"><span class="vatop rowform"></span></td>
        </tr>


        <tr class="noborder">
          <td colspan="2" class="required"><label class="" for="weburl">网页路径:<br></label>
<!--              <a title="-->
<!--                打开内嵌浏览器-->
<!--                borrowing://webview?url=http://m.zooernet.com&title=xxx&showTitle=1-->
<!--                电动车借款-->
<!--                borrowing://ebike?productId=1-->
<!--                租房借款-->
<!--                borrowing://rentingHouse?productId=2</div>">说明</a>-->

          </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="weburl" name="weburl" class="txt" value="<?php if(isset($info['weburl'])){echo $info['weburl'];}?>"></td>
            <td>   说明:
                <div style="margin-left:20px;font:12px 'Microsoft Yahei';">
                    打开内嵌浏览器设定例子：borrowing://webview?url=http://m.zooernet.com&title=xxx&showTitle=1<br>
                        （说明：“url=”后面是打开的链接网址，“title=”后面是界面的标题，“showTitle=1”表示用原生界面的导航栏，“showTitle=0”表示用H5页面自带导航栏）<br>
                    电动车借款项目设定列子：borrowing://ebike?productId=1<br>
                        （说明：“productId=”后面是电动车产品id，设置时，请对照“产品管理”栏目里的产品列表填写）<br>
                    租房借款项目设定列子：borrowing://rentingHouse?productId=2<br>
                        （说明“productId=”后面是租房产品id，设置时，请对照“产品管理”栏目里的产品列表填写）
                </div></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="" for="sort">排序:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="sort" name="sort" class="txt" onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)" onblur="this.v();" value="<?php if(isset($info['sort'])){echo $info['sort'];}?>"></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <input type="hidden" id="id" name="id" value="0">
          <td colspan="2"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表
$(document).ready(function(){
	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
	    if($("#add_form").valid()){
        var id = $("#id").val();
        var sort = $("#sort").val();
        var weburl = $("#weburl").val();
        var picurl = $("#picurl").val();
        var data = {
            id:id,
            sort:sort,
            picurl:picurl,
            weburl:weburl
        };
        $.post('<?php echo ADMIN_SITE_URL.'/operation/images_add_do'?>', data, function(response){
            if (response.status==1) {
              location.href='<?php echo ADMIN_SITE_URL.'/operation/images_list'?>';
            }else{
              alert(response.msg);
              return false;
            }
          }, 'json');
    		}
	});
	
	$("#add_form").validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            picurl : {
                required : true
            },
        },
        messages : {
            picurl : {
                required : '请上传图片'
            }
        }
	});
});
</script>
<script src="<?php echo _get_cfg_path('lib')?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<script type="text/javascript">
<?php $timestamp = time();?>
$(function() {
  upload_file('picurl','picurl','<?php echo $timestamp?>','<?php echo md5($this->config->item('encryption_key') . $timestamp );?>');
});
</script>
</body>
</html>
