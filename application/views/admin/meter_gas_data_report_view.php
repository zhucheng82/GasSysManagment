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

</head>

<body>


<div class = "right_info">
    <div class="row" ng-controller="ExportDataCtrl">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <h3 class="panel-title">查询</h3>
                    </h3>
                </div>
                <div class="panel-body text-left">
                    <form class="form-horizontal" role="form">
                        <legend class="righter">选择流量计</legend>
                        <div class="form-group">
                            <label for="province" class="col-md-3 control-label">公司</label>

                            <div class="col-md-9">
                                <select class="form-control input-sm"
                                        ng-options="key as value for (key , value) in users"
                                        ng-model="user" id="user"
                                        ng-change="user_update(user)">
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city" class="col-md-3 control-label">用户</label>

                            <div class="col-md-9">
                                <select class="form-control input-sm"
                                        ng-options="key as value for (key , value) in companys"
                                        ng-model="company" id="company" ng-change="company_update(company)">
                                </select>
                            </div>
                        </div>
                        <legend class="righter">日期选择</legend>
                        <div class="form-group">
                            <label for="startDate" class="col-md-3 control-label">开始</label>

                            <div class="col-md-9">
                                <div class="bs-component">
                                    <input type="date" class="form-control" datepicker-popup
                                           ng-model="startDate" is-open="opened" min-date="minDate"
                                           max-date="'2015-06-22'" datepicker-options="dateOptions"
                                           date-disabled="disabled(date, mode)" ng-required="true"
                                           close-text="Close">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="endDate" class="col-md-3 control-label">结束</label>

                            <div class="col-md-9">
                                <input type="date" class="form-control" datepicker-popup
                                       ng-model="stopDate" is-open="opened" min-date="minDate"
                                       max-date="'2015-06-22'" datepicker-options="dateOptions"
                                       date-disabled="disabled(date, mode)" ng-required="true"
                                       close-text="Close">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputStandard" style="color: red"
                                   class="col-lg-12 control-label text-left">{{ warning }}</label>
                        </div>
                    </form>
                </div>
                <div class="panel-footer text-left">
                    <button type="button" id="search-button" class="btn btn-danger light btn-block compose-btn"
                            ng-click="company_update(company)">条件查询
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">数据导出</h3>
                </div>
                <div class="panel-body text-left">
                    <div class="col-md-12">
                        <table st-table="displayedCollection" st-safe-src="rowCollection" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th st-sort="data_id">流量计名称</th>
                                <th st-sort="data_vb">标况总累计(Nm³)</th>
                                <th st-sort="data_vm">工况总累计(Nm³)</th>
                                <th st-sort="data_p">压力(bar)</th>
                                <th st-sort="data_t">温度(℃)</th>
                                <!--<th st-sort="data_t" ng-if="meter.meter_type != '卓度'">用气曲线</th>-->
                                <th st-sort="data_vb">导出数据</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="row in displayedCollection">
                                <td>{{row.meter_name}}</td>
                                <td>{{row.data_vb}}</td>
                                <td ng-if="meter.meter_type != '卓度'">{{row.data_vm}}</td>
                                <td ng-if="meter.meter_type != '卓度'">{{row.data_p}}</td>
                                <td ng-if="meter.meter_type != '卓度'">{{row.data_t}}</td>
                                <!--<td ng-if="meter.meter_type != '卓度'">
                                    <div>
                                        <button type="button" ng-click="openDataChart(row)"
                                                class="btn btn-sm btn-info">曲线图
                                        </button>
                                    </div>
                                </td>-->
                                <td ng-if="meter.meter_type != '卓度'">
                                    <div>
                                        <button type="button" ng-click="open(row)"
                                                class="btn btn-sm btn-info">导出数据
                                        </button>
                                    </div>
                                </td>
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
</div>




<script type="text/javascript">

    $(function () {

        //$('#list_content').html('<tr class="no_data"><td colspan="10">进来了111</td></tr>');


        sendSearch({page: 1});
        function sendSearch(obj){

            $.ajax({
                type:"POST",
                url:'<?php echo ADMIN_SITE_URL.'/home/getWarnInfo'?>',
                data: obj,
                dataType:'json',
                success:function(res){
                    $('#list_content').html('<tr class="no_data"><td colspan="10">进来了111</td></tr>');

  /*                  for(var i = 0 ;i < 10;i++){

                        var str = '<tr class="hover">';
                        str += '<td><span>'+'abc1'+'</span></td>'+
                            '<td><span>'+'abc2'+'</span></td>'+
                            '<td><span>'+'abc3'+'</span></td>'+
                            '<td><span>'+'abc4'+'</span></td>'+
                            '<td><span>'+'abc5'+'</span></td>'+
                            '<td><span>'+'abc6'+'</span></td>'+
                            '<td><span>'+'abc7'+'</span></td>';
                        str+='</tr>';
                        $('#list_content').append(str);

                    }
*/


                    //$('#list_content').html('');
                    $('.pagination').html('');
                    if (res.code == 1) {


                        //$('#list_content').html('');
                        $('.pagination').html('');
                        showList(res.data);
                    }

                    $('#list_content').append('<tr class="no_data"><td colspan="10">'+JSON.stringify(res)+'</td></tr>');


                },
                error:function(XMLHttpRequest, textStatus, thrownError){}
            });

            //ADMIN_SITE_URL.'/home/getWarnInfo


        }
        $('.pagination').find('a').live('click',function(){
            var page = $(this).attr('data-ci-pagination-page');
            if(!page){
                return;
            }
            if(!searchObj){
                searchObj = {};
            }
            searchObj.page = page;
            sendSearch(searchObj);
        });

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
                    str+='</tr>';
                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有报警信息</td></tr>');
            }
        }


    });




</script>

</body>
</html>
