<?php

namespace backend\controllers;

use backend\models\AuthItem;
use backend\models\AuthItemSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RoleController implements the CRUD actions for AuthItem model.
 */
class RoleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'init'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('角色管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * init rights
     */
    public function actionInit()
    {
        $model = new AuthItem();
        $model->init();
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model             = new AuthItem();
        $model->type       = 1;
        $model->created_at = time();
        $model->updated_at = time();
        $rightsList        = $model->getRightsString();
        $data              = Yii::$app->request->post();
        if ($data) {
            $model->load($data);
            $rightList = Yii::$app->request->post('rightlist');
            if ($rightList) {
                $model->saveDecription($rightList);
            }
            if ($model->save()) {
                $model->createRights($rightList);
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "角色管理", \backend\models\ManagerLog::CREATE, $model->name);
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
            'model'      => $model,
            'rightsList' => $rightsList,
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model             = $this->findModel($id);
        $model->updated_at = time();

        $rightsList = $model->getExistRightsString();
        $data       = Yii::$app->request->post();
        if ($data) {
            $model->load($data);
            $rightList = Yii::$app->request->post('rightlist');
            if ($rightList) {
                $model->saveDecription($rightList);
            }
            if ($model->save()) {
                $model->updateRights($rightList);
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "角色管理", \backend\models\ManagerLog::UPDATE, $model->name);
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model'      => $model,
            'rightsList' => $rightsList,
        ]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $auth  = Yii::$app->authManager;
        $role  = $auth->getRole($model->name);

        $auth->removeChildren($role);
        $roleUsers = \backend\models\Manager::getUsers($model->name);
        if (empty($roleUsers) && $model->delete()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "角色管理", \backend\models\ManagerLog::DELETE, $model->name);
            echo true;
        } else {
            echo false;
        }
        //return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
