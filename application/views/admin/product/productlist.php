<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>产品列表</title>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js', 'js'); ?>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'perfect-scrollbar.min.css', 'css'); ?>

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
            <h3>产品管理</h3>
            <ul class="tab-base">
                <li><a class="current"><span>产品列表</span></a></li>
                <li><a href="<?php echo BASE_SITE_URL?>/admin/product/newproduct"><span>新增产品</span></a></li>

            </ul>
            <a href="javascript:void(0);" class="btn" id="export_btn" style="float: right;margin-right: 100px;" onclick="get_order_excel()"><span>导出产品</span></a>
        </div>
    </div>
</div>
<div class="fixed-empty"></div>
<form method="post" name="formSearch" id="formSearch">
    <table class="tb-type1 noborder search">
        <tbody>

        <tr>
            <th>类型:</th>
            <td><select name="pro_type" id="pro_type">
                    <option value="">请选择...</option>
                    <?php foreach($pro_type as $k => $v){?>
                        <option value="<?php echo $v['id'];?>"><?php echo $v['name'];?></option>
                    <?php }?>
                </select></td>
            <th>状态:</th>
            <td><select name="pro_status" id="pro_status">
                    <option value="">请选择...</option>
                    <option value="1">上线</option>
                    <option value="-1">下线</option>
                </select></td>
            <th>产品名:</th>
            <td><input type="text" value="" name="pro_name" id="pro_name" class="txt"></td>
            <td><button type="submit" class="btn">搜 索</button></td>
        </tr>

        </tbody>
    </table>
</form>

<input type="hidden" id="voucher_price_id" name="voucher_price_id" value=""/>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>产品ID</th>
        <th>产品名称</th>
        <th>类型</th>
        <th>贷款金额</th>
        <th>还款周期</th>
        <th>周期类型</th>
        <th>周期间隔</th>
        <th>利率</th>
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
        var pro_type_list = <?php echo json_encode($pro_type);?>;
        var repay_type = <?php echo json_encode($repay_type);?>;

        sendSearch({page: 1});
        function sendSearch(obj){
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/product/getProList', function (res) {
                $('#list_content').html('');
                $('.pagination').html('');
                if (res.code == 1) {
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
                    var str = '<tr class="hover" pro_id="'+obj.id+'">';
                    str +=  '<td><span>'+obj.id+'</span></td>'+
                            '<td><span>'+obj.product_name+'</span></td>'+
                            '<td><span>'+getPtypeName(obj.productType)+'</span></td>'+
                            '<td><span>'+obj.credit_limit+'~'+obj.credit_upper_limit+'元</span></td>'+
                            '<td><span>'+obj.repayment_cycle_limit+'~'+obj.repayment_cycle_upper_limit+'</span></td>'+
                            '<td><span>'+repay_type[obj.repaymentType]+'</span></td>'+
                            '<td><span>'+(obj.repaymentType==1?'一月':obj.repaymentType==2?'一周':obj.repayment_cycle_days+'天')+'</span></td>'+
                            '<td><span>'+obj.rate+'%</span></td>'+
                            '<td><span class="pro_status">'+getStatus(obj.status)+'</span></td>'+
                            '<td><span><a href="productdetail?id='+obj.id+'">查看</a> | <a class="status_change" status_type="'+obj.status+'" pro_id="'+obj.id+'">'+getStatusF(obj.status)+'</a> | <a class="recommend_change" recommend_type="'+obj.is_recommend+'" pro_id="'+obj.id+'">'+getStatusT(obj.is_recommend)+'</a></span></td>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }
                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有数据了</td></tr>');
            }
        }

        function getPtypeName(id){
            for(var key in pro_type_list){
                var o = pro_type_list[key];
                if(o.id == id){
                    return o.name;
                }
            }
            return '';
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

        $('.recommend_change').live('click',function(){
            var pro_id = $(this).attr('pro_id');
            var recommend =$(this).attr('recommend_type');
            if(recommend == 1){
                recommend = '0';
            }else{
                recommend = 1;
            }

            sendPostData({id:pro_id,recommend_status:recommend},'<?php echo BASE_SITE_URL?>/admin/product/changeRecommend', function (res) {
               if(res.code == 1){
                   $('.hover[pro_id='+pro_id+']').find('.recommend_change').text(getStatusT(res.data.is_recommend)).attr('recommend_type',res.data.is_recommend);
                   showTips('操作成功',3);
               }
            });
            return false;

        })

        function getStatusF(status){
            if(status == 1){
                return '下线';
            }else{
                return '上线';
            }
        }

        function getStatusT(status){
            if(status == 1){
                return '取消推荐';
            }else{
                return '设为推荐';
            }
        }

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
                var pro_name = $('#pro_name').val();
                var pro_type = $('#pro_type').val();
                var pro_status = $('#pro_status').val();
                var obj = {};

                if(pro_name !==''){
                    obj['pro_name'] = pro_name;
                }

                if(pro_type !==''){
                    obj['pro_type'] = pro_type;
                }


                if(pro_status !==''){
                    obj['pro_status'] = pro_status;
                }

                searchObj = obj;
                sendSearch(obj);
                return;
            }
        });

    });

    function get_order_excel()
    {
       document.formSearch.action="/admin/product/export";
       document.formSearch.submit();
    };

</script>
</body>
</html>