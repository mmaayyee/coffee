<?php

namespace backend\controllers;

use backend\models\BuildType;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BuildTypeController implements the CRUD actions for BuildType model.
 */
class BuildTypeController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
     * Lists all BuildType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('渠道类型列表查看')) {
            return $this->redirect(['site/login']);
        }
        $buildTypeName = Yii::$app->request->get('build-type-name');
        $buildTypeCode = Yii::$app->request->get('build-type-code');
        if ($buildTypeCode) {
            $buildTypeList = Json::decode(Api::getBuildTypeCode($buildTypeCode), 1);
        } else {
            $buildTypeList = Json::decode(Api::getBuildType($buildTypeName), 1);
        }
        return $this->render('index', [
            'buildTypeList' => $buildTypeList,
            'buildTypeName' => $buildTypeName,
            'buildTypeCode' => $buildTypeCode,
        ]);
    }

    /**
     * Displays a single BuildType model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看渠道类型')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BuildType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加渠道类型')) {
            return $this->redirect(['site/login']);
        }
        $model     = new BuildType();
        $model->id = 0;
        $data      = Yii::$app->request->post('BuildType');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saveRes = Api::saveBuildType($data);
            if (!is_array($saveRes)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "渠道类型管理", \backend\models\ManagerLog::CREATE, "添加渠道类型");
                return $this->redirect(['index']);
            } else {
                $errors = isset($saveRes['type_name'][0]) ? $saveRes['type_name'][0] : $saveRes['type_code'][0];
                Yii::$app->getSession()->setFlash('error', $errors);
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
     * Updates an existing BuildType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑渠道类型')) {
            return $this->redirect(['site/login']);
        }
        $model         = new BuildType();
        $buildTypeInfo = Json::decode(Api::getBuildTypeInfo($id), 1);
        if (!$buildTypeInfo) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model->type_name = $buildTypeInfo['type_name'];
        $model->type_code = $buildTypeInfo['type_code'];
        $model->id        = $id;
        $data             = Yii::$app->request->post('BuildType');
        if ($model->load(Yii::$app->request->post())) {
            $saveRes = Api::saveBuildType($data);
            if (!is_array($saveRes)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "渠道类型管理", \backend\models\ManagerLog::UPDATE, "编辑渠道类型");
                return $this->redirect(['index']);
            } else {
                $errors = $saveRes['type_name'] != '' ? $saveRes['type_name'] : $saveRes['type_code'];
                Yii::$app->getSession()->setFlash('error', $errors);
                return $this->render('update', [
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
     * Deletes an existing BuildType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除渠道类型')) {
            return $this->redirect(['site/login']);
        }

        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "渠道类型管理", \backend\models\ManagerLog::DELETE, "删除渠道类型");

        return $this->redirect(['index']);
    }

    /**
     * Finds the BuildType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BuildType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BuildType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
