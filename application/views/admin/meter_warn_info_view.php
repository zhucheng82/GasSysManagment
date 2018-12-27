<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html;" charset="<?php echo CHARSET?>">

    <title>报警信息</title>

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

    <!--Bootstrap-->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!--
    <script src="/res/admin/js/jquery.validation.min.js"></script>
    <script src="/res/admin/js/jquery.cookie.js"></script>
    <script src="/res/admin/js/common.js"></script>

    -->

    <!--
    <link href="/res/admin/templates/default/css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    -->

    <!--
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=wdn4UpmNQQZYI5LxiXN2ljORFuxHXox7"></script>
    <link rel="stylesheet" href="/res/admin/css/perfect-scrollbar.min.css" />

    <link rel="stylesheet" href="/res/admin/templates/default/css/font-awesome.min.css" />
-->

    <style>
        a{
            cursor: pointer;
        }
    </style>


</head>

<body>


<div class = "right_info">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">所有报警信息</h3>
                </div>
                <div class="panel-body text-left">
                    <div ng-controller="WarnCtrl-all">
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
</div>




<script type="text/javascript">

    $(function () {

        //$('#list_content').html('<tr class="no_data"><td colspan="10">进来了111</td></tr>');


        sendSearch({page: 1});
        function sendSearch(obj){

            $.ajax({
                type:"POST",
                url:'<?php echo ADMIN_SITE_URL.'/home/getAllWarnInfoForAdmin'?>',
                data: obj,
                dataType:'json',
                success:function(res){

                    console.log(JSON.stringify(res));

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


                        $('#list_content').html('');
                        $('.pagination').html('');
                        showList(res.data);
                    }

                    //$('#list_content').append('<tr class="no_data"><td colspan="10">'+JSON.stringify(res)+'</td></tr>');


                },
                error:function(XMLHttpRequest, textStatus, thrownError){}
            });

            //ADMIN_SITE_URL.'/home/getWarnInfo


        }

        /*
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
        */

        $('.pagination').on('click', 'a', function(){
            var page = $(this).attr('data-ci-pagination-page');
            if(!page){
                return;
            }

            sendSearch({page: page});
        });

        function showList(data){

            if (data.rows && data.rows.length > 0) {

                for(var i = 0 ;i < data.rows.length;i++){
                    var obj  = data.rows[i];
                    var str = '<tr class="hover">';
                    str += '<td><span>'+obj.company+'</span></td>'+
                        '<td><span>'+obj.user+'</span></td>'+
                        '<td><span>'+obj.meter_name+'</span></td>'+
                        '<td><span>'+obj.data_warn_reason +'</span></td>'+
                        '<td><span>'+obj.data_warn_level+'</span></td>'+
                        '<td><span>'+obj.data_warn_solution+'</span></td>'+
                        '<td><span>'+obj.warn_date+'</span></td>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);

                //console.log('.pagination html:'+$('.pagination').html());
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有报警信息</td></tr>');
            }
        }


    });




</script>

</body>
</html>
