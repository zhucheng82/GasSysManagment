<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/3
 * Time: 9:34
 */
class Verity
{
    /**
     * 号码校验： 1 身份证   2 银行卡
     * @param string $number
     * @param int $type
     * @return bool
     */
    public function verity_number($number = '', $type = 1)
    {
        if (!in_array($type, array(1, 2, 3)))
            output_error(-1, '校验类型异常');
        if ($type == 1)
            return $this->check_id_card($number);
        else if ($type == 2)
            return $this->luhm($number);
        else if ($type == 3)
            return $this->checkMobileValidity($number);
    }

    /**
     * 身份证号码校验
     * @param string $id_card
     * @return bool
     */
    protected function check_id_card($id_card = '')
    {
        if (empty($id_card)) {
            return false;
        }
        $city = array(11 => '北京', 12 => '天津', 13 => '河北', 14 => '山西', 15 => '内蒙古', 21 => '辽宁', 22 => '吉林', 23 => '黑龙江', 31 => '上海',
            32 => '江苏', 33 => '浙江', 34 => '安徽', 35 => '福建', 36 => '江西', 37 => '山东', 41 => '河南', 42 => '湖北', 43 => '湖南',
            44 => '广东', 45 => '广西', 46 => '海南', 50 => '重庆', 51 => '四川', 52 => '贵州', 53 => '云南', 54 => '西藏', 61 => '陕西',
            62 => '甘肃', 63 => '青海', 64 => '宁夏', 65 => '新疆', 71 => '台湾', 81 => '香港', 82 => '澳门', 91 => '国外');
        $idCardLength = strlen($id_card);
        /* 身份证长度验证 */
        if ($idCardLength != 18 && $idCardLength != 15) {
            return false;
        }
        /* 验证身份证头 */
        if (!array_key_exists(intval(substr($id_card, 0, 2)), $city)) {
            return false;
        }

        /* 15位身份证验证生日，转换为18位 */
        if ($idCardLength == 15) {
            $sBirthday = '19' . substr($id_card, 6, 2) . '-' . substr($id_card, 8, 2) . '-' . substr($id_card, 10, 2);
            $d = new DateTime($sBirthday);
            $dd = $d->format('Y - m - d');
            if ($sBirthday != $dd) {
                return false;
            }
            $id_card = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);//15to18
            $Bit18 = $this->getVerifyBit($id_card);//算出第18位校验码
            $id_card = $id_card . $Bit18;
        }
        /* 判断是否大于2078年，小于1900年 */
        $year = substr($id_card, 6, 4);
        if ($year > 2078 || $year < 1900) {
            return false;
        }
        /* 18位身份证处理 */
        $sBirthday = substr($id_card, 6, 4) . '-' . substr($id_card, 10, 2) . '-' . substr($id_card, 12, 2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        //var_dump($id_card, $d, $dd);
        if ($sBirthday != $dd) {
            return false;
        }
        /* 身份证编码规范验证 */
        $idcard_base = substr($id_card, 0, 17);
        if (strtoupper(substr($id_card, 17, 1)) != $this->getVerifyBit($idcard_base)) {
            return false;
        }
        return true;
    }

    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     * @param $idcard_base
     * @return bool
     */
    protected function getVerifyBit($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        /* 加权因子 */
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        /* 校验码对应值 */
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /**
     * 银行卡校验规则
     * @param $s
     * @return bool
     */
    protected function luhm($s)
    {
        $n = 0;
        for ($i = strlen($s); $i >= 1; $i--) {
            $index = $i - 1;
            //偶数位
            if ($i % 2 == 0) {
                $n += $s{$index};
            } else {//奇数位
                $t = $s{$index} * 2;
                if ($t > 9) {
                    $t = (int)($t / 10) + $t % 10;
                }
                $n += $t;
            }
        }
        return ($n % 10) == 0;
    }

    /*protected function luhm($s)
    {
        $n = 0;
        for ($i = strlen($s) - 1; $i >= 0; $i--) {
            if ($i % 2) $n += $s{$i};
            else {
                $t = $s{$i} * 2;
                if ($t > 9) $t = $t{0} + $t{1};
                $n += $t;
            }
        }
        return ($n % 10) == 0;
    }*/

    protected function checkMobileValidity($mobilephone)
    {
        $exp = "/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]$/";
        if (preg_match($exp, $mobilephone)) {
            return true;
        } else {
            return false;
        }
    }
}