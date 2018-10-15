<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo lang('login_index_need_login'); ?></title>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'seller_center.css', 'css'); ?>
    <?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,common.js,jquery.tscookie.js,jquery.validation.min.js,jquery.supersized.min.js', 'js'); ?>
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
    <style type="text/css">
        a{
            cursor: pointer;
        }

    </style>
</head>
<body>

<div class="item-publish" id="info_view">
    <form method="post" id="newpro_form" >
        <div class="ncsc-form-goods">
            <h3>企业性质设置</h3>
            <dl id="cycle_days">
                <dt>企业性质</dt>
                <dd class="rowform">
                    <input type="text" name="nature"  id="nature" maxlength="30" value=""/>
                </dd>
            </dl>
            <dl id="cycle_days">
                <dt>排序</dt>
                <dd class="rowform">
                    <input type="text" name="sort"  id="sort" maxlength="10" value=""/>
                </dd>
            </dl>
        </div>
        <div class="bottom tc hr32">
            <label class="submit-border">
                <input type="submit" class="submit" id="submit"
                       value="添加"/>
<!--                <input type="submit" class="submit" id="submit"-->
<!--                       value="修改"/>-->
            </label>
        </div>
    </form>
</div>

<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th>编号</th>
        <th>企业性质</th>
        <th>排序</th>
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
        $('#sort').on('keyup change cut paste',function(){
            var str = $(this).val();
            $(this).val(str.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g, ''))
        })
        sendSearch({page:1});
        function sendSearch(obj){
            sendPostData(obj, '<?php echo BASE_SITE_URL?>/admin/operation/natureList', function (res) {
                $('#list_content').html('');
                $('.pagination').html('');
                if (res.code == 1) {
                    showList(res.data);
                }
            });
        }

        $('#list_content').on('click','.delnature',function(){
            var id = $(this).attr('nid');
            sendPostData({id:id}, '<?php echo BASE_SITE_URL?>/admin/operation/deleteNature', function (res) {
                if(res.code == 1){
                    showTips('删除成功');
                    sendSearch(searchObj);
                }
            });
        })

        var searchObj ;
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
                        '<td><span>'+obj.company_nature+'</span></td>'+
                        '<td><span >'+(obj.sort)+'</span></td>'+
                        '<td><span><a class="delnature" nid="'+obj.id+'">删除</a>';
                    str+='</tr>';
                    $('#list_content').append(str);

                }
                $('.pagination').html(data.pages);
                $('.pagination').find('a').removeAttr('href');

            } else {

                $('#list_content').html('<tr class="no_data"><td colspan="10">没有数据了</td></tr>');
            }
        }

        $('#newpro_form').validate({
            rules: {
                nature:{
                    required:true,
                    minlength:2,
                    maxlength:30
                },
                sort:{
                    digits:true
                }
            },
            messages: {
                nature:{
                    required:'需要填写企业性质',
                    maxlength:'不能超过30个字',
                    minlength:'至少两个字'
                },
                sort:{
                    digits:'必须是整数'
                }
            },
            submitHandler: function () {

                var data = $('#newpro_form').serialize();
                sendPostData(data, '/admin/operation/addNature', function (res) {
                    if(res.code == 1){
                        showTips('添加成功')
                        sendSearch(searchObj);
                        $('#nature').val('');
                        $('#sort').val('');
                    }

                })
                return false;
            }
        });


    })
</script>


</body>
</html>
