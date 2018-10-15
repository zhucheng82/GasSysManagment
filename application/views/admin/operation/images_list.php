<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('admin_css','perfect-scrollbar.min.css','css');?>

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
        <li><a class="current" href="JavaScript:void(0);" ><span>轮播图</span></a></li>
        <li><a href="<?php echo ADMIN_SITE_URL.'/operation/images_add'?>"><span>新增轮播图</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>

  
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5>操作提示</h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
          <ul>
             <li>排序数值越大越靠前</li>
<!--            <li>图片类型无需上传标题</li>
            <li>热点类型无需上传图片</li> -->
          </ul>
        </td>
      </tr>
    </tbody>
  </table>
  <form method="post" id='form_images' >
    <input type="hidden" name="form_images" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg">列表</th>
        </tr>
        <tr class="thead">
          <th></th>
          <th class="align-center">ID</th>
          <th class="align-center">图片</th>
          <th class="align-center">网页路径</th>
          <th class="align-center">排序</th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($images_list['rows'])){ ?>
        <?php foreach($images_list['rows'] as $k => $v){ ?>
        <tr class="hover">
          <td class="w24"></td>
          <td class="align-center"><?php echo $v['id'];?></td>
          <td class="align-center"><?php if($v['picurl']){?><img src="<?php echo _get_image_url($v['picurl']); ?>" style="width:100px;"><?php }?></td>
          <td class="align-center"><?php echo $v['weburl']; ?></td>
          <td class="align-center"><?php echo $v['sort']; ?></td>
          <td class="w150 align-center">
            <a href="javascript:void(0)" image_id="<?php echo $v['id'];?>" class="delete_image">删除</a> |
            <a href="<?php echo ADMIN_SITE_URL.'/operation/images_add?id='.$v['id']; ?>">编辑</a>
           </td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10">没有符合条件的记录</td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if(!empty($images_list) && is_array($images_list)){ ?>
        <tr>
          <td colspan="16">
            <div class="pagination"> <?php echo $images_list['pages'];?> </div>
          </td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
</body>
</html>
<script>
$(function(){
    $('#ncsubmit').click(function(){
      $('input[name="op"]').val('member');$('#formSearch').submit();
    });

  $(".delete_image").live('click',function(){
    var id = $(this).attr('image_id');
    var p = $(this).parents('tr');
    sendPostData({id:id},"<?php echo ADMIN_SITE_URL.'/operation/delete'; ?>",function(res){
      if(res.code == 1){
        showTips('删除成功!');
        p.remove();
      }
    })
  });
});
</script>
