<?php

namespace backend\controllers;

use backend\models\EquipRfidCard;
use backend\models\ManagerLog;
use Yii;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class EquipRfidCardUploadController extends Controller
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new EquipRfidCard();
        $param = Yii::$app->request->post("EquipRfidCard");
        if (isset($param)) {
            $signRfidSave    = true;
            $similarRfidCode = '';
            $errorStr        = '';
            $rfidCodeArr     = [];
            // 以逗号 空格 换行 分隔，
            $rfidCardCodeArr = EquipRfidCard::getStrTransforArr($param['rfid_card_code']);
            foreach ($rfidCardCodeArr as $rfidCode) {
                if (!$rfidCode) {
                    unset($rfidCode);
                    continue;
                }
                if (strlen($rfidCode) > 6) {
                    $errorStr = '字符过长！（卡号不可超过6位）';
                    continue;
                }
                // 插入前循环查询是否已有此门禁卡号、
                $retRfidCodeSelect = EquipRfidCard::getRetSelectRfidCard($rfidCode);
                if ($retRfidCodeSelect != "success") {
                    $rfidCodeArr[] = $retRfidCodeSelect; // 相同的卡号
                    continue;
                }
                // 批量添加门禁卡（初始化）
                $retRfidSave = EquipRfidCard::rfidCodeSaveInit($rfidCode);
                if (!$retRfidSave) {
                    $signRfidSave = false;
                } else {
                    ManagerLog::saveLog(Yii::$app->user->id, "批量添加门禁卡", ManagerLog::CREATE, "门禁卡管理");
                }
            }
            $similarRfidCode = implode(',', $rfidCodeArr);
            if ($signRfidSave && !$errorStr && !$similarRfidCode) {
                // 跳转到门禁卡主页
                return $this->redirect(['equip-rfid-card/index']);
            } else {
                return $this->render('index', [
                    'model'           => $model,
                    'signRfidSave'    => $signRfidSave,
                    'similarRfidCode' => $similarRfidCode, // 相同门禁卡号
                    'errorStr'        => $errorStr,
                ]);
            }

        }
        return $this->render('index', [
            'model' => $model,
        ]);

    }

}
