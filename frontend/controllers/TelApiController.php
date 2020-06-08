<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/14
 * Time: 15:42
 */
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\Json ;
use common\models\WxMember;
use common\models\Api;

class TelApiController extends Controller
{
    public $enableCsrfValidation = false;
    /*
     * 用户登录接口
     * @author sulingling
     * @param $tel   [手机号]
     * @param $verification   [验证码]
     * @renturn json 1 登录成功【有手机号，验证码正确，没有绑定过】  0 查库 没有的话非法用户 判断手机号或者验证码错误，
     */
    public function actionLogin()
    {
        $data = yii::$app->request->post();
        /**
         * $data['tel']   手机号
         * $data['verification']  验证码
         */
        $cache = yii::$app->cache;
        $verification = $cache->get($data['tel']);
        if ($verification != $data['verification']) {
            return Json::encode(['is_success' => 4,'msg' => '验证码不正确']);
        }
        $wxMemberData = WxMember::getOne(['mobile'=>$data['tel']]);
        if (!$wxMemberData || $wxMemberData->is_del == 2) return json::encode(['is_success' => 3, 'msg' => '非法手机号']);
        if ($wxMemberData->openID) return Json::encode(['is_success' => 5,'msg'=>'该用户已存在']);
        $cache = Yii::$app->cache;
        $openID = $cache->get($data['secretKey']);
        $wxMember = WxMember::findOne(['userid' => $wxMemberData->userid]);
        $wxMember->openID = $openID;
        $result = $wxMember->save();
        return $result ? json_encode(['is_success' => 1,'msg' => '用户注册成功']) : json_encode(['is_success' => 0,'msg' => '用户注册失败']);
    }

    /*
     * 获取手机号验证接口
     * @author  sulingling
     * @param $tel 手机号
     */
     public function actionTel($tel)
     {
         $wxMemberData = WxMember::getOne(['mobile'=>$tel]);

         if ($wxMemberData) {
             $tocken = Api::sendTel($tel);
             $cache = yii::$app->cache;
             $cache->set($tel,$tocken);
             return Json::encode(['is_success' => 1,'msg' => '发送验证成功']);
         } else {
             return json::encode(['is_success' => 3, 'msg' => '非法手机号']);
         }
     }
}