<?php

namespace backend\controllers;

use backend\models\Manager;
use backend\models\ScmWarehouse;
use backend\models\ScmWarehouseSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ScmWarehouseController implements the CRUD actions for ScmWarehouse model.
 */
class ScmWarehouseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ScmWarehouse models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看库信息')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ScmWarehouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScmWarehouse model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看库信息')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     *  处理后的参数
     *  @param $params
     *  @return $param
     **/
    public static function getParamDetail($params)
    {
        //获取当前用户的分公司
        $userId = Yii::$app->user->identity->id;
        $branch = Manager::find()->where(['id' => $userId])->asArray()->one()['branch'];
        if ($branch == 1) {
            //总公司
            $param = $params;
        } else {
            //分公司
            $param = $params ? $params : '';
            if ($param) {
                $param['ScmWarehouse']['organization_id'] = $branch;
            }
        }
        return $param;
    }

    /**
     * Creates a new ScmWarehouse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加库信息')) {
            return $this->redirect(['site/login']);
        }
        $model = new ScmWarehouse();
        $param = self::getParamDetail(Yii::$app->request->post());

        if ($model->load($param) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "库信息管理", \backend\models\ManagerLog::CREATE, $model->name);

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmWarehouse model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑库信息')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $param = self::getParamDetail(Yii::$app->request->post());
        if ($model->load($param) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "库信息管理", \backend\models\ManagerLog::UPDATE, $model->name);

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ScmWarehouse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除库信息')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "库信息管理", \backend\models\ManagerLog::DELETE, $model->name);

        return $this->redirect(['index']);
    }

    /**
     * Finds the ScmWarehouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScmWarehouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmWarehouse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
