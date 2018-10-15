<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>今日逾期还款统计</title>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,jquery.validation.min.js,jquery.cookie.js,common.js,jquery.datetimepicker.full.min.js', 'js'); ?>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'perfect-scrollbar.min.css,jquery.datetimepicker.min.css', 'css'); ?>

    <?php echo _get_html_cssjs('admin', TPL_ADMIN_NAME . 'css/font-awesome.min.css', 'css'); ?>

    <!--[if IE 7]>
    <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
    <![endif]-->
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
    <style>
        a{
            cursor: pointer;
        }

        .redfont{
            color:red;
        }
        .statistics{
            color:#0000FF;
            font-size: 13px;
            margin-left: 20px;
        }
        .statistics em{
            font-size: 18px;
            color:#F32613;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>今日借贷信息</h3>

            <ul class="tab-base">
                <li><a href="<?php echo ADMIN_SITE_URL.'/welcome/index'?>"><span>今日申请</span></a></li>
                <li><a class="current" href="JavaScript:void(0);" ><span>逾期还款</span></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="fixed-empty"></div>

<div style="color:#000000" text="#ffd2b9">
    <input type="hidden" id="statistic" name="statistic" value=""/>
    <div class="statistics">
        今日申请总人数：<em id = "total_apply"></em>&nbsp&nbsp&nbsp逾期还款总笔数：<em id = "total_delay"></em>
    </div>
</div>

<input type="hidden" id="voucher_price_id" name="voucher_price_id" value=""/>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>用户</th>
        <th>产品</th>
        <th>本金</th>
        <th>利息</th>
        <th>申请时间</th>
        <th>还款期数</th>
        <th>到期时间</th>
        <th>还款时间</th>
        <th>状态</th>
        <th>操作</th>

        <!--      <th class="align-center">--><?php //echo $lang['nc_handle'];?><!--</th>-->
    </tr>
    </thead>
    <tbody id="list_content">

    </tbody>
    <tfoot>
    <tr class="tfoot">
        <div class="pagination"></div>
        </td>
    </tr>
    </tfoot>
</table>
<script>



    $(function () {
        $.datetimepicker.setLocale('zh');
        $('#start_time').datetimepicker({
            mask:'',
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d'
        });
        $('#end_time').datetimepicker({
            mask:'',
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d'
        });
        sendSearch({page: 1});
        function sendSearch(obj){
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/welcome/delayRepaymentList', function (res) {
                $('#list_content').html('');
                $('.pagination').html('');
                if (res.code == 1) {
                    $('#total_apply').html(res.data.total_apply);
                    $('#total_delay').html(res.data.total_delay);
                    showList(res.data);
                }
            });
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
                    str +=  '<td><span>'+(obj.user_name?obj.user_name:obj.mobile)+'</span></td>'+
                    '<td><span>'+obj.product_name+'</span></td>'+
                    '<td><span>'+obj.repayment_amount+'</span></td>'+
                    '<td><span>'+obj.repayment_interest +'</span></td>'+
                    '<td><span>'+new Date(obj.applied_time*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                    '<td><span>'+obj.borrowing_period +'期'+'/'+obj.credit_cycle+'期</span></td>'+
                    '<td><span class="'+((obj.repayment_deadline*1000 < new Date().getTime())?'redfont':'')+'">'+new Date(obj.repayment_deadline*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                    '<td><span>'+(obj.repayment_time?new Date(obj.repayment_time*1000).Format('yyyy-MM-dd hh:mm:ss'):'--')+'</span></td>'+
                    '<td><span>'+(obj.repayment_status ==0?'未还款':'已还款')+'</span></td>'+
                    '<td><span><a href="repaymentDetail?id='+obj.borrowing_id+'">详情</a></span></td>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有逾期还款用户</td></tr>');
            }
        }




        var searchObj ;
        $('#formSearch').validate({
            submitHandler:function(){
                var work = $('#work').val();
                var a_status = $('#a_status').val();
                var user_name = $('#user_name').val();
                var start_time = new Date($('#start_time').val()).getTime()/1000;
                var end_time = new Date($('#end_time').val()).getTime()/1000;
                if(start_time && end_time && start_time >= end_time){
                    showTips('开始时间不能大于结束时间',3);
                    return;
                }
                var obj = {page:1};
                if(start_time ){
                    obj['start_time'] = start_time;
                }

                if(end_time ){
                    obj['end_time'] = end_time;
                }
                if(user_name !==''){
                    obj['user_name'] = user_name;
                }


                if(work !==''){
                    obj['work'] = work;
                }

                if(a_status !==''){
                    obj['a_status'] = a_status;
                }


                searchObj = obj;
                sendSearch(obj);
                return;
            }
        });

    });

    function get_order_excel()
    {

        var url = "/admin/finance/export_repayment_list";
        var start_time = new Date($('#start_time').val()).getTime()/1000;
        var end_time = new Date($('#end_time').val()).getTime()/1000;
        if(start_time && end_time && start_time >= end_time){
            showTips('开始时间不能大于结束时间',3);
            return;
        }
        if(start_time){
            url+='?start='+start_time;
            if(end_time){
                url+= '&end=' +end_time;
            }
        }else{
            if(end_time){
                url+= '?end=' +end_time;
            }
        }
        document.formSearch.action=url;
        document.formSearch.submit();
    };

</script>
</body>
</html>