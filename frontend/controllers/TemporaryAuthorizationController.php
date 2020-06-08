<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 16:00
 */
namespace frontend\controllers;

use backend\models\TemporaryAuthorization;
use backend\models\TemporaryAuthorizationSearch;
use backend\models\WxMemberSearch;
use common\models\SendNotice;
use yii;

class TemporaryAuthorizationController extends BaseController
{
    /**
     * 查看蓝牙锁申请临时开门记录
     * @return string|yii\web\Response
     */
    public function actionIndex()
    {
        $state = Yii::$app->request->get();
        if (!empty($state['id'])) {
            $temporaryAuthorization = TemporaryAuthorization::findOne($state['id']);
            if ($temporaryAuthorization->state == 0) {
                $temporaryAuthorization->state      = $state['state'];
                $temporaryAuthorization->audit_time = time();
                $temporaryAuthorization->save();
                $content = $temporaryAuthorization->wx_member_name .'(' . date('Y-m-d H:i:s',time()) .')(' ;
                $content .= $temporaryAuthorization->build_name .")临时开门申请[";
                $content .= $state['state'] == 1 ? "审核通过]" : (($state['state']) == 2 ? "审核未通过" : "请耐心等待") ."]";
                $userUrl = TemporaryAuthorization::findOne($state['id']);
                SendNotice::sendWxNotice($userUrl->userid, '', $content, '');
                return $this->render('index', [
                    'packetArr' => 1,
                ]);
            } else {
                return $this->render('index', [
                    'data' => ['state' => $temporaryAuthorization->state],
                ]);
            }
        } else {
            $userinfo     = $this->userinfo;
            $where        = ['userid' => $userinfo['userid']];
            $wxMemberData = WxMemberSearch::getOne($where);
            $resule       = TemporaryAuthorizationSearch::getJoinAll(['wx.org_id' => $wxMemberData->org_id, 'ta.id' => $state['temporaryAuthorizationId']]);
            if ($resule['state'] == 0) {
                $temporaryAuthorization = TemporaryAuthorization::isApplicationTime(['wx_member_name' => $resule['wx_member_name'], 'state' => 0, 'build_name' => $resule['build_name']]);
                if (!$temporaryAuthorization) {
                    $resule['state'] = 3;
                }
            }

            return $this->render('index', [
                'data' => $resule,
            ]);
        }

    }
}
