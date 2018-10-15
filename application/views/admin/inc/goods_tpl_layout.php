<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商家中心</title>

<?php echo _get_html_cssjs('seller_css','base.css','css');?>
<?php echo _get_html_cssjs('font','font-awesome/css/font-awesome.min.css','css');?>
<?php echo _get_html_cssjs('admin_css','seller_center.css','css');?>
<!--[if IE 7]>
  <?php echo _get_html_cssjs('font','font-awesome/font-awesome-ie7.min.css','css');?>
<![endif]-->
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';
var _CHARSET = '<?php echo strtolower(CHARSET);?>';
var SITEURL = '<?php echo BASE_SITE_URL;?>';
var RESURL = '<?php echo BASE_SITE_URL."/res";?>';
</script>
<?php echo _get_html_cssjs('seller_js','jquery.js,seller.js,waypoints.js,jquery-ui/jquery.ui.js,jquery.validation.min.js,common.js,member.js','js');?>
<script type="text/javascript" src="<?php echo _get_cfg_path('lib');?>dialog/dialog.js" id="dialog_js" charset="utf-8"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
	<?php echo _get_html_cssjs('seller_js','html5shiv.js,respond.min.js','js');?>
<![endif]-->
<!--[if IE 6]>
<?php echo _get_html_cssjs('seller_js','IE6_MAXMIX.js,IE6_PNG.js','js');?>
<script>
DD_belatedPNG.fix('.pngFix');
</script>
<script>
// <![CDATA[
if((window.navigator.appName.toUpperCase().indexOf("MICROSOFT")>=0)&&(document.execCommand))
try{
document.execCommand("BackgroundImageCache", false, true);
   }
catch(e){}
// ]]>
</script>
<![endif]-->



</head>
<body>


<?php echo _get_html_cssjs('seller_js','ToolTip.js','js');?>

<div class="ncsc-layout wrapper">
  <div id="layoutLeft" class="ncsc-layout-left">

    <!-- <div id="sidebar" class="sidebar">
      <div class="column-title" id="main-nav"><span class="ico-index"></span>
        <h2>首页</h2>
      </div>
      <div class="column-menu">
        <ul id="seller_center_left_menu">
          <div class="add-quickmenu"><a href="javascript:void(0);"><i class="icon-plus"></i>添加常用功能菜单</a></div>
        </ul>
      </div>
    </div> -->
  </div>
  <div id="layoutRight" class="ncsc-layout-right">
    <div class="main-content" id="mainContent">
      <?php require_once($tpl_file); ?>
    </div>
  </div>
</div>

<?php echo _get_html_cssjs('seller_js','common_select.js,jquery.mousewheel.js','js');?>


</body>
</html>