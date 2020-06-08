<?php

namespace backend\controllers;

use backend\models\DeliveryPerson;
use backend\models\DeliveryPersonSearch;
use common\models\DeliveryApi;
use common\models\WxMember;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DeliveryPersonController implements the CRUD actions for DeliveryPerson model.
 */
class DeliveryPersonController extends Controller
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
                    'delete' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all DeliveryPerson models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new DeliveryPersonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new DeliveryPerson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DeliveryPerson();
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            if ($model->load($postData)) {
                $saveResult = DeliveryApi::updateDeliveryPerson($postData['DeliveryPerson']);
                if ($saveResult['status'] == 'success') {
                    \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "外卖人员管理", \backend\models\ManagerLog::CREATE, "添加外卖配送人员");

                    return $this->redirect('index');
                }
                Yii::$app->getSession()->setFlash('error', $saveResult['msg']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DeliveryPerson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $deliveryPerson = DeliveryApi::getDeliveryPerson(['person_id' => $id]);
        if ($deliveryPerson['status'] == 'error') {
            return $this->redirect('index');
        }
        $model = new DeliveryPerson();
        $model->load(['DeliveryPerson' => $deliveryPerson['data']]);
        if (Yii::$app->request->isPost) {
            $postData                                = Yii::$app->request->post();
            $postData['DeliveryPerson']['person_id'] = $model->person_id;
            $saveResult                              = DeliveryApi::updateDeliveryPerson($postData['DeliveryPerson']);
            if ($saveResult['status'] == 'success') {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "外卖人员管理", \backend\models\ManagerLog::UPDATE, "编辑外卖配送人员");
                return $this->redirect('index');
            }
            Yii::$app->getSession()->setFlash('error', $saveResult['msg']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DeliveryPerson model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $delResult = DeliveryApi::delDeliveryPerson(['person_id' => $id]);
        if (!empty($delResult) && $delResult['status'] == 'success') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "外卖人员管理", \backend\models\ManagerLog::DELETE, "删除外卖配送人员");
        }
        return json_encode($delResult);
    }
    /**
     * @author  zmy
     */
    public function actionGetNameMob()
    {
        $name        = Yii::$app->request->post()['name'];
        $memberModel = WxMember::findOne(['name' => $name]);
        if (!$memberModel) {
            return false;
        }
        return json_encode(['realname' => $memberModel->name, 'mobile' => $memberModel->mobile, 'userid' => $memberModel->userid]);
    }
}
