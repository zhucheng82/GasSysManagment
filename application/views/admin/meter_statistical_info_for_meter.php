<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">



</head>

<body>


<div class = "right_info">
    <div class="row" ng-controller="MeterInfoCtrl">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">流量计基本信息</h3>

                    <!--
                    <div class="actions pull-right">
                        <button type="button" class="btn btn-sm btn-default">更新信息</button>
                    </div>

                    -->
                </div>
                <div class="panel-body">
                    <div class="row form-group">
                        <label class="col-lg-1 control-label text-right">名称:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_name']; ?></label> <label
                            class="col-lg-1 control-label text-right">品牌:</label> <label id="meter_type_name"
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_type_name']; ?></label>
                        <label class="col-lg-1 control-label text-right">修正仪:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_type_name']; ?></label>
                    </div>
                    <div class="row form-group">
                        <label class="col-lg-1 control-label text-right">型号: </label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_version']; ?></label> <label
                            class="col-lg-1 control-label text-right">序列号:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_index']; ?></label> <label
                            class="col-lg-1 control-label text-right">流量范围:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['outputMin']."--".$meter['outputMax']." Nm³/h"; ?></label>
                    </div>
                    <div class="row form-group">
                        <label class="col-lg-1 control-label text-right">所在区域:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_district']; ?></label> <label
                            class="col-lg-1 control-label text-right">检定有效期:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['valid_time']; ?></label> <label
                            class="col-lg-1 control-label text-right">压力范围:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['pressureMin']."--".$meter['pressureMax']." bar"; ?></label>
                    </div>
                    <div class="row form-group">
                        <label class="col-lg-1 control-label text-right">节点编号:</label> <label id="meter_eui"
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['meter_eui']; ?></label> <label
                            class="col-lg-1 control-label text-right">铅封编号:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo isset($meter['wrapCode'])?$meter['wrapCode']:'--'; ?></label> <label
                            class="col-lg-1 control-label text-right">温度范围:</label> <label
                            class="col-lg-3 control-label text-left"
                            style="font-weight: bold"><?php echo $meter['temperatureMin']."--".$meter['temperatureMax']." ℃"; ?></label>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row" ng-controller="MeterInfoCtrl">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">实时数据</h3>
                </div>
                <div class="panel-body text-left">
                    <div ng-controller="MeterDataCtrl">
                        <div class="row">
                            <!--
                            <div class="col-xs-6">
                                <div class="btn-group">
                                    <button id="oneWeekBt" type="button" class="btn btn-primary"
                                            ng-click="week()">一周
                                    </button>
                                    <button id="oneMonthBt" type="button" class="btn btn-default"
                                            ng-click="month()">一月
                                    </button>
                                    <button id="oneYearBt" type="button" class="btn btn-default"
                                            ng-click="year()">一年
                                    </button>
                                    <button id="totalBt" type="button" class="btn btn-default"
                                            ng-click="total()">所有
                                    </button>
                                </div>
                            </div>
                            <div class="col-xs-3 pull-right">
                                <input class="form-control" placeholder="在此搜索。。。" type="text"/>
                            </div>
                            -->
                        </div>


                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>数据编号</th>
                                <th>接收时间</th>
                                <th>标况总累计(Nm³)</th>
                                <?php if ($meter['meter_type_name'] != '卓度') {?>
                                <th>工况总累计(Nm³)</th>
                                <th>压力(bar)</th>
                                <th>温度(℃)</th>
                                <?php }?>
                                <th>标况瞬时流量(Nm³/h)</th>
                                <?php if ($meter['meter_type_name'] != '卓度') {?>
                                <th>工况瞬时流量(Nm³/h)</th>
                                <th>电量(月)</th>

                                <?php } else{?>
                                <th>电量(V)</th>
                                <?php }?>
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

        console.log("-----燃气表js-----");

        var meter_type = <?php echo $meter_type;?>;


        var meter_eui = $('label#meter_eui').text();
        var meter_type_name = $('label#meter_type_name').text();

        console.log("meter_eui:"+meter_eui);

        var meter_eui2 = '35ffdb0532573238';

        console.log(meter_eui2);


        var param = {};
        param.page = 1;
        param.meter_type = meter_type;
        param.meter_eui = meter_eui;
        sendSearch(param);
        function sendSearch(obj){

            console.log("@@@@@@@@--------sendSearch for meter data in meter~~~");

            console.log("page:"+obj.page+' meter_type:'+param.meter_type+' meter_eui:'+param.meter_eui);

            $.ajax({
                type:"POST",
                url:'<?php echo ADMIN_SITE_URL.'/home/getMeterDataList'?>',
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


        $('.pagination').on('click', 'a', function(){
            var page = $(this).attr('data-ci-pagination-page');
            if(!page){
                return;
            }
            var param = {};
            param.page = page;
            param.meter_eui = meter_type;
            param.meter_eui = meter_eui;
            sendSearch(param);
        });

        function showList(data){
            console.log("@@@@@@@@--------sendSearch showList");

            var meter_type = data.mater_type;
            if (data.rows && data.rows.length > 0) {

                console.log("@@@@@@@@--------data.rows && data.rows.length > 0");

                for(var i = 0 ;i < data.rows.length;i++){
                    var obj  = data.rows[i];
                    var str = '<tr class="hover">';
                    str += '<td><span>'+obj.data_id+'</span></td>'+
                        '<td><span>'+obj.data_date+'</span></td>'+
                        '<td><span>'+obj.data_vb+'</span></td>';

                    if(meter_type_name != '卓度')
                    {
                        str += '<td><span>'+obj.data_vm +'</span></td>'+
                            '<td><span>'+obj.data_p+'</span></td>'+
                            '<td><span>'+obj.data_t+'</span></td>';
                    }


                    str += '<td><span>'+obj.data_qb+'</span></td>';

                    if(meter_type_name != '卓度')
                    {
                        str += '<td><span>'+obj.data_qm +'</span></td>';
                    }

                    str += '<td><span>'+obj.data_battery+'</span></td>';

                    if (meter_type_name != '卓度')
                    {

                        str += '<td><div ng-controller="deviationCtrl"><button type="button" ng-click="open(row)" class="btn btn-sm btn-info">数据分析 </button> </div> </td>';
                    }

                    str+='</tr>';

                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {
                $('#list_content').html('<tr class="no_data"><td colspan="10">没有实时数据</td></tr>');
            }
        }


    });
</script>

</div>
</body>

</html>
