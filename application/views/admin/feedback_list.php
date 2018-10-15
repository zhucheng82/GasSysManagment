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
      <h3>反馈管理</h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span>反馈列表</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch" id="formSearch">
    <br />
    <tbody>
        <tr>
          <td>
            <select name="search_status">
              <?php foreach($status as $key=>$value){?>
              <option value="<?php echo $key?>" <?php if(!empty($arrParam['search_status']) && $key == $arrParam['search_status']){echo "selected";}?>><?php echo $value?></option>
              <?php }?>
            </select>
          </td>
          <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="搜索">&nbsp;</a></td>
        </tr>
      </tbody>
    </table>
  </form>
  
    <input type="hidden" name="form_images" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg"></th>
        </tr>
        <tr class="thead">
          <th></th>
          <th class="align-center">ID</th>
          <th class="align-center">反馈内容</th>
          <th class="align-center"><?php echo $status[$arrParam['search_status']];?></th>
          <th class="align-center">电话</th>
          <th class="align-center">反馈时间</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($feedback_list['rows'])){ ?>
        <?php foreach($feedback_list['rows'] as $k => $v){ ?>
        <tr class="hover">
          <td class="w24"></td>
          <td class="align-center"><?php echo $v['id'];?></td>
          <td class="align-center"><?php echo $v['content'];?></td>
          <td class="align-center"><?php echo $v['name'];?></td>
          <td class="align-center"><?php echo $v['user_name'];?></td>
          <td class="align-center"><?php echo date('Y.m.d H:i',$v['addtime']); ?></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10">没有符合条件的记录</td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if(!empty($feedback_list) && is_array($feedback_list)){ ?>
        <tr>
          <td colspan="16">
            <div class="pagination"> <?php echo $feedback_list['pages'];?> </div>
          </td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
</div>
</body>
</html>
<script>
$(function(){
    $('#ncsubmit').click(function(){
      $('input[name="op"]').val('member');$('#formSearch').submit();
    }); 
});
</script>
<script>
function verify(id){
    var id = id;
    var data = {
        id:id,
        status:1
    };
    $.post('<?php echo ADMIN_SITE_URL.'/comment/set_status'?>', data, function(response){
        if (response.status==1) {
          alert(response.msg);
          location.href=window.location.href;
        }else{
          alert(response.msg);
          return false;
        }
      }, 'json');
  };

function delete_comment(id){
    var id = id;
    var data = {
        id:id,
        status:-1
    };
    $.post('<?php echo ADMIN_SITE_URL.'/comment/set_status'?>', data, function(response){
        if (response.status==1) {
          alert(response.msg);
          location.href=window.location.href;
        }else{
          alert(response.msg);
          return false;
        }
      }, 'json');
  };
</script>
