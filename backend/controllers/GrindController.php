<?php

namespace backend\controllers;

use backend\models\Grind;
use backend\models\GrindSearch;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * GrindController implements the CRUD actions for Grind model.
 */
class GrindController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * Lists all Grind models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('预磨豆设置列表')) {
            return $this->redirect(['site/login']);
        }

        $searchModel  = new GrindSearch();
        $dataProvider = $searchModel->searchGrindList(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Grind model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Grind model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('预磨豆设置添加')) {
            return $this->redirect(['site/login']);
        }
        $model  = new Grind();
        $params = Yii::$app->request->post();
        //$model->setScenario('create');
        if ($params) {
            if ($model->load($params)) {
                $result = $model->createGrindInfo($params);
                if ($result['code']) {
                    \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "预磨豆设置管理", \backend\models\ManagerLog::CREATE, "添加预磨豆设置");
                    return $this->redirect(['index']);
                } else {
                    $model->isNewRecord = 1;
                    Yii::$app->getSession()->setFlash('error', $result['msg']);
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } else {
                $model->isNewRecord = 1;
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            $model->isNewRecord = 1;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Grind model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('预磨豆设置编辑')) {
            return $this->redirect(['site/login']);
        }
        $model                    = $this->findModel($id);
        $model->searchUpdateBuild = Api::searchUpdateBuild($id);
        $params                   = Yii::$app->request->post();
        if ($params && $model->load($params)) {
            $result = $model->updateGrindInfo($params);
            if ($result['code']) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "预磨豆设置管理", \backend\models\ManagerLog::UPDATE, "编辑预磨豆设置");
                return $this->redirect(['index']);
            } else {
                $model->isNewRecord = 0;
                Yii::$app->getSession()->setFlash('error', $result['msg']);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

        } else {
            $model->isNewRecord = 0;
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Grind model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Grind the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Grind::findModel($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSearchBuild()
    {
        $data      = Yii::$app->request->post();
        $buildList = Api::getGrindBuildList($data);
        return !$buildList ? [] : Json::encode($buildList);
    }

    /**
     * 产品组和特价排期查询楼宇共用方法
     * 格式："name":buildingName, "build_type":buildingType, "org_id":branch,'orgRange':orgRange, "equipmentType":equipmentType,
     * @author  zmy
     * @version 2017-10-20
     * @return  [type]     [description]
     */
    public function actionGetAllBuildingInProductSourceGrind()
    {
        $searchParam = Yii::$app->request->post();
        return Api::getAllBuildingInProductSourceGrind($searchParam);
    }

    public function actionUpdateInfo()
    {
        $data = Yii::$app->request->post();
        return Api::getUpdateGrind($data);
    }

    public function actionIndexBuilding()
    {
        $searchModel = new GrindSearch();
        $params      = Yii::$app->request->queryParams;
        if (isset($params['id']) && $params['id'] > 0) {
            $params['GrindSearch']['grind_id'] = $params['id'];
        }
        $dataProvider = $searchModel->search($params);
        return $this->render('index_building', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelete()
    {
        if (!Yii::$app->user->can('预磨豆设置删除')) {
            return $this->redirect(['site/login']);
        }
        $id     = Yii::$app->request->get('id');
        $result = Api::getDeleteGrind($id);
        if ($result) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "预磨豆设置管理", \backend\models\ManagerLog::DELETE, "删除预磨豆设置");
            return true;
        }
        return false;
    }

    public function actionBuildDelete()
    {
        $equipmentCode = Yii::$app->request->get('equipmentCode');
        return Api::getDeleteGrindBuild($equipmentCode);
    }
}
