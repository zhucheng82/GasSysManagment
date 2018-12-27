<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">


    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <style type="text/css">

    </style>


</head>

<body>


<div class = "right_info">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <!--<h3 class="panel-title">南京金昇能源科技股份有限公司</h3>-->
                    <img src="/res/admin/images/Jinsheng.jpg" height="20" width="80"/>
                </div>
                <div class="panel-body text-left">
                    <!-- <div id="area-chart"></div>
                    <p>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp南京金昇燃气设备有限公司自2003年12月成立以来，一直专注于提供燃气行业优质、可靠的计量和控制设备，同时提供计量管理、设备成撬及周期检定技术服务等。是ELSTER在中国区授权代理商。公司坚持“准确计量、精确控制”的原则，不断努力为用户提供最佳的产品和服务。</p>

                    <p>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp公司已建立完整的服务体系，提供从售前、售中到售后全面的技术支持和服务。服务团队由具有丰富实践经验的技术人员组成，具备不同类型的工程和设备安装、调试和应急处理能力。
                    </p>-->
                    <br>
                    <p>南京金昇能源科技股份有限公司</p>
                    <p><b>地址：</b>南京市建邺区创智路2号瑞泰大厦1楼</p>

                    <p><b>联系电话：</b>025-58818283, 58832455</p>
                    <p><b>网址: </b>www.kingsungas.com.cn</p>

                    <p><b>传真：</b>025-58822672</p>

                    <p><b>微信公众号：</b>南京金昇</p>
                </div>
            </div>
        </div>
    </div>

    <!--
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">用户反馈信息</h3>
                </div>
                <div class="panel-body text-left">
                    <div ng-controller="UserFeedbackCtrl">
                        <table st-table="displayedCollection" st-safe-src="rowCollection"
                               class="table table-striped">
                            <thead>
                            <tr>
                                <th colspan="2"><input st-search="" class="form-control"
                                                       placeholder="在此搜索。。。" type="text"/></th>
                            </tr>
                            <tr>
                                <th st-sort="user_company">客户名称</th>
                                <th st-sort="report_time">反馈时间</th>
                                <th st-sort="solution_deadline">处理时限</th>
                                <th st-sort="problem">问题描述</th>
                                <th st-sort="solution_result">处理结果</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="row in displayedCollection">
                                <td>{{row.user_company}}</td>
                                <td>{{row.report_time}}</td>
                                <td>{{row.solution_deadline}}</td>
                                <td>{{row.problem}}</td>
                                <td>{{row.solution_result}}</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div st-items-by-page="10" st-pagination=""
                                         st-template="pagination.custom.html"></div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    -->

    <!--
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">重要报警信息</h3>
                </div>
                <div class="panel-body text-left">
                    <div ng-controller="WarnCtrl-1">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>企业名称</th>
                                <th>用户信息</th>
                                <th>表计信息</th>
                                <th>报警信息</th>
                                <th>报警级别</th>
                                <th>处理办法</th>
                                <th>报警时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="row in displayedCollection">
                                <td>{{row.company}}</td>
                                <td>{{row.user_id}}</td>
                                <td>{{row.meter_info}}</td>
                                <td>{{row.warn_info}}</td>
                                <td>{{row.warn_level}}</td>
                                <td>{{row.solution}}</td>
                                <td>{{row.warn_date}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <tm-pagination conf="paginationConf"></tm-pagination>

                    </div>
                </div>
            </div>
        </div>
    </div>

    -->


    <script type="application/javascript">
        $(function () {
            console.log("--------------------测试------------------------");
        });
    </script>

</div>

</body>



</html>
