<?php

namespace frontend\controllers;

use backend\models\AppVersionManagement;
use backend\models\DistributionTask;
use backend\models\DistributionWater;
use backend\models\EquipAcceptance;
use backend\models\EquipDebug;
use backend\models\EquipDebugSearch;
use backend\models\EquipDelivery;
use backend\models\EquipLightBoxDebug;
use backend\models\EquipSymptom;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\WxMember;
use frontend\models\Delivery;
use frontend\models\FrontendDistributionTask;
use frontend\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * EquipDeliveryController implements the CRUD actions for EquipDelivery model.  Base
 */
class EquipDeliveryController extends BaseController
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
                ],
            ],
        ];
    }

    /**
     * 投放待办的主页  $this->userinfo['userid']
     * @return mixed
     */
    public function actionPutToDoIndex()
    {
        $this->layout   = "main";
        $equipTaskArr   = EquipTask::find()->where(['task_type' => 2, 'process_result' => 1, 'assign_userid' => $this->userinfo['userid']])->asArray()->orderBy('create_time DESC')->all();
        $equipTaskCount = EquipTask::find()->where(['task_type' => 2, 'process_result' => 1, 'assign_userid' => $this->userinfo['userid']])->asArray()->count();
        return $this->render('upcoming', [
            'equipTaskArr'   => $equipTaskArr,
            'equipTaskCount' => $equipTaskCount,
        ]);
    }

    /**
     *  投放待办--主页面
     *  @param $buildId
     */
    public function actionPutClockIndex()
    {
        // 获取投放单id
        $relevantId = Yii::$app->request->get('relevant_id');
        //获取任务对象
        $taskModel = EquipTask::find()->where(['relevant_id' => $relevantId])->one();
        // 获取投放单对象
        $model = EquipDelivery::find()->where(['Id' => $relevantId])->one();
        //如果打过卡，则直接跳到绑定页面
        if ($taskModel->start_repair_time) {
            // 初始化产品组、灯箱调试项、app版本号变量
            $proGroupArr = $lightBoxDebugArr = $appVersionArr = [];
            //获取设备调试项
            $searchModel  = new EquipDebugSearch();
            $dataProvider = $searchModel->search(["EquipDebugSearch" => ["equip_type_id" => $model->equip_type_id]]);
            if (isset($model->equip_type_id)) {
                // 获取产品组
                $proGroupArr = Equipments::getProGroupArr($taskModel->delivery->equip_type_id);
                // 获取app版本号
                $appVersionArr = AppVersionManagement::find()->where(['equip_type_id' => $model->equip_type_id])->asArray()->one();
            }
            if ($model->is_lightbox > 0) {
                // 获取灯箱调试项
                $lightBoxDebugArr = EquipLightBoxDebug::find()->where(['light_box_id' => $model->is_lightbox, 'is_del' => EquipLightBoxDebug::DEL_NOT])->all();
            }
            // 获取该用户所在分公司
            $orgId = WxMember::getOrgId($this->userinfo['userid']);
            // 获取设备故障现象（验收失败的时候调用）
            $malfunctionArr = EquipSymptom::find()->where(['!=', 'is_del', '2'])->asArray()->all();
            return $this->render('equip-group', [
                'dataProvider'     => $dataProvider,
                'proGroupArr'      => $proGroupArr,
                'lightBoxDebugArr' => $lightBoxDebugArr,
                'taskId'           => $taskModel->id,
                'malfunctionArr'   => $malfunctionArr,
                'appVersionArr'    => $appVersionArr,
                'deliveryModel'    => $model,
                'orgId'            => $orgId,
            ]);
        }
        return $this->render('put-clock-index', [
            'model'     => $model,
            'taskModel' => $taskModel,
        ]);
    }

    /**
     *  投放待办任务打卡修改时间
     *  @param $delivery_id
     *
     */
    public function actionAcceptance($delivery_id, $start_latitude = '', $start_longitude = '', $start_address = '')
    {
        //点击打卡，修改任务中的接收时间
        $taskModel = EquipTask::findOne(['relevant_id' => $delivery_id]);
        $res       = true;
        if (!$taskModel->recive_time) {
            $taskModel->recive_time = time();
            $res                    = $taskModel->save();
        } else if ($taskModel->recive_time && !$taskModel->start_repair_time) {
            $taskModel->start_repair_time = time();
            $taskModel->start_latitude    = $start_latitude;
            $taskModel->start_longitude   = $start_longitude;
            $taskModel->start_address     = $start_address;
            $res                          = $taskModel->save();
        }
        if (!$res) {
            Yii::$app->getSession()->setFlash('error', '对不起，打卡记录时间失败.');
        }
        $this->redirect(['put-clock-index', 'relevant_id' => $delivery_id]);

    }

    /**
     *   获取接口中的产品分组的产品
     *   @return array $materialArray
     **/
    public function actionProGroupList()
    {
        $proGroupId = Yii::$app->request->get('proGroupId');
        return \common\models\Api::getProducts($proGroupId);
    }

    /**
     *   设备表中添加产品组ID 并 绑定楼宇ID （页面总逻辑处理 包括 验收成功或失败）
     *   @return array $materialArray
     **/
    public function actionBindAndProGroup($delivery_id)
    {
        $params = Yii::$app->request->post();
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        if ($params && $delivery_id) {
            $processBindArr = Delivery::deliveryAcceptance($params, $delivery_id, $this->userinfo['userid']);
            if ($processBindArr === false) {
                $transaction->rollBack();
                return $this->redirect(['equip-delivery/acceptance', 'delivery_id' => $delivery_id]);
            }

            //添加水单
            $build['build_id'] = EquipDelivery::getField('build_id', ['id' => $delivery_id]);
            $waterResult       = FrontendDistributionTask::createDistributionWater($params, $build, $delivery_id);
            if ($waterResult === false) {
                $transaction->rollBack();
                return $this->redirect(['equip-delivery/acceptance', 'delivery_id' => $delivery_id]);
            }
        } else {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '参数不可为空，请检测');
            return $this->redirect(['equip-delivery/acceptance', 'delivery_id' => $delivery_id]);
        }
        //事务通过
        $transaction->commit();
        return $this->redirect(['equip-delivery/put-to-do-index']);
    }

    /**
     *  投放(验收)记录
     */
    public function actionDeliveryRecord()
    {
        $this->layout = "main";
        $equipTaskArr = EquipTask::find()->where(['task_type' => 2, 'process_result' => [2, 3], 'assign_userid' => $this->userinfo['userid']])->orderBy('id DESC')->asArray()->all();
        $taskCount    = EquipTask::find()->where(['task_type' => 2, 'process_result' => [2, 3], 'assign_userid' => $this->userinfo['userid']])->count();
        return $this->render('delivery-record', [
            'equipTaskArr' => $equipTaskArr,
            'taskCount'    => $taskCount,
        ]);
    }

    /**
     *  投放验收记录信息
     *  @param
     *
     */
    public function actionDeliveryInfo()
    {
        $params           = Yii::$app->request->get();
        $deliveryArr      = EquipDelivery::find()->where(['Id' => $params['delivery_id']])->one();
        $acceptEptanceArr = EquipAcceptance::find()->where(['delivery_id' => $params['delivery_id']])->asArray()->one();
        $equipTaskArr     = EquipTask::find()->where(['relevant_id' => $params['delivery_id'], 'task_type' => EquipTask::MAINTENANCE_TASK])->asArray()->one();
        $waterInfo        = DistributionWater::getWaterInfoByDistributionId($params['delivery_id']);
        return $this->render('record-details', [
            'deliveryArr'      => $deliveryArr,
            'acceptEptanceArr' => $acceptEptanceArr,
            'recive_time'      => $params['recive_time'],
            'end_repair_time'  => $params['end_repair_time'],
            'repair_remark'    => $equipTaskArr['remark'],
            'waterInfo'        => $waterInfo,
        ]);
    }

    /**
     *  ajax获取设备验收详情
     */
    public function actionAjaxEquipAcceptance()
    {
        $deliveryId         = $_GET['delivery_id'];
        $detail             = $_GET['detail'];
        $acceptEptanceArr   = array();
        $acceptEptanceArray = EquipAcceptance::find()->where(['delivery_id' => $deliveryId])->asArray()->one();
        $valueArr           = array();
        if ($detail == 'equip_detail') {
            $debug_result = json_decode($acceptEptanceArray['debug_result']);
            foreach ($debug_result as $resultKey => $resultValue) {
                $valueArr = EquipDebug::find()->where(['Id' => $resultKey])->asArray()->one();

                $valueArr['ret_result'] = $resultValue;
                $acceptEptanceArr[]     = $valueArr;
            }
        } else {
            $light_box_result = json_decode($acceptEptanceArray['light_box_result']);
            foreach ($light_box_result as $resultKey => $resultValue) {
                $valueArr = EquipLightBoxDebug::find()->where(['Id' => $resultKey])->asArray()->one();

                $valueArr['ret_result'] = $resultValue;
                $acceptEptanceArr[]     = $valueArr;
            }
        }
        echo json_encode($acceptEptanceArr);
    }

    /**
     * ajax验证
     * @author  zgw
     * @version 2016-09-10
     * @return  [type]     [description]
     */
    public function actionAjaxVerifyFactoryCode($deliveryId, $factoryCode)
    {
        $code = EquipDelivery::verifyFactoryCode($deliveryId, $factoryCode);
        if (!empty($code['equipInfo'])) {
            unset($code['equipInfo']);
            unset($code['deliveryModel']);
        }
        echo json_encode($code);
    }

    /**
     * 修改设备产品组
     * @param $deliveryId
     * @param $factoryCode
     * @param $productGroup
     * @return bool
     */
    public function actionAjaxSaveProductGroup($deliveryId, $factoryCode, $productGroup)
    {
        // 验证出厂编号是否合法
        $verifyFactoryCodeResult = EquipDelivery::verifyFactoryCode($deliveryId, $factoryCode);
        if ($verifyFactoryCodeResult['result'] == false) {
            Yii::$app->getSession()->setFlash('error', $verifyFactoryCodeResult['msg']);
            return false;
        }
        $equipInfo     = $verifyFactoryCodeResult['equipInfo'];
        $deliveryModel = $verifyFactoryCodeResult['deliveryModel'];
        $equipRes      = Equipments::changeEquip($equipInfo, $deliveryModel->build_id, $productGroup);
        $param         = [
            'build_id'      => $deliveryModel->build_id,
            'assign_userid' => $this->userinfo['userid'],
        ];
        //生成配送任务
        $ret = DistributionTask::JudgmentTaskType($param);
        // 发送消息 参数：人员
        //楼宇ID加产品组,发生改变或第一次才发送消息
        $mdBuild = $deliveryModel->build_id . '&' . $productGroup;
        if (!Yii::$app->cache->get('build_pro_group_string') || Yii::$app->cache->get('build_pro_group_string') != $mdBuild) {
            Yii::$app->cache->set('build_pro_group_string', $mdBuild, 0);
            $taskType     = DistributionTask::DELIVERY;
            $assignUserid = trim($this->userinfo['userid']);
            DistributionTask::detailCreateWxInfo($taskType, $assignUserid);
        }
        echo $equipRes && $ret;
    }

    /**
     *  ajax验证卡号
     * @param $simNumber
     */
    public function actionAjaxVerifySimNumber($simNumber)
    {
        $exist = false;
        if (!empty($simNumber)) {
            $sim   = Equipments::getEquipmentsDetail('id', ['card_number' => $simNumber]);
            $exist = isset($sim['id']) ? true : false;
        }
        echo $exist;
    }
}
