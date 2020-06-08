<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\ScmMaterial;
use backend\models\ScmStock;
use backend\models\ScmStockGram;
use backend\models\ScmStockNum;
use backend\models\ScmStockSearch;
use backend\models\ScmTotalInventory;
use backend\models\ScmTotalInventoryGram;
use backend\models\ScmUserSurplusMaterial;
use backend\models\ScmUserSurplusMaterialGram;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

//use backend\models\ScmMaterial;

/**
 * ScmStockController implements the CRUD actions for ScmStock model.
 */
class ScmStockController extends Controller
{
    /**
     * Lists all ScmStock models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('入库信息管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ScmStockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScmStock model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看入库信息')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScmStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加入库信息')) {
            return $this->redirect(['site/login']);
        }
        $model = new ScmStock();
        if (Yii::$app->request->post()) {
            // 开启事务
            $transaction                  = Yii::$app->db->beginTransaction();
            $param                        = Yii::$app->request->post()['ScmStock'];
            $model->distribution_clerk_id = (isset($param['distribution_clerk_id']) && ($param['reason'] == ScmStock::DISTRIBUTION_RETURN)) ? $param['distribution_clerk_id'] : '';
            $model->warehouse_id          = $param['warehouse_id'];
            $model->reason                = $param['reason'];
            $model->author                = Yii::$app->user->identity->realname;
            $model->ctime                 = time();
            if ($model->save() === false) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '入库单添加失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            foreach ($param['material_num'] as $key => $value) {
                if (!$value) {
                    continue;
                }
                // 添加入库单
                $_model               = new ScmStockNum();
                $_model->scm_stock_id = $model->id;
                $_model->material_id  = $param['material_id'][$key];
                $_model->material_num = $value;
                if ($_model->save() === false) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '入库单添加失败');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            if (isset($param['material_gram'])) {
                foreach ($param['material_gram'] as $k => $item) {
                    if (!$item) {
                        continue;
                    }
                    $scmStockGram               = new ScmStockGram();
                    $scmStockGram->scm_stock_id = $model->id;
                    //根据物料ID查询供应商
                    $material                       = ScmMaterial::getMaterialDetail('supplier_id', ['id' => $param['material_id'][$k]]);
                    $scmStockGram->supplier_id      = isset($material['supplier_id']) ? $material['supplier_id'] : 0;
                    $scmStockGram->material_gram    = $item;
                    $materialType                   = ScmMaterial::getMaterialDetail('material_type', ['id' => $param['material_id'][$k]]);
                    $scmStockGram->material_type_id = isset($materialType['material_type']) ? $materialType['material_type'] : 0;
                    if ($scmStockGram->save() === false) {
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', '入库单添加失败');
                        return $this->render('create', [
                            'model' => $model,
                        ]);
                    }
                }
            }

            //事务通过
            $transaction->commit();
            // 添加日志
            ManagerLog::saveLog(Yii::$app->user->id, "入库信息管理", ManagerLog::CREATE, Yii::$app->user->identity->username);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmStock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑入库信息')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $stock = ScmStockNum::getStockNum($id);

        if (Yii::$app->request->post()) {
            $param = Yii::$app->request->post('ScmStock');
            // 获取配送员id
            $model->distribution_clerk_id = isset($param['distribution_clerk_id']) && $param['distribution_clerk_id'] ? $param['distribution_clerk_id'] : '';
            // 开启事务
            $transaction         = Yii::$app->db->beginTransaction();
            $model->warehouse_id = $param['warehouse_id'];
            $model->reason       = $param['reason'];
            $model->ctime        = time();
            $model->author       = Yii::$app->user->identity->realname;
            if ($model->save() === false) {
                Yii::$app->getSession()->setFlash('error', '入库单添加失败');
                $transaction->rollBack();
                return $this->redirect(['index']);
            }
            //删除入库单
            ScmStockNum::deleteAll(['scm_stock_id' => $id]);
            // 循环添加入库单
            foreach ($param['material_num'] as $key => $value) {
                if (!$value) {
                    continue;
                }
                // 添加入库单
                $_model               = new ScmStockNum();
                $_model->scm_stock_id = $id;
                $_model->material_id  = $param['material_id'][$key];
                $_model->material_num = $value;
                if ($_model->save() === false) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '入库单添加失败');
                    return $this->redirect(['index']);
                }
            }
            //删除散料入库单
            ScmStockGram::deleteAll(['scm_stock_id' => $id]);
            //处理散料数据
            if (isset($param['material_gram'])) {
                foreach ($param['material_gram'] as $k => $item) {
                    if (!$item) {
                        continue;
                    }
                    $scmStockGram               = new ScmStockGram();
                    $scmStockGram->scm_stock_id = $id;
                    //根据物料ID查询供应商
                    $material                       = ScmMaterial::getMaterialDetail('supplier_id', ['id' => $param['material_id'][$k]]);
                    $scmStockGram->supplier_id      = isset($material['supplier_id']) ? $material['supplier_id'] : 0;
                    $scmStockGram->material_gram    = $item;
                    $materialType                   = ScmMaterial::getMaterialDetail('material_type', ['id' => $param['material_id'][$k]]);
                    $scmStockGram->material_type_id = isset($materialType['material_type']) ? $materialType['material_type'] : 0;
                    if ($scmStockGram->save() === false) {
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', '入库单添加失败');
                        return $this->redirect(['index']);
                    }
                }
            }

            // 添加日志
            ManagerLog::saveLog(Yii::$app->user->id, "入库信息管理", ManagerLog::UPDATE, Yii::$app->user->identity->realname);
            //事务通过
            $transaction->commit();
            return $this->redirect(['index']);

        } else {
            return $this->render('update', [
                'model' => $model,
                'stock' => $stock,
            ]);
        }
    }

    /**
     * 出库单审核功能
     * @author  zgw
     * @version 2016-12-05
     * @return  [type]     [description]
     */
    public function actionCheck($id)
    {
        // 验证操作权限
        if (!Yii::$app->user->can('入库审核')) {
            return $this->redirect(['site/login']);
        }
        // 验证参数是否合法
        if (!$id) {
            Yii::$app->getSession()->setFlash('error', '参数有误');
            return $this->redirect('index');
        }
        $transaction = Yii::$app->db->beginTransaction();
        // 验证入库单是否存在
        $model = $this->findModel($id);
        if (!$model || $model->is_sure == 2) {
            Yii::$app->getSession()->setFlash('error', '请求数据不存在,或者已通过审核');
            return $this->redirect('index');
        }
        $model->is_sure   = 2;
        $model->sure_time = time();
        if ($model->save() === false) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '操作失败');
            return $this->redirect('index');
        }
        // 更新总库存和配送员剩余物料
        $updateInventoryRes = $this->updateInventoryAndDistribution($id);
        if ($updateInventoryRes === false) {
            $transaction->rollBack();
            return $this->redirect('index');
        }
        $transaction->commit();
        return $this->redirect('index');
    }

    /**
     * 出库单审核功能
     * @author  zgw
     * @version 2016-12-05
     * @return  [type]     [description]
     */
    public function actionCheckAll()
    {
        // 验证操作权限
        if (!Yii::$app->user->can('入库审核')) {
            return $this->redirect(['site/login']);
        }
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $list        = ScmStock::getScmStockList('id', ['is_sure' => ScmStock::WAIT_SURE]);
        // 更新总库存和配送员剩余物料
        foreach ($list as $stock) {
            $updateInventoryRes = $this->updateInventoryAndDistribution($stock['id']);
            if ($updateInventoryRes === false) {
                $transaction->rollBack();
                return $this->redirect('index');
            }
        }

        // 批量更新入库单
        $scmStockSaveRes = ScmStock::updateAll(['is_sure' => 2, 'sure_time' => time()], ['is_sure' => 1]);
        // 验证入库单更新操作是否成功不成功则回滚
        if ($scmStockSaveRes === false) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '操作失败');
            return $this->redirect('index');
        }
        $transaction->commit();
        return $this->redirect('index');
    }

    /**
     * 入库时更新配送员的剩余物料和总库存
     * @author  zgw
     * @version 2016-12-05
     * @return  [type]     [description]
     */
    private function updateInventoryAndDistribution($id = '')
    {
        // 验证入库单是否存在
        $model = ScmStock::getScmStock('*', ['id' => $id]);
        if (!$model) {
            Yii::$app->getSession()->setFlash('error', '请求数据不存在');
            return false;
        }
        //更新总库存和配送员手中物料
        $numList = ScmStockNum::getMaterialNumById($id);
        foreach ($numList as $num) {
            // 更新总库存整包
            $inventorySaveRes = ScmTotalInventory::changeInventory($model['warehouse_id'], $num['material_id'], $num['material_num']);
            if ($inventorySaveRes === false) {
                Yii::$app->getSession()->setFlash('error', '总库存更新失败');
                return false;
            }

            if ($model['reason'] == ScmStock::GIVE_BACK && $model['distribution_clerk_id']) {
                $userSurplusSaveRes = ScmUserSurplusMaterial::editSurplusMaterial($model['distribution_clerk_id'], $num['material_id'], $num['material_num'], 2);
                if ($userSurplusSaveRes === false) {
                    Yii::$app->getSession()->setFlash('error', '配送员剩余物料更新失败');
                    return false;
                }
            }
        }
        //更新散料总库存和配送员手中物料
        $gramList = ScmStockGram::getStockGram($id);

        foreach ($gramList as $k => $gram) {
            $result = ScmTotalInventoryGram::changeInventoryGram($model['warehouse_id'], $gram['supplier_id'], $gram['material_type_id'], $gram['material_gram']);
            if ($result === false) {
                Yii::$app->getSession()->setFlash('error', '总库存更新失败');
                return false;
            }

            //物料ID
            $materialId = isset($numList[$k]['material_id']) ? $numList[$k]['material_id'] : 0;

            if ($model['reason'] == ScmStock::GIVE_BACK && $model['distribution_clerk_id']) {
                $userSurplusSaveRes = ScmUserSurplusMaterialGram::editSurplusMaterialGram($materialId, $model['distribution_clerk_id'], $gram['supplier_id'], $gram['material_type_id'], $gram['material_gram'], 'del');
                if ($userSurplusSaveRes === false) {
                    Yii::$app->getSession()->setFlash('error', '配送员剩余物料更新失败');
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Deletes an existing ScmStock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除入库信息')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        // 开启事务
        if ($model->is_sure == 1 && $model->delete() !== false) {
            //删除散料入库单
            ScmStockGram::deleteAll(['scm_stock_id' => $id]);
            //删除物料入库单
            ScmStockNum::deleteAll(['scm_stock_id' => $id]);
            // 添加操作日志
            ManagerLog::saveLog(Yii::$app->user->id, "入库信息管理", ManagerLog::DELETE, Yii::$app->user->identity->username);
        } else {
            Yii::$app->getSession()->setFlash('error', '删除失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the ScmStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ScmStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmStock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
