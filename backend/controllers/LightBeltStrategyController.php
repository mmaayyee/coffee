<?php

namespace backend\controllers;

use Yii;
use backend\models\LightBeltStrategy;
use backend\models\LightBeltStrategySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Api;
use yii\data\Pagination;

/**
 * LightBeltStrategyController implements the CRUD actions for LightBeltStrategy model.
 */
class LightBeltStrategyController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'use-strategy-view'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all LightBeltStrategy models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('灯带策略管理')) {
            return $this->redirect(['site/login']);
        }
        $model = new LightBeltStrategy();
        
        $param = Yii::$app->request->queryParams;
        $page  = Yii::$app->request->get('page', 1);
        
        $pageSize = 20;
        $data = [];
        if(isset($param['LightBeltStrategy']) && $param['LightBeltStrategy'])
        {
            $data["strategy_name"]  =   $model->strategy_name   = $param["LightBeltStrategy"]['strategy_name'];
            $data['light_belt_type']=   $model->light_belt_type = $param['LightBeltStrategy']['light_belt_type'];
            $data["light_status"]   =   $model->light_status    = $param["LightBeltStrategy"]['light_status'];
        }
        // 获取灯带策略管理数据
        $strategyList = json_decode(Api::getLightBeltStrategyArr($data, $pageSize, $page), true);
        $pages = new Pagination(['totalCount' =>$strategyList['totalCount'], 'pageSize' => $pageSize]);
        return $this->render('index',[
            'model' =>  $model,
            'page'  =>  $page,
            'pageSize'=>$pageSize,
            'pages' =>  $pages,
            'strategyList'   =>  $strategyList['lightBeltStrategyArr'],
        ]);
    }

    /**
     * Displays a single LightBeltStrategy model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看灯带策略')) {
            return $this->redirect(['site/login']);
        }
        $strategyArr = Api::getLightBeltStrategyById($id);
        return $this->render('view', [
            'strategyArr'  => $strategyArr,
        ]);
    }

    /**
     * 查看使用的策略
     * @author  zmy
     * @version 2017-07-14
     * @return  [type]     [description]
     */
    public function actionUseStrategyView()
    {
        $strategyID     = Yii::$app->request->get("id");
        $useStrategyList   = json_decode(Api::getUseScenarioByStrategyId($strategyID), true);
        if(!$useStrategyList)
        {
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        return $this->render("use-progroup-view", [
            'useStrategyList'   =>  $useStrategyList,
        ]);
    }

    /**
     * 添加时进行处理
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加灯带策略')) {
            return $this->redirect(['site/login']);
        }
        
        $model = new LightBeltStrategy();
        $param = Yii::$app->request->post();

        $strategyArr = null;
        if ($param) {
            if($param['light_belt_type'] == 0)
            {
                unset($param['startTimeArr']);
                unset($param['startColorArr']);
                unset($param['startColorArr']);
                unset($param['endTimeArr']);
                unset($param['endColorArr']);
            }
            $strategyList = json_decode(Api::saveLightBeltStrategy($param), true);
            if ($strategyList) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带策略管理", \backend\models\ManagerLog::CREATE, "添加灯带策略");
                if ($param['light_belt_type'] == 1) {
                    return $this->redirect(['update', 'id'=>$strategyList['id'] ]);
                }else{
                    return $this->redirect(['view', 'id'=>$strategyList['id'] ]);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', '策略添加失败，请重新添加');
            }
            return $this->render('_form', [
                'model' => $model,
                'lightBeltList' =>  json_encode( LightBeltStrategy::getLightBeltList(), JSON_UNESCAPED_UNICODE),
                'strategyArr'   =>  $strategyArr,
            ]);
        } else {
            return $this->render('_form', [
                'model' => $model,
                'lightBeltList' =>  json_encode( LightBeltStrategy::getLightBeltList(), JSON_UNESCAPED_UNICODE),
                'strategyArr'   =>  json_encode($strategyArr, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    /**
     * Updates an existing LightBeltStrategy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑灯带策略')) {
            return $this->redirect(['site/login']);
        }
        
        $strategyArr = Api::getLightBeltStrategyById($id);

        $model = new LightBeltStrategy();
        $param = Yii::$app->request->post();
        $strategyList = json_decode($strategyArr, 1);

        $lightBeltNameArr = [];
        if ($param) {
            $param['id'] = $id;
            $strategyList = json_decode(Api::saveLightBeltStrategy($param), true);
            
            if(!$strategyList){
                Yii::$app->getSession()->setFlash('error', '策略修改失败，请重新修改');
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带策略管理", \backend\models\ManagerLog::UPDATE, "编辑灯带策略");
            return $this->render('_form', [
                'model' => $model,
                'lightBeltList' =>  json_encode( LightBeltStrategy::getLightBeltList(), JSON_UNESCAPED_UNICODE),
                'strategyArr'   =>  json_encode($strategyList, JSON_UNESCAPED_UNICODE),
                'id'    =>  $id,
            ]);
        } else {
            return $this->render('_form', [
                'model' => $model,
                'lightBeltList' =>  json_encode( LightBeltStrategy::getLightBeltList(), JSON_UNESCAPED_UNICODE),
                'strategyArr'   =>  json_encode($strategyList, JSON_UNESCAPED_UNICODE),
                'id'    =>  $id,

            ]);
        }
    }

    /**
     * Deletes an existing LightBeltStrategy model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除灯带策略')) {
            return $this->redirect(['site/login']);
        }
        $id  = Yii::$app->request->post("id");
        $ret = Api::getDelLightBeltStrategyById($id);
        if ($ret == "false") {
            echo "false";
        } else {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带策略管理", \backend\models\ManagerLog::DELETE, "删除灯带策略");
            echo "true";
        }
    }

    /**
     * Finds the LightBeltStrategy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LightBeltStrategy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LightBeltStrategy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
