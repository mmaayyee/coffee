<?php
namespace common\helpers;

class Wechat
{
    // private static $corpId         = 'wx398f0d55f5c0122c';
    // private static $token          = 'coffee08';
    // private static $encodingAesKey = '1JfysUZnq1qum36YwT3PpaaP8x8cjjdgmdutH2ULtDB';
    // private static $secret         = 'tziY-3ptIcQ44OT_Y0lfodoPE-HI0INhKp67lHlQuh9b_wr24o284eBkPw5S0XJR';

    // public function __construct($options = array())
    // {
    //     if ($options) {
    //         self::$secret = $options['secret'];
    //     }
    // }

    // /**
    //  * 验证回调模式url
    //  */
    // public static function verify_url()
    // {
    //     $sVerifyMsgSig = $_GET['msg_signature'];
    //     // $sVerifyTimeStamp = HttpUtils.ParseUrl("timestamp");
    //     $sVerifyTimeStamp = $_GET['timestamp'];
    //     // $sVerifyNonce = HttpUtils.ParseUrl("nonce");
    //     $sVerifyNonce = $_GET['nonce'];
    //     // $sVerifyEchoStr = HttpUtils.ParseUrl("echostr");
    //     $sVerifyEchoStr = $_GET['echostr'];
    //     $wxcpt          = new WXBizMsgCrypt();
    //     $wxcpt->WXBizMsgCrypt(self::$token, self::$encodingAesKey, self::$corpId);
    //     $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
    //     if ($errCode == 0) {
    //         echo $sEchoStr;
    //     } else {
    //         print("ERR: " . $errCode . "\n\n");
    //     }
    // }

    // /**
    //  * 消息内容加密
    //  */
    // public static function EnCryption($sRespData)
    // {

    //     // 需要发送的明文
    //     $sRespData   = "<xml><ToUserName><![CDATA[mycreate]]></ToUserName><FromUserName><![CDATA[wx5823bf96d3bd56c7]]></FromUserName><CreateTime>1348831860</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[this is a test]]></Content><MsgId>1234567890123456</MsgId><AgentID>128</AgentID></xml>";
    //     $sEncryptMsg = ""; //xml格式的密文
    //     $errCode     = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
    //     if ($errCode == 0) {
    //         // TODO:
    //         // 加密成功，企业需要将加密之后的sEncryptMsg返回
    //         // HttpUtils.SetResponce($sEncryptMsg);  //回复加密之后的密文
    //         echo $sEncryptMsg;
    //     } else {
    //         print("ERR: " . $errCode . "\n\n");
    //         // exit(-1);
    //     }
    // }
    // /**
    //  * 消息内容解密
    //  */
    // public static function DeCryption()
    // {
    //     // $sReqMsgSig = HttpUtils.ParseUrl("msg_signature");
    //     $sReqMsgSig = "477715d11cdb4164915debcba66cb864d751f3e6";
    //     // $sReqTimeStamp = HttpUtils.ParseUrl("timestamp");
    //     $sReqTimeStamp = "1409659813";
    //     // $sReqNonce = HttpUtils.ParseUrl("nonce");
    //     $sReqNonce = "1372623149";
    //     // post请求的密文数据
    //     // $sReqData = HttpUtils.PostData();
    //     $sReqData = "<xml><ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt><AgentID><![CDATA[218]]></AgentID></xml>";
    //     $sMsg     = ""; // 解析之后的明文
    //     $errCode  = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
    //     if ($errCode == 0) {
    //         // 解密成功，sMsg即为xml格式的明文
    //         // TODO: 对明文的处理
    //         // For example:
    //         $xml = new DOMDocument();
    //         $xml->loadXML($sMsg);
    //         $content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;
    //         print("content: " . $content . "\n\n");
    //         // ...
    //         // ...
    //     } else {
    //         print("ERR: " . $errCode . "\n\n");
    //         //exit(-1);
    //     }
    // }

    // public static function returnResult($res, $msg = 'errmsg')
    // {
    //     $res = json_decode($res, true);
    //     if ($res['errcode'] === 0) {
    //         return $res[$msg];
    //     } else {
    //         return $res['errmsg'];
    //     }
    // }

    // /**
    //  * 获取access_token
    //  */
    // public static function getAccessToken()
    // {
    //     $accessToken = Yii::$app->cache->get('access-token');
    //     if (!$accessToken) {
    //         $res         = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=" . self::$corpId . "&corpsecret=" . self::$secret);
    //         $res         = json_decode($res, true);
    //         $accessToken = $res['access_token'];
    //         Yii::$app->cache->set('access-token', $accessToken, 100);
    //     }
    //     return $accessToken;
    // }

    // /**
    //  * 生成菜单
    //  */
    // public static function menuAdd($data = array())
    // {
    //     if (empty($data)) {
    //         $data = array(
    //             'button' => array(
    //                 array(
    //                     'name'       => '工作单',
    //                     'sub_button' => array(
    //                         array(
    //                             'type' => 'view',
    //                             'name' => '计划工作',
    //                             'url'  => 'http://erpback.coffee08.com/wechat/work-plan',
    //                         ),
    //                         array(
    //                             'type' => 'view',
    //                             'name' => '工作统计',
    //                             'url'  => 'http://erpback.coffee08.com/wechat/work-statistics',
    //                         ),
    //                         array(
    //                             'type' => 'view',
    //                             'name' => '工作记录',
    //                             'url'  => 'http://erpback.coffee08.com/wechat/work-record',
    //                         ),
    //                     ),
    //                 ),
    //                 array(
    //                     'name'       => '水单',
    //                     'sub_button' => array(
    //                         array(
    //                             'type' => 'view',
    //                             'name' => '提交水单',
    //                             'url'  => 'http://erpback.coffee08.com/wechat/water-submit',
    //                         ),
    //                         array(
    //                             'type' => 'view',
    //                             'name' => '水单记录',
    //                             'url'  => 'http://erpback.coffee08.com/wechat/water-record',
    //                         ),
    //                     ),
    //                 ),
    //                 array(
    //                     'type' => 'view',
    //                     'name' => '考勤记录',
    //                     'url'  => 'http://erpback.coffee08.com/wechat/signin',
    //                 ),
    //             ),
    //         );
    //     }
    //     $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    //     $res  = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=" . self::getAccessToken() . "&agentid=4", $data);
    //     return json_encode($res);
    // }

    // /**
    //  * 添加标签
    //  */
    // public static function tagAdd($tagname)
    // {
    //     $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/create?access_token=" . self::getAccessToken(), json_encode(array('tagname' => $data), JSON_UNESCAPED_UNICODE));
    //     return self::returnResult($res);
    // }
    // /**
    //  * 获取标签
    //  */
    // public static function tagList()
    // {
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/tag/list?access_token=" . self::getAccessToken());
    //     return self::returnResult($res, 'taglist');
    // }

    // /**
    //  * 更新标签
    //  */
    // public static function tagEdit($data)
    // {
    //     $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/update?access_token=" . self::getAccessToken(), json_encode($data, JSON_UNESCAPED_UNICODE));
    //     return self::returnResult($res);
    // }

    // /**
    //  * 删除标签
    //  */
    // public static function tagDel($tagid)
    // {
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/tag/delete?access_token=" . self::getAccessToken() . "&tagid=" . $tagid);
    //     return self::returnResult($res);
    // }

    // /**
    //  * 获取标签用户
    //  */
    // public static function tagUserList($tagid)
    // {
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/tag/get?access_token=" . self::getAccessToken() . "&tagid=" . $tagid);
    //     return self::returnResult($res, 'userlist');
    // }

    // /**
    //  * 获取标签用户
    //  */
    // public static function tagUserAdd($data)
    // {
    //     $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers?access_token=" . self::getAccessToken(), json_encode($data, JSON_UNESCAPED_UNICODE));
    //     return self::returnResult($res);
    // }

    // /**
    //  * 获取标签用户
    //  */
    // public static function tagUserDel($data)
    // {
    //     $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers?access_token=" . self::getAccessToken(), json_encode($data, JSON_UNESCAPED_UNICODE));
    //     return self::returnResult($res);
    // }

    // /**
    //  * 获取部门成员
    //  */
    // public static function partUserSimpleList($data)
    // {
    //     // echo self::getAccessToken();die;
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=?access_token=" . self::getAccessToken() . "&department_id=" . $data['department_id'] . "&fetch_child=" . $data['fetch_child'] . '&status=' . $data['status']);
    //     return self::returnResult($res, 'userlist');
    // }
    // /**
    //  * 获取部门成员详情
    //  */
    // public static function partUserList($data)
    // {
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=?access_token=" . self::getAccessToken() . "&department_id=" . $data['department_id'] . "&fetch_child=" . $data['fetch_child'] . '&status=' . $data['status']);
    //     return self::returnResult($res, 'userlist');
    // }

    // /**
    //  * 获取用户详情
    //  */
    // public static function userDetail($userid)
    // {
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=" . self::getAccessToken() . "&userid=" . $userid);
    //     return self::returnResult($res);
    // }

    // /**
    //  * 获取标签用户
    //  */
    // public static function userDel($data)
    // {
    //     $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete?access_token=" . self::getAccessToken(), json_encode($data, JSON_UNESCAPED_UNICODE));
    //     return self::returnResult($res);
    // }

    // /**
    //  * 获取部门列表
    //  */
    // public static function departmentList($id = '')
    // {
    //     $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=" . self::getAccessToken() . "&id=" . $id);
    //     return self::returnResult($res, 'department');
    // }

}
