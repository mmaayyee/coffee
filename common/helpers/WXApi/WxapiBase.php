<?php
namespace common\helpers\WXApi;

use common\helpers\Tools;
use Yii;

class WxapiBase
{
    public $corpid;
    public $secret;

    public function __construct($options = array())
    {
        $this->corpid = Yii::$app->params['corpid'];
        $this->secret = Yii::$app->params['secret'];
    }

    /**
     * 返回错误信息
     * @author  zgw
     * @version 2017-02-24
     * @param   [type]     $res [description]
     * @param   string     $msg [description]
     * @return  [type]          [description]
     */
    protected function returnResult($res, $msg = 'errmsg')
    {
        $res = json_decode($res, true);
        if ($res['errcode'] === 0) {
            return $res[$msg];
        } else {
            return $res['errmsg'];
        }
    }

    /**
     * 获取access_token
     */
    protected function getAccessToken($agentId = 'address_book')
    {
        $accessToken = Yii::$app->cache->get($agentId);
        if (!$accessToken) {
            $secret      = $this->secret[$agentId];
            $res         = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=" . $this->corpid . "&corpsecret=" . $secret);
            $res         = json_decode($res, true);
            $accessToken = $res['access_token'];
            Yii::$app->cache->set($agentId, $accessToken, 7000);
        }
        return $accessToken;
    }

}
