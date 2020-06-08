<?php
/**
 * 验证应用回调url、以及消息加解密
 */
namespace common\helpers\WXApi;

use common\helpers\WXEnDeCrypt\WXBizMsgCrypt;
use Yii;

class EnDeCrypt
{
    /**
     * 验证回调模式url
     */
    public static function verify_url()
    {
        $sVerifyMsgSig    = $_GET['msg_signature'];
        $sVerifyTimeStamp = $_GET['timestamp'];
        $sVerifyNonce     = $_GET['nonce'];
        $wxcpt            = new WXBizMsgCrypt();
        if (empty($_GET['echostr'])) {
            $sReqData = Yii::$app->request->post();
            $sMsg     = ""; // 解析之后的明文
            $errCode  = $wxcpt->DecryptMsg($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sReqData, $sMsg);
            if ($errCode == 0) {
                // 解密成功，sMsg即为xml格式的明文
                print_r($sMsg);
                file_put_contents(Yii::$app->basePath . '/web/uploads/tmp.log', \yii\helpers\Json::encode($sMsg) . "\n\n", FILE_APPEND);
            } else {
                print("ERR: " . $errCode . "\n\n");
            }

        } else {
            $sVerifyEchoStr = $_GET['echostr'];
            $wxcpt->WXBizMsgCrypt(Yii::$app->params['token'], Yii::$app->params['encodingAesKey'], Yii::$app->params['corpid']);
            $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {
                echo $sEchoStr;
            } else {
                print("ERR: " . $errCode . "\n\n");
            }
        }
    }
}
