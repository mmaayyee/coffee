<?php
namespace frontend\models;
use Yii;
use frontend\models\CacheSimple;
use yii\helpers\Json;
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

    private function getJsApiTicket() {
        //jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例

        $jsApiTicket = Yii::$app->cache->get('apiTicket');
        if(!empty($jsApiTicket)){
            //判断是否失效1未失效0失效
            $jsApiTicket = Json::decode($jsApiTicket);
            $isInvalid =  time() - $jsApiTicket['update_time'] < $jsApiTicket['effective_time'] ? 1 : 0;
            if($isInvalid){
                return $jsApiTicket['cache_content'];
            }
        }
        $accessToken = $this->getAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $res = json_decode($this->httpGet($url));
        $jsApiTicket = $res->ticket;
        if (!empty($jsApiTicket)) {
            $cacheSimple = CacheSimple::findOne(['cache_key'=>'apiTicket']);
            $cacheSimple = $cacheSimple ? $cacheSimple : new CacheSimple();
            $cacheSimple -> cache_key = 'apiTicket';
            $cacheSimple -> cache_content = $jsApiTicket;
            $cacheSimple -> cache_description = '微信js_api_ticket';
            $cacheSimple -> update_time = time();
            $cacheSimple -> effective_time = 7000;
            $cacheSimple -> save();
            Yii::$app->cache->set('apiTicket',Json::encode($cacheSimple));
        }
        return $jsApiTicket;
    }

    private function getAccessToken() {

        $accessToken = Yii::$app->cache->get('apiToken');
        if(!empty($accessToken)){
            //判断是否失效1未失效0失效
            $accessToken = Json::decode($accessToken);
            $isInvalid =  time() - $accessToken['update_time'] < $accessToken['effective_time'] ? 1 : 0;
            if($isInvalid){
                return $accessToken['cache_content'];
            }
        }
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
        $res = json_decode($this->httpGet($url));
        $accessToken = $res->access_token;
        if (!empty($accessToken)) {
            $cacheSimple = CacheSimple::findOne(['cache_key'=>'apiToken']);
            $cacheSimple = $cacheSimple ? $cacheSimple : new CacheSimple();
            $cacheSimple -> cache_key = 'apiToken';
            $cacheSimple -> cache_content = $accessToken;
            $cacheSimple -> cache_description = '微信access_token';
            $cacheSimple -> update_time = time();
            $cacheSimple -> effective_time = 7000;
            $cacheSimple -> save();
            Yii::$app->cache->set('apiToken',Json::encode($cacheSimple));
        }
        return $accessToken;
    }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
  }
}

