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
            <h3>提现列表</h3>

            <a href="javascript:void(0);" class="btn" id="export_btn" style="float: right;margin-right: 100px;" onclick="get_order_excel()"><span>导出提现清单</span></a>
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
                    <option value="">状态</option>
                    <option value="2">已打款</option>
                    <option value="1">未打款</option>
                    <option value="-1">已拒绝</option>
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

            <th class="reportth">提现总笔数:</th><td><label id="total_count" class="report"></label></td>
            <th class="reportth">提现总额:</th><td><label id="amount" class="report"></label></td>
<!--            <th class="reportth">提现手续总额:</th><td><label id="fees" class="report"></label></td>-->
        </tr>
        </tbody>
    </table>
</div>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>姓名</th>
        <th>电话</th>
        <th>银行</th>
        <th>银行卡号</th>
        <th>提现金额</th>
        <th>提现手续费</th>
        <th>申请时间</th>
        <th>操作时间</th>
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
<div id="sure_div" style="display:none;position:fixed;left:0;top:0;width:100%;height: 100%;z-index:99;background:#000;background:rgba(0, 0, 0, 0.6)!important;filter:Alpha(opacity=60);background:#000; text-align:center;">
    <div style="width: 25%;left: 35%;top: 20%;position: absolute;background: #fff" >
        <table class="table tb-type2 tb-type1" style="width: 100%;height: 100%">
            <tr class="space">
                <th colspan="3" id="tips">打款处理</th>
            </tr>
            <tr class="space">
                <td colspan="3">
                    <label id="desc">确定同意放款吗?</label>
                </td>
            </tr>
            <tr class="tfoot">
                <td colspan="3"><a class="btn" id="submitBtn" ><span>确定</span></a><a class="btn" id="cancelBtn"><span>取消</span></a></td>
            </tr>
        </table>
    </div>
</div>
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
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/finance/withdrawList', function (res) {
                $('#list_content').html('');
                $('.pagination').html('');
                if (res.code == 1) {
                    showList(res.data);
                    nullClear(res.data.report,0);
                    $('#total_count').text((res.data.report.total_count)+'');
                    $('#amount').text('￥'+res.data.report.amount+' ');
//                    $('#fees').text('￥'+res.data.report.fees+' ');
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
        var currentID;
        var currentType;
        $('#submitBtn').click(function(){
            if(!currentID){
                return;
            }
            sendPostData({id:currentID,type:currentType},'<?php echo ADMIN_SITE_URL.'/finance/withdrawdeal'?>',function(res){
                if(res.code == 1){
                    if(currentType == 1){
                        showTips('打款成功');
                    }else if(currentType == 2){
                        showTips('已拒绝打款');
                    }
                    sendSearch(searchObj);
                    $('#sure_div').hide();
                }
            })
        });
        $('#cancelBtn').click(function(){
            $('#sure_div').hide();
            currentID = 0;

        })

        $('.order_deal').live('click',function(){
            currentID = $(this).attr('oid');
            var type = $(this).attr('otype');
            currentType = type
            if(type == 1){
                $('#desc').text('确定同意打款吗?');
            }else if(type == 2){
                $('#desc').text('确定拒绝打款吗?');
            }
            $('#sure_div').show();


        })

        function showList(data){

            if (data.rows && data.rows.length > 0) {
                for(var i = 0 ;i < data.rows.length;i++){
                    var obj  = data.rows[i];
                    var status = parseInt(obj.status);

                    var str = '<tr class="hover">';
                    str +=  '<td><span>'+obj.user_name+'</span></td>'+
                            '<td><span>'+obj.user_phone+'</span></td>'+
                            '<td><span>'+obj.bank+'</span></td>'+
                            '<td><span>'+obj.card_id+'</span></td>'+
                            '<td><span>'+obj.amount +'</span></td>'+
                            '<td><span>'+obj.fees +'</span></td>'+

                            '<td><span>'+new Date(obj.createtime*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                            '<td><span>'+(obj.op_time?new Date(obj.op_time*1000).Format('yyyy-MM-dd hh:mm:ss'):'--')+'</span></td>'+
                        '<td><span>'+getStatus(status)+'</span></td>'+
//                            '<td><span><a href="withdrawDetails?id='+obj.id+'">详情</a></span></td>';
                        '<td>'+(obj.status==1?'<span><a class="order_deal" href="JavaScript:void(0);" oid="'+obj.id+'" otype="1">同意</a></span> | <span><a class="order_deal" href="JavaScript:void(0);" oid="'+obj.id+'" otype="2">拒绝</a></span>':'')+'</td>';
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
            
            switch (status){
                case 1:
                    return '未打款';
                case 2:
                    return '已打款';
                case 3:
                    return '银行处理中';
                case 4:
                    return '银行交易失败';
                case -1:
                    return '已拒绝';

            }
        }


        var searchObj ;
        $('#formSearch').validate({
            submitHandler:function(){
                var user_name = $('#user_name').val();
                var a_status = $('#a_status').val();
                var obj = {page:1};
                var start_time = new Date($('#start_time').val()).getTime()/1000;
                var end_time = new Date($('#end_time').val()).getTime()/1000;
                if(start_time && end_time && start_time >= end_time){
                    showTips('开始时间不能大于结束时间',3);
                    return;
                }
                if(a_status){
                    obj['status'] = a_status;
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


        var url = "/admin/finance/export_withdraw_list";
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