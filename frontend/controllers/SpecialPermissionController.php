<?php

namespace frontend\controllers;

use backend\models\EquipRfidCard;
use backend\models\EquipRfidCardRecord;
use backend\models\Organization;
use common\models\ChangePasswordFrontend;
use Yii;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class SpecialPermissionController extends BaseController
{
    /**
     * 特殊开门返回卡密码主页
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new EquipRfidCard();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * 修改手机页特殊开门提示信息
     * @author  zmy
     * @version 2016-12-22
     * @return  [type]     [description]
     */
    public function actionCreate()
    {
        $param = Yii::$app->request->get("EquipRfidCard");
        $model = new EquipRfidCard();
        $error = '';
        //获取当前登录的用户（微信）
        if (!$param['verificateCode']) {
            $error = "对不起，请输入验证码";
        }
        // 测试环境使用
        $textSign = (isset($param['text_sign']) && $param['text_sign']) ? $param['text_sign'] : "";

        $retPassStrArr = EquipRfidCard::getSpecialPermissionPass($param['verificateCode'], $this->userinfo['userid'], $param['equipId'], $textSign);
        $orgName       = isset(Organization::getOrgName("*", ['org_id' => $retPassStrArr['orgId']])->org_name) ? Organization::getOrgName("*", ['org_id' => $retPassStrArr['orgId']])->org_name : "当前分公司";
        if (!$retPassStrArr) {
            // 此用户未在后台绑定
            $error = '对不起，此用户或设备未在后台绑定';
        } else if ($retPassStrArr == 9) {
            $error = '对不起，此用户未绑定本台设备';
        } else if ($retPassStrArr == 10) {
            $error = '对不起，此设备不在' . $orgName . '下';
        } else if ($retPassStrArr == 11) {
            $error = '对不起，设备编号输入有误';
        }

        if (!$param['equipId']) {
            $error = '对不起，请输入设备编号';
        }
        $passStr               = $retPassStrArr["md5Str"];
        $equipRfidCardArr      = EquipRfidCard::getRfidAssocEquipCodeArr($this->userinfo['userid']);
        $model->verificateCode = $param['verificateCode'];
        $model->equipId        = $param['equipId'];
        $model->org_id         = $retPassStrArr["orgId"];
        if (!$error) {
            //添加到后台开门记录中
            $retSaveRfidRecord = EquipRfidCardRecord::saveRfidData($equipRfidCardArr["rfidCardObj"], $param['equipId'], 1, 2);
            if (!$retSaveRfidRecord) {
                Yii::$app->getSession()->setFlash('error', '对不起，开门记录添加失败');
                return $this->render('index', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('index', [
            'error'    => $error ? $error : '',
            'passStr'  => $passStr,
            'equipArr' => $equipRfidCardArr["equipArr"],
            'model'    => $model,
        ]);
    }

    /**
     * 修改用户的RFID卡密码
     * [actionUpdate description]
     * @author  zmy
     * @version 2016-12-09
     * @return  [type]     [description]
     */
    public function actionChangePassword()
    {
        $model = new ChangePasswordFrontend();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $rfidCardObj = EquipRfidCard::find()->where(['member_id' => $this->userinfo['userid']])->one();
            if (empty($rfidCardObj)) {
                Yii::$app->getSession()->setFlash('error', '对不起，此用户未绑定');
                return $this->render('changePassword', [
                    'model' => $model,
                ]);
            }
            // 传输的原密码（未加密）
            $currentPassword = Yii::$app->request->post('ChangePasswordFrontend')['currentPassword'];
            $retValidatePass = ChangePasswordFrontend::validatePass($currentPassword, $rfidCardObj);
            if ($retValidatePass == 7) {
                Yii::$app->getSession()->setFlash('error', '原密码错误！');
                return $this->render('changePassword', [
                    'model' => $model,
                ]);
            }
            $rfidCardObj->rfid_card_pass = md5($model->password);

            if (!$rfidCardObj->save()) {
                Yii::$app->getSession()->setFlash('error', '密码更新失败！');
                return $this->render('changePassword', [
                    'model' => $model,
                ]);
            } else {
                return $this->render('changePassword', [
                    'model'       => $model,
                    'successSign' => 1,
                ]);
            }

        } else {
            return $this->render('changePassword', [
                'model' => $model,
            ]);
        }

    }

    /**
     * ajax 通过设备ID 返回设备的数组 equip_code=>楼宇名称
     * param orgId 设备ID
     * [actionGetMember description]
     * @author  zmy
     * @version 2016-12-06
     * @return  [type]     [description]
     */
    public function actionGetEquipId()
    {
        // 获取当前登录用户的 id  $this->userinfo['userid']
        $equipArr = EquipRfidCard::getRfidAssocEquipCodeArr($this->userinfo['userid']);
        return json_encode($equipArr['equipArr']);
    }

}
