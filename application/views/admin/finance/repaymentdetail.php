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
        .th25 {
            line-height: 25px;
            width: 300px;
        }

        .w100 {
            width: 120px;
            height: 25px;
            display: inline-block;
        }
    </style>

</head>
<body>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>还款详情</h3>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <br>

    <table class="table tb-type2 nobdb" style="table-layout:fixed;">
        <tbody>
        <tr class="rzxx">
            <td colspan="5"><h5>用户信息</h5></td>
        </tr>
        <tr>

            <td>
                <img style="width: 150px;height: 150px;" src="<?php echo $user['portrait']; ?>"/>
            </td>
            <td>
                <div class="th25">
                    <span class="w100">姓名</span><label><?php echo $user['user_name']; ?></label>
                </div>
                <div class="th25">
                    <span class="w100">电话</span><label><?php echo $user['name']; ?></label>
                </div>
                <div class="th25">
                    <span class="w100">身份证号</span><label><?php echo $user['user_identify_card']; ?></label>

                </div>
                <div class="th25">
                        <span
                            class="w100">职业</span><label><?php echo $user['work'] == 0 ? '学生' : ($user['work'] == 1 ? '上班族' : '未知'); ?></label>
                </div>
                <div class="th25">
                        <span
                            class="w100">单位/院校</span><label><?php echo isset($user['school_name']) ? $user['school_name'] : $user['company_name']; ?></label>

                </div>
                <div class="th25">
                        <span
                            class="w100">职务/学历</span><label><?php echo isset($user['education']) ? $user['education'] : $user['post']; ?></label>

                </div>
                <div class="th25">
                    <span class="w100">入职/入学</span><label><?php echo $user['entry_time']; ?></label>
                </div>
                <div class="th25">
                        <span
                            class="w100">状态</span><label><?php echo  $user['authentication_status'] == 1?'已认证': ($user['authentication_status'] == 2?'审核中':'未认证'); ?></label>
                </div>
            </td>


        </tr>
        <tr class="rzxx">
            <td colspan="5"><h5>产品信息</h5></td>
        </tr>
        <tr>

            <td colspan="5">
                <div class="th25">
                    <span class="w100">贷款编号</span><label><?php echo $order['id']; ?></label>
                </div>
                <div class="th25">
                    <span class="w100">产品类型</span><label><?php echo $order['productType']; ?></label>
                </div>
                <div class="th25">
                    <span class="w100">产品名称</span><label><?php echo $order['product_name']; ?></label>
                </div>
                <div class="th25">
                    <span class="w100">借款金额</span><label><?php echo $order['apply_amount']; ?></label>
                </div>
                <div class="th25">
                    <span class="w100">还款期数</span><label><?php echo $order['credit_cycle']; ?>期</label>
                </div>
                <div class="th25">
                    <span class="w100">放款时间</span><label><?php echo date("Y-m-d H:i:s",$order['approved_time']); ?></label>
                </div>
                <div class="th25">
                    <span class="w100">利率</span><label><?php echo $order['rate']; ?>%</label>
                </div>
                <div class="th25">
                    <span class="w100">补偿金</span><label><?php if($order['compensation_amount']){echo $order['compensation_amount'].'元';} ?></label>
                </div>

            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        </tbody>
    </table>
    <h5>明细</h5>
    <table class="table tb-type2 nobdb" style="table-layout:fixed;">

        <tbody>

        <thead>
        <tr class="thead">
            <th>期数</th>
            <th>还款金额</th>
            <th>截止还款时间</th>
            <th>实际还款时间</th>
            <th>违约金</th>
            <th>状态</th>

            <!--      <th class="align-center">--><?php //echo $lang['nc_handle'];?><!--</th>-->
        </tr>
        </thead>
        <?php foreach ($data as $k => $v) { ?>
            <tr>
                <td>第<?php echo $v['borrowing_period']; ?>期</td>
                <td>￥<?php echo '本金:'.$v['repayment_amount'].'+'.'利息:'.$v['repayment_interest']; ?></td>
                <td style="<?php
                if($v['repayment_status']==0){
                    echo (time()>$v['repayment_deadline'])?'color:red;':'';
                }else if($v['repayment_status']==1){
                    echo ($v['repayment_deadline']<$v['repayment_time'])?'color:red;':'';
                }


                ?>"><?php echo date("Y-m-d H:i:s",$v['repayment_deadline']); ?></td>
                <td style="<?php echo $v['repayment_time']>$v['repayment_deadline']?'color:red;':''; ?>"><?php echo $v['repayment_time']?date("Y-m-d H:i:s",$v['repayment_time']):'--'; ?></td>
                <td><?php echo $v['delay_fine']; ?></td>
                <td><?php echo $v['repayment_status']==1?'已还款':'未还款'; ?></td>

            </tr>
        <?php } ?>
        <tfoot>

        </tfoot>

        </tbody>

    </table>
</div>
<script>


</script>
</body>
</html>
