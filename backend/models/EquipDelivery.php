<?php

namespace backend\models;

use backend\models\BuildType;
use backend\models\EquipDelivery;
use backend\models\Manager;
use backend\models\Organization;
use backend\models\ScmEquipType;
use common\helpers\WXApi\WxMessage;
use common\models\Api;
use common\models\Building;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "equip_delivery".
 *
 * @property string $Id
 * @property string $build_id
 * @property string $equip_type_id
 * @property integer $delivery_result
 * @property string $delivery_time
 * @property string $sales_person
 * @property integer $delivery_status
 * @property string $reason
 * @property string $remark
 * @property string $create_time
 * @property string $delivery_number
 * @property integer $is_ammeter
 * @property integer $is_lightbox
 * @property string $special_require
 * @property string $update_time
 * @property string $grounds_refusal
 *
 * @property Building $build
 * @property EquipDeliveryDebugAssoc[] $equipDeliveryDebugAssocs
 * @property EquipDeliveryLightBoxAssoc[] $equipDeliveryLightBoxAssocs
 */
class EquipDelivery extends \yii\db\ActiveRecord
{
    public $original_build_id;

    public $orgId; //分公司ID

    public $orgType; //机构类型

    //投放状态 0-待审批 1-投放中 2-投放成功已运营 3-驳回 4-终止 5-投放成功未运营 6-投放失败
    /**
     *   投放状态
     **/

    /*待审批*/
    const PENDING = 0;

    /*投放中*/
    const TRAFFICKING_IN = 1;

    /*投放成功已运营*/
    const TRAFFICK_SUCCESS = 2;

    /*驳回*/
    const TURN_DOWN = 3;

    /*终止*/
    const TERMINATION = 4;

    /*投放成功未运营*/
    const UN_TRAFFICK_SUCCESS = 5;

    /*投放失败*/
    const DELIVERY_FAILURE = 6;

    // 投放结果（运营状态)
    /*商业运营*/
    const COMMERCIAL_OPERATION = 0;
    /*未运营*/
    const NO_OPERATION = 1;
    /*内部使用*/
    const INTERNAL_USE = 2;
    /*测试使用*/
    const USE_TEST = 3;
    /*临时运营*/
    const TEMPORARY_OPERATION = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_delivery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'equip_type_id', 'delivery_time'], 'required'],
            [['build_id', 'equip_type_id', 'delivery_result', 'delivery_status', 'create_time', 'delivery_number', 'update_time', 'is_ammeter', 'is_lightbox'], 'integer'],
            [['reason', 'remark', 'special_require', 'grounds_refusal'], 'string', 'max' => 255],
            [['sales_person', 'voice_type'], 'string', 'max' => 50],
            [['orgId', 'orgType'], 'safe'],
            ['build_id', 'verifyBuild'],
        ];
    }

    public function verifyBuild($attr, $params)
    {
        $query = self::find()->where([
            'build_id'        => $this->$attr,
            'delivery_status' => [self::PENDING, self::TRAFFICKING_IN],
        ]);
        if ($this->Id) {
            $query->andWhere(['!=', 'Id', $this->Id]);
        }
        $isexists = $query->one();
        if (!$isexists) {
            return true;
        } else {
            $this->addError("build_id", "该点位有未完成的投放单，请完成后再创建。");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id'              => 'ID',
            'build_id'        => '楼宇',
            'equip_type_id'   => '设备类型',
            'delivery_result' => '投放结果',
            'delivery_time'   => '投放时间',
            'sales_person'    => '销售责任人',
            'delivery_status' => '投放状态',
            'reason'          => '原因',
            'remark'          => '备注',
            'create_time'     => '创建时间',
            'delivery_number' => '投放数量',
            'is_ammeter'      => '是否需要电表',
            'is_lightbox'     => '是否外包灯箱',
            'special_require' => '特殊要求',
            'update_time'     => '修改时间',
            'grounds_refusal' => '驳回理由',
            'voice_type'      => '大屏广告声音',
            'org_id'          => '分公司',
            'orgType'         => '机构类型',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Manager::className(), ['username' => 'sales_person']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipType()
    {
        return $this->hasOne(ScmEquipType::className(), ['id' => 'equip_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLightBox()
    {
        return $this->hasOne(EquipLightBox::className(), ['id' => 'is_lightbox']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipDeliveryDebugAssocs()
    {
        return $this->hasMany(EquipDeliveryDebugAssoc::className(), ['equip_delivery_id' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipDeliveryLightBoxAssocs()
    {
        return $this->hasMany(EquipDeliveryLightBoxAssoc::className(), ['equip_delivery_id' => 'Id']);
    }

    /**
     *   获取设备类型数组
     *   @return array $equipTypeModelArray
     **/
    public function getEquipTypeModelArray()
    {
        $equipTypeModels         = ScmEquipType::find()->select(['id', 'model'])->asArray()->all();
        $equipTypeModelArray     = array();
        $equipTypeModelArray[''] = '请选择';
        foreach ($equipTypeModels as $equipTypeModel) {
            $equipTypeModelArray[$equipTypeModel['id']] = $equipTypeModel['model'];
        }
        return $equipTypeModelArray;
    }

    /**
     *   按条件获取楼宇状态为1的楼宇数组
     *   @return array $cupstyleArray
     **/
    public function getBuildNameArray($type = 1)
    {
        $orgId = Manager::getManagerBranchID();
        //分公司为全国，则显示所有的公司
        $query = Building::find()
            ->select(['id', 'name', 'org_id']);
        if ($type == 1) {
            $query->where(['build_status' => [Building::PRE_DELIVERY, Building::SERVED]]);
        }
        if ($orgId != '1') {
            $query->andWhere([
                'org_id' => $orgId,
            ]);
        }
        $builds         = $query->asArray()->all();
        $orgIdNameList  = Organization::getBranchArray(2);
        $buildArray     = array();
        $buildArray[''] = '请选择';
        foreach ($builds as $build) {
            $orgName                  = empty($orgIdNameList[$build['org_id']]) ? '' : $orgIdNameList[$build['org_id']];
            $buildArray[$build['id']] = $build['name'] . "--" . $orgName;
        }
        return $buildArray;
    }

    /**
     * 获取音量数组
     * @param  $voiceTypeArr
     */
    public static function getVoiceTypeArr()
    {
        $voiceTypeArr = array(
            '低音' => '低音',
            '中音' => '中音',
            '高音' => '高音',
        );
        return $voiceTypeArr;
    }

    /**
     *   定义是否需要
     *   @return $ammeterArr
     **/
    public function getIsNeedArr()
    {
        $ammeterArr = array(
            "1" => "是",
            "0" => "否",
        );
        return $ammeterArr;
    }
    /**
     * 获取灯箱列表
     * @author  zgw
     * @version 2016-09-26
     * @return  [type]     [description]
     */
    public static function getLightBoxArr()
    {
        $lightBoxArr = \backend\models\EquipLightBox::getLightBoxIdNameArr();
        unset($lightBoxArr['']);
        $lightBoxArr[0] = '否';
        ksort($lightBoxArr);
        return $lightBoxArr;
    }
    /**
     * 获取投放状态数组 equipStatusArr
     * @return array
     **/
    public function equipDeliveryStatusArray()
    {
        $equipDeliveryStatusArray = array(
            ''                        => '请选择',
            self::PENDING             => '待审批',
            self::TRAFFICKING_IN      => '投放中',
            self::TRAFFICK_SUCCESS    => '投放成功已运营',
            self::TURN_DOWN           => '驳回',
            self::TERMINATION         => '终止',
            self::UN_TRAFFICK_SUCCESS => '投放成功未运营',
            self::DELIVERY_FAILURE    => '投放失败',
        );
        return $equipDeliveryStatusArray;
    }

    /**
     * 获取投放状态
     * @return $delivery_status
     */
    public function getDeliveryStatus()
    {
        $equipDeliveryStatusArray = $this->equipDeliveryStatusArray();
        return $equipDeliveryStatusArray[$this->delivery_status];
    }

    /**
     * 获取投放状态数组 equipStatusArr
     * @return array
     **/
    public function deliveryResultArray()
    {
        $deliveryResultArray = array(
            ''                         => '请选择',
            self::COMMERCIAL_OPERATION => '商业运营',
            self::TEMPORARY_OPERATION  => '临时运营',
            self::NO_OPERATION         => '未运营',
            self::INTERNAL_USE         => '内部使用',
            self::USE_TEST             => '测试使用',
        );
        return $deliveryResultArray;
    }

    /**
     *  修改楼宇的状态
     *  @param $apram
     *  @return $retBuildModel
     **/
    public static function updateBuildStats($build_id, $buildStatus = Building::TRAFFICKING_IN)
    {
        $buildModel               = Building::findOne($build_id);
        $buildModel->build_status = $buildStatus;
        $retBuildModel            = $buildModel->save();
        // 同步修改楼宇状态
        $syncBuildRes = Building::syncBuild($buildModel);
        return ($retBuildModel && $syncBuildRes);
    }

    /**
     * 修改投放单 及 楼宇的部分数据
     * @param $param
     **/
    public static function updateDelivery($model, $param, $transaction)
    {
        $model->load(Yii::$app->request->post());
        if ($param['original_build_id'] != $param['build_id']) {
            // 将原楼宇状态改为未投放
            $buildModel               = Building::findOne($param['original_build_id']);
            $buildModel->build_status = Building::PRE_DELIVERY;
            $retStatsBuild            = $buildModel->save();
            // 将修改后的楼宇状态改为已投放
            $newBuildModel               = Building::findOne($param['build_id']);
            $newBuildModel->build_status = Building::TRAFFICKING_IN;
            $retStatsNewBuild            = $newBuildModel->save();
            // 同步修改楼宇状态
            $syncOldBuildRes = Building::syncBuild($buildModel);
            $syncNewBuildRes = Building::syncBuild($newBuildModel);
            if (!$retStatsBuild || !$syncOldBuildRes || !$syncNewBuildRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash("error", "对不起，修改楼宇状态失败");
                return [$model, 1];
            }
        }
        $model->delivery_time   = strtotime($param['delivery_time']);
        $model->update_time     = time();
        $model->delivery_status = EquipDelivery::PENDING;
        $model->grounds_refusal = '';
        if (!($model->validate() && $model->save())) {
            $transaction->rollBack();
            return [$model, 1];
        }
        return [$model, 0];
    }

    /**
     *  插入相关人员阅读表
     *  @param $memberNameArr, $read_type='0'
     *  @return $retRead
     **/
    public static function createDeliveryRead($memberNameArr, $delivery_id, $read_type = '0')
    {
        $res = true;
        foreach ($memberNameArr as $key => $value) {
            $equipDeliveryReadModel              = new EquipDeliveryRead();
            $equipDeliveryReadModel->delivery_id = $delivery_id;
            $equipDeliveryReadModel->userId      = $value;
            $equipDeliveryReadModel->read_type   = $read_type;
            if (!$equipDeliveryReadModel->save()) {
                $res = false;
            }
        }
        return $res;
    }

    /**
     *  微信消息发送的人
     * @param  $org_id 分公司ID
     * @return $manager 分公司下所属人员
     */
    // 投放商时候，应该检验此投放商是否有该分公司
    public static function getWxMemberArr($org_id)
    {
        $orgCondition = ['org_id' => $org_id];
        //查询该公司是否是代维护,代维护查出父公司下的相关人员
        $organizationInfo = Api::getOrgDetailsModel(['org_id' => $org_id]);
        //代维护
        if (isset($organizationInfo['is_replace_maintain']) && $organizationInfo['is_replace_maintain'] == 2) {
            $parentId     = $organizationInfo['parent_id'];
            $orgCondition = ['in', 'org_id', [$org_id, $parentId]];
        }

        $where = ['or',
            ['and',
                $orgCondition,
                ['in', 'position', [WxMember::EQUIP_RESPONSIBLE,
                    WxMember::DISTRIBUTION_RESPONSIBLE, WxMember::SALE_MEMBER]],
            ],
            ['in', 'position', [WxMember::EQUIP_MANAGER, WxMember::DISTRIBUTION_MANAGER, WxMember::SUPPLY_CHAIN_MANAGER, WxMember::EQUIP_ASSISTANT]],
        ];
        $manager = WxMember::find()->where($where)->andFilterWhere(['is_del' => WxMember::DEL_NO])->asArray()->all();
        return $manager;
    }

    /**
     *  获取微信人数组
     *  @param $build_id
     *  @return array
     **/
    public static function getMemberNameArr($build_id)
    {
        $buildModel    = Building::findOne($build_id);
        $memberArr     = self::getWxMemberArr($buildModel->org_id);
        $memberNameArr = array();
        foreach ($memberArr as $key => $value) {
            $memberNameArr[] = $value['userid'];
        }
        return $memberNameArr;
    }

    /**
     *  发送微信消息
     *  @param $param
     *  @return $ret
     **/
    public static function sendWxInfo($param = '', $buildId = '', $model = '', $retTaskId = '')
    {
        $agentId = Yii::$app->params['equip_agentid'];
        $arr     = array('msgtype' => 'text', 'text' => array('content' => ''), 'agentid' => $agentId);
        // 获取楼宇信息
        if ($buildId) {
            $buildModel = Building::findOne($buildId);
        } else {
            $buildModel = Building::findOne($param['build_id']);

            $arr['text']['content'] = "<a href=\"" . Yii::$app->params['frontend'] . "equip-delivery-note/pre-delivery-detail?agentId=" . $agentId . "&id=" . $model->Id . "\">预投放通知：投放楼宇：" . $buildModel->name . '; 投放地址：' . $buildModel->address . "; 投放时间：" . $param['delivery_time'] . "</a>";
        }
        $memberArr = self::getWxMemberArr($buildModel->org_id);
        $WxMessage = new WxMessage();
        //不同的角色，发送到不同应用；不同的角色，接收信息不同（设备主管、经理，配送主管、经理 收到加链接的消息。）
        foreach ($memberArr as $key => $value) {
            $arr['touser'] = $value['userid'];
            if ($buildId) {
                if ($value['position'] == WxMember::DISTRIBUTION_RESPONSIBLE) {
                    $arr['text']['content'] = '投放单通知：投放楼宇：' . $buildModel->name . '; 投放地址：' . $buildModel->address . '; 投放时间：' . date("Y-m-d", $model->delivery_time) . " -->>><a href=\"" . Yii::$app->params['frontend'] . "equip-task/assigned-personnel?agentId=" . $agentId . "&id=" . $retTaskId . "\">点击分配投放验收任务</a>";
                } else {
                    $arr['text']['content'] = "<a href=\"" . Yii::$app->params['frontend'] . "equip-delivery-note/delivery-info-detail?agentId=" . $agentId . "&id=" . $model->Id . "\">投放单通知：投放楼宇：" . $buildModel->name . "; 投放地址：" . $buildModel->address . "; 投放时间：" . date("Y-m-d", $model->delivery_time) . "</a>";
                }
            }
            $WxMessage->sendMessage($arr, $agentId);
        }
        return true;
    }

    /**
     * 修改投放单信息(投放验收)
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $params         [description]
     * @param   [type]     $deliveryId     [description]
     * @param   [type]     $deliveryStatus [description]
     * @return  [type]                     [description]
     */
    public static function changeDelivery($params, $deliveryModel)
    {
        //更改投放单中的数据
        $deliveryModel->delivery_result = $params['delivery_result'] !== '' ? $params['delivery_result'] : self::NO_OPERATION;
        $deliveryModel->reason          = $params['reason'] ? $params['reason'] : ''; //原因
        $deliveryModel->remark          = $params['remark'] ? $params['remark'] : ''; //备注
        if ($deliveryModel->save() === false) {
            Yii::$app->getSession()->setFlash('error', '投放单信息修改失败');
            return false;
        }
        return true;
    }
    /**
     * 投放申请审核成功添加投放验收任务
     * @author  zgw
     * @version 2016-09-10
     * @param   [type]     $equipDeliveryModel [description]
     * @return  [type]                         [description]
     */
    public static function createTaskInfo($equipDeliveryModel)
    {
        $buildTypeName = isset($equipDeliveryModel->build->build_type) ? BuildType::getBuildType($equipDeliveryModel->build->build_type) : '';
        $buildName     = isset($equipDeliveryModel->build->name) ? $equipDeliveryModel->build->name : '';
        $buildAddr     = isset($equipDeliveryModel->build->address) ? $equipDeliveryModel->build->address : '';

        $content = '楼宇类别：' . $buildTypeName . '<br/>' . '楼宇名称：' . $buildName . '<br/> 楼宇地址：' . $buildAddr . '<br/>' . '发起时间：' . date("Y-m-d", $equipDeliveryModel->create_time) . '<br/>' . '投放时间：' . date("Y-m-d", $equipDeliveryModel->delivery_time) . '<br/>' . '销售负责人：' . $equipDeliveryModel->sales_person . '<br/>' . '设备数量：' . $equipDeliveryModel->delivery_number . '台';

        $equipTask = EquipTask::getEquipTaskByDeliveryId($equipDeliveryModel->Id);
        //插入设备任务表操作
        $equipTaskModel              = $equipTask ? $equipTask : new EquipTask();
        $equipTaskModel->build_id    = $equipDeliveryModel->build_id;
        $equipTaskModel->task_type   = EquipTask::TRAFFICKING_TASK;
        $equipTaskModel->relevant_id = $equipDeliveryModel->Id;
        $equipTaskModel->content     = $content;
        $equipTaskModel->create_user = Yii::$app->user->identity->realname;
        $equipTaskModel->create_time = time();
        $equipTaskModel->update_time = time();
        $ret                         = $equipTaskModel->save();

        return array('ret' => $ret, 'equipTaskId' => $equipTaskModel->id);
    }

    /**
     * 获取指定字段的值
     * @author  zgw
     * @version 2016-09-11
     * @param   [type]     $filed [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getField($filed, $where)
    {
        $deliveryInfo = self::find()->select($filed)->where($where)->one();
        return isset($deliveryInfo->$filed) ? $deliveryInfo->$filed : '';
    }

    /**
     * 投放任务验证出厂编号是否正确
     * @author zhenggangwei
     * @date   2019-01-28
     * @param  integer     $deliveryId  投放单ID
     * @param  string      $factoryCode 出厂编号
     * @return array                    验证结果
     */
    public static function verifyFactoryCode($deliveryId, $factoryCode)
    {
        $code      = '';
        $equipCode = '';
        // 获取投放单的数据
        $deliveryModel = self::findOne($deliveryId);
        if (empty($deliveryModel->build_id)) {
            return ['result' => false, 'msg' => '投放数据异常'];
        }
        //判断楼宇是否已经绑定
        $status = Building::getField('build_status', ['id' => $deliveryModel->build_id]);
        //已绑定
        if ($status == Building::SERVED) {
            return ['result' => false, 'msg' => '楼宇已绑定设备'];
        }
        //根据出厂编号获取设备信息（库存状态的设备）
        $equipInfo = Equipments::getEquipBuildDetail('*', ['factory_code' => $factoryCode]);
        if (!$equipInfo) {
            //设备编号不存在
            return ['result' => false, 'msg' => '出厂编号不存在'];
        }
        if ($equipInfo->equip_type_id != $deliveryModel->equip_type_id) {
            return ['result' => false, 'msg' => '设备出厂编号类型不符'];
        } elseif ($equipInfo->operation_status != Equipments::PRE_SELIVERY) {
            return ['result' => false, 'msg' => '请输入设备库存状态的出厂编号'];
        } else {
            return ['result' => true, 'equip_code' => $equipInfo->equip_code, 'equipInfo' => $equipInfo, 'deliveryModel' => $deliveryModel];
        }
    }

}
