<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">

    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

</head>

<body>


<div class = "right_info">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">用户用气曲线图</h3>
                </div>
                <div class="panel-body text-center">
                    <div id="dataChart" ng-controller="UserMeterDataCtrl">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <button id="oneWeekBt" type="button" class="btn btn-primary" ng-click="week()" >一周
                                </button>
                                <button id="oneMonthBt" type="button" class="btn btn-default" ng-click="month()" >一月
                                </button>
                                <button id="oneYearBt" type="button" class="btn btn-default" ng-click="year()">一年
                                </button>
                                <button id="totalBt" type="button" class="btn btn-default" ng-click="total()">所有
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <canvas id="chart" config="chartConfig" class="span10" style="width:100%; height:500px;"></canvas>
                            <p id="no-data-tip">暂无数据</p>
                        </div>

                        <!--
                        <div class="row">
                            <div id="container" style="width: 550px; height: 400px; margin: 0 auto"></div>

                            <div id="pie" style="width: 550px; height: 400px; margin: 0 auto"></div>
                        </div>

                        -->

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">重要报警信息</h3>
                </div>
                <div class="panel-body text-left">
                    <div ng-controller="WarnCtrl-1">
                        <table st-table="displayedCollection"
                               st-safe-src="rowCollection" class="table table-striped">
                            <thead>

                            <tr>
                                <th st-sort="company">企业名称</th>
                                <th st-sort="user_id">用户信息</th>
                                <th st-sort="meter_info">表计信息</th>
                                <th st-sort="warn_info">报警信息</th>
                                <th st-sort="warn_level">报警级别</th>
                                <th st-sort="solution">处理办法</th>
                                <th st-sort="warn_date">报警时间</th>
                            </tr>
                            </thead>
                            <tbody  id="list_content">

                            </tbody>
                            <tfoot>


                            <tr>
                                <td colspan="5">
                                    <nav  class="pagination">

                                    </nav>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $(function () {

            console.log("++++++++++++++++++company js++++++++++++++++++");

            var type = <?php echo $type;?>;
            var company_id = <?php echo $company_id;?>;

            getInfoForCompany();

            function getInfoForCompany()
            {
                getGasUsageInfoForWeek();
            }

            //一周
            $("#oneWeekBt").click(function(){

                $("#oneWeekBt").attr("class","btn btn-primary");
                $("#oneMonthBt").attr("class","btn btn-default");
                $("#oneYearBt").attr("class","btn btn-default");
                $("#totalBt").attr("class","btn btn-default");

                getGasUsageInfoForWeek();
            });

            //一月
            $("#oneMonthBt").click(function(){

                $("#oneWeekBt").attr("class","btn btn-default");
                $("#oneMonthBt").attr("class","btn btn-primary");
                $("#oneYearBt").attr("class","btn btn-default");
                $("#totalBt").attr("class","btn btn-default");


                getGasUsageInfoForMonth();
            });

            //一年
            $("#oneYearBt").click(function(){

                $("#oneWeekBt").attr("class","btn btn-default");
                $("#oneMonthBt").attr("class","btn btn-default");
                $("#oneYearBt").attr("class","btn btn-primary");
                $("#totalBt").attr("class","btn btn-default");


                getGasUsageInfoForYear();
            });

            //所有
            $("#totalBt").click(function(){

                $("#oneWeekBt").attr("class","btn btn-default");
                $("#oneMonthBt").attr("class","btn btn-default");
                $("#oneYearBt").attr("class","btn btn-default");
                $("#totalBt").attr("class","btn btn-primary");
                getGasUsageInfoForAll();
            });

            function getGasUsageInfoForWeek()
            {
                var param = {};
                param.date_type = 0;//0:一周 1:一月 2:一年 3:所有
                param.company_id = company_id;
                getGasUsageInfo(param);
            }

            function getGasUsageInfoForMonth()
            {
                var param = {};
                param.date_type = 1;//0:一周 1:一月 2:一年 3:所有
                param.company_id = company_id;
                getGasUsageInfo(param);
            }

            function getGasUsageInfoForYear()
            {
                var param = {};
                param.date_type = 2;//0:一周 1:一月 2:一年 3:所有
                param.company_id = company_id;
                getGasUsageInfo(param);
            }

            function getGasUsageInfoForAll()
            {
                var param = {};
                param.date_type = 3;//0:一周 1:一月 2:一年 3:所有
                param.company_id = company_id;
                getGasUsageInfo(param);
            }

            function getGasUsageInfo(obj)
            {
                var param = {};
                param.company_id = obj.company_id;
                param.date_type = obj.date_type;
                $.ajax({
                    type:"POST",
                    url:'<?php echo ADMIN_SITE_URL.'/home/getUserGasConsumption'?>',
                    data: obj,
                    dataType:'json',
                    success:function(res){

                        console.log("ajax 返回 res:"+JSON.stringify(res));

                        if (res.code == 1) {

                            //console.log(JSON.stringify(res));
                            var arrMeterVbInfo = res.data['gas_usage'];
                            if (arrMeterVbInfo && arrMeterVbInfo.length > 0) {

                                var arrGasUsage = new Array();
                                var arrDate = new Array();

                                for(var i = 0 ;i < arrMeterVbInfo.length;i++){
                                    var obj  = arrMeterVbInfo[i];

                                    var date = obj.date;
                                    var data_vb = obj.data_vb;

                                    arrGasUsage.push(data_vb);
                                    arrDate.push(date);

                                }

                                document.getElementById("chart").style.visibility="visible";
                                document.getElementById("no-data-tip").style.visibility="hidden";

                                var canvas=document.getElementById("chart");
                                canvas.height = 500;

                                drawLine(arrDate, arrGasUsage);



                            } else {

                                console.log("无数据");

                                document.getElementById("chart").style.visibility="hidden";
                                document.getElementById("no-data-tip").style.visibility="visible";

                                var canvas = document.getElementById("chart");
                                var cxt = document.getElementById("chart").getContext("2d");
                                cxt.clearRect(0,0,canvas.width,canvas.height);

                                canvas.height = 10;
                            }

                        }

                    },
                    error:function(XMLHttpRequest, textStatus, thrownError){}
                });
            }

            //用气曲线图
            function drawLine(arrXLabels, arrData) {

                var ctx = document.getElementById("chart").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: 'line', // line 表示是 曲线图，当然也可以设置其他的图表类型 如柱形图 : bar  或者其他
                    data: {
                        labels : arrXLabels, //按时间段 可以按星期，按月，按年
                        datasets : [
                            {
                                label: "流量计标况统计",  //当前数据的说明
                                fill: true,  //是否要显示数据部分阴影面积块  false:不显示
                                borderColor: "rgba(255,187,205,1)",//数据曲线颜色
                                pointBackgroundColor: "#fff", //数据点的颜色
                                data: arrData,  //填充的数据
                            }
                        ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: '流量计标况统计'
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: '日期'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: '总流量(Nm3)'
                                }
                            }]
                        }
                    }
                });


            }

            var param = {};
            param.page = 1;
            param.type = type;
            param.company_id = company_id;
            sendSearch(param);
            function sendSearch(obj){

                $.ajax({
                    type:"POST",
                    url:'<?php echo ADMIN_SITE_URL.'/home/getWarnInfoForCompany'?>',
                    data: obj,
                    dataType:'json',
                    success:function(res){
                        $('#list_content').html('');
                        $('.pagination').html('');
                        if (res.code == 1) {

                            $('.pagination').html('');
                            showList(res.data);
                        }

                    },
                    error:function(XMLHttpRequest, textStatus, thrownError){}
                });

            }
            /*$('.pagination').find('a').live('click',function(){
                var page = $(this).attr('data-ci-pagination-page');
                if(!page){
                    return;
                }
                if(!searchObj){
                    searchObj = {};
                }
                searchObj.page = page;
                searchObj.type = type;
                searchObj.company_id = company_id;
                sendSearch(searchObj);
            });
            */

            function showList(data){

                if (data.rows && data.rows.length > 0) {

                    for(var i = 0 ;i < data.rows.length;i++){
                        var obj  = data.rows[i];
                        var str = '<tr class="hover">';
                        str += '<td><span>'+obj.name+'</span></td>'+
                            '<td><span>'+obj.user_id+'</span></td>'+
                            '<td><span>'+obj.meter_name+'</span></td>'+
                            '<td><span>'+obj.data_warn_reason +'</span></td>'+
                            '<td><span>'+obj.data_warn_level+'</span></td>'+
                            '<td><span>'+obj.data_warn_solution+'</span></td>'+
                            '<td><span>'+obj.warn_date+'</span></td>';
                        str += '</tr>';
                        $('#list_content').append(str);

                    }

                    $('.pagination').html(data.pages);
                    //$('.pagination').find('a').removeAttr('href');

                } else {

                    $('#list_content').html('<tr class="no_data"><td colspan="10">没有报警信息</td></tr>');
                }
            }



        });
    </script>
</div>






</body>
</html>
