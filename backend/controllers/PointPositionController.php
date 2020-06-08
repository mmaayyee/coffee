<?php

namespace backend\controllers;

use backend\models\BuildType;
use backend\models\PointPosition;
use common\models\CoffeeBackApi;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * 点位助手小程序点位管理
 */
class PointPositionController extends Controller
{

    /**
     * 楼宇列表
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('点位助手查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel                        = new PointPosition();
        list($dataProvider, $pointTypeList) = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
            'pointTypeList' => $pointTypeList,
        ]);
    }

    /**
     * Displays a single Organization model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('点位助手查看')) {
            return $this->redirect(['site/login']);
        }
        $pointInfoList = CoffeeBackApi::getPointPositionInfo($id);
        return $this->render('view', [
            'model'         => (object) $pointInfoList['pointInfo'],
            'pointTypeList' => $pointInfoList['buildTypeList'],
            'pointList'     => Json::decode($pointInfoList['pointInfo']['point_list']),
        ]);
    }

    /**
     * Creates a new Organization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('点位助手创建')) {
            return $this->redirect(['site/login']);
        }
        $model = new PointPosition();
        $model->setScenario('create');
        return $this->render('create', [
            'model'         => $model,
            'pointTypeList' => BuildType::getBuildType(),
        ]);
    }

    /**
     * Updates an existing Organization model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('点位助手修改')) {
            return $this->redirect(['site/login']);
        }
        $pointInfoList = CoffeeBackApi::getPointPositionInfo($id);
        $model         = new PointPosition();
        $model->setScenario('update');
        $model->load(['PointPosition' => $pointInfoList['pointInfo']]);
        $model->day_peoples = floatval($model->day_peoples);
        return $this->render('update', [
            'model'         => $model,
            'pointTypeList' => $pointInfoList['buildTypeList'],
        ]);
    }

    /**
     * Deletes an existing Organization model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除机构')) {
            return $this->redirect(['site/login']);
        }
        $delRes = CoffeeBackApi::getPointPositionInfo($id);
        if ($delRes) {
            ManagerLog::saveLog(Yii::$app->user->id, "点位助手管理", ManagerLog::DELETE, $id);
        } else {
            Yii::$app->getSession()->setFlash('error', '点位删除失败');
        }
        return $this->redirect(['index']);
    }
}
