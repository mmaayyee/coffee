<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\models\Manager;
use backend\models\ManagerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\WxMember;

/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class ManagerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'get-name-mob'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],              

        ];
    }

    /**
     * Lists all Manager models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('管理员管理')){
            return $this->redirect(['site/login']);
        }       
        
        $searchModel = new ManagerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Manager model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Manager model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Manager();
        $model->setScenario("create");
        $model->generateAuthKey(); 
        $model->setPassword('');
        $managerData = Yii::$app->request->post('Manager');
        $userid = $managerData['userid'];
        $model->branch = WxMember::getOrgId($userid);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            $model->setPassword($model->password);
            $model->generateAuthKey();   
            $model->save();
            $model->resetAuth();
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "管理员管理", \backend\models\ManagerLog::CREATE, $model->realname);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Manager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $managerData = Yii::$app->request->post('Manager');
        $userid = $managerData['userid'];
        $model->branch = WxMember::getOrgId($userid);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(!empty($_POST['Manager']['password'])){
                $model->setPassword($_POST['Manager']['password']);
                $model->generateAuthKey();   
                $model->save();  
                
            }
            $model->resetAuth();
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "管理员管理", \backend\models\ManagerLog::UPDATE, $model->realname);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Manager model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "管理员管理", \backend\models\ManagerLog::DELETE, $model->realname);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Manager model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Manager the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Manager::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @author  zmy
     */
    public function actionGetNameMob() {
        $userid = Yii::$app->request->post()['userid'];
        $memberModel = WxMember::findOne(['userid'=>$userid]);
        if(!$memberModel){
            return false;
        }
        return json_encode(['realname'=>$memberModel->name, 'mobile'=>$memberModel->mobile, 'email'=>$memberModel->email]);
    }
}
