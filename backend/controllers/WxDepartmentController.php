<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\Organization;
use backend\models\WxDepartmentSearch;
use common\helpers\WXApi\Department;
use common\models\WxDepartment;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * WxDepartmentController implements the CRUD actions for WxDepartment model.
 */
class WxDepartmentController extends Controller
{
    /**
     * Lists all WxDepartment models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看部门')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new WxDepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Creates a new WxDepartment model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加部门')) {
            return $this->redirect(['site/login']);
        }
        $model = new WxDepartment();
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $data        = Yii::$app->request->post('WxDepartment');
        if ($data) {
            $data['parentid']    = $data['parentid'] ? $data['parentid'] : 1; //上级部门id
            $data['headquarter'] = $data['headquarter'] ? $data['headquarter'] : 0;
            //添加部门
            if ($model->load(['WxDepartment' => $data]) && $model->save()) {
                //获取当前部门路径
                $model->level = $model::findOne($model->parentid)['level'] ? $model::findOne($model->parentid)['level'] . $model->id . '-' : '-' . $model->id . '-';
                //保存部门路径
                if (!$model->save()) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '部门路径保存失败');
                    return $this->render('create', ['model' => $model]);
                }
                //调用企业微信添加部门接口
                $data['id'] = $model->id;
                $depart     = new Department();
                $res        = $depart->departmentAdd($data);
                if ($res !== 'created') {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '微信接口调用失败' . $res);
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }

                //添加操作日志
                $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "部门管理", ManagerLog::CREATE, $model->name);
                if (!$managerLogRes) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                    return $this->render('create', ['model' => $model]);
                }
                $transaction->commit();
                return $this->redirect(['index']);
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
     * Updates an existing WxDepartment model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑部门')) {
            return $this->redirect(['site/login']);
        }
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        $data        = Yii::$app->request->post('WxDepartment');
        if ($data) {
            $data['parentid']    = $data['parentid'] ? $data['parentid'] : 1;
            $data['headquarter'] = $data['headquarter'] ? $data['headquarter'] : 0;
            //获取当前部门路径
            $model->level = $model::findOne($model->parentid)['level'] ? $model::findOne($model->parentid)['level'] . $model->id . '-' : '-' . $model->id . '-';
        }

        //上级部门id
        if ($model->load(['WxDepartment' => $data]) && $model->save()) {
            //调用企业微信更新部门接口
            $data['id'] = $model->id;
            if ($data['id'] != 1) {
                $depart = new Department();
                $res    = $depart->departmentEdit($data);
                if ($res != 'updated') {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '微信接口调用失败' . $res);
                    return $this->render('update', ['model' => $model]);
                }
            }
            //添加操作日志
            $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "部门管理", ManagerLog::UPDATE, $model->name);
            if (!$managerLogRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('update', ['model' => $model]);
            }
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WxDepartment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除部门')) {
            return $this->redirect(['site/login']);
        }
        $depart = new Department();
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        //删除部门
        if ($model->delete()) {
            //调用企业微信删除部门接口
            $res = $depart->departmentDel($id);
            if ($res == 'deleted' || $res == 'department not found') {
                //添加操作日志
                $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "部门管理", ManagerLog::DELETE, $model->name);
                if (!$managerLogRes) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                    return $this->redirect(['index']);
                }
                $transaction->commit();
                return $this->redirect(['index']);
            }
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '微信接口调用失败');
            return $this->redirect(['index']);
        } else {
            die('删除失败');
        }

    }

    /**
     * Finds the WxDepartment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WxDepartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WxDepartment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 同步部门数据
     * @return [type] [description]
     */
    public function actionSyncDepartment()
    {
        $result         = true;
        $departIdArr    = [];
        $depart         = new Department();
        $departmentlist = $depart->departmentList();
        if (!is_array($departmentlist)) {
            $result = false;
        }

        foreach ($departmentlist as $v) {
            $departIdArr[] = $v['id'];
            $_model        = WxDepartment::findOne($v['id']);
            $_model        = empty($_model) ? new WxDepartment() : $_model;
            $v['sort']     = $v['order'];
            $_model->id    = $v['id'];
            if (!$_model->org_id) {
                $_model->org_id = 0;
            }
            $_model->setAttributes($v);
            $res = $_model->save();
        }
        if ($result) {
            // 删除企业微信中没有的标签
            $delModel = WxDepartment::deleteAll(['not in', 'id', $departIdArr]);
            return $this->redirect('index');
        } else {
            die('同步失败');
        }
    }

    /**
     * 获取上级部门列表
     * @return [type] [description]
     */
    public function actionParentDepartList()
    {
        //1-需要总公司部门列表 2-不需要总公司部门列表
        $type = Yii::$app->request->get('type', 1);
        // 分公司id
        $org_id = Yii::$app->request->get('org_id');
        // 部门id
        $depart_id = Yii::$app->request->get('depart_id');
        // 查询条件(获取当前分公司下所有的部门)
        $where = ['or', ['org_id' => $org_id], ['org_id' => Organization::HEAD_OFFICE]];
        if ($type == 2) {
            $where = ['org_id' => $org_id];
        }
        // 根据条件获取部门列表
        $departList = WxDepartment::getList('*', $where);
        // 页面中展示的数据
        $html = "<option value=''>请选择</option>";
        foreach ($departList as $k => $v) {
            if ($depart_id == $v['id']) {
                $html .= "<option value='" . $v['id'] . "' selected='selected'>" . $v['name'] . "</option>";
            } else {
                $html .= "<option value='" . $v['id'] . "'>" . $v['name'] . "</option>";
            }
        }
        echo $html;
    }

    /**
     *  传入部门ID，查询是否为供水部
     *  @return true false
     **/
    public function actionHeadquarter()
    {
        $department_id = Yii::$app->request->get('department_id');
        $departmentArr = WxDepartment::find()->where(['id' => $department_id])->asArray()->one();
        if ($departmentArr && $departmentArr['headquarter'] == WxDepartment::WATERMINISTRY) {
            echo true;
        } else {
            echo false;
        }
    }

}
