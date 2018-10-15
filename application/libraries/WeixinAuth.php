<?php


class WeixinAuth
{
    public $apiurl = 'https://api.weixin.qq.com';
    public $appid ;
    public $secret ;
    //public $token = PayConfig::WapWxTOKEN;
    public $accessToken;
    public $postStr;
    public $postObj;
    public $fromUsername;
    public $toUsername;
    public $msgType;

    public $isTest = 0;
    public $test_authorize_url = '/test_box/auth.html';
    public $test_sandbox_result = '{"access_token":1,"openid":"wx_openid$randid$","nickname":3,"sex":4,"city":"hz","province":"zj","country":"cn","unionid":1111,"headimgurl":"headimgurl1.jpg"}';

    function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('curl');
        $this->appid = getWxappIDByType();
        $this->secret = getWxAPPSECRETByType();
    }

    function tgcode($scene_id)
    {
        $json = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . $scene_id . '}}}';
        $url = $this->apiurl . '/cgi-bin/qrcode/create?access_token=' . $this->get_access_token();
        return $this->ci->curl->simple_post($url, $json);
    }

    public function token()
    {
        //file_put_contents('../data/log/wx.log', date('Y-m-d H:i:s').'=>: '.$GLOBALS["HTTP_RAW_POST_DATA"]."\r\n\r\n",FILE_APPEND);
        if ($this->checkSignature()) {
            if (isset($_GET["echostr"])) {
                echo $_GET["echostr"];
                exit;
            }
        }

        $this->responseMsg();
    }

    function send_text($openid, $content)
    {
        $json = '{
                    "touser":"' . $openid . '",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"' . $content . '"
                    }
                }';
        $url = $this->apiurl . '/cgi-bin/message/custom/send?access_token=' . $this->get_access_token();
        return $this->ci->curl->simple_post($url, $json);
    }

    private function _response_text($object, $content)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";

        return sprintf($textTpl, $object->FromUserName, $object->ToUserName, $this->timestamp, $content);
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    function get_access_token()
    {
        $accessTokenTime = rkcache('accessTokenTime');
        if (empty($accessTokenTime))
            $accessTokenTime = 0;
        if (!$this->accessToken = rkcache('accessToken') || time() - $accessTokenTime > 3 * 60) {
            $result = $this->ci->curl->simple_get($this->apiurl . "/cgi-bin/token", array(
                "grant_type" => "client_credential",
                "appid" => $this->appid,
                "secret" => $this->secret,
            ));
            $jsonObj = json_decode($result);
            $this->accessToken = $jsonObj->access_token;
            wkcache('accessTokenTime', time());
            wkcache('accessToken', $this->accessToken);
        }
        return $this->accessToken;
    }

    public function authz($redirect_uri)
    {
        $this->appid = PayConfig::WapWxAPPID;
        $this->secret = PayConfig::WapWxAPPSECRET;

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appid . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        if ($this->isTest == 1)
            $url = $this->test_authorize_url;

        header('location:' . $url);
        exit;
    }

    function init_auth($code, $url = '', $invite_id = 0, $client_type = 'app')
    {
        if ($client_type == 'app') {
            $this->appid = getWxappIDByType();
            $this->secret = getWxAPPSECRETByType();
        } else {
            $this->appid = PayConfig::WapWxAPPID;
            $this->secret = PayConfig::WapWxAPPSECRET;
        }
        $result = '';
        if ($this->isTest == 1)
            $result = $this->test_sandbox_result;
        else {
            $result = $this->ci->curl->simple_get($this->apiurl . "/sns/oauth2/access_token", array(
                "grant_type" => "authorization_code",
                "appid" => $this->appid,
                "secret" => $this->secret,
                'code' => $code
            ));

            //file_put_contents('../data/log/wx.log', date('Y-m-d H:i:s').'=>: '.$result."\r\n\r\n",FILE_APPEND);
        }
        $jsonObj = json_decode($result);
        if (!empty($jsonObj->access_token)) {
            if ($this->isTest == 1)
                $result = str_replace('$randid$', rand(), $this->test_sandbox_result);
            else
                $result = $this->ci->curl->simple_get($this->apiurl . "/sns/userinfo", array(
                    "access_token" => $jsonObj->access_token,
                    "openid" => $jsonObj->openid,
                    'lang' => 'zh_CN'
                ));

            $jsonObj = json_decode($result);
            if ($jsonObj->openid) {
                if (!$jsonObj->nickname) {
                    $jsonObj->nickname = '匿名';
                }
                $headimgurl = $jsonObj->headimgurl;
                if (substr($headimgurl, -1) === '0') {
                    $headimgurl = substr($headimgurl, 0, -2);
                }
                $headimgurl = $headimgurl . '/96';
                $map = array(
                    'openid' => $jsonObj->openid,
                    'unionid' => $jsonObj->unionid,
                    'nickname' => $jsonObj->nickname,
                    'sex' => $jsonObj->sex,
                    'city' => $jsonObj->city,
                    'province' => $jsonObj->province,
                    'country' => $jsonObj->country,
                    'head_url' => $headimgurl,
                    'addtime' => time(),
                );
                $userAuth_model = M('user_auth');
                //$ticket_invite_model = M('invite_ticket');
                $uinfo = $userAuth_model->get_by_where(array('unionid' => "'" . $jsonObj->unionid . "'"), 'id,user_id');
                $userid = 0;
                if (empty($uinfo)) {
                    $id = $userAuth_model->insert($map);
                    $uinfo = array('id' => $id);
                } else {

                    $id = $uinfo['id'];
                    $userid = $uinfo['user_id'];
                    $userAuth_model->update_by_where(array('unionid' => "" . $jsonObj->unionid), array('openid' => $jsonObj->openid, 'updatetime' => time()));
                }
                $arrData = array('openid' => $jsonObj->openid, 'unionid' => $jsonObj->unionid, 'userid' => $userid, 'sex' => $jsonObj->sex,
                    'nickname' => $jsonObj->nickname, 'head_url' => $headimgurl, 'invite_id' => $invite_id);
                //新用户-绑定分佣关系
                if (empty($userid) && !empty($invite_id)) {
                    $userAuth_model->update_by_where(array('unionid' => "" . $jsonObj->unionid), array('invite_id' => $invite_id, 'updatetime' => time()));
                }


                if ($uinfo) {
                    if (empty($url))
                        $url = '/';
                    header('location:/api/jump?url=' . $url . '&' . http_build_query($arrData) . '&' . time());
                    exit;
                } else {
                    header('location:/api/jump?url=none');
                    exit;
                }


            } else {
                echo $result;
                exit;
            }
        } else {
            echo $result;
            exit;
        }
    }


    function new_init_auth($code, $url = '', $invite_id = 0, $client_type = 'app')
    {
        if ($client_type == 'app') {
            $this->appid = getWxappIDByType();
            $this->secret = getWxAPPSECRETByType();
        } else {
            $this->appid = PayConfig::WapWxAPPID;
            $this->secret = PayConfig::WapWxAPPSECRET;
        }
        $result = '';
        if ($this->isTest == 1)
            $result = $this->test_sandbox_result;
        else {
            $result = $this->ci->curl->simple_get($this->apiurl . "/sns/oauth2/access_token", array(
                "grant_type" => "authorization_code",
                "appid" => $this->appid,
                "secret" => $this->secret,
                'code' => $code
            ));

            //file_put_contents('../data/log/wx.log', date('Y-m-d H:i:s').'=>: '.$result."\r\n\r\n",FILE_APPEND);
        }
        $jsonObj = json_decode($result);
        if (!empty($jsonObj->access_token)) {
            if ($this->isTest == 1) {
                $result = str_replace('$randid$', rand(), $this->test_sandbox_result);
            } else {
                $result = $this->ci->curl->simple_get($this->apiurl . "/sns/userinfo", array(
                    "access_token" => $jsonObj->access_token,
                    "openid" => $jsonObj->openid,
                    'lang' => 'zh_CN'
                ));
            }
            $userAuth_model = M('user_auth');
            $uinfo = $userAuth_model->get_by_where(array('openid' => "'.$jsonObj->openid.'", 'auth_type' => 4));
            if (!empty($uinfo)) {
                $return['user_id'] = $uinfo['user_id'];
                if (empty($url))
                    $return['url'] = WAP_SITE_URL . '/page/index.html';
                else
                    $return['url'] = $url;
            } else {
                $return['url'] = WAP_SITE_URL . "/login/signup.html?signtype=2&auth_param=" . $result . "&invite_id=" . $invite_id;
            }
        } else {
            echo $result;
            exit;
        }
    }


    function sync_user_info($openid)
    {
        $result = $this->ci->curl->simple_get($this->apiurl . "/cgi-bin/user/info", array(
            "access_token" => $this->get_access_token(),
            'openid' => $openid,
            "lang" => 'zh_CN',
        ));
        if (!$result) return;

        $jsonObj = json_decode($result);
        if (!$jsonObj->openid && !$jsonObj->nickname) return;
        $headimgurl = $jsonObj->headimgurl;
        if (substr($headimgurl, -1) === '0') {
            $headimgurl = substr($headimgurl, 0, -2);
        }
        $map = array(
            'subscribe' => $jsonObj->subscribe,
            'openid' => $jsonObj->openid,
            'nickname' => $jsonObj->nickname,
            'sex' => $jsonObj->sex,
            'city' => $jsonObj->city,
            'province' => $jsonObj->province,
            'country' => $jsonObj->country,
            'headimgurl' => $headimgurl,
            'subscribe_time' => $jsonObj->subscribe_time,
            'unionid' => $jsonObj->unionid,
        );
        D('user')->insert_ignore_update($map, 'openid');
    }

    public function responseMsg()
    {
        $this->postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $this->postObj = simplexml_load_string($this->postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->fromUsername = $this->postObj->FromUserName;
        $this->toUsername = $this->postObj->ToUserName;
        $this->msgType = $this->postObj->MsgType;
        switch ($this->msgType) {
            case 'event':
                $this->eventAction();
                break;
            case 'text':
                $this->textAction();
                break;
        }


    }


    private function eventAction()
    {
        switch ($this->postObj->Event) {
            case 'LOCATION':
                $lat = $this->postObj->Latitude;
                $lng = $this->postObj->Longitude;
                $pre = $this->postObj->Precision;
                break;
            case 'scan':
            case "subscribe":
                if ($this->postObj->Ticket) {
                    $ticket_invite_model = Model('ticket_invite');
                    $arrInfo = $ticket_invite_model->getInfo(array('openid' => $this->postObj->FromUserName), 'invite_id,id');
                    if (empty($arrInfo)) {
                        $map = array(
                            'ticket' => (string)$this->postObj->Ticket,
                            'invite_id' => intval(substr($this->postObj->EventKey, 8)),
                            'openid' => (string)$this->postObj->FromUserName,
                            'updated' => date('Y-m-d H:i:s'),
                        );
                        $ticket_invite_model->insert($map);
                    }
                    // else
                    // {
                    // 	$map = array(
                    // 			'ticket'=>$Ticket[0],
                    // 			'invite_id'=>intval(substr($this->postObj->EventKey,8)),
                    // 			'openid'=>$FromUserName[0],
                    // 			'updated'=>date('Y-m-d H:i:s'),
                    // 		);
                    // 	$ticket_invite_model->update($map,array('id'=>$arrInfo['id']));
                    // }
                }
                $this->send_text($this->postObj->FromUserName, '欢迎来茶叶商城');
                break;
            case "SCAN":
                if ($this->postObj->Ticket) {
                    $openid = (string)$this->postObj->FromUserName;
                    $invite_id = intval($this->postObj->EventKey);
                    $ticket_invite_model = Model('ticket_invite');
                    $arrInfo = $ticket_invite_model->getInfo(array('openid' => $this->postObj->FromUserName), 'invite_id,id');
                    if (empty($arrInfo)) {
                        $map = array(
                            'ticket' => (string)$this->postObj->Ticket,
                            'invite_id' => $invite_id,
                            'openid' => $openid,
                            //'updated'=>date('Y-m-d H:i:s'),
                        );

                        $ticket_invite_model->insert($map);
                    }

                    // $map = array(
                    // 			'ticket'=>(string)$this->postObj->Ticket,
                    // 			'invite_id'=>$invite_id,
                    // 			'openid'=>$openid.'--'.$invite_id.'--',
                    // 			//'updated'=>date('Y-m-d H:i:s'),
                    // 		);

                    // 	$ticket_invite_model->insert($map);

                }
                break;
        }
    }

    private function textAction()
    {
        $this->send_text($this->postObj->FromUserName, site_setting('WXAUTO_TEXT'));
    }

} 