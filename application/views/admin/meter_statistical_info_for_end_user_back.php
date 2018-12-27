<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">
    <title><?php echo $output['html_title'];?></title>

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
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">用户用气曲线图</h3>
                </div>
                <div class="panel-body text-center">
                    <div id="dataChart" ng-controller="UserMeterDataCtrl">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <button id="oneWeekBt" type="button" class="btn btn-primary" ng-click="week()">一周
                                </button>
                                <button id="oneMonthBt" type="button" class="btn btn-default" ng-click="month()">一月
                                </button>
                                <button id="oneYearBt" type="button" class="btn btn-default" ng-click="year()">一年
                                </button>
                                <button id="totalBt" type="button" class="btn btn-default" ng-click="total()">所有
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <highchart id="chart1" config="chartConfig" class="span10"
                                       style="width:100%; height:500px;"></highchart>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">重要报警信息</h3>
                </div>
                <div class="panel-body text-left">
                    <div ng-controller="WarnCtrl-1">
                        <table st-table="displayedCollection"
                               st-safe-src="rowCollection" class="table table-striped">
                            <thead>
                            <tr>
                                <th colspan="2"><input st-search=""
                                                       class="form-control" placeholder="在此搜索。。。"
                                                       type="text"/></th>
                            </tr>
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
                            <tbody>
                            <?php
                            if(isset(meter_warn_info_pages)){
                            foreach(meter_warn_info_pages as $data ){

                            $data_warn = $data['data_warn'];
                            $date = $data['warn_date'];
                            $warn_level = $data['warn_level'];
                            $warn_solution = $data['warn_solution'];

                            $meter_name = $data['meter_name'];
                            ?>
                            <tr>
                                <!--
                                <td>{{row.company}}</td>
                                <td>{{row.user_id}}</td>
                                <td>{{row.meter_info}}</td>
                                <td>{{row.warn_info}}</td>
                                <td>{{row.warn_level}}</td>
                                <td>{{row.solution}}</td>
                                <td>{{row.warn_date}}</td>
                                -->
                                <td><?php echo $warn_level ?>{{row.company}}</td>
                                <td><?php echo $warn_level ?>{{row.user_id}}</td>
                                <td><?php echo $warn_level ?>{{row.meter_info}}</td>
                                <td><?php echo $warn_level ?>{{row.warn_info}}</td>
                                <td><?php echo $warn_level ?></td>
                                <td><?php echo $warn_solution ?></td>
                                <td><?php echo $date ?></td>

                            </tr>

                            <?php }?>
                            <?php }?>

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

                    //$('#list_content').html('<tr class="no_data"><td colspan="10">进来了111</td></tr>');

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

                    //$('#list_content').append('<tr class="no_data"><td colspan="10">'+JSON.stringify(res)+'</td></tr>');


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
