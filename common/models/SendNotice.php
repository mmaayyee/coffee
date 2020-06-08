<?php
namespace common\models;

use common\helpers\WXApi\WxMessage;
use Yii;

class SendNotice extends \yii\db\ActiveRecord
{
    /**
     * 发送短信通知（微网联接口）
     * @param type $mobile
     * @param type $content
     */
    public static function sendMobileNotice($mobile, $content)
    {
        // $target = "http://cf.51welink.com/submitdata/Service.asmx/g_Submit";
        // $data = "sname=dlldkjkj&spwd=fD2mgxI5&scorpid=&sprdid=1012888&sdst=$mobile&smsg=".rawurlencode("{$content}");
        // $url_info = parse_url($target);
        // $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        // $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        // $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        // $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
        // $httpheader .= "Connection:close\r\n\r\n";
        // //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
        // $httpheader .= $data;

        // $fd = fsockopen($url_info['host'], 80);
        // fwrite($fd, $httpheader);
        // $gets = "";
        // while(!feof($fd)) {
        //     $gets .= fread($fd, 128);
        // }
        // fclose($fd);
        // if($gets != ''){
        //     $start = strpos($gets, '<?xml');
        //     if($start > 0) {
        //         $gets = substr($gets, $start);
        //     }
        // }
        // return $gets;
    }

    /**
     * 邮件发通知
     * @param  [type] $userid [description]
     * @param  [type] $text   [description]
     * @return [type]         [description]
     */
    public static function sendEmailNotice($userid, $text, $title)
    {
        // $mail = Yii::$app->mailer->compose()
        //     ->setTo($userid)
        //     ->setSubject($title)
        //     ->setTextBody($text) //发布纯文字文本
        //     ->send();
        // return $mail;

    }

    /**
     * 发送微信通知
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function sendWxNotice($userid, $url, $text, $appid, $msgtype = 'text')
    {
        // 应用id
        $appid = $appid ? $appid : Yii::$app->params['system_agentid'];

        if ($url) {
            $symbol = strpos($url,'?') > 0 ? '&' : '?';
            $content = "<a href='" . Yii::$app->params['frontend'] . $url . $symbol . "agentId=" .$appid . "'>" . $text . "</a>";
        } else {
            $content = $text;
        }

        // 发送对象
        $data = array(
            'touser'  => $userid,
            'msgtype' => $msgtype,
            'agentid' => $appid,
            'text'    => array('content' => $content),
        );
        $WxMessage = new WxMessage();
        return $WxMessage->sendMessage($data,$appid);
    }
}
