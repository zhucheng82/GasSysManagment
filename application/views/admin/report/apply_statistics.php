<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>借款申请统计</title>
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
    </style>
</head>
<body>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>申请列表</h3>

            <a href="javascript:void(0);" class="btn" id="export_btn" style="float: right;margin-right: 100px;" onclick="get_order_excel()"><span>导出借款清单</span></a>
        </div>
    </div>
</div>

<div class="fixed-empty"></div>
<form method="post" name="formSearch" id="formSearch">

    <table class="tb-type1 noborder search">
        <tbody>

        <tr>
            <th>开始:</th>
            <td><input type="text" value="" name="start_time" id="start_time" class="txt"></td>
            <th>结束</th>
            <td><input type="text" value="" name="end_time" id="end_time" class="txt"></td>
            <td><select name="a_status" id="a_status">
                    <option value="">请选择...</option>
                    <option value="0">未认证</option>
                    <option value="1">已认证</option>
                </select></td>
            <th>用户名:</th>
            <td><input type="text" value="" name="user_name" id="user_name" class="txt"></td>
            <td><button type="submit" class="btn">搜 索</button></td>
        </tr>

        </tbody>
    </table>

</form>

<div style="color:#000000" text="#ffd2b9">
    <input type="hidden" id="statistic" name="statistic" value=""/>
    <b id = "total_info"></b>
</div>

<input type="hidden" id="voucher_price_id" name="voucher_price_id" value=""/>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>姓名</th>
        <th>手机</th>
        <th>申请产品</th>
        <th>申请金额</th>
        <th>用途</th>
        <th>申请时间</th>
        <th>通过时间</th>
        <th>通过金额</th>
        <th>扣留金</th>
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
        var borrowingStatus = <?php echo json_encode($borrowingStatus);?>;
        sendSearch({page: 1});
        function sendSearch(obj){
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/finance/getOrderList', function (res) {
                $('#list_content').html('');
                $('.pagination').html('');
                if (res.code == 1) {
//                    $('#total_info').html('&nbsp申请总人数：'+res.data.total+'&nbsp&nbsp&nbsp'+'申请总金额：'+res.data.total_amount);
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
                    str +=  '<td><span>'+(obj.user_name?obj.user_name:'')+'</span></td>'+
//                            '<td><span>'+obj.borrowing_product_id+'</span></td>'+
                            '<td><span>'+obj.mobile+'</span></td>'+
                            '<td><span>'+obj.product_name+'</span></td>'+
                            '<td><span>￥'+obj.apply_amount+'</span></td>'+
                            '<td><span>'+obj.usage +'</span></td>'+
                            '<td><span>'+new Date(obj.applied_time*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                            '<td><span>'+(obj.approved_time?new Date(obj.approved_time*1000).Format('yyyy-MM-dd hh:mm:ss'):'--')+'</span></td>'+
                            '<td><span>'+(obj.approve_amount?('￥'+obj.approve_amount):"--")+'</span></td>'+
                            '<td><span>'+(obj.detain_amount?('￥'+obj.detain_amount):"--")+'</span></td>'+
                        '<td><span>'+borrowingStatus[obj.status] +'</span></td>'+
                            '<td><span><a href="details?id='+obj.id+'&work='+obj.work+'">查看</a></span></td>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有数据了</td></tr>');
            }
        }

        function getStatus(status){
            if(status == 1){
                return '上线';
            }else{
                return '下线';
            }
        }

        $('.status_change').live('click',function(){
            var pro_id = $(this).attr('pro_id');
            var status =$(this).attr('status_type');
            if(status == 1){
                status = -1;
            }else{
                status = 1;
            }
            sendPostData({id:pro_id,status:status},'<?php echo BASE_SITE_URL?>/admin/product/changeStatus', function (res) {
               if(res.code == 1){
                    $('.hover[pro_id='+pro_id+']').find('.pro_status').text(getStatus(res.data.status));
                   $('.hover[pro_id='+pro_id+']').find('.status_change').text(getStatusF(res.data.status)).attr('status_type',res.data.status);
                   showTips('操作成功',3);
               }
            });
            return false;

        })



        function getTimeFormat(time){
            if(Number(time)){
                return new Date(time*1000).Format('MM-dd hh:mm:ss')
            }else{
                return "--";
            }
        }


        var searchObj ;
        $('#formSearch').validate({
            submitHandler:function(){
//                var work = $('#work').val();
//                var a_status = $('#a_status').val();
                var user_name = $('#user_name').val();
                var obj = {page:1};
                var start_time = new Date($('#start_time').val()).getTime()/1000;
                var end_time = new Date($('#end_time').val()).getTime()/1000;
                if(start_time && end_time && start_time >= end_time){
                    showTips('开始时间不能大于结束时间',3);
                    return;
                }

                if(start_time){
                    obj['start_time'] = start_time;
                }

                if(end_time){
                    obj['end_time'] = end_time;
                }
                if(user_name !==''){
                    obj['user_name'] = user_name;
                }

                searchObj = obj;
                sendSearch(obj);
                return;
            }
        });

    });

    function get_order_excel()
    {
        var url = "/admin/finance/export_borrow_list";
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