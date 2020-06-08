<?php

namespace frontend\controllers;

use backend\models\EquipDelivery;
use backend\models\EquipDeliveryRead;
use frontend\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

// $this->userinfo['userid']

class EquipDeliveryNoteController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'order-success'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    // 'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     *  预投放通知 主页 $this->userinfo['userid']  'read_status'=>0,
     */
    public function actionPreDelivery()
    {
        $equipDeliveryReadArr = EquipDeliveryRead::find()->joinWith('deliver')->where(['read_type' => 0, 'userId' => $this->userinfo['userid']])->asArray()->orderBy('read_status,read_time DESC')->all();
        return $this->render('pre-delivery', [
            'deliveryArr' => $equipDeliveryReadArr,
        ]);
    }

    /**
     *  预投放通知的详情页
     *  @param id
     */
    public function actionPreDeliveryDetail($id)
    {
        // 获取投放单详情
        $deliveryInfo = EquipDelivery::find()->where(['Id' => $id])->one();
        // 获取相关人员反馈想详情
        $deliveryReadInfo = EquipDeliveryRead::getDetail('read_feedback', ['delivery_id' => $id, 'userId' => $this->userinfo['userid'], 'read_type' => 0]);
        if (!$deliveryInfo || !$deliveryReadInfo) {
            return $this->render('/site/error', ['message' => '请求数据不存在，或者您没有此权限查看']);
        }
        return $this->render('pre-delivery-detail', [
            'deliveryInfo' => $deliveryInfo,
            'readFeedback' => $deliveryReadInfo->read_feedback,
        ]);
    }

    /**
     *  修改预投放相关人员阅读表
     */
    public function actionAddDeliveryRead()
    {
        $params = Yii::$app->request->get();
        $model  = EquipDeliveryRead::find()->where(['delivery_id' => $params['deliveryId'], "userId" => $this->userinfo['userid'], 'read_type' => EquipDeliveryRead::PRE_READ])->one();
        if ($model) {
            $model->read_time     = time();
            $model->read_feedback = $params['read_feedback'];
            $model->read_status   = 1;
            if ($model->save()) {
                $this->redirect(['equip-delivery-note/pre-delivery']);
            } else {
                Yii::$app->getSession()->setFlash("error", "对不起，阅读反馈失败.");
                $this->redirect(['equip-delivery-note/pre-delivery-detail', 'id' => $_GET['id']]);
            }
        } else {
            Yii::$app->getSession()->setFlash("error", "对不起，阅读反馈失败.");
            $this->redirect(['equip-delivery-note/pre-delivery-detail', 'id' => $_GET['id']]);
        }
    }

    /**
     *  投放单模块 信息主页 'read_status'=>0, $this->userinfo['userid']
     */
    public function actionDeliveryIndex()
    {
        $equipDeliveryReadArr = EquipDeliveryRead::find()->joinWith('deliver')->where(['read_type' => 1, 'userId' => $this->userinfo['userid']])->asArray()->orderBy('read_status,read_time DESC')->asArray()->orderby('equip_delivery.delivery_time desc')->limit(20)->all();
        return $this->render('delivery-index', [
            'deliveryArr' => $equipDeliveryReadArr,
        ]);
    }

    /**
     *  投放单模块信息 详情页
     */
    public function actionDeliveryInfoDetail($id)
    {

        $deliveryInfo = EquipDelivery::find()->where(['Id' => $id])->one();
        // 获取相关人员反馈想详情
        $deliveryReadInfo = EquipDeliveryRead::getDetail('read_feedback', ['delivery_id' => $id, 'userId' => $this->userinfo['userid'], 'read_type' => 1]);
        if (!$deliveryInfo || !$deliveryReadInfo) {
            return $this->render('/site/error', ['message' => '请求数据不存在，或者您没有此权限查看']);
        }
        return $this->render('delivery-info-detail', [
            'deliveryInfo' => $deliveryInfo,
            'readFeedback' => $deliveryReadInfo->read_feedback,

        ]);
    }

    /**
     *  修改投放相关人员阅读表 投放单状态
     *
     */
    public function actionUpdateDeliveryRead()
    {
        $params               = Yii::$app->request->get();
        $model                = EquipDeliveryRead::find()->where(['delivery_id' => $params['deliveryId'], "userId" => $this->userinfo['userid'], 'read_type' => EquipDeliveryRead::READ_TYPE])->one();
        $model->read_time     = time();
        $model->read_feedback = $params['read_feedback'];
        $model->read_status   = 1;
        if ($model->save()) {
            $this->redirect(['equip-delivery-note/delivery-index']);
        } else {
            echo '对不起，阅读反馈失败.';exit();
        }
    }

}
