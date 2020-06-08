<?php
namespace common\helpers\WXApi;

use common\helpers\Tools;
use Yii;

class Auth extends WxapiBase
{

    public function getCode()
    {
        $backurl = Yii::$app->request->hostinfo . Yii::$app->request->getUrl();
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->corpid . "&redirect_uri=" . urlencode($backurl) . "&response_type=code&scope=snsapi_base#wechat_redirect";
        header("Location: " . $url);
    }

    public function getUserInfo($code,$agentID)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=" . $this->getAccessToken($agentID) . "&code=" . $code);
        return json_decode($res, true);
    }
}
