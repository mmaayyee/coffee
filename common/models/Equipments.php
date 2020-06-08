<?php

namespace common\models;

use backend\models\DistributionUser;
use backend\models\EquipLightBox;
use backend\models\EquipLog;
use backend\models\EquipWarn;
use backend\models\MaterialSafeValue;
use backend\models\Organization;
use backend\models\ProductMaterialStockAssoc;
use backend\models\ScmEquipType;
use backend\models\ScmMaterialStock;
use backend\models\ScmSupplier;
use backend\models\ScmWarehouse;
use common\helpers\Tools;
use common\models\AgentsApi;
use common\models\Api;
use common\models\Building;
use common\models\EquipDeliveryRecord;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "equipments".
 *
 * @property string $id
 * @property string $equip_type_id
 * @property string $equip_code
 * @property integer $equipment_status
 * @property integer $operation_status
 * @property string $create_time
 * @property integer $equip_operation_time
 * @property integer $org_id
 * @property float $concentration
 * @property string $factory_code
 * @property integer $batch
 */
class Equipments extends \yii\db\ActiveRecord
{
    public $number;
    public $symptom;
    public $syncnum;
    public $buildCode;
    public $org_type;
    public $cleaningCycle;
    public $refuelCycle;
    public $dayNum;
    public $organization_type;
    //附件ID
    public $equip_extra_id;
    public $orgArr;

    /** 设备状态 equipment_status 1-正常 2-故障 3-缺料 4-警告*/
    const NORMAL              = 1;
    const MALFUNCTION         = 2;
    const LACKMATERIAL        = 3;
    const WORK_STATUS_WARNING = 4;

    /** 锁定状态 is_lock 1-解锁 2-锁定 3-复位 */
    const UNLOCK = 1;
    const LOCKED = 2;
    const RESET  = 3;

    /** 锁定类型 lock_type 0-公司 1-代理商 */
    const COMPANYLOCKED = 0;
    const AGENTSLOCKED  = 1;

    /**
     * 是否解绑过
     **/
    /** 设备绑定状态 is_unbinding 0-未绑定过 1-绑定没解绑过 2-解绑过 */
    const NOBINDING   = 0;
    const NOTSOLUTION = 1;
    const SOLUTION    = 2;

    /** 设备运营状态 operation_status 0-商业运营 1-未运营 2-内部使用 3-测试使用 4-临时运营 5-库存 6-报废 7-暂停运营 */

    const COMMERCIAL_OPERATION = 0;
    const NO_OPERATION         = 1;
    const INTERNAL_USE         = 2;
    const USE_TEST             = 3;
    const TEMPORARY_OPERATIONS = 4;
    const PRE_SELIVERY         = 5;
    const SCRAPPED             = 6;
    const STOP_OPERATION       = 7;

    /**
     * 锁定数组
     */

    public static $lock = [
        ''           => '请选择',
        self::UNLOCK => '已解锁',
        self::LOCKED => '已锁定',
        self::RESET  => '复位',
    ];

    public static $changeLock = [
        self::UNLOCK => '已解锁',
        self::LOCKED => '已锁定',
        self::RESET  => '复位',
    ];

    public static $equipStatusArray = [
        self::NORMAL       => '正常',
        self::MALFUNCTION  => '故障',
        self::LACKMATERIAL => '缺料',
    ];

    public static $operationStatusArray = array(
        self::COMMERCIAL_OPERATION => '商业运营',
        self::NO_OPERATION         => '未运营',
        self::INTERNAL_USE         => '内部使用',
        self::USE_TEST             => '测试使用',
        self::TEMPORARY_OPERATIONS => '临时运营',
        self::PRE_SELIVERY         => '库存',
        self::SCRAPPED             => '报废',
        self::STOP_OPERATION       => '暂停运营',
    );

    /**
     * 机构类型
     */
    public static $orgType = [
        ''                        => '全部',
        Organization::TYPE_ORG    => '自身',
        Organization::TYPE_AGENTS => '下级',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'warehouse_id', 'batch'], 'required'],
            [['equip_type_id', 'warehouse_id', 'equipment_status', 'operation_status', 'create_time', 'equip_operation_time', 'org_id', 'batch', 'number', 'build_id', 'syncnum', 'wash_time', 'is_lock', 'is_unbinding', 'refuel_time', 'pro_group_id', 'organization_type'], 'integer'],
            [['equip_code', 'factory_code'], 'unique'],
            [['concentration'], 'double'],
            [['equip_code', 'factory_code', 'factory_equip_model', 'card_number'], 'string', 'max' => 50],
            [['miscellaneou_remark'], 'string', 'max' => 500],
            [['bluetooth_name'], 'string', 'max' => 30],
            [['bluetooth_name'], 'unique', 'message' => '蓝牙名称已存在'],
            [['build_id'], 'required', 'on' => 'bind'],
            [['number'], 'required', 'on' => 'create'],
            [['number'], 'match', 'pattern' => '/^[1-9][0-9]{0,2}$/', 'message' => '{attribute}只能为小于999正整数', 'on' => 'create'],
            [['pro_group_id'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => 'ID',
            'equip_type_id'          => '设备类型',
            'warehouse_id'           => '分库',
            'equip_code'             => '设备编号',
            'equipment_status'       => '设备状态',
            'operation_status'       => '运营状态',
            'create_time'            => '设备添加时间',
            'equip_operation_time'   => '设备开始运营时间',
            'org_id'                 => '分公司',
            'factory_code'           => '出厂编号',
            'batch'                  => '批次',
            'number'                 => '数量',
            'build_id'               => '楼宇',
            'symptom'                => '故障现象',
            'pro_group_id'           => '产品组名',
            'syncnum'                => '设备数量',
            'is_lock'                => '是否锁定',
            'buildCode'              => '楼宇编号',
            'concentration'          => '浓度值',
            'factory_equip_model'    => '出厂设备型号',
            'is_unbinding'           => '是否解绑过',
            'card_number'            => '流量卡号',
            'miscellaneou_remark'    => '设备备注',
            'light_box_id'           => '所选灯箱',
            'last_log'               => '最新日志',
            'last_update'            => '更新时间',
            'org_type'               => '范围',
            'cleaningCycle'          => '清洗天数',
            'refuelCycle'            => '换料天数',
            'dayNum'                 => '配送天数',
            'equip_extra_id'         => '附件名称',
            'bluetooth_name'         => '蓝牙锁名称',
            'equipment_longitude'    => '设备的经度',
            'equipment_latitude'     => '设备的纬度',
            'specific_position'      => '设备回传位置',
            'difference_latitude'    => '纬度的差值',
            'difference_longitude'   => '经度的差值',
            'building_location_time' => '设备回传位置的时间',
            'organization_type'      => '机构类型',
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
    public function getEquipLog()
    {
        return $this->hasMany(EquipLog::className(), ['equip_code' => 'equip_code']);
    }

    /**
     *   获取设备类型数组
     *   @return array $materialArray
     **/
    public function getEquipTypeArray()
    {
        $devices         = ScmEquipType::find()->select(['id', 'model'])->asArray()->all();
        $deviceArray     = array();
        $deviceArray[''] = '请选择';
        foreach ($devices as $device) {
            if (!isset($device['id']) || !isset($device['model'])) {
                continue;
            }
            $deviceArray[$device['id']] = $device['model'];
        }
        return $deviceArray;
    }

    /**
     *   获取接口中的产品分组的数组
     *   @return array $materialArray
     **/
    public static function getProGroupArr($equipTypeId)
    {
        return json_decode(Api::getGroups($equipTypeId), true);
    }
    /**
     *   根据设备ID获取设备编号
     *
     **/
    public static function getEquipmentCode($equipId)
    {
        return Equipments::find()->where(['id' => $equipId])->select('equip_code')->asArray()->one();
    }

    /**
     *  根据where条件 orgId 获取仓库数组
     *  @param $orgId
     *  @return array $warehouArray
     **/
    public function getWarehousArray($orgId = '')
    {
        // 分库查询条件
        $wareHouseWhere['use'] = ScmWarehouse::EQUIP_USE;
        if ($orgId != 1) {
            $wareHouseWhere['organization_id'] = $orgId;
        }
        $warehouseArr = ScmWarehouse::getWarehouseNameArray($wareHouseWhere);
        if (!$warehouseArr) {
            return ['' => '请先添加分库'];
        }
        return $warehouseArr;
    }

    /**
     *   设备类型生成规则
     *   @param string $equipTypeId, $batch
     *   @return array $equipCode
     **/
    public function getEeqipCode($equipTypeId, $batch)
    {
        //厂商2位
        $deviceModel = ScmEquipType::find()->where(["id" => $equipTypeId])->one();
        if ($deviceModel->supplier_id) {
            $firm = $deviceModel->supplier->supplier_code;
        }
        //批次3位
        $batchLen = strlen($batch);
        if ($batchLen == 1) {
            $batch = '00' . $batch;
        } else if ($batchLen == 2) {
            $batch = '0' . $batch;
        }
        $equipCode = $firm . $batch;

        return $equipCode;
    }

    /**
     * 设备状态数组 equipStatusArr
     * @return array
     **/
    public function equipStatusArray()
    {
        $equipStatusArray = array(
            ''                        => '请选择',
            self::NORMAL              => '正常',
            self::MALFUNCTION         => '故障',
            self::LACKMATERIAL        => '缺料',
            self::WORK_STATUS_WARNING => '警告',
        );
        return $equipStatusArray;
    }

    /**
     * 获取设备状态
     * @return
     **/
    public function getEquipStatus()
    {
        $equipStatusArray = $this->equipStatusArray();
        return $equipStatusArray[$this->equipment_status];
    }

    /**
     * 运营状态数组 operationStatusArr
     * @return array
     **/
    public function operationStatusArray()
    {
        $operationStatusArray = array(
            ''                         => '请选择',
            self::COMMERCIAL_OPERATION => '商业运营',
            self::NO_OPERATION         => '未运营',
            self::INTERNAL_USE         => '内部使用',
            self::USE_TEST             => '测试使用',
            self::TEMPORARY_OPERATIONS => '临时运营',
            self::PRE_SELIVERY         => '库存',
            self::SCRAPPED             => '报废',
            self::STOP_OPERATION       => '暂停运营',
        );

        return $operationStatusArray;
    }

    /**
     * 运营状态数组 operationStatusArr(设备绑定时，可选择修改此数组)
     * @return array
     **/
    public static function operationStatusByConditionsArray($type = 1)
    {
        $operationStatusArray = array(
            ''                         => '请选择',
            self::COMMERCIAL_OPERATION => '商业运营',
            self::NO_OPERATION         => '未运营',
            self::INTERNAL_USE         => '内部使用',
            self::USE_TEST             => '测试使用',
            self::TEMPORARY_OPERATIONS => '临时运营',
            self::STOP_OPERATION       => '暂停运营',
        );
        if ($type != 1) {
            unset($operationStatusArray['']);
        }
        return $operationStatusArray;
    }

    /**
     * @return \yii\db\ActiveQuery
     **/
    public function getOperationStatus()
    {
        $operationStatusArray = $this->operationStatusArray();
        return $operationStatusArray[$this->operation_status];
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getCompanyName()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getLightBox()
    {
        return $this->hasOne(EquipLightBox::className(), ['id' => 'light_box_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipTypeModel()
    {
        return $this->hasOne(ScmEquipType::className(), ['id' => 'equip_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouseName()
    {
        return $this->hasOne(ScmWarehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * @return
     **/
    public function getEquipTypeModelById($equipTypeId)
    {
        $equipTypeModel = ScmEquipType::find()->where(['id' => $equipTypeId])->one();

        return isset($equipTypeModel) ? $equipTypeModel->model : '';
    }

    /**
     * @return
     **/
    public function getWarehouseNameById($warehouseId)
    {
        $warehouseModel = ScmWarehouse::find()->where(['id' => $warehouseId])->one();
        return isset($warehouseModel->name) ? $warehouseModel->name : '';
    }

    /**
     * 获取设备详细信息
     * @param  string $filed 要查询的字段
     * @param  array  $where 查询条件
     * @return array        返回的数据
     */
    public static function getEquipmentsDetail($filed = '*', $where = [])
    {
        return self::find()
            ->select($filed)
            ->where($where)
            ->asArray()
            ->one();
    }

    /**
     *  获取上/下限值
     *  @param $equipCode $type[1== 上限值， 2== 下限值] $materialStockId 料仓编号
     *  @return $overValue 上限值/ $bottomValue 下限值
     */
    public static function getOverBottomValue($equipCode, $materialStockId, $type = '1')
    {
        $proGroupId           = self::getEquipmentsDetail("*", ['equip_code' => $equipCode])['pro_group_id'];
        $equipmentID          = self::getEquipmentsDetail("*", ['equip_code' => $equipCode])['id'];
        $stockIdToBottomValue = MaterialSafeValue::getEquipmentStockBottomValue($equipmentID);
        $stockId              = ScmMaterialStock::getMaterialStockDetail('*', array('stock_code' => $materialStockId))["id"];
        if ($type == 1) {
            return ProductMaterialStockAssoc::find()->where(['pro_group_id' => $proGroupId, 'material_stock_id' => $stockId])->asArray()->one()['top_value'];
        } else {
            //如果料仓预警值设置了下限值则优先返回
            if (isset($stockIdToBottomValue[$stockId]) && $stockIdToBottomValue[$stockId] > 0) {
                return $stockIdToBottomValue[$stockId];
            }
            return ProductMaterialStockAssoc::find()->where(['pro_group_id' => $proGroupId, 'material_stock_id' => $stockId])->asArray()->one()['bottom_value'];
        }

    }

    /**
     * 获取设备和楼宇以及设备类型的详细信息
     * @param  string $filed 要查询的字段
     * @param  array  $where 查询条件
     * @return array        返回的数据
     */
    public static function getEquipBuildDetail($filed = '*', $where = [])
    {
        return self::find()->select($filed)->where($where)
        // -> createCommand()->getRawSql();
            ->one();
    }

    /**
     * 设备数据统计
     * @return [type] [description]
     */
    public static function equipSync()
    {
        return self::find()->select('org_id,operation_status,equip_type_id,count(id) as syncnum')->groupby('org_id,operation_status,equip_type_id')->orderby('org_id, operation_status, equip_type_id')->asArray()->all();
    }

    /**
     *  修改设备
     *  @param $equipModel, $warehouse_id
     *  @return true/false
     **/
    public static function updateEquip($equipModel, $warehouseId)
    {
        $orgId                            = ScmWarehouse::getOrgIdById($warehouseId);
        $equipModel->build_id             = 0;
        $equipModel->is_lock              = self::LOCKED; //设备是否锁定 锁定
        $equipModel->is_unbinding         = self::SOLUTION; //解绑过
        $equipModel->operation_status     = self::PRE_SELIVERY; //运营状态：0-库存
        $equipModel->equip_operation_time = 0;
        $equipModel->pro_group_id         = 0; // 产品组id置空
        $equipModel->warehouse_id         = $warehouseId;
        $equipModel->org_id               = $orgId;
        return $equipModel->save();
    }

    /**
     *  修改楼宇
     *  @param $buildModel
     *  @return true/false
     **/
    public static function updateBuild($buildModel)
    {
        //楼宇状态变为 1--预投放
        $buildModel->build_status        = Building::PRE_DELIVERY;
        $buildModel->distribution_userid = '';
        $retBuild                        = $buildModel->save(false);
        return $retBuild;
    }

    /**
     *  修改投放单记录表
     *  @param $deliveryRecordModel
     *  @return true/false
     **/
    public static function updateDeliveryRecord($deliveryRecordModel)
    {
        $deliveryRecordModel->un_bind_time = time();
        $deliveryRecordRet                 = $deliveryRecordModel->save();
        return $deliveryRecordRet;
    }

    /**
     *  处理解绑操作
     **/
    public static function getUnBind($id, $warehouse_id)
    {
        $equipModel = self::findOne($id);
        if (!$equipModel->build_id) {
            Yii::$app->getSession()->setFlash('error', '设备已解绑或未进行绑定');
            return false;
        }
        $buildModel = Building::findOne($equipModel->build_id);

        //修改设备
        $retEquip = self::updateEquip($equipModel, $warehouse_id);
        if ($retEquip === false) {
            Yii::$app->getSession()->setFlash('error', '修改设备表状态失败');
            return false;
        }

        // 修改楼宇
        $retBuild = self::updateBuild($buildModel);
        if ($retBuild === false) {
            Yii::$app->getSession()->setFlash('error', '修改楼宇状态失败');
            return false;
        }

        //解绑后设备状态变为库存
        $equipModel->operation_status = self::PRE_SELIVERY;
        //解绑后设备流量卡号删除
        $equipModel->card_number = '';
        if (!$equipModel->save()) {
            Yii::$app->getSession()->setFlash('error', '删除设备流量卡号失败');
            return false;
        }

        //修改投放单记录表中的解绑时间
        $deliveryRecordModel = EquipDeliveryRecord::find()->where(["equip_id" => $equipModel->id, 'build_id' => $buildModel->id])->orderby("Id DESC")->one();
        if ($deliveryRecordModel) {
            $deliveryRecordRet = self::updateDeliveryRecord($deliveryRecordModel);
            if ($deliveryRecordRet === false) {
                Yii::$app->getSession()->setFlash('error', '修改投放单记录失败');
                return false;
            }
        }
        // 同步解绑接口
        $unbindSync = self::syncBind($equipModel, $buildModel);
        if (!$unbindSync) {
            return false;
        }
        return true;
    }

    /**
     * 同步数据
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $equipModel [description]
     * @param   [type]     $buildModel [description]
     * @return  [type]                 [description]
     */
    public static function syncBind($equipModel, $buildModel)
    {
        // 同步设备到智能平台
        $equipSyncRes = self::syncEquip($equipModel);
        if (!$equipSyncRes) {
            Yii::$app->getSession()->setFlash('error', "同步设备信息操作失败");
            return false;
        }
        // 同步绑定操作
        $syncResData = ['equip_code' => $equipModel->equip_code, 'build_number' => $buildModel->build_number, 'bind' => '0', 'online' => $equipModel->operation_status, 'group_id' => 0];
        $syncRes     = Api::equipmentBind($syncResData);
        if (!$syncRes) {
            Yii::$app->getSession()->setFlash("error", "同步绑定操作失败");
            return false;
        }
        return true;
    }

    /**
     * 根据设备id返回设备类型id
     * @param  [type] $equip_id [description]
     * @return [type]           [description]
     */
    public static function getEquipTypeId($equip_id)
    {
        $equipModel = self::findOne($equip_id);
        return $equipModel ? $equipModel->equip_type_id : '';
    }
    /**
     * 获取清洗时间
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getWashTime($id)
    {
        $equipModel = self::findOne($id);
        return $equipModel ? $equipModel->wash_time : 0;
    }

    /**
     * 获取换料时间
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getRefuelTime($id)
    {
        $equipModel = self::findOne($id);
        return $equipModel ? $equipModel->refuel_time : 0;
    }

    /**
     *  楼宇下的分公司 和设备其他条件
     *  @param $buildId, $where
     *  解绑时可用
     **/
    public static function getMaterialWarehouseArr()
    {
        $where = ['use' => ScmSupplier::EQUIPMENT];
        return ScmWarehouse::getWarehouseNameArray($where);
    }

    /**
     * 根据设备id获取配送员id
     * @param  integer $id 楼宇id
     * @return string  返回配送员用户id
     */
    public static function getDistributionUserid($equipCode)
    {
        $equipModel = self::findOne(['equip_code' => $equipCode]);
        if (!$equipModel) {
            return '';
        }

        // 获取该设备所在分公司下配送主管的用户id
        $distributionResponsibleId = WxMember::getFiled('userid', ['org_id' => $equipModel->org_id, 'position' => WxMember::DISTRIBUTION_RESPONSIBLE, 'is_del' => WxMember::DEL_NO]);

        // 该设备所在楼宇没有配送负责人则返回配送主管id
        if (!isset($equipModel->build->distribution_userid) || !isset($equipModel->build->distributionUser)) {
            return $distributionResponsibleId;
        }

        // 该设备所在楼宇有配送员且为上班可接单状态则返回对应配送员的id
        if ($equipModel->build->distributionUser->user_status == DistributionUser::WORK_ON) {
            return $equipModel->build->distribution_userid;
        }

        // 该设备所在楼宇有配送员且为组长不可接单状态则返回配送主管id
        if ($equipModel->build->distributionUser->is_leader == DistributionUser::LEADER_ON) {
            return $distributionResponsibleId;
        }

        // 获取配送员组长的上班状态
        $leaderStatus = DistributionUser::getField('user_status', ['userid' => $equipModel->build->distributionUser->leader_id]);

        // 该设备所在楼宇有配送员且为不可接单状态如果其组长为可接单则返回组长id否则返回配送主管id
        if ($leaderStatus == DistributionUser::WORK_ON) {
            return $equipModel->build->distributionUser->leader_id;
        } else {
            return $distributionResponsibleId;
        }
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-08-26
     * @param   string|int     $field 要获取的字段
     * @param   array          $where 查询条件
     * @return  string|int            要获取的字段的值
     */
    public static function getField($field, $where)
    {
        $equipDetail = self::getEquipBuildDetail($field, $where);
        return $equipDetail ? $equipDetail->$field : '';
    }

    /**
     * 同步设备数据
     * @author  zgw
     * @version 2016-09-06
     * @param   [type]     $model [description]
     * @return  [type]            [description]
     */
    public static function syncEquip($model)
    {
        $data[] = [
            'equip_type_id'        => $model->equip_type_id,
            'organization_id'      => $model->org_id,
            'equipment_code'       => "$model->equip_code", // 若不为双引号 则会报错
            'pro_group_id'         => $model->pro_group_id ? $model->pro_group_id : "0",
            'equip_operation_time' => $model->equip_operation_time ? date('Y-m-d', $model->equip_operation_time) : "0",
            'equipment_status'     => $model->equipment_status ? $model->equipment_status : "1",
            'operation_status'     => $model->operation_status ? $model->operation_status : "0",
            'is_lock'              => $model->is_lock ? $model->is_lock : "1",
            'factory_code'         => $model->factory_code,
        ];
        // 同步设备数据
        return Api::equipmentSync($data);

    }

    /**
     * 修改设备状态(投放验收任务)
     * @author  zgw
     * @version 2016-09-08
     * @return  [type]     [description]
     */
    public static function changeEquip($equipInfo, $buildId, $proGroupId, $operationStatus = '')
    {
        // 清空分库
        $equipInfo->warehouse_id = 0;
        $equipInfo->pro_group_id = $proGroupId;
        $equipInfo->build_id     = $buildId;
        $equipInfo->is_lock      = self::UNLOCK; //设备解锁
        if ($operationStatus !== '') {
            $equipInfo->operation_status = $operationStatus;
        }
        $orgId = Building::getOrgIdById($buildId);
        // 设备绑定时修改设备是否绑定状态
        $equipInfo->is_unbinding = self::NOTSOLUTION;
        $equipInfo->org_id       = $orgId;
        if ($equipInfo->save() === false) {
            Yii::$app->getSession()->setFlash('error', '设备信息修改失败');
            return false;
        }
        return true;
    }

    /**
     * 修改设备浓度值
     * @author wangxl
     * @param $equipInfo
     * @param $concentration
     * @return bool
     */
    public static function changeConcentration($equipInfo, $concentration)
    {
        $equipInfo->concentration = $concentration;
        if ($equipInfo->save() === false) {
            Yii::$app->getSession()->setFlash('error', '修改设备浓度值失败');
            return false;
        }
        return true;
    }

    /**
     * 修改设备状态（验收失败后生成的维修任务，维修成功时把设备状态改为正常）
     * @author  zgw
     * @version 2016-09-09
     * @param   [type]     $equipId [description]
     * @return  [type]              [description]
     */
    public static function editEquipStatus($equipId)
    {
        $equipInfo = self::findOne($equipId);
        if ($equipInfo) {
            $equipInfo->equipment_status = self::NORMAL;
            $equipRes                    = $equipInfo->save();
            if ($equipRes !== false) {
                // 同步数据
                if (self::syncEquip($equipInfo)) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    /**
     * 获取设备类型id数组
     * @author  zgw
     * @version 2016-09-13
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getEquipIdArr($where, $field = 'id')
    {
        return ArrayHelper::getColumn(self::find()->where($where)->all(), $field);
    }

    /**
     * 获取设备类型id数组
     * @author  zgw
     * @version 2016-09-13
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getEquipList($field = '*', $where)
    {
        return self::find()->select($field)->where($where)->all();
    }

    /**
     *  查询已投放的城市
     *  @return Array
     **/
    public static function getPutCityArr($orgId)
    {
        // 已投放的楼宇
        $where = ['build_status' => Building::SERVED];
        if ($orgId > 1) {
            $where['org_id'] = $orgId;
        }
        $buildListObj = Building::getBuildObj('province, city', $where);
        $buildCity    = [];
        foreach ($buildListObj as $key => $value) {
            if ($value->province == '北京市' || $value->province == '上海市') {
                $buildCity[$value->province] = $value->province;
            } else {
                $buildCity[$value->city] = $value->city;
            }
        }
        return $buildCity;
    }

    /**
     *  获取BuildEquipAssocArr
     *  @param $buildListObj
     *  @return array
     **/
    public static function getBuildEquipAssocArr($buildListObj)
    {
        $buildEquipAssocArr = [];
        foreach ($buildListObj as $key => $value) {
            $buildEquipAssocArr[$key]['id']               = $value->id;
            $buildEquipAssocArr[$key]['longitude']        = $value->longitude;
            $buildEquipAssocArr[$key]['latitude']         = $value->latitude;
            $buildEquipAssocArr[$key]['province']         = $value->province;
            $buildEquipAssocArr[$key]['city']             = $value->city;
            $buildEquipAssocArr[$key]['longitude']        = $value->longitude;
            $buildEquipAssocArr[$key]['area']             = $value->area;
            $buildEquipAssocArr[$key]['address']          = $value->address;
            $buildEquipAssocArr[$key]['name']             = $value->name;
            $buildEquipAssocArr[$key]['equipment_status'] = !empty($value->equip) ? $value->equip->equipment_status : '';
            $buildEquipAssocArr[$key]['is_lock']          = !empty($value->equip) ? $value->equip->is_lock : '';
        }
        return $buildEquipAssocArr;
    }

    /**
     * 更新设备清洗时间
     * @author  zgw
     * @version 2016-10-21
     * @return  [type]     [description]
     */
    public static function updateWaterTime($equipId)
    {
        if (!$equipId) {
            return false;
        }
        // 获取设备详情
        $equipModel = self::findOne($equipId);
        // 更新设备清洗时间
        $equipModel->wash_time = time();
        $saveRes               = $equipModel->save();
        if (!$saveRes) {
            return false;
        }
        return true;
    }

    /**
     * 更新设备换料时间
     * @author  zgw
     * @version 2016-10-21
     * @return  [type]     [description]
     */
    public static function updateRefuelTime($equipId)
    {
        if (!$equipId) {
            return false;
        }
        // 获取设备详情
        $equipModel = self::findOne($equipId);
        // 更新设备换料时间
        $equipModel->refuel_time = time();
        if ($equipModel->save() === false) {
            return false;
        }
        return true;
    }

    /**
     * 获取设备数量
     * @author  zgw
     * @version 2016-11-03
     * @return  [type]     [description]
     */
    public static function getEquipSum()
    {
        // 获取三个月前的第一天开始的时间戳
        $secondTime = strtotime(date('Y-m-01', strtotime('-2 month')));
        $oneTime    = strtotime(date('Y-m-01', strtotime('-1 month')));
        $time       = strtotime(date('Y-m-01'));

        // 获取当前已运营的设备数量
        $equipList = self::find()->select('id,equip_operation_time')->where(['>', 'equip_operation_time', 0])->all();

        // 初始化最近三个月的设备数量
        $threeCount = $secondCount = $oneCount = 0;
        foreach ($equipList as $equipOjb) {
            if ($equipOjb->equip_operation_time < $secondTime) {
                $threeCount += 1;
                $secondCount += 1;
                $oneCount += 1;
            } else if ($equipOjb->equip_operation_time < $oneTime) {
                $secondCount += 1;
                $oneCount += 1;
            } else if ($equipOjb->equip_operation_time < $time) {
                $oneCount += 1;
            }
        }
        return [$threeCount, $secondCount, $oneCount];
    }

    /**
     * 修改灯箱
     * @author  zgw
     * @version 2016-11-14
     * @return  [type]     [description]
     */
    public function changeLightBox()
    {
        if ($this->build_id) {
            if (Yii::$app->user->can('选择灯箱')) {
                $lightBoxList = EquipLightBox::getLightBoxIdNameArr(1);
                return yii\helpers\Html::dropDownList('选择灯箱', $this->light_box_id, $lightBoxList, ['id' => 'lightBox', 'data-id' => $this->id, 'class' => 'form-control']);
            } else {
                return isset($this->lightBox->light_box_name) ? $this->lightBox->light_box_name : '';
            }
        }
        return '';
    }

    /**
     *  修改设备表
     *  @param $equipModel, $data, $buildModel, $transaction
     *  @author zmy
     *  @return ''
     */
    public static function modifyEquip($equipModel, $data, $buildModel, $transaction)
    {
        if (!$equipModel) {
            return '设备编号不存在或编号已投放';
        }
        if (!$buildModel) {
            return '楼宇编号不存在或已投放';
        }
        $equipModel->build_id             = $buildModel->id;
        $equipModel->operation_status     = $data['operate_status'];
        $equipModel->pro_group_id         = $data['product_group_id'];
        $equipModel->equip_operation_time = $data['equip_operation_time'];
        $equipModel->is_unbinding         = self::NOTSOLUTION;
        if (!$equipModel->save()) {
            $transaction->rollBack();
            return '设备更新失败';
        }
        return '';
    }

    /**
     *  修改楼宇表 楼宇的状态
     *  @param $buildModel  $transaction
     *  @author zmy
     *  @return ''
     */
    public static function modifyBuild($buildModel, $transaction)
    {
        if (!$buildModel) {
            return '楼宇编号错误';
        }
        $buildModel->build_status = Building::SERVED; // 3.已投放
        $buildModel->is_bind      = 2; //已绑定过
        if (!$buildModel->save()) {
            $transaction->rollBack();
            return '楼宇更新失败';
        }
        return '';
    }

    /**
     *  根据设备编号查找特定的设备。
     *  @param $data 传输的数组参数
     *  @return json
     *  @author zmy
     */
    public static function updateEquipInfo($data)
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $equipModel  = Equipments::find()->where(['equip_code' => $data['equip_code'], 'build_id' => 0])->one();
        $buildModel  = Building::find()->where(['build_number' => $data['build_code']])->one();
        $retEquip    = self::modifyEquip($equipModel, $data, $buildModel, $transaction);
        $retBuild    = self::modifyBuild($buildModel, $transaction);
        if ($retEquip) {
            AgentsApi::returnData(1, $retEquip);
        }
        if ($retBuild) {
            AgentsApi::returnData(1, $retBuild);
        }

        //同步绑定操作到智能平台
        $agentData = ['equip_code' => $data['equip_code'], 'build_number' => $data['build_code'], 'bind' => 1, 'group_id' => $data['product_group_id'], 'online' => $data['operate_status'], 'start_date' => date('Y-m-d', $data['equip_operation_time'])];
        $coffeeRes = Api::equipmentBind($agentData);
        if (!$coffeeRes) {
            AgentsApi::returnData(1, '智能平台绑定失败');
        }

        //事务通过
        $transaction->commit();
        AgentsApi::returnData(0, '同步成功');
    }

    /**
     *  代理商解锁
     *  @param data
     *  @author zmy
     */
    public static function unBindEquip($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        // 获取代理商设备信息
        $equipModel = self::find()->where(['equip_code' => $data['equip_code']])->one(); //代理商
        if (!$equipModel) {
            AgentsApi::returnData(1, '没有要解锁的设备');
        }
        $equipModel->is_lock = self::UNLOCK;
        if (!$equipModel->save()) {
            AgentsApi::returnData(1, '解锁同步失败');
        }
        // 同步锁定操作 coffee后台
        $lock     = $equipModel->is_lock - 1;
        $syncData = ['equip_code' => $equipModel->equip_code, 'lock' => $lock];
        $syncRes  = Api::equipmentLock($syncData);
        if (!$syncRes) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '智能平台解锁失败');
        }
        $transaction->commit();
        AgentsApi::returnData(0, '解锁同步成功');
    }

    /**
     *  代理商锁定
     *  @param data
     *  @author zmy
     */
    public static function bindEquip($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $equipModel  = self::find()->where(['equip_code' => $data['equip_code']])->one(); //代理商
        if (!$equipModel) {
            AgentsApi::returnData(1, '没有要锁定的设备');
        }
        $equipModel->is_lock   = self::LOCKED;
        $equipModel->lock_type = self::AGENTSLOCKED;
        if ($data['lock_type'] == 1) {
            $equipModel->lock_type = self::COMPANYLOCKED;
        }
        if (!$equipModel->save()) {
            AgentsApi::returnData(1, '锁定同步失败');
        }
        // 同步锁定操作 coffee后台
        $lock     = $equipModel->is_lock - 1;
        $syncData = ['equip_code' => $equipModel->equip_code, 'lock' => $lock];
        $syncRes  = Api::equipmentLock($syncData);
        if (!$syncRes) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '智能平台解锁失败');
        }
        $transaction->commit();
        AgentsApi::returnData(0, '锁定同步成功');
    }

    /**
     * 获取产品组列表
     * @author  zgw
     * @version 2016-12-02
     * @return  [type]     [description]
     */
    public function proGroupList($groupId = '')
    {

        $groupList = json_decode(Api::getGroups($this->equip_type_id), 1);
        if ($groupId) {
            return isset($groupList[$groupId]) ? $groupList[$groupId] : '';
        } else {
            return $groupList ? $groupList : [];
        }
    }

    /**
     * 获取 设备编号=>楼宇名称 数组
     * [getEquipArr description]
     * @author  zmy
     * @version 2016-12-06
     * @return  [type]     [description]
     */
    public static function getEquipArr($orgId = 1, $special = '')
    {
        $orgId     = empty($orgId) || $orgId == 1 ? [] : $orgId;
        $orgIdList = [];
        foreach ((array) $orgId as $orgIdc) {
            $corgId = Api::getOrgIdArray([
                'parant_path'         => $orgIdc,
                'is_replace_maintain' => Organization::INSTEAD_YES,
            ]);
            $orgIdList = array_merge($orgIdList, $corgId);
        }
        $orgId = $orgIdList;
        $query = self::find()
            ->andFilterWhere(['org_id' => $orgId]);
        if (!$special) {
            $query->andWhere(['>', 'build_id', 0]);
        }
        $equipArr = $query->orderBy("id DESC")
            ->all();
        $equipCodeArr = [];
        if (!$special) {
            foreach ($equipArr as $value) {
                $equipCodeArr[$value->equip_code] = isset($value->build->name) ? $value->build->name : "";
            }
        } else {
            foreach ($equipArr as $value) {
                $equipCodeArr[$value->equip_code] = $value->equip_code;
            }
        }

        return $equipCodeArr;

    }

    /**
     * 获取 设备编号=>设备编号 数组
     * @author  zmy
     * @version 2016-12-06
     * @return  [type]     [description]
     */
    public static function getEquipCodeArr($orgId = '')
    {
        if ($orgId && $orgId != 1) {
            $equipArr = self::find()->where(['and', ['org_id' => $orgId], ['not', ['is_unbinding' => 0]]])->orderBy("id DESC")->all();
        } else {
            $equipArr = self::find()->where(['and', ['not', ['is_unbinding' => 0]]])->orderBy("id DESC")->all();
        }
        $equipCodeArr = [];
        foreach ($equipArr as $key => $value) {
            $equipCodeArr[$value->equip_code] = $value->equip_code;
        }
        return $equipCodeArr;

    }

    /**
     * 根据设备端回传日志更新设备状态和最新日志内容
     * @author  zgw
     * @version 2016-12-09
     * @param   [type]     $equipDetail [description]
     * @param   [type]     $data        [description]
     * @return  [type]                  [description]
     */
    public static function updateEquipments($equipDetail, $data)
    {
        // 更改设备状态
        if ($equipDetail->equipment_status != $data['equip_status']) {
            $equipDetail->equipment_status = $data['equip_status'];
        }
        // 如果设备处于复位状态，则改为解锁
        if ($equipDetail->is_lock == Equipments::RESET) {
            $equipDetail->is_lock = Equipments::UNLOCK;
        }
        // 设备最新日志和最新更新时间
        $equipDetail->last_log    = $data['last_log'];
        $equipDetail->last_update = time();
        // 保存设备信息
        if ($equipDetail->save() === false) {
            return false;
        }
    }

    /**
     * 检查设备状态--超过20分钟设为不正常
     * @author  zgw
     * @version 2016-12-09
     * @return  [type]     [description]
     */
    public static function check()
    {
        $checkTime = time() - 1260; //21分钟前
        //Equipments::updateAll(['last_log' => '超过20分钟无上传', 'equipment_status' => 2], ['and', ['<', 'operation_status', 4], ['<', 'last_update', $checkTime]]);
        $equipments = Equipments::find()->where(['<', 'last_update', $checkTime])->all();
        $info       = [];
        foreach ($equipments as $k => $equip) {
            $equip->last_log         = '设备超过20分钟无上传';
            $equip->equipment_status = 2;
            $equip->save();

            if (!$equip->equip_code) {
                continue;
            }
            // 获取设备信息
            $equipDetail = Equipments::getEquipBuildDetail('*', ['equip_code' => $equip->equip_code]);
            if (!$equipDetail || $equipDetail->build_id == 0) {
                continue;
            }
            if (Building::getField('name', ['id' => $equipDetail->build]) == '') {
                continue;
            }
            $info['equip_code']   = $equip->equip_code;
            $info['equip_status'] = 1;
            $info['log_type']     = 1;
            $info['content']      = ['01090400' => EquipWarn::$warnContent['01090400']];
            //调用发送消息
            EquipWarn::callUploadMessage($info, $equipDetail);
        }

        //$data = '{"equip_code":"010090003","equip_status":"1","log_type":"1","content":{"01090400":"超过20分钟无上传", "0101002":"热胆温度温度底"}}';

    }

    /**
     * 获取指定状态未锁定的设备
     * @param $operationStatus
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEquipmentByStatus($operationStatus)
    {
        $equipmentInfo = self::find()->select(['id', 'equip_code'])->where(['in', 'operation_status', $operationStatus])->asArray()->all();
        return Tools::map($equipmentInfo, 'id', 'equip_code', null, null);
    }

    /**
     * 获取设备楼宇对应数据
     * @return array
     */
    public static function getEquipmentBuildIds()
    {
        $list = self::find()->select('id,build_id')->asArray()->all();
        return Tools::map($list, 'id', 'build_id', null, null);
    }

    /**
     * 批量修改设备产品组
     * @author  zmy
     * @version 2017-09-12
     * @param   [Array]     $equipCodeList  [设备编号数组]
     * @param   [string]    $proGroupID     [产品组ID]
     * @return  [boolen]                    [true/false]
     */
    public static function updateEquipmentsProGroup($equipCodeList, $proGroupID)
    {
        if (!$equipCodeList) {
            return false;
        }
        $updateEquipSign = true;
        foreach ($equipCodeList as $key => $equipCode) {
            $model               = self::findOne(['equip_code' => $equipCode]);
            $model->pro_group_id = $proGroupID;
            if (!$model->save()) {
                $updateEquipSign = false;
            }
        }
        return $updateEquipSign;
    }

    /**
     * 查询equipment表和building表的数据
     * @author sulingling
     * @param $where 查询的条件
     * @param $join 关联关系
     * @param $tableName 表名
     * @return array()
     */
    public static function getOne($where, $field = '*')
    {
        $data = self::find()
            ->select($field)
            ->where($where)
            ->asArray()
            ->one();
        return $data ? $data : false;
    }

    /**
     * 将设备传过来的数据在数据库中进行修改
     * @author sulingling
     * @version 2018-05-21
     * @param $date array()
     * @rerurn boolean
     */
    public static function saveEquipment($data)
    {
        $model                         = self::findOne(['equip_code' => $data['equipCode']]);
        $model->equipment_longitude    = $data['equipmentLongitude'];
        $model->equipment_latitude     = $data['equipmentLatitude'];
        $model->specific_position      = $data['specificPosition'];
        $model->building_location_time = time();
        return $model->save();
    }

    /**
     * 计算设备和点位的距离
     * @author sulingling
     * @version 2018-05-23
     * @param $equipmentLongitude float 设备的经度
     * @param $longitude float 经度
     * @return $ float
     */
    public function difference($from, $to)
    {
        $url         = "https://apis.map.qq.com/ws/distance/v1/?mode=driving&from={$from}&to={$to}&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ";
        $content     = \common\helpers\Tools::http_get($url);
        $contentList = \yii\helpers\Json::decode($content);
        return empty($contentList['result']['elements'][0]['distance']) ? 0 : $contentList['result']['elements'][0]['distance'];
    }

    /**
     * 获取设备编号下面设备产品分组料仓详细信息
     * @author sulingling
     * @version 2018-06-08
     * @param $equipmentCode 设备编号  string
     * @param $stockCode 料仓编号  string
     * @return array() | boolean
     */
    public static function equipmentProductGroupStock($equipmentCode = '')
    {
        $equipmentProductGroupStockInfo = Api::getEquipmentProductGroupStock($equipmentCode);
        $equipmentProductGroupStockArr  = Json::decode($equipmentProductGroupStockInfo, true);
        if (empty($equipmentProductGroupStockArr)) {
            return [];
        }
        return $equipmentProductGroupStockArr;
    }

    /**
     * 通过设备ID获取设备编号
     * @author wangxiwen
     * @version 2018-07-19
     * @param int $equipId 设备ID
     * @return string
     */
    public static function getEquipCode($equipId)
    {
        return self::find()
            ->where(['id' => $equipId])
            ->select('equip_code')
            ->scalar();
    }

    /**
     * 获取楼宇设备关系数据
     * @author wangxiwen
     * @version 2018-10-12
     * @return array 楼宇ID=>设备ID
     */
    public static function getBuildEquipAssoc($orgIdArr)
    {
        $equipments = self::find()->orderBy('build_id')
            ->select('id,build_id')
            ->andFilterWhere(['>', 'build_id', 0])
            ->andFilterWhere(['org_id' => $orgIdArr])
            ->andFilterWhere(['in', 'operation_status',
                [
                    Equipments::COMMERCIAL_OPERATION,
                    Equipments::INTERNAL_USE,
                    Equipments::TEMPORARY_OPERATIONS,
                ],
            ])
            ->asArray()
            ->all();
        return Tools::map($equipments, 'build_id', 'id', null, null);
    }

    /**
     *  根据设备编号获得设备信息
     * @author sulingling
     * @dateTime 2018-08-15
     * @version  [version]
     * @param    string()       $equipCode [设备编号]
     * @return   object()                  [对象]
     */
    public static function equip($equipCode)
    {
        return self::findOne(['equip_code' => $equipCode]);
    }

    /**
     * 获取设备信息
     * @author wangxiwen
     * @version 2018-05-17
     * @return array
     */
    public static function getEquipments()
    {
        return self::find()
            ->alias('e')
            ->leftJoin('building b', 'b.id = e.build_id')
            ->select('e.equip_type_id,e.build_id,e.org_id,e.equip_code,e.wash_time,e.equipment_status,b.distribution_userid')
            ->andWhere(['>', 'build_id', 0])
            ->andWhere(['in', 'operation_status',
                [
                    Equipments::COMMERCIAL_OPERATION,
                    Equipments::INTERNAL_USE,
                    Equipments::TEMPORARY_OPERATIONS,
                ],
            ])
            ->asArray()
            ->all();
    }

}
