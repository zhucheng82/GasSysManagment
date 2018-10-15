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
    </style>
</head>
<body>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>统计</h3>


        </div>
    </div>
</div>
<div class="fixed-empty"></div>
<div>
    <div id="chart1"></div>
</div>
<form method="post" name="formSearch" id="formSearch">
    <table class="tb-type1 noborder search">
        <tbody>
        <tr>

            <td><select name="a_work" id="a_work">
                    <option value="0" selected>学生</option>
                    <option value="1">白领</option>

                </select></td>
            <td><select name="a_status" id="a_status">
                    <option value="1" selected>地区统计</option>
                    <option value="2">性别统计</option>
                    <option value="3">出生年月</option>
                    <option value="4">入学年份/入职年份</option>
                    <option value="5">身份证所在地区统计</option>
                    <option value="6">产品申请统计</option>
                </select></td>
            <td>
                <a href="javascript:void(0);" class="btn" id="export_btn" style="" ><span>刷新</span></a>
            </td>

        </tr>
        </tbody>
    </table>
</form>
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
<script src="/res/admin/js/fushionCharts/JSClass/FusionCharts.js" type="text/javascript"></script>
<script>


    var chart;
    var reportType = 1;
    var workType = 0;
    var searchObj ;
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
//        var borrowingStatus = <?php //echo json_encode($borrowingStatus);?>//;
        var dataXml1 = "<graph caption='饼图' xAxisName='月份' yAxisName='Units' showNames='1' decimalPrecision='3' formatNumberScale='1'>" +
            "<set name='1' value='0' color='008ED6' />"+
            "</graph>";

        chart = new FusionCharts("/res/admin/js/fushionCharts/Charts/Pie3D.swf", "swfchart1", "100%", "500","0","1","",false);
        chart.setDataXML(dataXml1);
        chart.render("chart1");
        setTimeout(function(){
            reshow();
        },500);







        $("#a_status").change(function(){
            reportType =$(this).val();
            if(reportType == 6){
                $("#a_work").hide();
            }else{
                $("#a_work").show();
            }
            reshow();
//            chart.render("chart1");
        })


        $("#a_work").change(function(){
            workType =$(this).val();
            reshow();
//            chart.render("chart1");
        })


        sendSearch({page: 1,work:workType});



        function reshow(){
            searchObj = {page:1,work:workType};
            if(reportType == 1){
                showAreaReport();
            }else if(reportType == 2){
                showGenderReport();
            }else if(reportType == 3){
                showBrithdayReport();
            }else if(reportType == 4){
                showSchoolReport();
            }else if(reportType == 5){
                showIDAreaReport();
            }else if(reportType == 6){
                showproductReport();
                delete searchObj.work;
            }

            sendSearch(searchObj);

        }

        $('#export_btn').click(function(){
            reshow();
        });

    });


    function sendSearch(obj){
        sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/report/getUserList', function (res) {
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
    function showList(data){

        if (data.rows && data.rows.length > 0) {


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


                str+='<td><span>'+status_type[obj.authentication_status]+'</span></td>'+
                    '<td><span>'+new Date(obj.createtime*1000).Format('yyyy-MM-dd hh:mm:ss')+'</span></td>'+
                    '<td><span><a href="<?php echo BASE_SITE_URL;?>/admin/user/details?id='+obj.user_id+'&work='+obj.work+'">查看</a></span></td>';
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









    function showproductReport(){

        sendPostData({work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/productReport',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
//                    obj.link = 'JavaScript:showByProduct("'+obj.id+'");';
                    obj.link = '';
                }
                addColor(res.data);
                var str = '产品分布统计';

                var dataXml = getXMLData(str,'产品',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
            }
        });
    }



    function showSchoolReport(){
        sendPostData({work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getEnterSchoolReport',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showByEnterTime("'+obj.year+'");';
                }
                addColor(res.data);
                var str = '';
                if(workType == 0){
                    str = '入学年份分布统计';
                }else{
                    str = '入职年份分布统计';
                }
                var dataXml = getXMLData(str,'年份',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
            }
        });
    }
    function showBrithdayReport(){
        sendPostData({work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getBrithdayReport',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showByBirthdayType("'+obj.type+'");';
                }
                addColor(res.data);
                var dataXml = getXMLData('年龄分布统计','年龄',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
            }
        });
    }

    function showAreaReport(){
        sendPostData({work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getProvinceReportlist',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showCity("'+obj.id+'","'+obj.name+'");';
                }
                addColor(res.data);
                var dataXml = getXMLData('家庭住址统计','住址',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
            }
        });
    }



    function showGenderReport(){
        sendPostData({work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getGrenderReport',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showByGender("'+obj.gender+'");';
                }
                addColor(res.data);
                var dataXml = getXMLData('性别统计','性别',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
            }
        });
    }

    function showByGender(gender){
        searchObj = {page:1,gender:gender,work:workType};
        sendSearch(searchObj);
    }
    function showByBirthdayType(type){
        searchObj = {page:1,btype:type,work:workType};
        sendSearch(searchObj);
    }

    function showByEnterTime(type){
        searchObj = {page:1,ytype:type,work:workType};
        sendSearch(searchObj);
    }

    function showByProduct(id){
        searchObj = {page:1,product:id};
        sendSearch(searchObj);
    }


    function showCity(id,name){
//        showTips(id+name);
        sendPostData({pid:id,work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getCityReportlist',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showDistrict("'+obj.id+'","'+obj.name+'");';
                }
                addColor(res.data);
                var dataXml = getXMLData(name+' 家庭住址统计','住址',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
                searchObj = {page:1,work:workType,cur_pid:id};
                sendSearch(searchObj);
            }
        });
    }




    function showDistrict(id,name){
        sendPostData({pid:id,work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getDistrictReportlist',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = '';
                }
                addColor(res.data);
                var dataXml = getXMLData(name+'家庭住址统计','住址',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
                searchObj = {page:1,work:workType,cur_cid:id};
                sendSearch(searchObj);
            }
        });
    }


    function showIDAreaReport(){
        sendPostData({work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getIDProvinceReportlist',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showIDCity("'+obj.id+'","'+obj.name+'");';
                }
                addColor(res.data);
                var dataXml = getXMLData('身份证所在地统计','住址',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
            }
        });
    }



    function showIDCity(id,name){
//        showTips(id+name);
        sendPostData({pid:id,work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getIDCityReportlist',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = 'JavaScript:showIDDistrict("'+obj.id+'","'+obj.name+'");';
                }
                addColor(res.data);
                var dataXml = getXMLData(name+' 统计','住址',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
                searchObj = {page:1,work:workType,id_pid:id};
                sendSearch(searchObj);
            }
        });
    }

    function showIDDistrict(id,name){
        sendPostData({pid:id,work:workType},'<?php echo BASE_SITE_URL;?>/admin/report/getIDDistrictReportlist',function(res){
            if(res.code ==1){
                nullClear(res.data);
                for(var i = 0 ;i < res.data.length;i++){
                    var obj = res.data[i];
                    obj.link = '';
                }
                addColor(res.data);
                var dataXml = getXMLData(name+' 统计','住址',res.data);
                chart.setDataXML(dataXml);
                chart.render("chart1");
                searchObj = {page:1,work:workType,id_cid:id};
                sendSearch(searchObj);
            }
        });
    }
    function nullClear(data){
        for(var key in data){
            if(data[key] === null){
                data[key] = '';
            }else if(typeof data[key] === 'undefined' ){
                data[key] = '';
            }else if(typeof data[key] === 'object' || typeof data[key] === 'array'){
                nullClear(data[key]);
            }
        }
    }

    function addColor(data){
        var color = [];
        for(var i = 0 ;i < data.length;i++){
            var c =getColor();
            while(color.indexOf(c)>=0 ){
                c = getColor();
            }
            color.push(c);
            data[i].color = c;
        }
    }

    function getColor(){
        var str = '';
        for(var i = 0 ;i < 6;i++){
            str += '0123456789abcdef'[Math.floor(Math.random()*16)];
        }
        return str;
    }

    function getXMLData(title,subtitle,data){
        var xml = "";
        try{
            xml += "<graph caption='"+title+"' xAxisName='"+subtitle+"' yAxisName='Units' showNames='1' decimalPrecision='3' formatNumberScale='1' unescapeLinks='0'>";
            for(var key in data){
                var v = data[key];
                if(!v['name']){
                    v['name'] = '未知';
                }
                xml +="<set name='"+v['name']+"' value='"+v['value']+"' color='"+v['color']+"' link='"+v['link']+"' />";
            }
            xml+="</graph>";
        }catch (e){
            console.log('xml data error');
            return '';
        }
        return xml;
    }


</script>
</body>
</html>