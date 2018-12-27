<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">

    <title><?php echo $output['html_title'];?></title>

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <!--导入Font Awesome图标字库css文件-->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!--
    <script src="https://cdn.bootcss.com/Chart.js/2.7.3/Chart.bundle.min.js"></script>
    -->

    <script src="https://cdn.bootcss.com/Chart.js/2.7.0/Chart.bundle.min.js"></script>


    <script src="http://code.highcharts.com/highcharts.js"></script>
    <!--
    <script src="https://code.highcharts.com/highcharts.src.js"></script>
    -->


</head>

<body>


<div class = "right_info">
    <div class="row" ng-controller="DCOutDiffCtrl-shunshi">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">实时输差分析-瞬时输差</h3>
                </div>
                <div class="panel-body text-left">
                    <div class="col-md-4">
                        <form class="form-horizontal" role="form" ng-sumbit="submit()">
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">燃气公司:
                                </label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <select class="form-control input-sm"
                                                ng-options="key as value for (key , value) in companys"
                                                ng-model="company" id="company"
                                                ng-change="company_update(company)">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">燃气用户:
                                </label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <select class="form-control input-sm"
                                                ng-options="key as value for (key , value) in users"
                                                ng-model="user_selected" id="user" ng-change="user_update()">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">流量计名称:
                                </label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <select class="form-control input-sm"
                                                ng-options="key as value for (key , value) in meters"
                                                ng-model="meter_selected" id="meter"
                                                ng-change="meter_update()">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">瞬时工况流量(Nm³/h):</label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <input class="form-control" ng-model="qm_edit"
                                               placeholder="瞬时工况流量" ng-change="update()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">温度(℃):</label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <input class="form-control" ng-model="temp_edit"
                                               ng-change="update_diff()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">压力(bar):</label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <input class="form-control" ng-model="pressure_edit"
                                               ng-change="update_diff()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="col-lg-11 btn btn-primary">瞬时偏差: {{
                                    outdiff }} (Nm³/h)
                                </button>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" style="color: red"
                                       class="col-lg-10 control-label text-left">{{ warning }}</label>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <canvas id="chart1" config="chartConfig" class="span10"
                                       style="width:100%; height:420px;"></canvas>
                        </div>
                        <div class="row">
                            <div id="container" style="width: 550px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" ng-controller="DCOutDiffCtrl-total">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">实时输差分析-累计输差</h3>
                </div>
                <div class="panel-body text-left">
                    <div class="col-md-4">
                        <form class="form-horizontal" role="form" ng-sumbit="submit()">
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">燃气公司:
                                </label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <select class="form-control input-sm"
                                                ng-options="key as value for (key , value) in companys"
                                                ng-model="company" id="company"
                                                ng-change="company_update(company)">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">燃气用户:
                                </label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <select class="form-control input-sm"
                                                ng-options="key as value for (key , value) in users"
                                                ng-model="user_selected" id="user" ng-change="user_update()">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputStandard" class="col-lg-5 control-label">流量计名称:
                                </label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <select class="form-control input-sm"
                                                ng-options="key as value for (key , value) in meters"
                                                ng-model="meter_selected" id="meter"
                                                ng-change="meter_update()">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-controller="DataPickerCtrl">
                                <label for="inputStandard" class="col-lg-5 control-label">开始日期:</label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <input type="date" class="form-control" datepicker-popup
                                               ng-model="start_date" is-open="opened"
                                               datepicker-options="dateOptions"
                                               date-disabled="disabled(date, mode)" ng-required="true"
                                               close-text="Close"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-controller="DataPickerCtrl">
                                <label for="inputStandard" class="col-lg-5 control-label">结束日期:</label>

                                <div class="col-lg-6">
                                    <div class="bs-component">
                                        <input type="date" class="form-control" datepicker-popup
                                               ng-model="stop_date" is-open="opened" min-date="minDate"
                                               max-date="'2015-06-22'" datepicker-options="dateOptions"
                                               date-disabled="disabled(date, mode)" ng-required="true"
                                               close-text="Close"/>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="form-group">
                            <button class="col-lg-6 btn btn-primary" ng-click="submit()">计算累计输差(Nm³):</button>
                            <div class="col-lg-5">
                                <div class="bs-component">
                                    <input class="form-control" ng-model="total_diff">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputStandard" style="color: red"
                                   class="col-lg-10 control-label text-left">{{ warning }}</label>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="row" ng-controller="DCDeviationCtrl">
                            <highchart id="chart1" config="chartConfig" class="span10"
                                       style="width:100%; height:420px;"></highchart>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>




<script type="text/javascript">

    var data = {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [
            {
                fillColor: "#CCCCFF",
                strokeColor: "rgba(220,220,220,1)",
                label: "2010年",
                data: [65, 59, 90, 81, 56, 55, 40]
            },
            {
                fillColor: "#CCFFCC",
                strokeColor: "#CCFFCC",
                label:"2011年",
                data: [28, 48, 40, 19, 96, 27, 100]
            },
            {
                fillColor: "#FFFFCC",
                strokeColor: "#FFFFCC",
                label: "2012年",
                data: [13, 55, 40, 19, 23, 27, 64]
            },
            {
                fillColor: "#99FFFF",
                strokeColor: "#99FFFF",
                label: "2013年",
                data: [98, 11, 52, 19, 65, 20, 77]
            }
        ]
    };

    $(function () {


        var ctx = document.getElementById("chart1").getContext("2d");
        var myChart = new Chart(ctx, {
            type: 'line', // line 表示是 曲线图，当然也可以设置其他的图表类型 如柱形图 : bar  或者其他
            data: {
                labels : ["January","February","March","April","May","June","July"], //按时间段 可以按星期，按月，按年
                datasets : [
                    {
                        label: "123",  //当前数据的说明
                        fill: true,  //是否要显示数据部分阴影面积块  false:不显示
                        borderColor: "rgba(255,187,205,1)",//数据曲线颜色
                        pointBackgroundColor: "#fff", //数据点的颜色
                        data: [70, 90, 80, 30, 67, 59, 88, 88, 88, 88, 88, 88],  //填充的数据
                    },
                    {
                        label: "456",  //当前数据的说明
                        fill: true,  //是否要显示数据部分阴影面积块  false:不显示
                        borderColor: "rgba(75,192,192,1)",//数据曲线颜色
                        pointBackgroundColor: "#fff", //数据点的颜色
                        data: [21, 34, 35, 50, 45, 21, 70],  //填充的数据
                    }
                ]
            }
        });

        var title = {
            text: '月平均气温'
        };
        var subtitle = {
            text: 'Source: runoob.com'
        };
        var xAxis = {
            categories: ['一月', '二月', '三月', '四月', '五月', '六月'
                ,'七月', '八月', '九月', '十月', '十一月', '十二月']
        };
        var yAxis = {
            title: {
                text: 'Temperature (\xB0C)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        };

        var tooltip = {
            valueSuffix: '\xB0C'
        }

        var legend = {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        };

        var series =  [
            {
                name: 'Tokyo',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2,
                    26.5, 23.3, 18.3, 13.9, 9.6]
            },
            {
                name: 'New York',
                data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8,
                    24.1, 20.1, 14.1, 8.6, 2.5]
            },
            {
                name: 'Berlin',
                data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6,
                    17.9, 14.3, 9.0, 3.9, 1.0]
            },
            {
                name: 'London',
                data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0,
                    16.6, 14.2, 10.3, 6.6, 4.8]
            }
        ];

        var json = {};

        json.title = title;
        json.subtitle = subtitle;
        json.xAxis = xAxis;
        json.yAxis = yAxis;
        json.tooltip = tooltip;
        json.legend = legend;
        json.series = series;

        $('#container').highcharts(json);

    });




</script>

</body>
</html>
