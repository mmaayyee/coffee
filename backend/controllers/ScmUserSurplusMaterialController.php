<?php

namespace backend\controllers;

use backend\models\ScmUserSurplusMaterial;
use backend\models\ScmUserSurplusMaterialGramSearch;
use backend\models\ScmUserSurplusMaterialSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ScmUserSurplusMaterialController implements the CRUD actions for ScmUserSurplusMaterial model.
 */
class ScmUserSurplusMaterialController extends Controller
{

    /**
     * Lists all ScmUserSurplusMaterial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new ScmUserSurplusMaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $searchGramModel  = new ScmUserSurplusMaterialGramSearch();
        $dataGramProvider = $searchGramModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'      => $searchModel,
            'dataProvider'     => $dataProvider,
            'dataGramProvider' => $dataGramProvider,
        ]);
    }

    /**
     * Updates an existing ScmUserSurplusMaterial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "yun'wei", \backend\models\ManagerLog::CREATE, "添加库存信息");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the ScmUserSurplusMaterial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScmUserSurplusMaterial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmUserSurplusMaterial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
