<?php

namespace backend\controllers;

use backend\models\BuildingTaskSetting;
use backend\models\BuildingTaskSettingSearch;
use backend\models\Manager;
use backend\models\ProductMaterialStockAssoc;
use backend\models\ScmMaterialType;
use common\models\Building;
use common\models\Equipments;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BuildingTaskSettingController implements the CRUD actions for BuildingTaskSetting model.
 */
class BuildingTaskSettingController extends Controller
{
    /**
     * @inheritdoc
     */
    /*public function behaviors()
    {
    return [
    'verbs' => [
    'class' => VerbFilter::className(),
    'actions' => [
    'delete' => ['POST'],
    ],
    ],
    ];
    }*/

    /**
     * Lists all BuildingTaskSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('楼宇日常任务管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new BuildingTaskSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $orgId        = Manager::getManagerBranchID();
        //获取楼宇名称列表
        $buildName = Building::getBuildNameList($orgId);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'buildName'    => $buildName,
        ]);
    }

    /**
     * Displays a single BuildingTaskSetting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看楼宇日常任务')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BuildingTaskSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加楼宇日常任务')) {
            return $this->redirect(['site/login']);
        }
        $data  = [];
        $model = new BuildingTaskSetting();
        if ($param = Yii::$app->request->post()) {
            $data = [];
            if (isset($param['BuildingTaskSetting']['refuel_cycle'])) {
                foreach ($param['BuildingTaskSetting']['refuel_cycle'] as $material_type => $refuelCycle) {
                    $data[] = ['material_type' => $material_type, 'refuel_cycle' => $refuelCycle];
                }
            }

            $model->building_id    = $param['BuildingTaskSetting']['building_id'];
            $model->cleaning_cycle = $param['BuildingTaskSetting']['cleaning_cycle'];
            $model->day_num        = $param['BuildingTaskSetting']['day_num'];
            $model->refuel_cycle   = json_encode($data);
            $model->error_value    = $param['BuildingTaskSetting']['error_value'];

            if ($model->validate() && $model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "楼宇日常任务管理", \backend\models\ManagerLog::CREATE, "添加楼宇日常任务");
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
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
     * Updates an existing BuildingTaskSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑楼宇日常任务')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        if ($param = Yii::$app->request->post()) {
            $data = [];
            if (isset($param['BuildingTaskSetting']['refuel_cycle'])) {
                foreach ($param['BuildingTaskSetting']['refuel_cycle'] as $material_type => $refuelCycle) {
                    $data[] = ['material_type' => $material_type, 'refuel_cycle' => $refuelCycle];
                }
            }

            $model->cleaning_cycle = $param['BuildingTaskSetting']['cleaning_cycle'];
            $model->day_num        = $param['BuildingTaskSetting']['day_num'];
            $model->refuel_cycle   = json_encode($data);
            $model->error_value    = $param['BuildingTaskSetting']['error_value'];
            if ($model->validate() && $model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "楼宇日常任务管理", \backend\models\ManagerLog::UPDATE, "编辑楼宇日常任务");
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->redirect(['view', 'id' => $model->id]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing BuildingTaskSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除楼宇日常任务')) {
            return $this->redirect(['site/login']);
        }
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "楼宇日常任务管理", \backend\models\ManagerLog::DELETE, "删除楼宇日常任务");
        return $this->redirect(['index']);
    }

    /**
     * Finds the BuildingTaskSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BuildingTaskSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BuildingTaskSetting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 获取楼宇设备料仓信息
     * @param int $buildId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAjaxGetProductGroupMaterial($buildId = 0)
    {
        //根据楼宇ID获取产品组ID
        if (Yii::$app->request->isAjax) {
            $productGroupId = Equipments::getField('pro_group_id', ['build_id' => $buildId]);
            if (!$productGroupId) {
                return json_encode([]);
            }
            $list = ProductMaterialStockAssoc::getProductGroupMaterialList('*', ['pro_group_id' => $productGroupId]);
            if (!$list) {
                return json_encode([]);
            }
            $bulkMaterial = ScmMaterialType::getBulkMaterialName();
            //料仓物料名称的数组
            $stock = [];
            foreach ($list as $key => $material) {
                if (!in_array($material->materialType->material_type_name, $bulkMaterial)) {
                    continue;
                }
                $stock[] = [
                    'material_type'      => $material->material_type,
                    'material_type_name' => $material->materialType->material_type_name,
                ];
            }
            echo json_encode($stock);
        } else {
            throw new NotFoundHttpException('不是ajax请求');
        }
    }
}
