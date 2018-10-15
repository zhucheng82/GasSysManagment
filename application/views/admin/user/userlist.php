<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>产品列表</title>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,jquery.validation.min.js,jquery.cookie.js,common.js', 'js'); ?>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=wdn4UpmNQQZYI5LxiXN2ljORFuxHXox7"></script>
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
            <h3>用户列表</h3>

            <a href="javascript:void(0);" class="btn" id="export_btn" style="float: right;margin-right: 100px;" onclick="get_order_excel()"><span>导出用户</span></a>
        </div>
    </div>
</div>
<div class="fixed-empty"></div>
<form method="post" name="formSearch" id="formSearch">
    <table class="tb-type1 noborder search">
        <tbody>

        <tr>
            <th>状态:</th>
            <td><select name="a_status" id="a_status">
                    <option value="">请选择...</option>
                    <option value="0">未认证</option>
                    <option value="1">已认证</option>
                    <option value="2">审核中</option>
                </select></td>
            <th>职业:</th>
            <td><select name="work" id="work">
                    <option value="">请选择...</option>
                    <option value="-1">未知</option>
                    <option value="0">学生</option>
                    <option value="1">上班族</option>
                </select></td>
            <th>手机:</th>
            <td><input type="text" value="" name="user_name" id="user_name" class="txt"></td>
            <td><button type="submit" class="btn">搜 索</button></td>
        </tr>

        </tbody>
    </table>
</form>

<input type="hidden" id="voucher_price_id" name="voucher_price_id" value=""/>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>用户</th>
        <th>职业</th>
        <th>姓名</th>
        <th>身份证号码</th>
        <th>公司/学校</th>
        <th>职位/学历</th>
        <th>是否新生</th>
        <th>余额</th>
        <th>状态</th>
        <th>申请时间</th>
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
<div style="width: 100%;height: 100%; background-color: rgba(0,0,0,0.6);position: fixed;left: 0;top:0;z-index: 1000;display: none;" id="mapcontent">
    <div id="map" style="width: 80%;height: 80%;position: absolute;left: 10%;top: 10%;">

    </div>
    <a href="javascript:void(0);" class="btn" id="closemap" style="position:absolute;right:11%;top: 11%;" ><span>关闭地图</span></a>
</div>
<script>



    $(function () {
        var map = new BMap.Map('map');
        var poi = new BMap.Point(120.307852,30.057031);
        map.centerAndZoom(poi, 13);
        map.enableScrollWheelZoom();

        var ctrlNav = new window.BMap.NavigationControl({
            anchor: BMAP_ANCHOR_TOP_LEFT,
            type: BMAP_NAVIGATION_CONTROL_LARGE
        });
        //向地图中添加缩略图控件
        var ctrlOve = new window.BMap.OverviewMapControl({
            anchor: BMAP_ANCHOR_BOTTOM_RIGHT,
            isOpen: 1
        });

        var ctrlSca = new window.BMap.ScaleControl({
            anchor: BMAP_ANCHOR_BOTTOM_LEFT
        });
        var marker = new window.BMap.Marker(poi); //按照地图点坐标生成标记
        map.addOverlay(marker);

        map.addControl(ctrlNav);
        map.addControl(ctrlOve);
        map.addControl(ctrlSca);
//        $('#mapcontent').hide();


        sendSearch({page: 1});
        function sendSearch(obj){
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/user/getUserList', function (res) {
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

        var status_type = ['未认证','已认证','审核中'];
        var work_type = ['学生','上班族'];
        var userData ;
        function showList(data){

            if (data.rows && data.rows.length > 0) {

                userData = data.rows;
                for(var i = 0 ;i < data.rows.length;i++){
                    var obj  = data.rows[i];
                    var str = '<tr class="hover">';
                    str +=  '<td><span>'+obj.name+'</span></td>'+
                            '<td><span>'+getWork(obj.work)+'</span></td>';
                    if(obj.work == 0){
                        str +=  '<td><span>'+obj.user_name+'</span></td>'+
                            '<td><span>'+obj.user_identify_card+'</span></td>'+
                            '<td><span>'+obj.school_name+'</span></td>'+
                            '<td><span>'+obj.education+'</span></td>'+
                            '<td><span>'+((obj.is_new_student==1)?'是':'否')+'</span></td>';
                    }else if(obj.work == 1){
                        str +=  '<td><span>'+obj.work_user_name+'</span></td>'+
                            '<td><span>'+obj.work_user_identify_card+'</span></td>'+
                            '<td><span>'+obj.work_company_name+'</span></td>'+
                            '<td><span>'+obj.work_post+'</span></td>'+
                            '<td><span>否</span></td>';
                    }else{
                        str +=  '<td><span>未知</span></td>'+
                            '<td><span>未知</span></td>'+
                            '<td><span>未知</span></td>'+
                            '<td><span>未知</span></td>'+
                            '<td><span>未知</span></td>';
                    }

                    str+='<td><span>'+obj.balance+'</span></td>';
                    str+='<td><span>'+status_type[obj.authentication_status]+'</span></td>'+
                            '<td><span>'+new Date(obj.createtime*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                            '<td><span><a href="details?id='+obj.uid+'&work='+obj.work+'">详情</a> | <a class="position" uid="'+obj.uid+'">查看位置</a></span></td>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }

                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有数据了</td></tr>');
            }
        }

        function getWork(work){
            if(work == 0){
                return '学生';
            }else if(work ==1){
                return  '上班族';
            }else{
                return '未知';
            }
        }


        $('#closemap').click(function(){
            $('#mapcontent').hide();
        })

        function getUserById(id){
            for(var key in userData){
                var obj = userData[key];
                if(obj.uid == id){
                    return obj;
                }
            }

            return null;
        }




        var searchObj ;
        $('#formSearch').validate({
            submitHandler:function(){
                var work = $('#work').val();
                var a_status = $('#a_status').val();
                var user_name = $('#user_name').val();
                var obj = {};

                if(work !==''){
                    obj['work'] = work;
                }

                if(a_status !==''){
                    obj['a_status'] = a_status;
                }


                if(user_name !==''){
                    obj['user_name'] = user_name;
                }

                searchObj = obj;
                sendSearch(obj);
                return;
            }
        });

        $('#mapcontent').click(function(){
            if(event.target == this){
                $(this).hide();
            }

        });

        var currentUser;

        $('#list_content').on('click','.position',function(){


            var obj = getUserById($(this).attr('uid'));

            if(!obj || !obj.longitude){
                showTips('没有找到用户定位信息');
                return;
            }



//            if(obj == currentUser){
//                $('#mapcontent').show();
//                map.panTo(poi);
//                return;
//            }
            $('#mapcontent').fadeIn(function(){

                var convertor = new BMap.Convertor();
                poi =  new BMap.Point(obj.longitude,obj.latitude);
                var pointArr = [];
                pointArr.push(poi);
                convertor.translate(pointArr, 1, 5, function(data){

                    if(data.status === 0) {
                        poi = data.points[0];
                    }else{
//                        showTips('坐标转换错误')
                    }

                    map.addOverlay(marker);
                    map.setCenter(poi);
                    map.setZoom(13);
                    marker.setPosition(poi);
                    var gc = new BMap.Geocoder();
                    gc.getLocation(poi, function(rs){
                        var addComp = rs.addressComponents;
                        var addr = (addComp.province + addComp.city + addComp.district +  addComp.street +  addComp.streetNumber);
                        var info = new window.BMap.InfoWindow("用户 : "+obj.name+"</br>地址： "+addr+" </br>时间： "+new Date(Number(obj.location_time)*1000).Format('yyyy-MM-dd hh:mm:ss' )); // 创建信息窗口对象
                        marker.openInfoWindow(info);
                    });
                })



            });
            currentUser = obj;






//        marker.setAnimation(BMAP_ANIMATION_BOUNCE);

//        var label = new window.BMap.Label('位置', { offset: new window.BMap.Size(20, -10) });
//        marker.setLabel(label);


        })

    });

    function get_order_excel()
    {
       document.formSearch.action="/admin/user/export";
       document.formSearch.submit();
    };

</script>
</body>
</html>