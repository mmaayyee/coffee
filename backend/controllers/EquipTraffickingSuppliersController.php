<?php

namespace backend\controllers;

use Yii;
use common\models\EquipTraffickingSuppliers;
use backend\controllers\EquipTraffickingSuppliersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\EquipTraffickingOrgAssoc;
use backend\models\ManagerLog;
use yii\helpers\ArrayHelper;
use backend\models\Organization;

/**
 * EquipTraffickingSuppliersController implements the CRUD actions for EquipTraffickingSuppliers model.
 */
class EquipTraffickingSuppliersController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipTraffickingSuppliers models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('投放商列表')) 
            return $this->redirect(['site/login']);
        
        $searchModel = new EquipTraffickingSuppliersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipTraffickingSuppliers model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看投放商')) 
            return $this->redirect(['site/login']);
        
        $model = $this->findModel($id);
        $model->org_id = $this->orgNames($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    
    /**
     * Creates a new EquipTraffickingSuppliers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加投放商')) 
            return $this->redirect(['site/login']);

        $transaction = Yii::$app->db->beginTransaction();
        $model = new EquipTraffickingSuppliers();
        $data = Yii::$app->request->post('EquipTraffickingSuppliers');
        if ($data) {
            $data['create_time'] = time();
        }
        if ($model->load(['EquipTraffickingSuppliers'=>$data]) && $model->save()) {
            //删除该供应商原有公司数据
            if ($this->delTraffickingSuppliersOrg($model->id) === false) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error','删除原公司数据失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            //添加该供应商新的公司数据
            if (!$this->saveTraSuppData($model->id, $data['org_id'])) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error','添加分公司信息失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            //添加操作日志
            if (!ManagerLog::saveLog(Yii::$app->user->id, '投放商管理', ManagerLog::CREATE, $model->name)) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error','添加操作日志失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipTraffickingSuppliers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑投放商')) 
            return $this->redirect(['site/login']);
        
        $transaction = Yii::$app->db->beginTransaction();
        
        $model = $this->findModel($id);
        $data = Yii::$app->request->post('EquipTraffickingSuppliers');
        if ($model->load(['EquipTraffickingSuppliers' => $data]) && $model->save()) {
            //删除该供应商原有公司数据
            if ($this->delTraffickingSuppliersOrg($model->id) === false) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error','删除原公司数据失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            //添加该供应商新的公司数据
            if (!$this->saveTraSuppData($model->id, $data['org_id'])) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error','添加分公司信息失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            //添加操作日志
            if (!ManagerLog::saveLog(Yii::$app->user->id, '投放商管理', ManagerLog::UPDATE, $model->name)) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error','添加操作日志失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            $model->org_id = ArrayHelper::getColumn(EquipTraffickingOrgAssoc::findAll(['trafficking_suppliers_id' => $model->id]),'org_id');
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipTraffickingSuppliers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除投放商')) 
            return $this->redirect(['site/login']);
        
        $transaction = Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);
        if ($model->delete() === false) {
            Yii::$app->getSession()->setFlash('error','操作失败');
            
        }
        if (!ManagerLog::saveLog(Yii::$app->user->id,'投放商管理',ManagerLog::DELETE,$model->name)) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error','操作日志添加失败');
        } else {
            $transaction->commit();
        }
        return $this->redirect(['index']);

        
    }

    /**
     * Finds the EquipTraffickingSuppliers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipTraffickingSuppliers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipTraffickingSuppliers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 获取该投放商所属分公司名称
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function orgNames($id)
    {
        //获取分公司id
        $org_id_arr = EquipTraffickingOrgAssoc::getColumn('org_id',['trafficking_suppliers_id'=>$id]);
        //获取分公司名称
        $org_name_arr = Organization::getOrgNameArr($org_id_arr);
        //多个分公司名称组成一个字符串
        return implode('，', $org_name_arr);
    }

    /**
     * 添加投放商所属分公司
     * @param  [type] $tra_org_id [description]
     * @param  [type] $org_id_arr [description]
     * @return [type]             [description]
     */
    public function saveTraSuppData($tra_org_id,$org_id_arr) 
    {
        $data = [];
        foreach ($org_id_arr as $k => $v) {
            $data[$k]['trafficking_suppliers_id'] = $tra_org_id;
            $data[$k]['org_id'] = $v;
        }
        return $this->addTraffickingSuppliersOrg($data);
    }

    /**
     * 执行添加投放商所属分公司操作
     * @param [type] $data [description]
     */
    public function addTraffickingSuppliersOrg($data)
    {
        return Yii::$app->db->createCommand()->batchInsert('equip_trafficking_org_assoc',['trafficking_suppliers_id','org_id'],$data)->execute();
    }

    /**
     * 删除该供应商原有公司数据
     * @param  [type] $trafficking_suppliers_id [description]
     * @return [type]                           [description]
     */
    public function delTraffickingSuppliersOrg($trafficking_suppliers_id)
    {
        return EquipTraffickingOrgAssoc::deleteAll(['trafficking_suppliers_id' => $trafficking_suppliers_id]);
    }
}
