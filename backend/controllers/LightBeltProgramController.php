<?php

namespace backend\controllers;

use backend\models\LightBeltProgram;
use common\models\Api;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LightBeltProgramController implements the CRUD actions for LightBeltProgram model.
 */
class LightBeltProgramController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'get-search-scenario', 'build-program-view', 'set-default-program', 'release-program', 'check-scenario'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }
    /**
     * Lists all LightBeltProgram models.
     * @return mixed
     */

    public function actionIndex()
    {
        if (!Yii::$app->user->can('灯带方案管理')) {
            return $this->redirect(['site/login']);
        }
        $param = Yii::$app->request->queryParams;

        $page  = Yii::$app->request->get('page', 1);
        $model = new LightBeltProgram();
        $data  = [];
        if (isset($param['LightBeltProgram'])) {
            $data["program_name"]       = $model->program_name       = $param['LightBeltProgram']["program_name"];
            $data["scenario_name"]      = $model->scenario_name      = $param['LightBeltProgram']['scenario_name'];
            $data["strategy_name"]      = $model->strategy_name      = $param['LightBeltProgram']['strategy_name'];
            $data['is_default']         = $model->is_default         = $param['LightBeltProgram']['is_default'];
            $data["product_group_name"] = $model->product_group_name = $param['LightBeltProgram']['product_group_name'];
        }
        $pageSize    = 10;
        $programList = json_decode(Api::getLightBeltProgramArr($data, $pageSize, $page), true);
        $pages       = new Pagination(['totalCount' => $programList['totalCount'], 'pageSize' => $pageSize]);
        return $this->render('index', [
            'model'       => $model,
            'page'        => $page,
            'pages'       => $pages,
            'pageSize'    => $pageSize,
            'programList' => $programList['lightBeltProgramArr'] ? json_encode($programList['lightBeltProgramArr'], JSON_UNESCAPED_UNICODE) : "null",
        ]);
    }

    /**
     * Displays a single LightBeltProgram model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看灯带方案')) {
            return $this->redirect(['site/login']);
        }

        $param = Yii::$app->request->queryParams;
        $page  = Yii::$app->request->get('page', 1);

        $model = new LightBeltProgram();
        $data  = [];
        if (isset($param['LightBeltProgram'])) {
            $data["buildName"] = $model->buildName = $param['LightBeltProgram']["buildName"];
            $data["equipType"] = $model->equipType = $param['LightBeltProgram']['equipType'];
            $data["branch"]    = $model->branch    = $param['LightBeltProgram']['branch'];
            // $data["agent"]          =   $model->agent       =   $param['LightBeltProgram']['agent'];
            // $data["partner"]        =   $model->partner     =   $param['LightBeltProgram']['partner'];
        }
        $pageSize = 20;
        $buildArr = json_decode(Api::getBuildInProgramWhere($id, $data, $pageSize, $page), true);
        // 详情页面数据
        $programList = Api::getLightBeltProgramById($id);
        $pages       = new Pagination(['totalCount' => $buildArr['totalCount'], 'pageSize' => $pageSize]);

        return $this->render('view', [
            'model'       => $model,
            'page'        => $page,
            'pageSize'    => $pageSize,
            'pages'       => $pages,
            'id'          => $id,
            'programList' => $programList,
            'buildList'   => $buildArr['buildArr'],
        ]);
    }

    /**
     * 检测方案中添加的场景是否符合条件。
     * @return [string] [json]
     */
    public function actionCheckScenario()
    {
        $param = Yii::$app->request->post("scenarioArr");
        $error = json_decode(Api::checkLightBeltScenario($param), true);
        if ($error) {
            $errorArr = explode('；', $error);
            echo json_encode($errorArr, JSON_UNESCAPED_UNICODE);die();
        }
        echo json_encode([], JSON_UNESCAPED_UNICODE);die();
    }

    /**
     * Creates a new LightBeltProgram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加灯带方案')) {
            return $this->redirect(['site/login']);
        }
        $model = new LightBeltProgram();
        $param = Yii::$app->request->post();

        if ($param) {
            $defaultStrategyId = $param['LightBeltProgram']['default_strategy_id'];
            unset($param['LightBeltProgram']);
            $param['default_strategy_id'] = $defaultStrategyId;

            $ret = Api::saveLightBeltProgram($param);
            if ($ret == 'true') {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带方案管理", \backend\models\ManagerLog::CREATE, "添加灯带方案");
                return $this->redirect(['index']);
            } else {
                if ($ret == 'false') {
                    Yii::$app->getSession()->setFlash('error', '方案添加失败，请重试');
                } else if ($ret == '255') {
                    Yii::$app->getSession()->setFlash('error', "对不起，添加方案中的策略编号已超过255，添加失败.");
                }
                return $this->render('_form', [
                    'model'       => $model,
                    'programList' => json_encode([]),
                ]);
            }
        } else {
            return $this->render('_form', [
                'model'       => $model,
                'programList' => json_encode([]),
            ]);
        }
    }

    /**
     * Updates an existing LightBeltProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑灯带方案')) {
            return $this->redirect(['site/login']);
        }
        $model = new LightBeltProgram();
        $param = Yii::$app->request->post();

        $programList = Api::getLightBeltProgramById($id);
        if ($param) {
            $param['id']       = $id;
            $defaultStrategyId = $param['LightBeltProgram']['default_strategy_id'];
            unset($param['LightBeltProgram']);
            $param['default_strategy_id'] = $defaultStrategyId;

            $ret = Api::saveLightBeltProgram($param);
            if ($ret == 'true') {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带方案管理", \backend\models\ManagerLog::UPDATE, "编辑灯带方案");
                return $this->redirect(['index']);
            } else {
                if ($ret == 'false') {
                    Yii::$app->getSession()->setFlash('error', '方案修改失败，请重试');
                }
                return $this->render('_form', [
                    'model'       => $model,
                    'programList' => json_encode([]),
                ]);
            }
            return $this->redirect(['index']);
        } else {
            $model->default_strategy_id = json_decode($programList, true)["default_strategy_id"];
            return $this->render('_form', [
                'model'       => $model,
                'programList' => $programList,
                'id'          => $id,
            ]);
        }
    }

    /**
     * Deletes an existing LightBeltProgram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除灯带方案')) {
            return $this->redirect(['site/login']);
        }
        $id  = Yii::$app->request->post("id");
        $ret = Api::getDelLightBelProgramById($id);
        if ($ret == 'true') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯带方案管理", \backend\models\ManagerLog::DELETE, "删除灯带方案");
        }
        echo $ret;
    }

    /**
     * ajax设置默认灯带方案
     * @author  zmy
     * @version 2017-07-21
     * @return  [type]     [true/false]
     */
    public function actionSetDefaultProgram()
    {
        if (!Yii::$app->user->can('设置默认方案')) {
            return $this->redirect(['site/login']);
        }
        $id  = Yii::$app->request->post("id");
        $ret = Api::getUpdateDefaultProgram($id);
        if ($ret) {
            echo "true";
        } else {
            echo "false";
        }
    }

    /**
     * 发布方案
     * @author  zmy
     * @version 2017-07-21
     * @return  [type]     [true/false]
     */
    public function actionReleaseProgram()
    {
        if (!Yii::$app->user->can('发布方案')) {
            return $this->redirect(['site/login']);
        }
        $id  = Yii::$app->request->post("id");
        $ret = Api::getUpdateVersionProgram($id);
        if ($ret) {
            echo "true";
        } else {
            echo "false";
        }
    }

    /**
     * ajax 通过条件， 获取场景数据
     * @author  zmy
     * @version 2017-06-29
     * @return  [type]     [description]
     */
    public function actionGetSearchScenario()
    {
        $param       = Yii::$app->request->post();
        $scenarioArr = Api::getSpecifiedScenarioArr($param);
        echo $scenarioArr;
    }

    /**
     * 灯带楼宇方案管理
     * @author  zmy
     * @version 2017-07-03
     * @return  [type]     [description]
     */
    public function actionBuildProgramView()
    {
        if (!Yii::$app->user->can('查看灯带楼宇方案')) {
            return $this->redirect(['site/login']);
        }
        $param    = Yii::$app->request->queryParams;
        $page     = Yii::$app->request->get('page', 1);
        $model    = new LightBeltProgram();
        $data     = [];
        $pageSize = 10;
        if (isset($param['LightBeltProgram'])) {
            $data["buildName"]          = $model->buildName          = $param['LightBeltProgram']['buildName'];
            $data['program_name']       = $model->program_name       = $param['LightBeltProgram']['program_name'];
            $data['equipType']          = $model->equipType          = $param["LightBeltProgram"]['equipType'];
            $data['branch']             = $model->branch             = $param['LightBeltProgram']['branch'];
            $data['agent']              = $model->agent              = $param["LightBeltProgram"]['agent'];
            $data['partner']            = $model->partner            = $param['LightBeltProgram']['partner'];
            $data['scenario_name']      = $model->scenario_name      = $param["LightBeltProgram"]['scenario_name'];
            $data['strategy_name']      = $model->strategy_name      = $param["LightBeltProgram"]['strategy_name'];
            $data['product_group_name'] = $model->product_group_name = $param['LightBeltProgram']['product_group_name'];
            $data['equipCode']          = $model->equipCode          = $param['LightBeltProgram']['equipCode'];
        }
        $buildProgramList = json_decode(Api::getBuildProgramByWhere($data, $pageSize, $page), true);
        $pages            = new Pagination(['totalCount' => $buildProgramList['totalCount'], 'pageSize' => $pageSize]);
        return $this->render('build_program_view', [
            'page'      => $page,
            'pages'     => $pages,
            'pageSize'  => $pageSize,
            'buildList' => $buildProgramList['buildArr'] ? json_encode($buildProgramList['buildArr'], JSON_UNESCAPED_UNICODE) : "null",
            'model'     => $model,
        ]);
    }

    /**
     * Finds the LightBeltProgram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LightBeltProgram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LightBeltProgram::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
