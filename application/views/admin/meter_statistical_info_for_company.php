<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">


    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>

    <script src="http://code.highcharts.com/highcharts.js"></script>
    <!--
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts.src.js"></script>

    -->

</head>

<body>


<div class = "right_info">

    <!--
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">设备基本信息统计</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="row form-group" ng-controller="NodeNumCtrl">
                        <!--<label class="col-md-3 text-right glyphicon glyphicon-home">
                            设备接入总数:
                            <button type="button" class="btn btn-sm btn-info">{{nums.all_node_num}}</button>
                        </label> <label
                            class="col-md-3 text-right glyphicon glyphicon-cloud-upload">
                            正常上报设备数:
                            <button type="button" class="btn btn-sm btn-success">{{nums.valid_node_num}}</button>
                        </label> <label
                            class="col-md-3 text-right glyphicon glyphicon-remove-circle">
                            超24小时未上报设备数:
                            <button type="button" class="btn btn-sm btn-danger">{{nums.all_node_num
                                - nums.valid_node_num}}
                            </button>
                        </label>
                        -->
    <!--
                        <label class="col-md-3 text-right glyphicon glyphicon-home">
                            设备接入总数:
                            <button type="button" class="btn btn-sm btn-info">3</button>
                        </label> <label
                            class="col-md-3 text-right glyphicon glyphicon-cloud-upload">
                            正常上报设备数:
                            <button type="button" class="btn btn-sm btn-success">3</button>
                        </label> <label
                            class="col-md-3 text-right glyphicon glyphicon-remove-circle">
                            超24小时未上报设备数:
                            <button type="button" class="btn btn-sm btn-danger">0
                            </button>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    -->

    <div class="row" ng-controller="DataPickerCtrl">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">设备基本信息统计</h3>
                </div>
                <div class="panel-body text-left">
                    <div class="col-md-5">
                        <div class="col-md-12" style="width:100%; height:50px;">
                            <label class="text-right glyphicon glyphicon-home">
                                设备接入总数:
                                <button type="button" class="btn btn-sm btn-info">3</button>
                            </label>
                        </div>
                        <div class="col-md-12" style="width:100%; height:50px;">
                            <label class="text-right glyphicon glyphicon-cloud-upload">
                                正常上报设备数:
                                <button type="button" class="btn btn-sm btn-success">3</button>
                            </label>
                        </div>
                        <div class="col-md-12" style="width:100%; height:50px;">
                            <label class="text-right glyphicon glyphicon-remove-circle">
                                超24小时未上报设备数:
                                <button type="button" class="btn btn-sm btn-danger">0
                                </button>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-7">

                        <!--
                        <div class="row">
                            <canvas id="chart" config="chartConfig" class="span10"
                                    style="width:100%; height:420px;"></canvas>
                        </div>

                        <div class="row">
                            <div id="container" style="width: 550px; height: 400px; margin: 0 auto"></div>

                            <div id="pie" style="width: 550px; height: 400px; margin: 0 auto"></div>
                        </div>

                        -->

                        <div class="col-md-12">
                            <label class="col-md-12 text-center">
                                工况瞬时流量/最大流量占比燃气表数量比例饼状图
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <canvas id="chartPie" style="width:100%; height:300px;"></canvas>
                            </div>
                            <!--
                            <div class="col-md-6">
                                <div id="pie" style="width: 550px; height: 300px; margin: 0 auto"></div>
                            </div>
                            -->
                        </div>

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
                        <table st-table="displayedCollection" st-safe-src="rowCollection"
                               class="table table-striped">
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
                            <tr class="tfoot">
                                <div class="pagination"></div>
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

            var param = {};
            param.page = 1;
            param.type = type;
            param.company_id = company_id;

            getInfoForCompany(param);

            function getInfoForCompany(obj)
            {
                getStatisticsInfo(obj);

            }

            function getStatisticsInfo(obj)
            {
                var param = {};
                param.company_id = obj.company_id;
                $.ajax({
                    type:"POST",
                    url:'<?php echo ADMIN_SITE_URL.'/home/getInfoForCompany'?>',
                    data: obj,
                    dataType:'json',
                    success:function(res){

                        console.log(JSON.stringify(res));

                        if (res.code == 1) {

                            console.log(JSON.stringify(res));
                            var arrMeterQmInfo = res.data['meter_qm'];
                            if (arrMeterQmInfo && arrMeterQmInfo.length > 0) {

                                var arrRatioCnt=new Array(8);
                                arrRatioCnt[0]=0;
                                arrRatioCnt[1]=0;
                                arrRatioCnt[2]=0;
                                arrRatioCnt[3]=0;
                                arrRatioCnt[4]=0;
                                arrRatioCnt[5]=0;
                                arrRatioCnt[6]=0;
                                arrRatioCnt[7]=0;

                                for(var i = 0 ;i < arrMeterQmInfo.length;i++){
                                    var obj  = arrMeterQmInfo[i];

                                    var qm = obj.data_qm;
                                    var outPutMax = obj.outputMax;

                                    if (typeof(qm) != 'number' || outPutMax == null)
                                    {
                                        arrRatioCnt[0] = arrRatioCnt[0]+1;
                                    }
                                    else {
                                        var ratio = qm / outPutMax;

                                        if (0 <= ratio && ratio < 0.2) {
                                            arrRatioCnt[1] = arrRatioCnt[1] + 1;
                                        }
                                        else if (0.2 <= ratio && ratio < 0.4)
                                        {
                                            arrRatioCnt[2] = arrRatioCnt[2]+1;
                                        }
                                        else if (0.4 <= ratio && ratio < 0.6)
                                        {
                                            arrRatioCnt[3] = arrRatioCnt[3]+1;
                                        }
                                        else if (0.6 <= ratio && ratio < 0.8)
                                        {
                                            arrRatioCnt[4] = arrRatioCnt[4]+1;
                                        }
                                        else if (0.8 <= ratio && ratio < 1.0)
                                        {
                                            arrRatioCnt[5] = arrRatioCnt[5]+1;
                                        }
                                        else if (1.0 <= ratio && ratio < 1.2)
                                        {
                                            arrRatioCnt[6] = arrRatioCnt[6]+1;
                                        }
                                        else if (1.2 < ratio)
                                        {
                                            arrRatioCnt[7] = arrRatioCnt[7]+1;
                                        }



                                    }


                                }



                                drawPie(arrRatioCnt);



                            } else {

                                //$('#list_content').html('<tr class="no_data"><td colspan="10">没有报警信息</td></tr>');
                            }

                        }

                    },
                    error:function(XMLHttpRequest, textStatus, thrownError){}
                });
            }

            var param = {};
            param.page = 1;
            param.type = type;
            param.company_id = company_id;
            sendSearch(param);
            function sendSearch(obj){

                console.log("@@@@@@@@--------sendSearch for warning info in company~~~");


                $.ajax({
                    type:"POST",
                    url:'<?php echo ADMIN_SITE_URL.'/home/getWarnInfoForCompany'?>',
                    data: obj,
                    dataType:'json',
                    success:function(res){

                        console.log("@@@@@@@@--------sendSearch succeed");

                        console.log(JSON.stringify(res));

                        $('#list_content').html('');
                        $('.pagination').html('');
                        if (res.code == 1) {

                            $('.pagination').html('');
                            showList(res.data);
                        }

                    },
                    error:function(XMLHttpRequest, textStatus, thrownError){
                        console.log("@@@@@@@@--------sendSearch error");
                    }
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
                console.log("@@@@@@@@--------sendSearch showList");
                if (data.rows && data.rows.length > 0) {

                    console.log("@@@@@@@@--------data.rows && data.rows.length > 0");

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
                        str+='</tr>';
                        $('#list_content').append(str);

                    }

                    $('.pagination').html(data.pages);
                    //$('.pagination').find('a').removeAttr('href');

                } else {

                    $('#list_content').html('<tr class="no_data"><td colspan="10">没有报警信息</td></tr>');
                }
            }


            function drawPie(arrPieData) {
                //new Chart(ctx).Pie(data,options);

                var ctx = document.getElementById("chartPie").getContext("2d");

                var pieData1 = [
                    {
                        value: 300,
                        color: "#69D2E7",
                        highlight:  "#69D2E7",
                        label: "Red"

                    },
                    {
                        value: 200,
                        color:  "#69D2E7",
                        highlight:  "#69D2E7",
                        label: "Green"
                    },
                    {
                        value: 100,
                        color:  "#69D2E7",
                        highlight:  "#69D2E7",
                        label: "Yellow"
                    },
                    {
                        value: 400,
                        color:  "#69D2E7",
                        highlight:  "#69D2E7",
                        label: "Grey"
                    },
                    {
                        value: 120,
                        color:  "#69D2E7",
                        highlight:  "#69D2E7",
                        label: "Dark Grey"
                    }
                ];
                var ctx2 = document.getElementById("chartPie").getContext("2d");


                var pieData = {
                    labels: [
                        "未知",
                        "0~20%",
                        "20~40%",
                        "40~60%",
                        "60~80%",
                        "80~100%",
                        "100~120%",
                        "120%以上"
                    ],
                    datasets: [
                        {
                            data: arrPieData,
                            backgroundColor: [
                                "#FF6384",
                                "#36A2EB",
                                "#FFCE56",
                                "#2C4B21",
                                "#5a0099",
                                "#00B492",
                                "#8E2823",
                                "#BA6F04"
                            ],
                            hoverBackgroundColor: [
                                "#FF6384",
                                "#36A2EB",
                                "#FFCE56",
                                "#2C4B21",
                                "#5a0099",
                                "#00B492",
                                "#8E2823",
                                "#BA6F04"
                            ]
                        }]
                };

                var myChart = new Chart(ctx2, {
                    type: 'pie', // line 表示是 曲线图，当然也可以设置其他的图表类型 如柱形图 : bar  或者其他
                    data: pieData
                });
            }

        });
    </script>
</div>
</body>



</html>
