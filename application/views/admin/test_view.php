<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <!--导入jquery-->
    <!--
    <script type="text/javascript" src="js/jquery-3.3.1.js" ></script>
    -->
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>

    <script src="http://code.highcharts.com/highcharts.js"></script>

    <style>

        .container-fluid {
            padding-right: 0px;
            padding-left: 0px;
            margin-right: 0px;
            margin-left: 0px;
            height: 100%;
        }

        .container {
            padding-right: 0px;
            padding-left: 0px;
            margin-right: 0px;
            margin-left: 0px;

        }

        .row-fluid {
            background-color: #1c2229;
            margin:0;
        }

        .row {
            margin:0;
        }

        .col-md-3 {
            /*background-color: #1c2229;*/

            height: 100%;
        }

        .col-md-9 {
            /*background-color: #1c2229;*/

            margin:0;
        }

        .content{
            margin:0;
        }

        .col-md-12{
            margin:0;
        }

        /*bootstrap无间距栅格*/
        .row.no-gutter {
            margin-left: 0;
            margin-right: 0;
        }
        .row.no-gutter > [class*='col-'] {
            padding-right: 0;
            padding-left: 0;
        }

        .col-md-10 {
            height: 1000px;
        }


        .navMenuBox{
            width: 100%;
            height: 1000px;
        }

        /*添加背景色和滚动条*/
        .navMenuBox {
            background-color: #1c2229;

            padding-right: 0px;
            padding-left: 0px;
            margin-right: 0px;
            margin-left: 0px;

            /*overflow:auto;*/
        }

        /*去掉默认样式*/
        .navMenuBox ul,
        .navMenuBox li {
            list-style: none;
            padding: 0px;
            margin: 0px;
        }

        .navMenu>li>a {
            display: block;
            line-height: 40px;
            font-size: 17px;
            text-decoration: none;
            color: #ABB1B7;
            border-top: 1px solid #222932;
            border-bottom: 2px solid #191e24;
        }

        .navMenu>li.active>a,
        .navMenu>li>a:hover,
        .subMenu>li.active>a,
        .subMenu>li>a:hover {
            color: #FFF;
            background: #12181b;
        }

        .subMenu>li>a {
            display: block;
            line-height: 36px;
            font-size: 16px;
            text-decoration: none;
            color: #ABB1B7;
        }

        ul.subMenu {
            margin-top: 8px;
            margin-left: 10px;
            padding-bottom: 5px;
        }

        .subMenu>li> a {
            padding-left: 20px;
        }

        a.arrow:after {
            display: block;
            float: right;
            margin-right: 15px;
            font-size: 16px;
            line-height: 40px;
            font-family: FontAwesome;
            content: "\f105";
            font-weight: 300;
            text-shadow: none;
        }

        li.active>a.arrow:after {
            display: block;
            float: right;
            margin-right: 15px;
            font-size: 16px;
            line-height: 40px;
            font-family: FontAwesome;
            content: "\f107";
            font-weight: 300;
            text-shadow: none;
        }

        .navMenu>li>a:before{
            display: block;
            float: left;
            margin: 0 5px;
            font-size: 20px;
            line-height: 40px;
            font-family: FontAwesome;
            content: "\f0ac";
            font-weight: 300;
            text-shadow: none;
        }


    </style>
</head>
<body>
<!--包裹层-->

<div class="container-fluid">
    <div class="row no-gutter">
        <div class="col-lg-2 col-md-2">

            <div class="navMenuBox">
                <!--一级菜单-->
                <ul class="navMenu">
                    <!--菜单项-->
                    <li>
                        <!--arrow类给具有下级菜单项添加箭头图标-->
                        <a href="#" class="arrow" level="0" company_id="0">金昇能源</a>

                        <!--子菜单-->
                        <ul class="subMenu">

                            <?php
                            if(count($company_lists)>0){
                            foreach($company_lists as $company ){
                                $id = $company['id'];
                                $compName = $company['name'];
                                $level = $company['level'];
                                $sub = $company['sub'];?>

                            <li>
                                <a href="#" level="<?php echo $level;?>" company_id="<?php echo $id;?>" ><?php echo $compName;?></a><!--一级目录(公司/子公司)-->

                                <?php
                                if(isset($sub) && count($sub)>0){

                                    foreach($sub as $company ){
                                        $compName = "";

                                        if (array_key_exists("name",$company))
                                        {
                                            $compName = $company['name'];
                                        }
                                        elseif (array_key_exists("meter_name",$company))
                                        {
                                            $compName = $company['meter_name'];
                                        }

                                        $id = $company['id'];

                                        $level = -1;
                                        if (array_key_exists("level",$company))
                                        {
                                            $level = $company['level'];
                                        }

                                        if (array_key_exists("sub",$company))
                                        {
                                            $subNext1 = $company['sub'];
                                        }
                                        else
                                        {
                                            $subNext1 = null;
                                        }

                                        $meter_eui = "";
                                        if (array_key_exists("meter_eui",$company))
                                        {
                                            $meter_eui = $company['meter_eui'];
                                        }

                                ?>
                                <!--子菜单-->
                                <ul class="subMenu">
                                    <li>
                                        <a href="#" level = "<?php echo $level;?>" company_id="<?php echo $id;?>" meter_eui="<?php echo $meter_eui;?>"><?php echo $compName;?></a><!--二级目录(子公司/仪表)-->



                                    <?php
                                    if(isset($subNext1) && count($subNext1)>0){?>

                                        <ul class="subMenu">

                                            <?php

                                        foreach($subNext1 as $company ){
                                            $compName = $company['meter_name'];
                                            $level = -1;
                                            if (array_key_exists("level",$company))
                                            {
                                                $level = $company['level'];
                                            }

                                            $meter_eui = "";
                                            if (array_key_exists("meter_eui",$company))
                                            {
                                                $meter_eui = $company['meter_eui'];
                                            }

                                        ?>


                                                <li><a href="#" level = "<?php echo $level;?>" meter_eui="<?php echo $meter_eui;?>"><?php echo $compName;?></a></li><!--三级目录(仪表)-->

                                        <?php }?>

                                        </ul>

                                    <?php }?>

                                    </li>

                                </ul>

                                <?php }?>

                                <?php }?>

                            </li>

                            <?php }?>

                            <?php }?>


                        </ul>

                    </li>

                </ul>
                </ul>
            </div>

        </div>

        <div class="col-lg-10 col-md-10">
            <!--Body content-->

            <div class="content">



            </div>
        </div>

    </div>

</div>



</body>


<script type="text/javascript">
    $(function(){

        //初始化
        $('.subMenu').hide();
        $('li.active>.subMenu').show();

        showRightContent(0, 0, 0);

        //给菜单项添加事件
        $('.navMenu a').click(function(){

            console.log("menu click");

            var level = $(this).attr("level");

            console.log("param level:"+level);

            var company_id = "";
            var meter_eui = "";
            var type = level;

            if (level == -1)
            {
                type = 3;
                meter_eui = $(this).attr("meter_eui");

                console.log("param type:"+type);
                console.log("param meter_eui:"+meter_eui);
            }
            else
            {
                company_id = $(this).attr("company_id");

                console.log("param company_id:"+company_id);
            }

            var para = 'type:'+type+' companyId:'+company_id+' meterEui'+meter_eui;

            console.log(para);

            showRightContent(type, company_id, meter_eui);

            //获取所属列表ul
            var $subMenuElement=$(this).next();
            var $liElement=$(this).parent();
            var $ulElement=$(this).parent().parent();
            //没有子菜单，则直接返回
            if(!$subMenuElement.is('ul'))
            {
                $ulElement.find('li').removeClass('active');
                $ulElement.find('ul.subMenu').slideUp();
                $liElement.addClass('active');
                return;
            }
            //如果存在子菜单，则打开或者关闭
            if(! $liElement.hasClass('active')){
                $ulElement.find('li').removeClass('active');
                $ulElement.find('ul.subMenu').slideUp();
                $liElement.addClass('active');
                $subMenuElement.slideDown();
            }else{
                //打开状态 则关闭本菜单
                $subMenuElement.slideUp();
                $liElement.removeClass('active');

            }
        });

        function showRightContent(type, company_id, meter_eui) {

            console.log("======》》》Enter showRightContent");

            var data = {'type':type, 'companyId':company_id,'meterEui':meter_eui};

            var url = <?php echo $view_folder.'/admin/brief_intro_for_company_view'; ?>;

            console.log("url :".url);

            $(".content").load(url, null, function(responseTxt,statusTxt,xhr){
                if(statusTxt=="success")
                    alert("外部内容加载成功！");
                if(statusTxt=="error")
                    alert("Error: "+xhr.status+": "+xhr.statusText);
            });


        }


        function executeScript(html)
        {
            console.log("======》》》Enter executeScript");
            //console.log(html);

            var reg = /<script[^>]*>([^\x00]+)$/i;
            //对整段HTML片段按<\/script>拆分
            var htmlBlock = html.split("<\/script>");

            //console.log(JSON.stringify(htmlBlock));

            for (var i in htmlBlock)
            {
                var blocks;//匹配正则表达式的内容数组，blocks[1]就是真正的一段脚本内容，因为前面reg定义我们用了括号进行了捕获分组
                if (blocks = htmlBlock[i].match(reg))
                {
                    console.log("@@@@@@ find js code!!!");
                    //清除可能存在的注释标记，对于注释结尾-->可以忽略处理，eval一样能正常工作
                    var code = blocks[1].replace(/<!--/, '');
                    try
                    {
                        console.log("@@@@@ eval js code!!!");
                        eval(code);//执行脚本
                    }
                    catch (e)
                    {
                        console.log("catch error++++++"+e.name+":"+e.message);

                    }
                }
            }

            console.log("============>>>>>>>>>>executeScript");
        }
    });
</script>

</html>