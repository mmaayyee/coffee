<?php

namespace backend\controllers;

use backend\models\ScmUserSurplusMaterial;
use backend\models\ScmUserSurplusMaterialGram;
use backend\models\ScmUserSurplusMaterialSureRecord;
use backend\models\ScmUserSurplusMaterialSureRecordGram;
use backend\models\ScmUserSurplusMaterialSureRecordSearch;
use backend\models\ScmUserSurplusMaterialSureRecordGramSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\ScmMaterial;

/**
 * ScmUserSurplusMaterialSureRecordController implements the CRUD actions for ScmUserSurplusMaterialSureRecord model.
 */
class ScmUserSurplusMaterialSureRecordController extends Controller {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ScmUserSurplusMaterialSureRecord models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel  = new ScmUserSurplusMaterialSureRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $gramSearchModel = new ScmUserSurplusMaterialSureRecordGramSearch();
        $gramDataProvider = $gramSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'gramDataProvider' => $gramDataProvider
        ]);
    }

    /**
     * 剩余物料修改申请审核操作
     * @author  zgw
     * @version 2016-10-21
     * @param   integer     $id      申请id
     * @param   integer     $is_sure 是否通过 2-通过 3-不通过
     * @return                       保存结果返回对应页面
     */
    public function actionUpdate($id, $is_sure) {
        $model = $this->findModel($id);

        $model->is_sure   = $is_sure;
        $model->sure_time = time();
        $transaction      = Yii::$app->db->beginTransaction();
        $saveRes          = false;
        // 保存剩余物料申请记录
        if ($model->save() !== false) {
            $saveRes = true;
            // 只有通过才会修改剩余物料数据
            if ($model->is_sure == 2) {
                $saveRes = ScmUserSurplusMaterial::editSurplusMaterial($model->author, $model->material_id, $model->material_num, $model->add_reduce);
            }
        }
        if ($saveRes === false) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '更新申请表操作失败');
        } else {
            $transaction->commit();
        }
        return $this->redirect(['index', 'ScmUserSurplusMaterialSureRecordSearch[author]' => $model->author ,'ScmUserSurplusMaterialSureRecordGramSearch[author]' => $model->author]);

    }


    /**
     * 剩余散料物料修改申请审核操作
     * @author  wxl
     * @param   integer     $id      申请id
     * @param   integer     $is_sure 是否通过 2-通过 3-不通过
     * @return                       保存结果返回对应页面
     */
    public function actionUpdateGram($id, $is_sure) {
        $model = ScmUserSurplusMaterialSureRecordGram::findOne($id);

        $model->is_sure   = $is_sure;
        $model->sure_time = time();
        $transaction      = Yii::$app->db->beginTransaction();
        $saveRes          = false;
        // 保存剩余物料申请记录
        if ($model->save() !== false) {
            $saveRes = true;
            // 只有通过才会修改剩余物料数据
            if ($model->is_sure == 2) {
                $type = $model->add_reduce == '1' ? 'add' : 'del';
                $saveRes = ScmUserSurplusMaterialGram::editSurplusMaterialGram(0,$model->author,  $model->supplier_id, $model->material_type_id, $model->material_gram, $type);
            }
        }
        if ($saveRes === false) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '更新申请表操作失败');
        } else {
            $transaction->commit();
        }
        return $this->redirect(['index', 'ScmUserSurplusMaterialSureRecordSearch[author]' => $model->author,'ScmUserSurplusMaterialSureRecordGramSearch[author]' => $model->author]);

    }

    /**
     * Finds the ScmUserSurplusMaterialSureRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScmUserSurplusMaterialSureRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ScmUserSurplusMaterialSureRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
