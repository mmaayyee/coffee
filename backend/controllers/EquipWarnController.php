<?php

namespace backend\controllers;

use backend\models\EquipWarn;
use backend\models\EquipWarnSearch;
use common\models\WxMember;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipWarnController implements the CRUD actions for EquipWarn model.
 */
class EquipWarnController extends Controller
{
    /**
     * Lists all EquipWarn models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('异常报警设置列表')) {
            return $this->redirect(['site/login']);
        }

        $searchModel  = new EquipWarnSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipWarn model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看异常报警设置')) {
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);

        $model->report_setting = EquipWarn::reportSetting($model->report_setting);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new EquipWarn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加异常报警设置')) {
            return $this->redirect(['site/login']);
        }

        $model = new EquipWarn();
        $data  = Yii::$app->request->post('EquipWarn');
        if ($data) {
            if (isset($data['is_report']) && $data['is_report'] == 1 && isset($data['report_setting'])) {
                foreach ($data['report_setting'] as $key => $value) {
                    if (!isset($value['type']) || empty($value['type'])) {
                        $data['report_setting'] = json_encode($data['report_setting']);
                        $model->load(['EquipWarn' => $data]);
                        Yii::$app->getSession()->setFlash('error', '请选择上级的通知类型');
                        return $this->render('create', [
                            'model' => $model,
                        ]);
                    }
                }
                $data['report_setting'] = json_encode($data['report_setting']);
            } else {
                $data['report_num']     = 0;
                $data['report_setting'] = '';
            }
            $data['notice_type'] = implode(',', $data['notice_type']);
            $data['userid']      = implode(',', $data['userid']);
            $data['create_time'] = time();
            $transaction         = Yii::$app->db->beginTransaction();
        }

        if ($model->load(['EquipWarn' => $data]) && $model->save()) {
            $managerLogRes = \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "异常报警设置", \backend\models\ManagerLog::CREATE, $model->warn_content);
            if (!$managerLogRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipWarn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑异常报警设置')) {
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);

        $data = Yii::$app->request->post('EquipWarn');
        if ($data) {
            if ($data['is_report'] == 1) {
                // 验证上级的通知方式
                foreach ($data['report_setting'] as $key => $value) {
                    if (!isset($value['type']) || empty($value['type'])) {
                        $data['report_setting'] = json_encode($data['report_setting']);
                        $model->load(['EquipWarn' => $data]);
                        Yii::$app->getSession()->setFlash('error', '请选择通知类型');
                        return $this->render('update', [
                            'model' => $model,
                        ]);
                    }
                }
                $data['report_setting'] = json_encode($data['report_setting']);
            } else {
                $data['report_num']     = 0;
                $data['report_setting'] = '';
            }
            $data['notice_type'] = implode(',', $data['notice_type']);
            $data['userid']      = implode(',', $data['userid']);
            $transaction         = Yii::$app->db->beginTransaction();

            if ($model->load(['EquipWarn' => $data]) && $model->save()) {
                $managerLogRes = \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "异常报警设置", \backend\models\ManagerLog::UPDATE, $model->warn_content);
                if (!$managerLogRes) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $model->userid      = $model->userid ? explode(',', $model->userid) : '';
                $model->notice_type = $model->notice_type ? explode(',', $model->notice_type) : '';
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            $model->userid      = explode(',', $model->userid);
            $model->notice_type = explode(',', $model->notice_type);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipWarn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除异常报警设置')) {
            return $this->redirect(['site/login']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        if ($model->delete()) {
            $managerLogRes = \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "异常报警设置", \backend\models\ManagerLog::DELETE, "$model->warn_content");
            if (!$managerLogRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->redirect(['index']);
            }
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            Yii::$app->getSession()->setFlash('error', '异常报警设置删除失败');
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the EquipWarn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipWarn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipWarn::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 验证报警对象有几级上级
     * @return [type] [description]
     */
    public function actionWarnObj()
    {
        //获取传过来的职位id列表
        $position = Yii::$app->request->get('userid');
        $level    = Yii::$app->request->get('level', 0);
        if (!$position) {
            return 0;
        }

        // 根据职位获取用户id
        $userIdArr = WxMember::getUserIdArr(['position' => $position]);
        if (!$userIdArr) {
            return 0;
        }

        $levelName = $levelNum = [];
        foreach ($userIdArr as $userId) {
            $levelNum[] = WxMember::memberLevelNum($userId);
            if ($level) {
                $levelNameArr = WxMember::memberLevelName($userId);
                foreach ($levelNameArr as $key => $value) {
                    if (isset($levelName[$key])) {
                        if (!in_array($value, $levelName[$key])) {
                            $levelName[$key][] = $value;
                        }
                    } else {
                        $levelName[$key][] = $value;
                    }
                }
            }
        }
        return json_encode(['num' => max($levelNum) - 1, 'level_name' => $levelName]);
    }

}
