<?php
namespace common\models;

use Yii;
use yii\helpers\Json;

class JSSDK
{
    private $corpid; //企业CorpID
    private $timestamp; //时间戳
    private $nonceStr; //随机字符串
    private $signature; //签名
    private $corpsecret; //企业应用的Secret
    private $url; //请求地址
    private $baseUrl; //当前页面地址

    public function __construct()
    {
        $protocol      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $baseUrl       = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->baseUrl = $baseUrl;
    }

    /**
     * 获取jsapi需要的信息
     * @return  json
     */
    public function getJsSdk()
    {
        $this->timestamp = time();
        $this->nonceStr  = $this->getNonceStr();
        $this->signature = $this->getSignature($this->timestamp, $this->nonceStr, $this->baseUrl);
        $config          = [
            'appId'     => Yii::$app->params['corpid'],
            'timestamp' => $this->timestamp,
            'nonceStr'  => $this->nonceStr,
            'signature' => $this->signature,
        ];
        return json_encode($config);
    }

    /**
     * 获取随机字符串
     * @return  string
     */
    private function getNonceStr()
    {
        $str        = '';
        $randData   = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $dataLength = count($randData);
        for ($i = 0; $i < 16; $i++) {
            $str .= $randData[mt_rand(0, $dataLength - 1)];
        }
        return $str;
    }

    /**
     * 获取签名
     * @author  wangxiwen
     * @date    2018-05-15
     * @param   $timestamp时间戳
     * @param   $nonceStr随机字符串
     * @param   $baseUrl当前页面地址
     * @return  string
     */
    private function getSignature($timestamp, $nonceStr, $baseUrl)
    {
        $ticket = Yii::$app->cache->get('jsApiTicket');
        if (!$ticket) {
            $ticketList = $this->getJsApiTicket();
            $ticket     = $ticketList->access_token;
            Yii::$app->cache->set('jsApiTicket', $ticket, 7000);
        }
        //拼接字符串
        $urlStr = 'jsapi_ticket=' . $ticket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $baseUrl;
        return sha1($urlStr);
    }

    /**
     * 获取js_api_ticket
     * @author  wangxiwen
     * @date 2018-05-15
     * @return  object
     */
    private function getJsApiTicket()
    {
        $this->corpsecret = Yii::$app->params['secret'][Yii::$app->params['distribution_agentid']];
        $this->corpid     = Yii::$app->params['corpid'];
        $this->url        = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=' . $this->corpid . '&corpsecret=' . $this->corpsecret;
        return self::http_get($this->url);
    }

    /**
     * get请求
     * @author  wangxiwen
     * @version 2018-05-15
     * @param string $url 请求路径
     */
    public static function http_get($url)
    {
        $headers = array("Content-Type: text/xml; charset=utf-8");
        $ch      = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $sContent = json_decode(curl_exec($ch));
        curl_close($ch);
        if ($sContent->errmsg == 'ok') {
            return $sContent;
        } else {
            return false;
        }
    }
}
