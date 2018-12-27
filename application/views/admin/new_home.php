<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">
    <title><?php echo $output['html_title'];?></title>

    <link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
    <?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,jquery.cookie.js','js');?>

    <link rel="stylesheet" href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/style.css" type="text/css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <?php echo _get_html_cssjs('admin_js','html5shiv.js,respond.min.js','js');?>

    <?php echo _get_html_cssjs('admin_js','jquery-1.8.3.min.js','js');?>
    <?php echo _get_html_cssjs('admin_js','index.js','js');?>

    <![endif]-->

    <script>
        //
        $(document).ready(function () {
            $('span.bar-btn').click(function () {
                $('ul.bar-list').toggle('fast');
            });
        });

        $(document).ready(function(){
            var pagestyle = function() {
                var iframe = $("#workspace");
                var h = $(window).height() - iframe.offset().top;
                var w = $(window).width() - iframe.offset().left;
                if(h < 300) h = 300;
                if(w < 973) w = 973;
                iframe.height(h);
                iframe.width(w);
            }
            pagestyle();
            $(window).resize(pagestyle);
            //turn location
            //if($.cookie('now_location_act') != null){
            //openItem($.cookie('now_location_op')+','+$.cookie('now_location_act')+','+$.cookie('now_location_nav'));
            //}else{
            $('#mainMenu>ul').first().css('display','block');
            //第一次进入后台时，默认定到欢迎界面
            $('#item_welcome').addClass('selected');
            $('#workspace').attr('src','<?php echo ADMIN_SITE_URL?>/welcome');
            //}
            $('#iframe_refresh').click(function(){
                var fr = document.frames ? document.frames("workspace") : document.getElementById("workspace").contentWindow;
                fr.location.reload();
            });

        });
        //收藏夹
        function addBookmark(url, label) {
            if (document.all)
            {
                window.external.addFavorite(url, label);
            }
            else if (window.sidebar)
            {
                window.sidebar.addPanel(label, url, '');
            }
        }


        function openItem(args){
            closeBg();
            //cookie

            if($.cookie('<?php echo COOKIE_PRE?>sys_key') === null){
                //location.href = 'index.php?act=login&op=login';
                //return false;
            }

            spl = args.split(',');
            op  = spl[0];
            try {
                act = spl[1];
                nav = spl[2];
            }
            catch(ex){}
            if (typeof(act)=='undefined'){var nav = args;}
            $('.actived').removeClass('actived');
            $('#nav_'+nav).addClass('actived');

            $('.selected').removeClass('selected');

            //show
            $('#mainMenu ul').css('display','none');
            $('#sort_'+nav).css('display','block');

            if (typeof(act)=='undefined'){
                //顶部菜单事件
                html = $('#sort_'+nav+'>li>dl>dd>ol>li').first().html();
                str = html.match(/openItem\('(.*)'\)/ig);
                arg = str[0].split("'");
                spl = arg[1].split(',');
                op  = spl[0];
                act = spl[1];
                nav = spl[2];
                first_obj = $('#sort_'+nav+'>li>dl>dd>ol>li').first().children('a');
                $(first_obj).addClass('selected');
                //crumbs
                $('#crumbs').html('<span>'+$('#nav_'+nav+' > span').html()+'</span><span class="arrow">&nbsp;</span><span>'+$(first_obj).text()+'</span>');
            }else{
                //左侧菜单事件
                //location
                $.cookie('now_location_nav',nav);
                $.cookie('now_location_act',act);
                $.cookie('now_location_op',op);
                $("a[name='item_"+op+act+"']").addClass('selected');
                //crumbs
                $('#crumbs').html('<span>'+$('#nav_'+nav+' > span').html()+'</span><span class="arrow">&nbsp;</span><span>'+$('#item_'+op+act).html()+'</span>');
            }
            src = '<?php echo ADMIN_SITE_URL?>/'+act+'/'+op;
            $('#workspace').attr('src',src);

        }

        $(function(){
            bindAdminMenu();
        })
        function bindAdminMenu(){

            $("[nc_type='parentli']").click(function(){
                var key = $(this).attr('dataparam');
                if($(this).find("dd").css("display")=="none"){
                    $("[nc_type='"+key+"']").slideDown("fast");
                    $(this).find('dt').css("background-position","-322px -170px");
                    $(this).find("dd").show();
                }else{
                    $("[nc_type='"+key+"']").slideUp("fast");
                    $(this).find('dt').css("background-position","-483px -170px");
                    $(this).find("dd").hide();
                }
            });
        }
    </script>
    <script type="text/javascript">
        //显示灰色JS遮罩层
        function showBg(ct,content){
            var bH=$("body").height();
            var bW=$("body").width();
            var objWH=getObjWh(ct);
            $("#pagemask").css({width:bW,height:bH,display:"none"});
            var tbT=objWH.split("|")[0]+"px";
            var tbL=objWH.split("|")[1]+"px";
            $("#"+ct).css({top:tbT,left:tbL,display:"block"});
            $(window).scroll(function(){resetBg()});
            $(window).resize(function(){resetBg()});
        }
        function getObjWh(obj){
            var st=document.documentElement.scrollTop;//滚动条距顶部的距离
            var sl=document.documentElement.scrollLeft;//滚动条距左边的距离
            var ch=document.documentElement.clientHeight;//屏幕的高度
            var cw=document.documentElement.clientWidth;//屏幕的宽度
            var objH=$("#"+obj).height();//浮动对象的高度
            var objW=$("#"+obj).width();//浮动对象的宽度
            var objT=Number(st)+(Number(ch)-Number(objH))/2;
            var objL=Number(sl)+(Number(cw)-Number(objW))/2;
            return objT+"|"+objL;
        }
        function resetBg(){
            var fullbg=$("#pagemask").css("display");
            if(fullbg=="block"){
                var bH2=$("body").height();
                var bW2=$("body").width();
                $("#pagemask").css({width:bW2,height:bH2});
                var objV=getObjWh("dialog");
                var tbT=objV.split("|")[0]+"px";
                var tbL=objV.split("|")[1]+"px";
                $("#dialog").css({top:tbT,left:tbL});
            }
        }

        //关闭灰色JS遮罩层和操作窗口
        function closeBg(){
            $("#pagemask").css("display","none");
            $("#dialog").css("display","none");
        }
    </script>
    <script type="text/javascript">
        $(function(){
            var $li =$("#skin li");
            $li.click(function(){
                $("#"+this.id).addClass("selected").siblings().removeClass("selected");
                $("#cssfile").attr("href","<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/"+ (this.id) +".css");
                $.cookie( "MyCssSkin" ,  this.id , { path: '/', expires: 10 });

                $('iframe').contents().find('#cssfile2').attr("href","<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/"+ (this.id) +".css");
            });

            var cookie_skin = $.cookie( "MyCssSkin");
            if (cookie_skin) {
                $("#"+cookie_skin).addClass("selected").siblings().removeClass("selected");
                $("#cssfile").attr("href","<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/"+ cookie_skin +".css");
                $.cookie( "MyCssSkin" ,  cookie_skin  , { path: '/', expires: 10 });
            }
        });
        function addFavorite(url, title) {
            try {
                window.external.addFavorite(url, title);
            } catch (e){
                try {
                    window.sidebar.addPanel(title, url, '');
                } catch (e) {
                    showDialog("<?php echo lang('nc_to_favorite');?>", 'notice');
                }
            }
        }
    </script>

    <style>
    .accordion {
    width: 100%;
    max-width: 360px;
    margin: 30px auto 20px;
    background: #FFF;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    }

    .accordion .link {
    cursor: pointer;
    display: block;
    padding: 15px 15px 15px 42px;
    color: #4D4D4D;
    font-size: 14px;
    font-weight: 700;
    border-bottom: 1px solid #CCC;
    position: relative;
    -webkit-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    }

    .accordion li:last-child .link {
    border-bottom: 0;
    }

    .accordion li i {
    position: absolute;
    top: 16px;
    left: 12px;
    font-size: 18px;
    color: #595959;
    -webkit-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    }

    .accordion li i.fa-chevron-down {
    right: 12px;
    left: auto;
    font-size: 16px;
    }

    .accordion li.open .link {
    color: #b63b4d;
    }

    .accordion li.open i {
    color: #b63b4d;
    }
    .accordion li.open i.fa-chevron-down {
    -webkit-transform: rotate(180deg);
    -ms-transform: rotate(180deg);
    -o-transform: rotate(180deg);
    transform: rotate(180deg);
    }

    /**
    * Submenu
    -----------------------------*/
    .submenu {
    display: none;
    background: #444359;
    font-size: 14px;
    }

    .submenu li {
    border-bottom: 1px solid #4b4a5e;
    }

    .submenu a {
    display: block;
    text-decoration: none;
    color: #d9d9d9;
    padding: 12px;
    padding-left: 42px;
    -webkit-transition: all 0.25s ease;
    -o-transition: all 0.25s ease;
    transition: all 0.25s ease;
    }

    .submenu a:hover {
    background: #b63b4d;
    color: #FFF;
    }

    </style>

</head>

<body style="min-width: 1200px; margin: 0px; ">

<div id="pagemask"></div>

<div id="dialog" style="display:none">
    <ul id="accordion" class="accordion">
        <li>
            <div class="link"><i class="fa fa-paint-brush"></i>公司1<i class="fa fa-chevron-down"></i></div>
            <ul class="submenu">
                <li><a href="#">Photoshop1</a></li>
                <li><a href="#">HTML</a></li>
                <li><a href="#">CSS</a></li>
                <li><a href="#">Maquetacion web</a></li>
            </ul>
        </li>
        <li>
            <div class="link"><i class="fa fa-code"></i>公司2<i class="fa fa-chevron-down"></i></div>
            <ul class="submenu">
                <li><a href="#">Javascript</a></li>
                <li><a href="#">jQuery</a></li>
                <li><a href="#">Frameworks javascript</a></li>
            </ul>
        </li>
        <li>
            <div class="link"><i class="fa fa-mobile"></i>公司3<i class="fa fa-chevron-down"></i></div>
            <ul class="submenu">
                <li><a href="#">Tablets</a></li>
                <li><a href="#">Dispositivos mobiles</a></li>
                <li><a href="#">Medios de escritorio</a></li>
                <li><a href="#">Otros dispositivos</a></li>
            </ul>
        </li>
        <li><div class="link"><i class="fa fa-globe"></i>公司4<i class="fa fa-chevron-down"></i></div>
            <ul class="submenu">
                <li><a href="#">Google</a></li>
                <li><a href="#">Bing</a></li>
                <li><a href="#">Yahoo</a></li>
                <li><a href="#">Otros buscadores</a></li>
            </ul>
        </li>
    </ul>
</div>



<table style="width: 100%;" id="frametable" height="100%" width="100%" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td colspan="2" height="90" class="mainhd"><div class="layout-header"> <!-- Title/Logo - can use text instead of image -->
                <div id="title"><a href="index.php"></a></div>
                <!-- Top navigation -->
                <div id="topnav" class="top-nav">
                    <ul>
                        <li class="adminid" title="<?php echo lang('nc_hello');?>:<?php echo $output['admin_info']['admin_name'];?>"><?php echo lang('nc_hello');?>&nbsp;:&nbsp;<strong><?php echo $output['admin_info']['admin_name'];?></strong></li>
                        <li><a class="div_msg" href="http://qxsms.qixiangnet.com/" target="_blank" id="sms_bale"></a></li>
                        <li><a href="<?php echo ADMIN_SITE_URL;?>/common/modifypw" target="workspace" ><span><?php echo lang('nc_modifypw'); ?></span></a></li>
                        <li><a href="<?php echo ADMIN_SITE_URL;?>/login/logout" title="<?php echo lang('nc_logout');?>"><span><?php echo lang('nc_logout');?></span></a></li>

                        <!-- <li><a href="<?php //echo BASE_SITE_URL;?>" target="_blank" title="<?php //echo lang('nc_homepage');?>"><span><?php //echo lang('nc_homepage');?>1111</span></a></li> -->
                    </ul>
                </div>
                <!-- End of Top navigation -->
                <!-- Main navigation -->
                <nav id="nav" class="main-nav">
                    <ul>
                        <?php echo $output['top_nav']; ?>
                    </ul>
                </nav>
                <div class="loca"><strong><?php echo lang('nc_loca');?>:</strong>
                    <div id="crumbs" class="crumbs"><span><?php echo lang('nc_console');?></span><span class="arrow">&nbsp;</span><span><?php echo lang('nc_welcome_page');?></span> </div>
                </div>
                <div class="toolbar">
                    <!-- <ul id="skin" class="skin"><span><?php //echo lang('nc_skin_peeler');?></span>
              <li id="skin_0" class="" title="<?php //echo lang('nc_default_style');?>"></li>
              <li id="skin_1" class="" title="<?php //echo lang('nc_mac_style');?>"></li>
            </ul> -->
                    <div class="sitemap"><a id="siteMapBtn" href="#rhis" onclick="showBg('dialog','dialog_content');"><span><?php echo lang('nc_sitemap');?></span></a></div>
                    <!-- <div class="toolmenu"><span class="bar-btn"></span>
              <ul class="bar-list">
                <li><a onclick="openItem('clear,cache,setting');" href="javascript:void(0)"><?php //echo lang('nc_update_cache');?></a></li>
                <li><a href="<?php //echo ADMIN_SITE_URL;?>" id="iframe_refresh"><?php //echo lang('nc_refresh');?><?php //echo lang('nc_admincp'); ?></a></li>
                <li><a href="<?php //echo ADMIN_SITE_URL;?>" title="<?php //echo lang('nc_admincp'); ?>-<?php //echo $output['html_title'];?>" rel="sidebar" onclick="addFavorite('<?php //echo ADMIN_SITE_URL;?>', '<?php //echo lang('nc_admincp'); ?>-<?php //echo $output['html_title'];?>');return false;"><?php //echo lang('nc_favorite'); ?><?php //echo lang('nc_admincp'); ?></a></li>

                <li><a href="index.php?act=setting&op=exetarget" target="_blank">执行计划任务</a></li>
              </ul>
            </div> -->
                </div>
            </div>
            <div >
            </div>
        </td>
    </tr>
    <tr>
        <td class="menutd" valign="top" width="161">
            <div id="mainMenu" class="main-menu">
                <?php echo $output['left_nav'];?>
            </div>
            <div class="copyright" style="display:none"></div></td>
        <td valign="top" width="100%"><iframe src="" id="workspace" name="workspace" style="overflow: visible;" frameborder="0" width="100%" height="100%" scrolling="yes" onload="window.parent"></iframe></td>
    </tr>
    </tbody>
</table>

<script>
    $.post('<?php echo ADMIN_SITE_URL.'/common/get_sms_quantity'?>',{}, function(response){
        if (response.code==1) {
            $("#sms_bale").text("短信余量："+response.data.quantity+"条");
        }else{

        }
    }, 'json');
</script>
</body>
</html>
