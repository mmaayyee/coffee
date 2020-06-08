<?php

namespace backend\controllers;

use backend\models\EquipVersion;
use backend\models\EquipVersionSearch;
use Yii;
use yii\web\Controller;
use common\models\Api;

/**
 * EquipVersionController implements the CRUD actions for EquipVersion model.
 */
class EquipVersionController extends Controller
{
    /**
     * 查看设备版本信息
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new EquipVersionSearch();
        $groupList = json_decode(Api::getGroups(),true);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$groupList);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'groupList'    => $groupList
        ]);
    }

}
