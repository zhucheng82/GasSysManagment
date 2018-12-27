<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <title></title>

    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <style>

        .logo-title
        {
            background-color: #00B492;
            color: #00B492;
            width: 10%;
            float: left;
            height: 50px;
        }

        .nav_item
        {
            background-color: #000000;
            color: #1ABC9D;
            width: 90%;
            float: left;

            height: 50px;
        }

        .col-md-11
        {
            background-color: #5a0099;
            background-color: #00a0ff;

        }


        .col-md-12
        {
            /*background-color: #0000FF;*/

        }

    </style>


</head>

<body style="min-width: 1200px; margin: 0px; ">

<nav class = "navbar navbar-default"  role="navigation">

    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">金昇燃气</a>
        </div>

        <div>

            <ul class="nav nav-pills">
                <li <?php if(!isset($index) || $index == 0) echo 'class="active"'; ?> ><a <?php if(!isset($index) || $index == 0) echo 'href="#"';else echo 'href='.BASE_SITE_URL.'/admin/home'; ?>  data-toggle="tab">首页</a> </li>
                <li <?php if(isset($index) && $index == 1) echo 'class="active"'; ?> ><a <?php if(isset($index) && $index == 1) echo 'href="#"';else echo 'href='.BASE_SITE_URL.'/admin/home/showWarnInfoView'; ?> data-toggle="tab">报警信息</a> </li>
                <li <?php if(isset($index) && $index == 2) echo 'class="active"'; ?> ><a <?php if(isset($index) && $index == 2) echo 'href="#"';else echo 'href='.BASE_SITE_URL.'/admin/data_analysis'; ?> data-toggle="tab">数据分析</a> </li>
                <li <?php if(isset($index) && $index == 3) echo 'class="active"'; ?> ><a <?php if(isset($index) && $index == 3) echo 'href="#"';else echo 'href='.BASE_SITE_URL.'/admin/gas_collection'; ?> data-toggle="tab">气量抄收</a> </li>
                <li <?php if(isset($index) && $index == 4) echo 'class="active"'; ?> ><a <?php if(isset($index) && $index == 4) echo 'href="#"';else echo 'href='.BASE_SITE_URL.'/admin/gas_data_report'; ?> data-toggle="tab">数据导出</a> </li>
            </ul>

        </div>

    </div>
</nav>




</body>
</html>
