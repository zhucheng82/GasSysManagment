<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>big city</title>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js', 'js'); ?>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'perfect-scrollbar.min.css', 'css'); ?>
    <?php echo _get_html_cssjs('lib', 'uploadify/uploadify.css', 'css'); ?>

    <?php echo _get_html_cssjs('admin', TPL_ADMIN_NAME . 'css/font-awesome.min.css', 'css'); ?>

    <!--[if IE 7]>
    <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
    <![endif]-->
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
    <style>
        td {
            width: 300px;
        }

        .th25 {
            line-height: 25px;
            width: 300px;
        }

        .w100 {
            width: 120px;
            height: 25px;
            display: inline-block;
        }

        .address {
            display: inline-block;
            width: 300px;
            vertical-align: top;
        }

    </style>

</head>
<body>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>用户详情</h3>
        </div>
    </div>
    <div class="fixed-empty"></div>
        <table class="table tb-type2 nobdb" style="table-layout:fixed;">
            <tbody>
            <tr class="rzxx">
                <td colspan="5"><h5>基本信息</h5></td>
            </tr>
            <tr>

                <td>
                    <img style="width: 150px;height: 150px;" src="<?php if ($portrait) {
                        echo $portrait;
                    }; ?>"/>
                </td>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label><?php echo $user_name; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">电话</span><label><?php echo $name; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">身份证号</span><label><?php echo $user_identify_card; ?></label>

                    </div>

                    <div class="th25">
                        <span
                            class="w100">单位/院校</span><label><?php echo isset($school_name) ? $school_name : $company_name; ?></label>

                    </div>
                    <div class="th25">
                        <span
                            class="w100">职务/学历</span><label><?php echo isset($education) ? $education : $post; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">入职/入学</span><label><?php echo $entry_time; ?></label>
                    </div>
                    <div class="th25">
                        <span
                            class="w100">状态</span><label><?php echo $authentication_status == 1?'已认证':($authentication_status == 2?'审核中':'未认证'); ?></label>
                    </div>
                    <div class="th25">
                        <?php if($authentication_status == 1){?>
                            <a href="JavaScript:void(0);" class="btn" id="cancelCheck" oid="193"><span>取消认证</span></a>
                        <?php }else if($authentication_status == 2){?>
                            <a href="JavaScript:void(0);" class="btn" id="faildCheck" oid="193"><span>认证失败</span></a>
                            <a href="JavaScript:void(0);" class="btn" id="passCheck" oid="193"><span>通过认证</span></a>
                        <?php }else{

                        }?>
                    </div>
                </td>

            </tr>


            </tr>
            <?php if ($additional_info_status == 1) { ?>
                <tr>
                    <td colspan="5"><h5>完善资料</h5></td>
                </tr>
                <tr>
                    <td>
                        <div class="th25">
                        <span
                            class="w100">父亲姓名/电话</span><label><?php echo $linkman_name1 . '  ' . $linkman_tel1; ?></label>
                        </div>
                        <div class="th25">
                        <span
                            class="w100">母亲姓名/电话</span><label><?php echo $linkman_name2 . '  ' . $linkman_tel2; ?></label>
                        </div>
                        <div class="th25">
                        <span
                            class="w100">同事/同学联系方式</span><label><?php echo $linkman_name3 . '  ' . $linkman_tel3; ?></label>
                        </div>
                        <div class="th25">
                        <span
                            class="w100">朋友联系方式</span><label><?php echo $linkman_name4 . '  ' . $linkman_tel4; ?></label>
                        </div>
                        <div class="th25">
                        <span
                            class="w100">芝麻信用</span><label><?php echo $zhima; ?></label>
                        </div>
                        <div class="th25">
                        <span class="w100">身份证地址</span>
                            <div class="address"><?php echo $province_name . $city_name . $district_name . $address; ?></div>
                        </div>
                        <div class="th25">
                            <span class="w100">当前住址</span>
                            <div class="address"><?php echo $cur_province_name . $cur_city_name . $cur_district_name . $cur_address; ?></div>
                        </div>

                        <!--                        <div class="th25">-->
                        <!--                            <span class="w100">录取证明/社保证明</span>-->
                        <!--                        <span class="type-file-show" style="margin-right: 150px;"><img class="show_image"-->
                        <!--                                                                                       src="-->
                        <?php //echo _get_cfg_path('admin_images'); ?><!--preview.png">-->
                        <!--                        <div class="type-file-preview"><img id="preview_picurl" src="-->
                        <?php //echo BASE_SITE_URL.'/'.$photo_url1; ?><!--"-->
                        <!--                                                            onload="javascript:DrawImage(this,500,500);"></div>-->
                        <!--                        </span>-->
                        <!--                        </div>-->
                        <!--                        <div class="th25">-->
                        <!--                            <span class="w100">入学证明/工作证明</span>-->
                        <!--                        <span class="type-file-show" style="margin-right: 150px;"><img class="show_image"-->
                        <!--                                                                                       src="-->
                        <?php //echo _get_cfg_path('admin_images'); ?><!--preview.png">-->
                        <!--                        <div class="type-file-preview"><img id="preview_picurl" src="-->
                        <?php //echo BASE_SITE_URL.'/'.$photo_url2; ?><!--"-->
                        <!--                                                            onload="javascript:DrawImage(this,500,500);"></div>-->
                        <!--                        </span>-->
                        <!--                        </div>-->
                        <!--                        <div class="th25">-->
                        <!--                            <span class="w100">身份证正面照</span>-->
                        <!--                        <span class="type-file-show" style="margin-right: 150px;"><img class="show_image"-->
                        <!--                                                                                       src="-->
                        <?php //echo _get_cfg_path('admin_images'); ?><!--preview.png">-->
                        <!--                        <div class="type-file-preview"><img id="preview_picurl" src="-->
                        <?php //echo BASE_SITE_URL.'/'.$id_photo_url1; ?><!--"-->
                        <!--                                                            onload="javascript:DrawImage(this,500,500);"></div>-->
                        <!--                        </span>-->
                        <!---->
                        <!--                        </div>-->
                        <!--                        <div class="th25">-->
                        <!--                            <span class="w100">身份证反面照</span>-->
                        <!--                        <span class="type-file-show" style="margin-right: 150px;"><img class="show_image"-->
                        <!--                                                                                      src="-->
                        <?php //echo _get_cfg_path('admin_images'); ?><!--preview.png">-->
                        <!--                        <div class="type-file-preview"><img id="preview_picurl" src="-->
                        <?php //echo BASE_SITE_URL.'/'.$id_photo_url2; ?><!--"-->
                        <!--                                                            onload="javascript:DrawImage(this,500,500);"></div>-->
                        <!--                        </span>-->
                        <!--                        </div>-->
                        <!--                        <div class="th25">-->
                        <!--                            <span class="w100">持证照</span>-->
                        <!--                        <span class="type-file-show" style="margin-right: 150px;"><img class="show_image"-->
                        <!--                                                                                      src="-->
                        <?php //echo _get_cfg_path('admin_images'); ?><!--preview.png">-->
                        <!--                        <div class="type-file-preview"><img id="preview_picurl" src="-->
                        <?php //echo BASE_SITE_URL.'/'.$id_photo_url3; ?><!--"-->
                        <!--                                                            onload="javascript:DrawImage(this,500,500);"></div>-->
                        <!--                        </span>-->
                        <!--                        </div>-->

                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="th25">
                            <span class="w100">录取证明/社保证明</span>
                        </div>

                    </td>
                    <td><img id="preview_picurl" src="<?php echo BASE_SITE_URL . '/' . $photo_url1; ?>"
                             onload="javascript:DrawImage(this,500,500);"></td>
                </tr>
                <tr>
                    <td>
                        <div class="th25">
                            <span class="w100">入学证明/工作证明</span>
                        </div>

                    </td>
                    <td><img id="preview_picurl" src="<?php echo BASE_SITE_URL . '/' . $photo_url2; ?>"
                             onload="javascript:DrawImage(this,500,500);"></td>
                </tr>

                <tr>
                    <td>
                        <div class="th25">
                            <span class="w100">身份证正面照</span>
                        </div>

                    </td>
                    <td><img id="preview_picurl" src="<?php echo BASE_SITE_URL . '/' . $id_photo_url1; ?>"
                             onload="javascript:DrawImage(this,500,500);"></td>
                </tr>


                <tr>
                    <td>
                        <div class="th25">
                            <span class="w100">身份证反面照</span>
                        </div>

                    </td>
                    <td><img id="preview_picurl" src="<?php echo BASE_SITE_URL . '/' . $id_photo_url2; ?>"
                             onload="javascript:DrawImage(this,500,500);"></td>
                </tr>

                <tr>
                    <td>
                        <div class="th25">
                            <span class="w100">持证照</span>
                        </div>

                    </td>
                    <td><img id="preview_picurl" src="<?php echo BASE_SITE_URL . '/' . $id_photo_url3; ?>"
                             onload="javascript:DrawImage(this,500,500);"></td>
                </tr>


            <?php } else { ?>
                <tr>
                    <td colspan="5"><h5 style="color: red;">资料未完善</h5></td>
                </tr>
            <?php } ?>
            <?php if ($blist && count($blist) > 0) { ?>
                <tr>
                    <td colspan="5"><h5>贷款记录</h5></td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div class="th25 w100">类型</div>
                        <div class="th25 w100">金额</div>
                        <div class="th25 w100">期数</div>
                        <div class="th25 w100">申请时间</div>
                        <div class="th25 w100">状态</div>
                        <div class="th25 w100"></div>

                    </td>

                </tr>
                <?php foreach ($blist as $k => $v) { ?>
                    <tr>
                        <td colspan="5">
                            <div class="th25 w100"><?php echo $v['product_name']; ?></div>
                            <div class="th25 w100">￥<?php echo $v['approve_amount']; ?></div>
                            <div class="th25 w100"><?php echo $v['credit_cycle']; ?></div>
                            <div class="th25 w100"><?php echo date('Y-m-d H:i:s', $v['applied_time']); ?></div>
                            <div class="th25 w100"><?php echo $v['status']; ?></div>
                            <div class="th25 w100"><a
                                    href="<?php echo ADMIN_SITE_URL . '/finance/details?id=' . $v['id'] . '&work=' . $work ?>">查看</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>

            <?php } else { ?>
                <tr>
                    <td colspan="5"><h4>没有贷款记录!</h4></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5"></td>
            </tr>
            </tfoot>

            </tbody>

        </table>
</div>
<div id="sure_div" style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 99; text-align: center; background: rgba(0, 0, 0, 0.6) !important; display: none;">
    <div style="width: 25%;left: 35%;top: 20%;position: absolute;background: #fff">
        <table class="table tb-type2 tb-type1" style="width: 100%;height: 100%">
            <tbody>
            <tr class="space">
                <th colspan="3" id="tips">提示</th>
            </tr>
            <tr class="space">
                <td colspan="3" id="tipsContent">

                </td>
            </tr>
            <tr class="tfoot" style="background: rgb(255, 255, 255);">
                <td colspan="3"><a class="btn" id="submitBtn"><span>确定</span></a><a class="btn" id="cancelBtn"><span>取消</span></a></td>
            </tr>
            </tbody></table>
    </div>
</div>
<script>
    $(function(){
        var status=1;
        var user_id = getUrlParam('id');
        $('#cancelCheck').click(function(){
            $('#sure_div').show();
            status = 0;
            $("#tipsContent").text('确定要取消这个用户的认证吗?');
        })

        $('#passCheck').click(function(){
            $('#sure_div').show();
            status = 1;
            $("#tipsContent").text('确定要通过这个用户的认证吗?');
        })
        $('#faildCheck').click(function(){
            $('#sure_div').show();
            status = 0;
            $("#tipsContent").text('确定这个用户认证不通过吗?');
        })

        $('#submitBtn').click(function(){
            $('#sure_div').hide();
            sendPostData({status:status,user_id:user_id},'<?php echo BASE_SITE_URL?>/admin/user/resetAuthenticationStatus',function(res){
                if(res.code == 1){
                    location.reload();
                }
            })
        });

        $('#cancelBtn').click(function(){
            $('#sure_div').hide();
        });
    })

</script>
</body>
</html>
