<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ccb
{
    #region 定义初始值

    private  $pubstr = 'af1bc4474819259747239c9b020111';//公钥
    private  $MERCHANTID = '';        //商户代码
    private  $POSID  = '';            //商户柜台代码
    private  $BRANCHID  = '';         //分行代码
    private  $ORDERID = '';           //订单编号
    private  $PAYMENT = '';           //订单金额

    private  $CURCODE = '';           //币种
    private  $TXCODE = '';            //交易码
    private  $REMARK1 = '';           //备注1
    private  $REMARK2 = '';           //备注2

    private  $TYPE = '';              //接口类型
    private  $GATEWAY = '';           //网关类型
    private  $CLIENTIP = '';          //客户端ip地址

    private  $PUB32TR2 = '';          //公钥后30位
    private  $bankURL = '';           //提交url
    private  $REGINFO = '';           //注册信息
    private  $PROINFO = '';           //商品信息

    private $REFERER = '';            //商户域名

    private  $URL = '';
    private  $tmp = '';
    private  $temp_New = '';
    private  $temp_New1 = '';

    #endregion

    /**
     * 构造函数  封装参数
     * @return  void
     */
    public function __construct()
    {
        $this->MERCHANTID ='105330173990089';
        $this->POSID = '471864905';
        $this->BRANCHID = '330000000';

        $this->CURCODE = '01';

        $this->TXCODE = '520100';
        $this->REMARK1 = '';
        $this->REMARK2 = '1';

        $this->bankURL = 'https://ibsbjstar.ccb.com.cn/app/ccbMain';
        $this->TYPE = 1;
        $this->PUB32TR2 = substr($this->pubstr, -30);

        $this->GATEWAY = 'Z2S1';
        $this->CLIENTIP = '183.128.236.157';
        $this->REGINFO = 'abc小飞侠';

        $this->PROINFO = 'cde充值卡';
        $this->REFERER = '';
    }

    public function setVar($name,$val){

        return $this->$name = $val;

    }

    public function getVar($name){
        return $this->$name;
    }

    /**
     * 生成url
     * @access  public
     * @return  url
     */
    public  function getUrl()
    {
        $this->tmp .='MERCHANTID='.$this->MERCHANTID.'&POSID='.$this->POSID.'&BRANCHID='.$this->BRANCHID.'&ORDERID='.$this->ORDERID.'&PAYMENT='.$this->PAYMENT.'&CURCODE='.$this->CURCODE.'&TXCODE='.$this->TXCODE.'&REMARK1='.$this->REMARK1.'&REMARK2='.$this->REMARK2;
        $this->temp_New .=$this->tmp."&TYPE=".$this->TYPE."&PUB=".$this->PUB32TR2."&GATEWAY=".$this->GATEWAY."&CLIENTIP=".$this->CLIENTIP."&REGINFO=".$this->REGINFO."&PROINFO=".$this->PROINFO."&REFERER=".$this->REFERER;
        $this->temp_New1 .=$this->tmp."&TYPE=".$this->TYPE."&GATEWAY=".$this->GATEWAY."&CLIENTIP=".$this->CLIENTIP."&REGINFO=".$this->REGINFO."&PROINFO=".$this->PROINFO."&REFERER=".$this->REFERER;

        $strMD5 = md5($this->temp_New);
        $this->URL .= $this->bankURL."?".$this->temp_New1."&MAC=".$strMD5;
        //var_dump($this->URL);
        return $this->URL;
    }
/**人的关系到*/
    public function writeLog($order){

        $this->setVar('ORDERID',$order['order_sn']);
        $this->setVar('PAYMENT',$order['total_price']);
        $fp = fopen('/'.$order['order_sn'].'.txt','a');
        if(flock($fp, LOCK_EX))
        {
            fwrite($fp, "提交到建行支付页面时间：\r");
            fwrite($fp,  date('Y-m-d H:i:s'));
            fwrite($fp,"\n");
            fwrite($fp, "传递url参数信息：\n");
            fwrite($fp, $this->getUrl($order['order_sn'], $order['total_price']));
            fwrite($fp, "\n记录支付前数据信息:\n");
            fwrite($fp, "订单号：".$order['order_sn']."\r订单金额：".$order['total_price']);
            fwrite($fp, "\r\n\n\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
}
