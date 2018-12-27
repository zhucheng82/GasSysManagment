<!DOCTYPE html>
<html lang="zh-CN">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--Bootstrap-->
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

        <!-- 可选的Bootstrap主题文件（一般不用引入） -->
        <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

        <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>

        <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
        <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

        <script src="//cdn.bootcss.com/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js" ></script>

        <![endif]-->
        <style>
            .panel-group{max-height:1000px;overflow: auto;}
            .leftMenu{margin:10px;margin-top:5px;}
            .leftMenu .panel-heading{font-size:14px;padding-left:20px;height:36px;line-height:36px;color:white;position:relative;cursor:pointer;}/*转成手形图标*/
            .leftMenu .panel-heading span{position:absolute;right:10px;top:12px;}
            .leftMenu .menu-item-left{padding: 2px; background: transparent; border:1px solid transparent;border-radius: 6px;}
            .leftMenu .menu-item-left:hover{background:#C4E3F3;border:1px solid #1E90FF;}
        </style>

    </head>

    <body>

    <!--
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">首页</a> </li>
        <li><a href="#tab1" data-toggle="tab">报警信息</a> </li>
        <li><a href="#tab1" data-toggle="tab">数据分析</a> </li>
    </ul>
    -->

    <div class="row">
        <div class="col-md-2">
            <div class="panel-group table-responsive" role="tablist">
                <div class="panel panel-primary leftMenu">


                    <?php
                    if(count($company_lists)>0){
                    foreach($company_lists as $company ){

                        $compName = $company['name'];
                        $level = $company['level'];
                        $sub = $company['sub'];?>

                        <!--利用data-target指定要折叠的分组列表-->
                        <div class="panel-heading" id="collapseListGroupHeading1" data-toggle="collapse" data-target="#collapseListGroup1" role="tab" >

                            <h4 class="panel-title">

                                <?php echo $compName;?>


                                <span class="glyphicon glyphicon-chevron-up right"></span>

                            </h4>

                        </div>

                        <div id="collapseListGroup1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseListGroupHeading1">

                        <ul class="list-group">


                        <?php foreach ($sub as $subInfo)
                        {
                            $name = $subInfo['name'];
                            ?>
                            <li class="list-group-item">

                                <button class="menu-item-left" data-target="test2.html">
                                    <span class="glyphicon glyphicon-triangle-right"></span><?php echo $name;?>
                                </button>
                            </li>
                        <?php }?>

                        </ul>

                    <?php }?>

                    <?php }?>



                </div><!--panel end-->

            </div>
        </div>
        <div class="col-md-10">
            内容
        </div>

            <div class="row">

                <div class="col-sm-4">

                    <h2>默认</h2>

                    <div id="treeview1" class=""></div>

                </div>

                <div class="col-sm-4">

                    <h2>自定义图标</h2>

                    <div id="treeview2" class=""></div>

                </div>

                <div class="col-sm-4">

                    <h2>丰富多彩</h2>

                    <div id="treeview3" class=""></div>

                </div>

            </div>
    </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <ul id="main-nav" class="nav nav-tabs nav-stacked" style="">
                        <li class="active">
                            <a href="#">
                                <i class="glyphicon glyphicon-th-large"></i>
                                首页
                            </a>
                        </li>
                        <li>
                            <a href="#systemSetting" class="nav-header collapsed" data-toggle="collapse">
                                <i class="glyphicon glyphicon-cog"></i>
                                系统管理
                                <span class="pull-right glyphicon glyphicon-chevron-down"></span>
                            </a>
                            <ul id="systemSetting" class="nav nav-list collapse secondmenu1" style="height: 0px;">
                                <li><a href="#meter"><i class="glyphicon glyphicon-user"></i>用户管理</a></li>

                                <ul id="meter" class="nav nav-list collapse thirdmenu" style="height: 0px;">
                                    <li><a href="#"><i class="glyphicon glyphicon-user-add"></i>添加用户</a></li>
                                    <li><a href="#"><i class="glyphicon glyphicon-user-del"></i>删除</a></li>
                                </ul>
                                <li><a href="#"><i class="glyphicon glyphicon-th-list"></i>菜单管理</a></li>
                                <li><a href="#"><i class="glyphicon glyphicon-asterisk"></i>角色管理</a></li>
                                <li><a href="#"><i class="glyphicon glyphicon-edit"></i>修改密码</a></li>
                                <li><a href="#"><i class="glyphicon glyphicon-eye-open"></i>日志查看</a></li>
                            </ul>

                            <ul id="meter" class="nav nav-list collapse thirdmenu" style="height: 0px;">
                                <li><a href="mytask"><i class="glyphicon glyphicon-user"></i>燃气表1</a></li>
                                <li><a href="#"><i class="glyphicon glyphicon-th-list"></i>燃气表2</a></li>
                            </ul>

                        </li>
                        <li>
                            <a href="./plans.html">
                                <i class="glyphicon glyphicon-credit-card"></i>
                                物料管理
                            </a>
                        </li>
                        <li>
                            <a href="./grid.html">
                                <i class="glyphicon glyphicon-globe"></i>
                                分发配置
                                <span class="label label-warning pull-right">5</span>
                            </a>
                        </li>
                        <li>
                            <a href="./charts.html">
                                <i class="glyphicon glyphicon-calendar"></i>
                                图表统计
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="glyphicon glyphicon-fire"></i>
                                关于系统
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>


        <div class="panel panel-primary">
            <div class="panel-heading">
                商品分类
            </div>
            <div class="panel-body">
                <button class="btn btn-link" role="button" type="button" data-toggle="collapse" data-target="#menu1">
                    一级菜单

                </button>
                <div id="menu1" class="collapse">
                    <button class="btn btn-link" role="button" type="button" data-toggle="collapse" data-target="#menu11">
                        二级菜单
                    </button>
                    <div id="menu11" class="collapse">
                        <button class="btn btn-link">
                            三级菜单
                        </button>
                    </div>
                </div>
            </div>
        </div>







    <script>


        $(function() {

            $(".panel-heading").click(function(e){
                /*切换折叠指示图标*/
                $(this).find("span").toggleClass("glyphicon-chevron-down");
                $(this).find("span").toggleClass("glyphicon-chevron-up");
            });

            var defaultData = [

                {

                    text: 'Parent 1',

                    href: '#parent1',

                    tags: ['4'],

                    nodes: [

                        {

                            text: 'Child 1',

                            href: '#child1',

                            tags: ['2'],

                            nodes: [

                                {

                                    text: 'Grandchild 1',

                                    href: '#grandchild1',

                                    tags: ['0']

                                },

                                {

                                    text: 'Grandchild 2',

                                    href: '#grandchild2',

                                    tags: ['0']

                                }

                            ]

                        },

                        {

                            text: 'Child 2',

                            href: '#child2',

                            tags: ['0']

                        }

                    ]

                },

                {

                    text: 'Parent 2',

                    href: '#parent2',

                    tags: ['0']

                },

                {

                    text: 'Parent 3',

                    href: '#parent3',

                    tags: ['0']

                },

                {

                    text: 'Parent 4',

                    href: '#parent4',

                    tags: ['0']

                },

                {

                    text: 'Parent 5',

                    href: '#parent5'  ,

                    tags: ['0']

                }

            ];

            $('#treeview1').treeview({

                backColor: "#FFFFFF",

                color: "#428bca",

                enableLinks: true,

                data: defaultData

            });

            $('#treeview2').treeview({

                color: "#428bca",

                expandIcon: 'glyphicon glyphicon-chevron-right',

                collapseIcon: 'glyphicon glyphicon-chevron-down',

                nodeIcon: 'glyphicon glyphicon-bookmark',

                data: defaultData

            });

            $('#treeview3').treeview({

                expandIcon: "glyphicon glyphicon-stop",

                collapseIcon: "glyphicon glyphicon-unchecked",

                nodeIcon: "glyphicon glyphicon-user",

                color: "yellow",

                backColor: "purple",

                onhoverColor: "orange",

                borderColor: "red",

                showBorder: false,

                showTags: true,

                highlightSelected: true,

                selectedColor: "yellow",

                selectedBackColor: "darkorange",

                data: defaultData

            });

        });

    </script>

    </body>

</html>