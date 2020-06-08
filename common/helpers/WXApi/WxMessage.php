<?php
/**
 * 发送消息
 */
namespace common\helpers\WXApi;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 成员管理
 */
class WxMessage extends WxapiBase
{
    public function sendMessage($data, $appId)
    {
        if (empty($data['touser'])) {
            @file_put_contents(Yii::$app->basePath . "/web/uploads/logs/wxSendMsg.log", date("Y-m-d H:i:s") . "，发送通知失败，通知内容为：" . Json::encode($data));
            return true;
        }
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $this->getAccessToken($appId), Json::encode($data));
        return $this->returnResult($res);
    }
}
