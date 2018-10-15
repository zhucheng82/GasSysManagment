<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>借款申请</title>
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
        .reportth{
            color:#0000FF !important;
            font-size: 13px;
            cursor: auto;
        }
        .report{
            color:red;
            font-size: 18px;
            cursor: auto;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>还款列表</h3>

            <a href="javascript:void(0);" class="btn" id="export_btn" style="float: right;margin-right: 100px;" onclick="get_order_excel()"><span>导出还款清单</span></a>
        </div>
    </div>
</div>
<div class="fixed-empty"></div>
<form method="post" name="formSearch" id="formSearch">
    <table class="tb-type1 noborder search">
        <tbody>

        <tr>
            <th>(借款时间)开始:</th>
            <td><input type="text" value="" name="start_time" id="start_time" class="txt"></td>
            <th>结束</th>
            <td><input type="text" value="" name="end_time" id="end_time" class="txt"></td>
            <td><select name="work" id="work">
                    <option value="">用户类型</option>
                    <option value="0">大学生</option>
                    <option value="1">上班族</option>
                </select></td>
            <td><select name="a_status" id="a_status">
                    <option value="">还款状态</option>
                    <option value="0">未还款</option>
                    <option value="1">已还款</option>
                </select></td>

            <th>用户名:</th>
            <td><input type="text" value="" name="user_name" id="user_name" class="txt"></td>
            <td><button type="submit" class="btn">搜 索</button></td>
        </tr>

        </tbody>
    </table>
</form>

<input type="hidden" id="voucher_price_id" name="voucher_price_id" value=""/>

<div>
    <table class="tb-type1 noborder search">
        <tbody>
        <tr >

            <th class="reportth">还款总笔数:</th><td><label id="total_count" class="report"></label></td>
            <th class="reportth">本金总额:</th><td><label id="bjtotal" class="report"></label></td>
            <th class="reportth">利息总额:</th><td><label id="lxtotal" class="report"></label></td>
            <th class="reportth">逾期罚款金额:</th><td><label id="detain" class="report"></label></td>
        </tr>
        </tbody>
    </table>
</div>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>用户</th>
        <th>产品</th>
        <th>本金</th>
        <th>利息</th>
        <th>申请时间</th>
        <th>还款期数</th>
        <th>截止还款时间</th>
        <th>实际还款时间</th>
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
            formatDate:'Y-m-d',
        });
        $('#end_time').datetimepicker({
            mask:'',
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d',
        });
        sendSearch({page: 1});
        function sendSearch(obj){
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/finance/repaymentList', function (res) {
                $('#list_content').html('');
                $('.pagination').html('');
                if (res.code == 1) {

                    showList(res.data);
                    nullClear(res.data.report,0);
                    $('#total_count').text((res.data.report.total_count)+'');
                    $('#bjtotal').text('￥'+res.data.report.bjtotal+' ');
                    $('#lxtotal').text('￥'+res.data.report.lxtotal+' ');
                    $('#detain').text('￥'+res.data.report.detain+' ');
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
                            '<td><span class="'+((obj.repayment_deadline*1000 < new Date().getTime() && obj.repayment_status==0)?'redfont':'')+'">'+new Date(obj.repayment_deadline*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                            '<td><span class="'+(obj.repayment_deadline < obj.repayment_time?'redfont':'')+'">'+(obj.repayment_time?new Date(obj.repayment_time*1000).Format('yyyy-MM-dd hh:mm:ss'):'--')+'</span></td>'+
                            '<td><span>'+(obj.repayment_status ==0?'未还款':'已还款')+'</span></td>'+
                            '<td><span><a href="repaymentDetail?id='+obj.borrowing_id+'">详情</a></span></td>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有数据了</td></tr>');
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