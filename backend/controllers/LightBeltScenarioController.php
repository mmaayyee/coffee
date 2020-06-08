<?php

namespace backend\controllers;

use Yii;
use backend\models\LightBeltScenario;
use backend\models\LightBeltScenarioSearch;
use common\models\Api;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * LightBeltScenarioController implements the CRUD actions for LightBeltScenario model.
 */
class LightBeltScenarioController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'use-scenario-view'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all LightBeltScenario models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('灯带场景管理')) {
            return $this->redirect(['site/login']);
        }
        $model = new LightBeltScenario();
        $param = Yii::$app->request->queryParams;
        $page  = Yii::$app->request->get("page", 1);
        $data = [];
        if(isset($param['LightBeltScenario']) && $param['LightBeltScenario']){
            $data["scenario_name"]          =   $model->scenario_name= $param['LightBeltScenario']["scenario_name"];
            $data["equip_scenario_name"]    =   $model->equip_scenario_name= $param['LightBeltScenario']["equip_scenario_name"];
            $data["product_group_name"]       =   $model->product_group_name= $param['LightBeltScenario']["product_group_name"];
            $data["strategy_name"]            =   $model->strategy_name= $param['LightBeltScenario']["strategy_name"];
        }
        $pageSize = 20;
        $scenarioList = json_decode(Api::getLightBeltScenarioArr($data, $pageSize, $page), true);

        $pages = new Pagination(['totalCount' =>$scenarioList['totalCount'], 'pageSize' => $pageSize]);
        return $this->render('index',[
            'model' =>  $model,
            'page'  =>  $page,
            'pageSize'=>  $pageSize,
            'pages' =>  $pages,
            'scenarioList'   =>  $scenarioList['scenarilArr'],
        ]);

    }

    /**
     * Displays a single LightBeltScenario model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看灯带场景')) {
            return $this->redirect(['site/login']);
        }
        $data = json_decode(Api::getLightBeltScenarioById($id), true);
        return $this->render('view', [
            'data'  => $data,
        ]);
    }


    /**
     * 查看使用场景的方案
     * @author  zmy
     * @version 2017-07-19
     * @return  [type]     [description]
     */
    public function actionUseScenarioView()
    {
        $scenarioID       = Yii::$app->request->get("id");
        $useScenarioList  = json_decode(Api::getUseProgramByScenarioId($scenarioID), true);
        if (!$useScenarioList) {
          return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        return $this->render("use-scenario-view", [
            'useScenarioList'   =>  $useScenarioList,
        ]);
    }

    /**
     * Creates a new LightBeltScenario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加灯带场景')) {
            return $this->redirect(['site/login']);
        }
        $model = new LightBeltScenario();
        $model->scenario = 'create';
        $param = Yii::$app->request->post();
        if ($param) {
            $ret  = Api::saveLightBeltScenario($param['LightBeltScenario']);
            if ($ret) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带场景管理", \backend\models\ManagerLog::CREATE, "添加灯带场景");
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', '添加灯带场景失败，请重试');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LightBeltScenario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑灯带场景')) {
            return $this->redirect(['site/login']);
        }
        $scenarioList       = json_decode(Api::getLightBeltScenarioById($id), true);
        $model              = LightBeltScenario::getUpScenarioModel($scenarioList);
        $param              = Yii::$app->request->post();
        $model->scenario    = 'update';
        $model->isNewRecord = 0;
        if ($param) {
            $data       = $param['LightBeltScenario'];
            $data['id'] = $id;

            $ret = Api::saveLightBeltScenario($data);
            if ($ret) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带场景管理", \backend\models\ManagerLog::UPDATE, "编辑灯带场景");
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', '修改灯带场景失败，请重试');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LightBeltScenario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除灯带场景')) {
            return $this->redirect(['site/login']);
        }

        $id  = Yii::$app->request->post("id");
        $ret = Api::getDelLightBeltScenarioById($id);
        if ($ret == 'true') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带场景管理", \backend\models\ManagerLog::DELETE, "删除灯带场景");
        }
        echo $ret;
    }

    /**
     * Finds the LightBeltScenario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LightBeltScenario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LightBeltScenario::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
