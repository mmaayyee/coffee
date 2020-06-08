<?php

namespace backend\controllers;

use backend\models\Region;
use backend\models\User;
use backend\models\UserSearch;
use common\models\OrderGoodsSearch;
use common\models\OrderInfo;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'interest'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('用户管理列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'regionArray'  => Region::getChild(Region::CHINA),
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionInterest()
    {
        if (!Yii::$app->user->can('红利收益统计')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new UserSearch();
        $params      = Yii::$app->request->queryParams;
        if (!isset($params['UserSearch'])) {
            $params['UserSearch'] = array();
        }

        $params['UserSearch'] = $params['UserSearch'] + array('is_master' => 1);
        $dataProvider         = $searchModel->search($params);

        return $this->render('interest', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'regionArray'  => Region::getChild(Region::CHINA),
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('用户管理列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new OrderGoodsSearch();
        $user        = $this->findModel($id);
        if ($user->is_master) {
            $params = array("OrderGoodsSearch" => array("user_id" => $id, 'source_type' => \common\models\OrderGoods::GROUP_COUPON, 'orderStatus' => OrderInfo::STATUS_PAYED));
            $groups = $searchModel->query($params)->getModels();
        } else {
            $groups = array();
        }
        return $this->render('view', [
            'model'  => $user,
            'groups' => $groups,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "用户管理", \backend\models\ManagerLog::CREATE, "添加用户");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "用户管理", \backend\models\ManagerLog::UPDATE, $model->mobile);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "用户管理", \backend\models\ManagerLog::DELETE, $model->mobile);
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
