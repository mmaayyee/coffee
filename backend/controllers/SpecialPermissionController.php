<?php

namespace backend\controllers;

use backend\models\EquipRfidCard;
use backend\models\EquipRfidCardRecord;
use Yii;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class SpecialPermissionController extends Controller
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
     * 生成特殊开门的密码
     * @author  zmy
     * @version 2016-12-22
     * @return  [type]     [description]
     */
    public function actionCreate()
    {
        $param = Yii::$app->request->get("EquipRfidCard");
        $model = new EquipRfidCard();
        $error = '';

        $retCheck    = EquipRfidCard::checkWhereJudgment($param, $error);
        $error       = $retCheck['error'];
        $rfidCardObj = $retCheck['rfidCardObj'];

        if ($error) {
            $model->rfid_card_code = $param['rfid_card_code'];
            $model->verificateCode = $param['verificateCode'];
            $model->equipId        = isset($param['equipId']) ? $param['equipId'] : "";

            Yii::$app->getSession()->setFlash('error', $error);
            return $this->render('index', [
                'model' => $model,
            ]);
        }

        // 处理返回的提示信息，（此用户未在后台绑定）
        // $retPromptArr = EquipRfidCard::retPrompt($param, $rfidCardObj, $error);
        $retPromptArr  = EquipRfidCard::retGeneratePass($param, $rfidCardObj, $error);
        $error         = $retPromptArr['error'];
        $retPassStrArr = $retPromptArr['retPassStrArr'];

        $passStr          = $retPassStrArr["md5Str"];
        $equipRfidCardArr = EquipRfidCard::getRfidAssocEquipCodeArr('', $param['rfid_card_code']);

        $model->rfid_card_code = $param['rfid_card_code'];
        $model->verificateCode = $param['verificateCode'];
        $model->equipId        = isset($param['equipId']) ? $param['equipId'] : "";
        $model->org_id         = $retPassStrArr['orgId'];
        if (!$error) {
            //添加到后台开门记录中
            $retSaveRfidRecord = EquipRfidCardRecord::saveRfidData($equipRfidCardArr["rfidCardObj"], $param['equipId'], 1, 2);
            if (!$retSaveRfidRecord) {
                Yii::$app->getSession()->setFlash('error', '对不起，开门记录失败');
                return $this->render('index', [
                    'model' => $model,
                ]);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "门禁卡特殊开门管理", \backend\models\ManagerLog::CREATE, "操作人:" . Yii::$app->user->identity->username);
        }
        return $this->render("index", [
            'error'    => $error ? $error : '',
            'passStr'  => $passStr,
            'equipArr' => $equipRfidCardArr["equipArr"],
            'model'    => $model,
        ]);
    }
}
