<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\Organization;
use backend\models\OrganizationSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrganizationController implements the CRUD actions for Organization model.
 */
class OrganizationController extends Controller
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
     * Lists all Organization models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('机构列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new OrganizationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organization model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看机构')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Organization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加机构')) {
            return $this->redirect(['site/login']);
        }
        $model = new Organization();
        $model->setScenario('create');
        $post = Yii::$app->request->post();

        if (!isset($post['Organization']['parent_id'])) {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
        //查询机构路径
        $parentPath                          = Organization::getField('parent_path', ['org_id' => $post['Organization']['parent_id']]);
        $post['Organization']['org_number']  = substr(time() . rand(1000, 9999), -8);
        $post['Organization']['parent_path'] = $parentPath;
        //$post['Organization']['is_replace_maintain'] = 1;

        if ($model->load($post) && $model->syncErpAddOrg($post)) {
            $logResult = ManagerLog::saveLog(Yii::$app->user->id, "机构管理", ManagerLog::CREATE, $model->org_name);
            if (!$logResult) {
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Organization model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑机构')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->setScenario('update');
        $post = Yii::$app->request->post();
        if ($post) {
            $parentPath = Organization::getField('parent_path', ['org_id' => $post['Organization']['parent_id']]);
            if ($id != 1) {
                $parentPath = $parentPath . $id . '-';
            }
            $post['Organization']['parent_path'] = $parentPath;
            $post['Organization']['org_id']      = $id;
        }
        if ($model->load($post) && $model->validate() && $model->syncErpUpdateOrg($post)) {
            $logResult = ManagerLog::saveLog(Yii::$app->user->id, "机构管理", ManagerLog::UPDATE, $model->org_name);
            if (!$logResult) {
                if ($model->org_id == 1) {
                    $model->parent_id = 1;
                }
                $model->isNewRecord = 0;
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            return $this->redirect(['view', 'id' => $id]);
        } else {
            if ($model->org_id == 1) {
                $model->parent_id = 1;
            }
            $model->isNewRecord = 0;
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Organization model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除机构')) {
            return $this->redirect(['site/login']);
        }
        $this->findModel($id)->delete();
        ManagerLog::saveLog(Yii::$app->user->id, "机构管理", ManagerLog::DELETE, $id);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Organization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Organization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Organization::findModel($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
