<?php

namespace backend\controllers;

use backend\models\EquipRfidCard;
use backend\models\EquipRfidCardAssoc;
use backend\models\EquipRfidCardSearch;
use common\models\Equipments;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipRfidCardController implements the CRUD actions for EquipRfidCard model.
 */
class EquipRfidCardController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['get-card-info'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'get-equip-id', 'check-open-door'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * 通过卡号获取绑卡人姓名
     */
    public function actionGetCardInfo()
    {
        $cardCode = Yii::$app->request->get('rfid_card_code');
        $userName = EquipRfidCard::getCardInfo($cardCode);
        return Json::encode($userName);
    }
    /**
     * Lists all EquipRfidCard models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('RFID门禁卡管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipRfidCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipRfidCard model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看RFID门禁卡')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new EquipRfidCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加RFID门禁卡')) {
            return $this->redirect(['site/login']);
        }
        $model = new EquipRfidCard();
        $param = Yii::$app->request->post("EquipRfidCard");
        if ($model->load(Yii::$app->request->post())) {
            if ($param) {
                $ret = EquipRfidCard::operationRfidCardData($param, $model);
            } else {
                $ret = '';
            }
            if (!$ret) {
                $model->ownedEquipCode = $param['ownedEquipCode'];
                $model->offEquipCode   = $param['offEquipCode'];
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "RFID门禁卡管理", \backend\models\ManagerLog::CREATE, "操作人:" . Yii::$app->user->identity->username);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipRfidCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑RFID门禁卡')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $param = Yii::$app->request->post("EquipRfidCard");
        if ($param) {
            $ret = EquipRfidCard::operationRfidCardData($param, $model, 'update');
        } else {
            $ret = '';
        }
        if ($model->load(Yii::$app->request->post()) && $ret) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "RFID门禁卡管理", \backend\models\ManagerLog::UPDATE, "操作人:" . Yii::$app->user->identity->username);
            return $this->redirect(['index']);
        } else {
            $rfidAssocArr          = EquipRfidCardAssoc::getRfidAssocArr($model->rfid_card_code);
            $rfidAssocCodeOff      = EquipRfidCardAssoc::getRfidAssocEquipCodeOff($model->rfid_card_code);
            $model->ownedEquipCode = $rfidAssocArr;
            $model->offEquipCode   = $rfidAssocCodeOff;
            $model->rfid_card_pass = '';
            $orgIdStr              = $model->org_id ? trim($model->org_id, ',') : '';
            $model->org_id         = explode(',', $orgIdStr);
            return $this->render('update', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Deletes an existing EquipRfidCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除RFID门禁卡')) {
            return $this->redirect(['site/login']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        if (!$model->delete()) {
            Yii::$app->getSession()->setFlash('error', '对不起，卡号关联表中删除失败.');
            $transaction->rollBack();
            return $this->redirect(['index']);
        }
        $rfidCodeArr = EquipRfidCardAssoc::find()->where(['rfid_card_code' => $model->rfid_card_code])->asArray()->one();
        if ($rfidCodeArr) {
            $retDeleteRfidAss = EquipRfidCard::deleteRfidAssoc($model);
            if (!$retDeleteRfidAss) {
                Yii::$app->getSession()->setFlash('error', '对不起，卡号关联表中删除失败.');
                $transaction->rollBack();
                return $this->redirect(['index']);
            }
        }

        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "RFID门禁卡管理", \backend\models\ManagerLog::DELETE, "操作人:" . Yii::$app->user->identity->username);
        //事务通过
        $transaction->commit();
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipRfidCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipRfidCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipRfidCard::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * ajax 通过设备ID 返回设备的数组 equip_code=>楼宇名称
     * param orgId 设备ID
     * [actionGetMember description]
     * @author  zmy
     * @version 2016-12-06
     * @return  [type]     [description]
     */
    public function actionGetEquipId()
    {
        $orgIdStr = Yii::$app->request->get("orgId");
        $orgId    = $orgIdStr ? explode(',', $orgIdStr) : '';
        // 获取equip_code=>'build_name'
        $equipArr = Equipments::getEquipArr($orgId);
        return json_encode($equipArr);
    }

    /**
     * 检测是否为正常链接开门
     * @author  zmy
     * @version 2017-03-03
     * @return  [type]     [description]
     */
    public function actionCheckOpenDoor()
    {
        $param      = Yii::$app->request->post("EquipRfidCard");
        $model      = new EquipRfidCard();
        $retMessage = '';
        if (isset($param)) {
            $equipmentCode         = $param['equipId'];
            $cardCode              = $param['rfid_card_code'];
            $endPassword           = sha1($cardCode . md5($param['rfid_card_pass']));
            $model->rfid_card_code = $cardCode;
            $model->rfid_card_pass = $param['rfid_card_pass'];
            $model->equipId        = $equipmentCode;

            if (!$param['equipId'] || !$param['rfid_card_pass'] || !$param['rfid_card_code']) {
                Yii::$app->getSession()->setFlash("error", "参数不可为空");
                return $this->render('check-door', [
                    'model'      => $model,
                    'retMessage' => $retMessage,
                ]);
            }

            // 调用erp测试环境线上接口返回方法 erpbacktest. erpback  http://erpback.coffee08.com/

            $retMessage = EquipRfidCard::retOpenRfidRes($equipmentCode, $cardCode, $endPassword);
            return $this->render('check-door', [
                'model'      => $model,
                'retMessage' => $retMessage,
            ]);
        }
        return $this->render('check-door', [
            'model'      => $model,
            'retMessage' => $retMessage,
        ]);
    }

}
