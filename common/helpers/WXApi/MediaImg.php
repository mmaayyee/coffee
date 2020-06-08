<?php
namespace common\helpers\WXApi;
use common\helpers\Tools;
use Yii;
/**
 * 成员管理
 */
class MediaImg extends WxapiBase
{
    /**
     * 获取素材图片
     */
    public function getMediaImg($mediaId)
    {
        $mediaImg = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=".$this->getAccessToken()."&media_id=".$mediaId);
        return $mediaImg;
    }
}