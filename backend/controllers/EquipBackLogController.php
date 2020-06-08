<?php

namespace backend\controllers;

use backend\models\CoffeeLabel;
use backend\models\EquipBackLog;
use backend\models\Organization;
use common\models\Building;
use Yii;
use yii\web\Controller;

/**
 * CoffeeLabelController implements the CRUD actions for CoffeeLabel model.
 */
class EquipBackLogController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 工厂模式操作日志列表
     * @author zhenggangwei
     * @date   2020-03-23
     * @return array
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看工厂模式操作日志')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->queryParams;
        if (!empty($params['export'])) {
            if (!Yii::$app->user->can('导出工厂模式操作日志')) {
                return $this->redirect(['site/login']);
            }
            $searchModel = new EquipBackLog();
            $searchModel->exportData();
            die;
        }
        $searchModel                        = new EquipBackLog();
        list($dataProvider, $masterialInfo) = $searchModel->getLogList($params);
        $orgIdNameList                      = Organization::getBranchArray(0);
        $orgIdList                          = array_keys($orgIdNameList);
        $buildIdNameList                    = Building::getBuildIdNameList($orgIdList);
        return $this->render('index', [
            'searchModel'     => $searchModel,
            'dataProvider'    => $dataProvider,
            'orgIdNameList'   => $orgIdNameList,
            'buildIdNameList' => $buildIdNameList,
            'masterialInfo'   => $masterialInfo,
        ]);
    }

}
