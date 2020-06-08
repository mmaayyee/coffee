<?php

namespace common\models;

use backend\models\DistributionTask;
use backend\models\DistributionTaskEquipSetting;
use backend\models\DistributionUser;
use backend\models\EquipDelivery;
use backend\models\Manager;
use backend\models\MaterialSafeValue;
use backend\models\Organization;
use common\helpers\Tools;
use common\models\AgentsApi;
use common\models\Api;
use common\models\Equipments;
use common\models\WxMember;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "building".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $contact_name
 * @property string $contact_tel
 * @property string $code
 * @property integer $people_num
 * @property integer $create_time
 * @property string $province
 * @property string $city
 * @property string $area
 * @property double $longitude
 * @property double $latitude
 * @property integer $build_type
 * @property integer $build_status
 * @property string $voice_type
 * @property integer $is_ammeter
 * @property integer $is_lightbox
 * @property string $build_number
 *
 * @property DistributionTask[] $distributionTasks
 * @property EquipAcceptance[] $equipAcceptances
 * @property EquipBuildingAssoc[] $equipBuildingAssocs
 * @property EquipDelivery[] $equipDeliveries
 * @property EquipSleep[] $equipSleeps
 */
class Building extends \yii\db\ActiveRecord
{
    public $equip_code;
    public $program_id; // 灯带方案
    public $orgArr;

    /** 楼宇状态 1-预投放 2-投放中 3-已投放 */
    const PRE_DELIVERY   = 1;
    const TRAFFICKING_IN = 2;
    const SERVED         = 3;

    /** 能否在附近咖啡吧搜索到 1-能 2-否*/
    const SEARCH_YES = 1;
    const SEARCH_NO  = 2;

    /** 业务类型 0-商业发展 1-代理委托投放 2-居间服务 3-代理加盟 4-求包养 5-其它 6-测试 7-自运维联营方 8-天九 9-托管运维联营方*/
    const BUSINESS_DEVELOP         = 0;
    const BUSINESS_AGENT           = 1;
    const BUSINESS_INTERVENING     = 2;
    const BUSINESS_JOIN            = 3;
    const BUSINESS_CULTIVATE       = 4;
    const BUSINESS_OTHER           = 5;
    const BUSINESS_TEST            = 6;
    const BUSINESS_PARTNER         = 7;
    const BUSINESS_TIAN_JIU        = 8;
    const BUSINESS_PARTNER_HOSTING = 9;

    /**
     * 是否可在附近咖啡吧搜索数组
     * @author  zgw
     * @version 2017-04-28
     * @param   integer    $type       用于区分是否有请选择按钮
     * @param   string     $isShareKey 用于获取值
     * @return  [type]                 [description]
     */
    public static function getShareArr($type = 1, $isShareKey = '')
    {
        $isShareArr = [];
        if ($type == 1) {
            $isShareArr[''] = '请选择';
        }
        $isShareArr[self::SEARCH_YES] = '是';
        $isShareArr[self::SEARCH_NO]  = '否';
        if ($isShareKey) {
            return !isset($isShareArr[$isShareKey]) ? '' : $isShareArr[$isShareKey];
        }
        return $isShareArr;
    }

    /**
     * 合作商类型
     * @var array
     */
    public static $cooperation_type = array(
        1 => '加盟商',
        2 => '包养商',
        3 => '代理商',
    );

    /**
     * 业务类型数组
     * @var [type]
     */
    public static $businessTypeList = [
        self::BUSINESS_DEVELOP         => '商业发展',
        self::BUSINESS_AGENT           => '代理委托投放',
        self::BUSINESS_INTERVENING     => '居间服务',
        self::BUSINESS_JOIN            => '代理加盟',
        self::BUSINESS_CULTIVATE       => '求包养',
        self::BUSINESS_OTHER           => '其它',
        self::BUSINESS_TEST            => '测试',
        self::BUSINESS_PARTNER         => '自运维联营方',
        self::BUSINESS_PARTNER_HOSTING => '托管运维联营方',
        self::BUSINESS_TIAN_JIU        => '天九',
    ];

    /**
     * 楼宇状态
     * @var array
     */
    public static $build_status = array(
        ''                   => '请选择',
        self::PRE_DELIVERY   => '未投放',
        self::TRAFFICKING_IN => '投放中',
        self::SERVED         => '已投放',
    );
    /** @var array 直辖市数组 */
    public static $cities = ['北京市', '天津市', '上海市', '重庆市'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'build_number', 'build_type', 'province', 'city', 'address', 'people_num', 'contact_name', 'contact_tel', 'longitude', 'latitude', 'org_id', 'sign_org_id', 'business_type'], 'required', 'on' => ['create', 'update']],
            [['first_free_strategy', 'first_backup_strategy', 'strategy_change_date'], 'required', 'on' => 'offersEdit'],
            [['people_num', 'create_time', 'build_type', 'build_status', 'org_id', 'is_bind'], 'integer'],
            [['first_free_strategy', 'first_backup_strategy', 'is_share', 'is_delivery'], 'number'],
            [['name'], 'string', 'max' => 30],
            [['distribution_userid', 'longitude', 'latitude'], 'string', 'max' => 64],
            [['longitude', 'latitude'], 'match', 'pattern' => '/^\d+\.\d+$/', 'message' => '{attribute}只能输入数字'],
            [['people_num'], 'match', 'pattern' => '/^[1-9]{1}\d{0,6}$/', 'message' => '{attribute}只能为小于6位数的正整数', 'on' => ['create', 'update']],
            [['address'], 'string', 'max' => 100],
            [['contact_name'], 'string', 'max' => 10],
            [['contact_tel', 'build_number', 'strategy_change_date'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 300],
            [['province', 'city', 'area', 'bd_maintenance_user'], 'string', 'max' => 50],
            //['contact_tel', 'mobileCheck'],
            [['name', 'build_number'], 'unique'],
            [['is_share', 'is_delivery'], 'default', 'value' => 1],
            [['building_level'], 'default', 'value' => 0],
            [['create_build_code', 'building_level', 'sign_org_id', 'business_type', 'source_org_id'], 'safe'],
            [['first_free_strategy', 'first_backup_strategy', 'source_org_id'], 'default', 'value' => 0],
        ];
    }

    /**
     * 手机号检查
     * @param type $attribute
     * @param type $params
     */
    /*public function mobileCheck($attribute, $params)
    {
    if (!preg_match('/^(1)\d{10}$/', $this->contact_tel)) {
    $this->addError($attribute, '请输入正确的手机号');
    }
    }*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => '点位id',
            'name'                   => '点位名称',
            'address'                => '点位地址',
            'contact_name'           => '点位联系人',
            'contact_tel'            => '联系人电话',
            'code'                   => '点位二维码',
            'people_num'             => '点位人数',
            'create_time'            => '添加时间',
            'province'               => '省份',
            'city'                   => '城市',
            'area'                   => '区域',
            'longitude'              => '经度',
            'latitude'               => '纬度',
            'build_type'             => '点位类型',
            'build_status'           => '点位状态',
            'build_number'           => '点位编号',
            'org_id'                 => '所属机构',
            'cooperation_type'       => '合作商类别',
            'cooperation_start_time' => '合作时间',
            'cooperation_end_time'   => '合作结束时间',
            'first_free_strategy'    => '首杯免费策略',
            'equip_code'             => '设备编号',
            'strategy_change_date'   => '首杯策略变更日期',
            'first_backup_strategy'  => '首杯备份策略',
            'is_share'               => '能否被搜索',
            'is_delivery'            => '是否支持配送',
            'program_id'             => '灯带方案',
            'create_build_code'      => '点位创建编码顺序',
            'building_level'         => '点位级别',
            'bd_maintenance_user'    => 'BD维护人员',
            'sign_org_id'            => '合同签约公司',
            'source_org_id'          => '客户来源',
            'business_type'          => '业务类型',
        ];
    }

    //暂时写死优惠券套餐
    /**
     * 获取首杯策略名称数组
     * @return string
     */
    public static function getFirstStagegyNameArray()
    {
        $couponGroupJson = Api::getCouponGroup();
        foreach ($couponGroupJson as $gid => &$gname) {
            $gname = $gid . '-' . $gname;
        }
        unset($gname);
        return !$couponGroupJson ? [] : $couponGroupJson;
    }
    /**
     * 获取首杯策略
     * @return array 获取首杯策略数组
     */
    public function getFirstStategyArray()
    {
        return array(
            '0' => array(
                'name'    => '首杯免费套餐',
                'image'   => 'http://wx.coffee08.com/images/red1.png',
                'coupons' => array(
                    '1' => 1, //1张兑换
                    '3' => 1, //1张5元
                    '8' => 2, //2张4元
                    '7' => 2, //2张3元
                ),
            ),
            '1' => array(
                'name'    => '首杯优惠套餐',
                'image'   => 'http://wx.coffee08.com/images/red2.png',
                'coupons' => array(
                    '3' => 1, //1张5元
                    '8' => 2, //2张4元
                    '7' => 2, //2张3元
                ),
            ),
        );
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getOrgName()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['build_id' => 'id']);
    }

    public function getDayTask()
    {
        return $this->hasOne(DistributionTaskEquipSetting::className(), ['build_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getDelivery()
    {
        return $this->hasOne(EquipDelivery::className(), ['build_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getDistributionUser()
    {
        return $this->hasOne(DistributionUser::className(), ['userid' => 'distribution_userid']);
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getWxUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'distribution_userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasOne(Equipments::className(), ['build_id' => 'id']);
    }

    /**
     * 获取楼宇相关信息
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getBuildingDetail($field, $where)
    {
        return self::find()
            ->select($field)
            ->where($where)
            ->asArray()
            ->one();
    }
    /**
     * 根据楼宇编号获取楼宇名称
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getBuildingName($build_id)
    {
        return self::find()
            ->select('name')
            ->where(['id' => $build_id])
            ->asArray()
            ->one();
    }

    /**
     * @return $clientArray
     */
    public static function buildNameArray()
    {
        $build             = Building::find()->select(['name'])->asArray()->all();
        $buildArrayContent = array();
        foreach ($build as $key => $value) {
            $buildArrayContent[$key] = $build[$key]['name'];
        }
        $buildArray = $buildArrayContent;
        return $buildArray;
    }

    /**
     * @return $clientArray
     */
    public static function buildStatusNameArr()
    {
        $build             = Building::find()->select(['name'])->where(['build_status' => 3])->asArray()->all();
        $buildArrayContent = array();
        foreach ($build as $key => $value) {
            $buildArrayContent[$key] = $build[$key]['name'];
        }
        $buildArray = $buildArrayContent;
        return $buildArray;
    }

    /**
     * 获取楼宇列表
     * @param  string $filed 要查寻的字段 如：'id,name'
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        楼宇列表
     */
    public static function getBuildList($filed = "*", $where = array())
    {
        return self::find()->select($filed)->where($where)->asArray()->all();
    }

    public static function getBuildObj($filed = "*", $where = array())
    {
        return self::find()->select($filed)->where($where)->all();
    }

    /**
     * 获取正常运营的设备楼宇
     * @author wangxl
     * @param int $orgID
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRunBuildList($orgID = 0)
    {
        $filed = 'building.id, longitude, latitude, distribution_userid, province, city, area, address, name, build_number';
        $where = ['parent_path' => $orgID];
        if ($orgID > 1) {
            $where['is_replace_maintain'] = Organization::INSTEAD_YES;
        }
        $orgID = Api::getOrgIdArray($where);
        return self::find()
            ->leftJoin('equipments e', 'e.build_id = building.id')
            ->select($filed)
            ->where([
                'build_status' => Building::SERVED,
                'e.org_id'     => $orgID,
            ])
            ->andFilterWhere([
                'in',
                'e.operation_status',
                [
                    Equipments::COMMERCIAL_OPERATION,
                    Equipments::NO_OPERATION,
                    Equipments::INTERNAL_USE,
                    Equipments::TEMPORARY_OPERATIONS,
                ],
            ])
            ->asArray()
            ->all();
    }

    /**
     * 获取楼宇数组
     * @param  $buildTypeArr
     */
    public static function getBuildTypeArray($buildType = 0)
    {
        $buildTypeArr = array(
            ''  => '请选择',
            '1' => '公司',
            '2' => '写字楼',
            '3' => '展会',
            '4' => '学校',
            '5' => '商场',
            '6' => '园区',
        );
        if ($buildType) {
            return empty($buildTypeArr[$buildType]) ? '' : $buildTypeArr[$buildType];
        }
        return $buildTypeArr;
    }

    /**
     * 获取当前用户所在分公司已投放的楼宇列表
     * @return [type] [description]
     */
    public static function getDeliveryBuildList($buildStatus = [])
    {
        $where = ['in', 'build_status', $buildStatus];
        $orgID = Manager::getManagerBranchID();
        if ($orgID > 1) {
            $orgID = Api::getOrgIdArray(['parent_path' => $orgID, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $where = ['and', ['org_id' => $orgID], $where];
        }
        $buildList = self::getBuildList('id,name', $where);

        $builNameList = array('' => '请选择');
        foreach ($buildList as $key => $value) {
            $builNameList[$value['id']] = $value['name'];
        }
        return $builNameList;
    }

    /**
     * 获取当前用户所在分公司已投放的楼宇列表
     * @return [type] [description]
     */
    public static function getDeliveryBuildNameList($where = [])
    {
        $org_id = Manager::getManagerBranchID();
        if ($org_id > 1) {
            $where = ['and', ['org_id' => $org_id], $where];
        }
        return ArrayHelper::getColumn(self::getBuildList('name', $where), 'name');
    }

    /**
     * 获取当前用户所在分公司已投放的楼宇列表
     * @return [type] [description]
     */
    public static function getOperationBuildList($type = 1)
    {
        $query = self::find()
            ->alias('b')
            ->orderBy('id desc')
            ->andFilterWhere(['build_status' => self::SERVED]);
        if ($type == 1) {
            $query->joinWith('equip e')
                ->andFilterWhere(['or',
                    ['e.operation_status' => Equipments::COMMERCIAL_OPERATION],
                    ['e.operation_status' => Equipments::INTERNAL_USE],
                    ['e.operation_status' => Equipments::NO_OPERATION],
                    ['e.operation_status' => Equipments::TEMPORARY_OPERATIONS],
                ]);
        }
        $orgID = Manager::getManagerBranchID();
        if ($orgID > 1) {
            $orgID = Api::getOrgIdArray(['parent_path' => $orgID, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->andFilterWhere(['b.org_id' => $orgID]);
        }
        $buildList    = $query->all();
        $builNameList = array('' => '请选择');
        foreach ($buildList as $key => $value) {
            $builNameList[$value['id']] = $value['name'];
        }
        return $builNameList;
    }

    /**
     * 过滤已设置料仓预警值的楼宇
     * @author wxl
     * @return array
     */
    public static function getOrganizationBuildList()
    {
        $buildNameList = self::getBusinessBuildByOrgId();

        //已经设置的设备
        $equipmentIds = MaterialSafeValue::getEquipmentId();
        //设备ID对应的楼宇ID
        $equipIdBuild = Equipments::getEquipmentBuildIds();
        foreach ($buildNameList as $key => $item) {
            if (in_array($key, array_intersect_key($equipIdBuild, array_flip($equipmentIds)))) {
                unset($buildNameList[$key]);
            }
        }
        return $buildNameList;

    }

    /**
     * 获取商业运营已投放的楼宇
     * @author wxl
     * @return array
     */
    public static function getBusinessBuildByOrgId()
    {

        $query = self::find()
            ->orderBy('id desc')
            ->andFilterWhere(['build_status' => self::SERVED])
            ->joinWith('equip e')
            ->andFilterWhere([
                'e.operation_status' => Equipments::COMMERCIAL_OPERATION,
            ]);
        $orgID = Manager::getManagerBranchID();
        if ($orgID > 1) {
            $orgID = Api::getOrgIdArray(['parent_path' => $orgID, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->andFilterWhere(['building.org_id' => $orgID]);
        }
        $buildList    = $query->all();
        $builNameList = array('' => '请选择');
        foreach ($buildList as $key => $value) {
            $builNameList[$value['id']] = $value['name'];
        }
        return $builNameList;
    }

    /**
     * 获取投放中和已投放的楼宇列表
     * @author  zgw
     * @version 2016-09-12
     * @return  [type]     [description]
     */
    public static function getPreDeliveryBuildList($type = 1, $orgId = '', $isAssociated = '')
    {
        $query = self::find()
            ->select('id,name')
            ->orderBy('id desc');
        if ($orgId > 1) {
            $orgId = $orgId ? $orgId : Manager::getManagerBranchID();
            $orgId = Api::getOrgIdArray(['parent_path' => $orgId, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->andFilterWhere(['org_id' => $orgId]);
        }
        return Tools::map($query->all(), 'id', 'name');
    }

    /**
     * 获取所有楼宇id和name的对应列表
     * @return [type] [description]
     */
    public static function getAllBuildIdNameArr($where = [])
    {
        if (isset($where['org_id']) && $where['org_id'] == 1) {
            // 分公司
            unset($where['org_id']);
        }
        $buildList    = self::getBuildList('id,name', $where);
        $builNameList = [];
        if ($buildList) {
            foreach ($buildList as $key => $value) {
                $builNameList[$value['id']] = $value['name'];
            }
        }
        return $builNameList;
    }

    /**
     * 获取楼宇设备 的关联数据
     * @author  zmy
     * @version 2017-10-31
     * @return  [type]     [description]
     */
    public static function getBuildEquipAssocList($where)
    {
        $query = self::find()->joinWith('equipments e');
        return $query->where($where)->asArray()->all();
    }

    /**
     * 获取所有楼宇id和name的对应列表
     * @return [type] [description]
     */
    public static function getAllBuildIdEquipCodeArr($where = [])
    {
        if (isset($where['building.org_id']) && $where['building.org_id'] == 1) {
            // 分公司
            unset($where['building.org_id']);
        } else {
            $where['building.org_id'] = Organization::getParentPathArr($where['building.org_id']);
        }
        // 获取楼宇设备 的关联数据
        $buildList    = self::getBuildEquipAssocList($where);
        $builNameList = [];
        if ($buildList) {
            foreach ($buildList as $key => $build) {
                $builNameList[$build['id']] = $build['name'] . '--' . $build['equipments']['equip_code'];
            }
        }
        return $builNameList;
    }

    /**
     * 获取当前登录用户所在分公司已经投放成功的楼宇id和name的对应列表
     * @return [type] [description]
     */
    public static function getOrgBuild($where = [])
    {
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $where['org_id'] = $orgId;
        }
        return self::getAllBuildIdNameArr($where);
    }

    /**
     * 通过where条件查询出设备编号对应的楼宇名称数组
     * @author  zmy
     * @version 2017-06-03
     * @param   [type]     $where [条件]
     * @return  [type]            [一维数组]
     */
    public static function getEquipCodeAndBuildNameArr($where)
    {
        $orgID = Manager::getManagerBranchID();
        if ($orgID > 1) {
            $where['org_id'] = Organization::getParentPathArr($orgID);
        }
        $buildArr  = self::find($where)->where($where)->all();
        $buildList = [];
        foreach ($buildArr as $key => $value) {
            if (!isset($value->equipments->equip_code)) {
                continue;
            }
            $buildList[$value->equipments->equip_code] = $value->name;
        }
        return $buildList;
    }

    /**
     * 楼宇名称数组
     * @param  [type] $distribution_id [description]
     * @return [type]                  [description]
     */
    public static function getBuildNameArr($distribution_userid)
    {
        return ArrayHelper::getColumn(self::findAll(['distribution_userid' => $distribution_userid]), 'name');
    }

    /**
     * 删除指定配送员负责的楼宇
     * @param  string $userid 配送员userid
     * @return [type]         [description]
     */
    public static function delDistributionUser($userid)
    {
        return self::updateAll(['distribution_userid' => ''], ['distribution_userid' => $userid]);
    }

    /**
     * 返回楼宇地址
     * @param  [type] $build_id [description]
     * @return [type]           [description]
     */
    public static function getBuildAddress($build_id)
    {
        $build_model = self::findOne($build_id);
        return $build_model ? $build_model->province . $build_model->city . $build_model->area . $build_model->name : '';
    }

    /**
     * 获取楼宇详情
     * @author  zgw
     * @version 2016-08-25
     * @param   array     $where 查询条件
     * @return  array            楼宇详情
     */
    public static function getBuildDetail($where)
    {
        return self::find()->where($where)->one();
    }

    // $dataString     = '{"name":"测试楼宇1","build_number":"100001123","address":"北京测试","code":"aaa","province":"","city":"","area":"","longitude":"1","latitude":"2","build_type":"1","build_status":"2","create_time":"1111","org_id":"1","first_free_strategy":"0","strategy_change_date":"","first_backup_strategy":""}';
    /**
     * 同步楼宇数据
     * @author  zgw
     * @version 2016-09-07
     * @param   [type]     $model [description]
     * @return  [type]            [description]
     */
    public static function syncBuild($model)
    {
        $data = [
            'name'                  => $model->name,
            'build_number'          => $model->build_number,
            'address'               => $model->address,
            'province'              => $model->province,
            'city'                  => $model->city,
            'area'                  => $model->area,
            'longitude'             => $model->longitude,
            'latitude'              => $model->latitude,
            'build_type'            => $model->build_type,
            'build_status'          => $model->build_status,
            'create_time'           => $model->create_time,
            'org_id'                => $model->org_id,
            'first_free_strategy'   => $model->first_free_strategy,
            'strategy_change_date'  => $model->strategy_change_date,
            'first_backup_strategy' => $model->first_backup_strategy,
            'distribution_userid'   => empty($model->distribution_userid) ? 0 : $model->distribution_userid,
        ];
        return Api::buildSync($data);
    }

    /**
     * 修改楼宇信息
     * @author  zgw
     * @version 2016-09-08
     * @param   $buildId       楼宇id
     * @return  boole          保存结果
     */
    public static function changeBuild($buildInfo)
    {
        // 修改楼宇状态为已投放
        $buildInfo->build_status = self::SERVED;
        // 修改楼宇绑定状态为绑定过
        $buildInfo->is_bind = 2;

        if ($buildInfo->save() === false) {
            Yii::$app->getSession()->setFlash('error', '楼宇信息修改失败');
            return false;
        }
        return true;
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-09-14
     * @param   [type]     $field [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getField($field, $where)
    {
        $buildInfo = self::find()->select($field)->where($where)->one();
        return $buildInfo ? $buildInfo->$field : '';
    }

    /**
     *  修改ERP后台和咖啡后台的楼宇数据 （未绑定时）
     *  @param $data
     *  @return retuenData
     **/
    public static function updateBuilding($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        // 获取楼宇信息
        $buildModel = self::find()->where(['build_number' => $data['build_number']])->one();
        $buildModel = $buildModel ? $buildModel : new Building();
        // 更新楼宇信息
        if (isset($data['name'])) {
            $buildModel->name = $data['name'];
        }
        if (isset($data['build_number'])) {
            $buildModel->build_number = $data['build_number'];
        }
        if (isset($data['build_type'])) {
            $buildModel->build_type = $data['build_type'];
        }
        if (isset($data['build_status'])) {
            $buildModel->build_status = $data['build_status'];
        }
        if (isset($data['contact_name'])) {
            $buildModel->contact_name = $data['contact_name'];
        }
        if (isset($data['contact_tel'])) {
            $buildModel->contact_tel = $data['contact_tel'];
        }
        if (isset($data['people_num'])) {
            $buildModel->people_num = $data['people_num'];
        }
        if (isset($data['province'])) {
            $buildModel->province = $data['province'];
        }
        if (isset($data['city'])) {
            $buildModel->city = $data['city'];
        }
        if (isset($data['area'])) {
            if (isset($data['province']) && isset($data['city']) && $data['city'] == $data['province']) {
                $buildModel->city = $data['area'];
                $buildModel->area = '';
            }
        }
        if (isset($data['address'])) {
            $buildModel->address = $data['address'];
        }
        if (isset($data['longitude'])) {
            $buildModel->longitude = $data['longitude'];
        }
        if (isset($data['latitude'])) {
            $buildModel->latitude = $data['latitude'];
        }
        if (isset($data['first_free_strategy'])) {
            $buildModel->first_free_strategy = $data['first_free_strategy'];
        }
        if (isset($data['strategy_change_date'])) {
            $buildModel->strategy_change_date = $data['strategy_change_date'];
        }
        if (isset($data['first_backup_strategy'])) {
            $buildModel->first_backup_strategy = $data['first_backup_strategy'];
        }
        if (isset($data['is_bind'])) {
            $buildModel->is_bind = $data['is_bind'];
        }
        if (isset($data['organization_id'])) {
            $buildModel->org_id      = $data['organization_id'];
            $buildModel->create_time = time();
        }
        if (isset($data['create_time'])) {
            $buildModel->create_time = $data['create_time'];
        }
        if ($buildModel->save(false) === false) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '同步失败');
        }
        // 同步到咖啡后台的楼宇数据
        $buildSync = Api::buildSync($data);
        if (!$buildSync) {
            $transaction->rollBack();
            AgentsApi::returnData(1, '同步咖啡后台（修改）失败');
        }
        //事务通过
        $transaction->commit();
        AgentsApi::returnData(0, '同步成功');
    }

    /**
     * 更新首杯策略
     * @author  zgw
     * @version 2016-12-09
     * @return  [type]     [description]
     */
    public static function updateFirst()
    {
        $checkDate = date("Y-m-d");
        $buildList = self::find()->all();
        foreach ($buildList as $build) {
            $backupStrategy = $build->first_backup_strategy;
            $firstStrategy  = $build->first_free_strategy;
            $changeDate     = $build->strategy_change_date;
            if (!empty($changeDate) && $firstStrategy != $backupStrategy && $checkDate >= $changeDate) {
                $build->first_free_strategy = $backupStrategy;
                // 开启事务
                $transaction = Yii::$app->db->beginTransaction();
                // 更新失败回滚
                if (!$build->save(false)) {
                    $transaction->rollBack();
                    continue;
                }
                // 同步到只能平台
                $data = ['build_number' => $build->build_number, 'first_free_strategy' => $backupStrategy];
                // 同步失败回滚
                if (!Api::buildSync($data)) {
                    $transaction->rollBack();
                    continue;
                }
                // 成功提交
                $transaction->commit();
            }
        }
    }

    /**
     * 根据设备id获取当前
     * @return [type] [description]
     */
    public static function getNoDeliveryBuildIdNameArr($orgId)
    {
        $org_id = $orgId ? $orgId : Manager::getManagerBranchID();
        if ($org_id > 1) {
            $where = ['org_id' => $org_id, 'build_status' => self::PRE_DELIVERY];
        } else {
            $where = ['build_status' => self::PRE_DELIVERY];
        }
        return Tools::map(self::getBuildList('id, name', $where), 'id', 'name');
    }

    /**
     * 获取负责该楼宇的配送主管
     * @author  zgw
     * @version 2017-04-28
     * @param   [type]     $buildId [description]
     * @return  [type]              [description]
     */
    public static function getDistributionManager($buildId)
    {
        $buildObj = self::findOne($buildId);
        return !$buildObj || !$buildObj->org_id ? '' : WxMember::getDisResponsibleFromOrg($buildObj->org_id);
    }

    /**
     * 获取当前用户所在分公司已投放的楼宇列表
     * @return [type] [description]
     */
    public static function getOperationBuildStore($type = 1, $userName = '')
    {
        $query = self::find()->orderBy('id desc');
        $query->andFilterWhere(['build_status' => self::SERVED]);
        if ($type == 1) {
            $query->joinWith('equip e')->andFilterWhere(['or', ['e.operation_status' => Equipments::COMMERCIAL_OPERATION], ['e.operation_status' => Equipments::NO_OPERATION], ['e.operation_status' => Equipments::INTERNAL_USE], ['e.operation_status' => Equipments::TEMPORARY_OPERATIONS]]);
        }
        $orgID = WxMember::getOrgId($userName);
        // if ($org_id > 1) {
        //     $query->andFilterWhere(['building.org_id' => $org_id]);
        // }
        if ($orgID > 1) {
            $orgID = Api::getOrgIdArray(['parent_path' => $orgID, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->andFilterWhere(['building.org_id' => $orgID]);
        }
        $buildList    = $query->all();
        $builNameList = array('' => '请选择');
        foreach ($buildList as $key => $value) {
            $builNameList[$value['id']] = $value['name'];
        }
        return $builNameList;
    }

    /**
     * 获取优惠券套餐列表
     * @author  zgw
     * @version 2017-07-24
     * @return  [type]     [description]
     */
    public static function getCouponGroupList()
    {
        $couponGroups[0] = '无';
        $couponGroupList = self::getFirstStagegyNameArray();
        foreach ($couponGroupList as $key => $couponGroup) {
            $couponGroups[$key] = $couponGroup;
        }
        return $couponGroups;
    }

    /**
     * 获取已投放商业运营的设备
     * @author wxl
     * @param int $type
     * @return array
     */
    public static function getBusinessOperation($type = 1)
    {
        $query = self::find()->orderBy('id desc');
        $query->andFilterWhere(['build_status' => self::SERVED]);
        if ($type == 1) {
            $query->joinWith('equip e')->andFilterWhere(['e.operation_status' => Equipments::COMMERCIAL_OPERATION]);
        }
        $userName = yii::$app->user->identity->username;
        $org_id   = WxMember::getOrgId($userName);
        if ($org_id > 1) {
            $query->andFilterWhere(['building.org_id' => $org_id]);
        }
        $buildList     = $query->asArray()->all();
        $buildNameList = ArrayHelper::getColumn($buildList, 'name');
        return $buildNameList;
    }

    /**
     * 获取指定配送员的楼宇列表
     * @author   tuqiang
     * @version  2017-11-28
     * @param    $userId;
     * @return   array     符合条件的楼宇列表
     */
    public static function getBuildingListByUserId($userId)
    {
        return self::find()->where(['distribution_userid' => $userId])->select('name,id')->asArray()->all();
    }

    /**
     * 更新楼宇的负责人
     * @param string $distributionUserID 楼宇负责人
     * @param array  $buildingList       楼宇ID列表
     */
    public static function setBuildingForUser($distributionUserID, $buildingList)
    {
        if ($buildingList) {
            $editRes = self::updateAll(['distribution_userid' => $distributionUserID], ['build_number' => $buildingList]);
        } else {
            $editRes = self::updateAll(['distribution_userid' => ''], ['distribution_userid' => $distributionUserID]);
        }
        if ($editRes !== false) {
            Api::buildingUser($distributionUserID, $buildingList);
            return 1;
        }
        return 0;
    }

    /**
     * 查询楼宇的名称
     * @author sulingling
     * @param $where Array()
     * @param $field  string
     * @return Array()
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
     * 运维任务中存在的运维人员
     * @author wangxiwen
     * @datetime 2018-06-10
     * return array
     */
    public static function getAssignUserid()
    {
        $orgID = Manager::getManagerBranchID();
        if ($orgID > 1) {

        }
        $assignUserid = DistributionTask::find()
            ->alias('dt')
            ->distinct()
            ->leftJoin('wx_member wx', 'wx.userid = dt.assign_userid')
            ->andWhere(['!=', 'assign_userid', ''])
            ->select('dt.assign_userid,wx.name')
            ->createCommand()
            ->queryAll();
        return $assignUserid;

    }

    /**
     * 求两个已知经纬度之间的距离,单位为km
     * @param lng1,lng2 经度
     * @param lat1,lat2 纬度
     * @return float 距离，单位为km
     **/
    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        //将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a       = $radLat1 - $radLat2;
        $b       = $radLng1 - $radLng2;
        $s       = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6371;
        return round($s, 1);
    }

    /**
     * 获取分公司下楼宇
     * @author wangxiwen
     * @version 2018-10-18
     * @param int $orgId 分公司ID
     * @return
     */
    public static function getBuildNameList($orgId)
    {
        $where          = $orgId > 1 ? ['org_id' => $orgId] : [];
        $buildNameArray = self::find()->where($where)->select('id,name')->asArray()->all();
        return Tools::map($buildNameArray, 'id', 'name', null, null);
    }
    /**
     * 点位级别
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-02
     * @return    [array]     [点位级别列表]
     */
    public static function getBuildLevel($level = 0)
    {
        $levelList = [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'X',
        ];
        return $level ? $levelList[$level] : '';
    }
    /**
     * 点位级别列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-03
     * @return    [type]     [description]
     */
    public static function getBuildLevelArr()
    {
        return [
            '' => '请选择',
            1  => 'A',
            2  => 'B',
            3  => 'C',
            4  => 'X',
        ];
    }

    /**
     * 获取楼宇信息
     * @author  wangxiwen
     * @version 2018-12-25
     * @return
     */
    public static function getBuildingArray()
    {
        return Tools::map(self::find()->select('id,name')->asArray()->all(), 'id', 'name', null, null);
    }

    /**
     * 根据楼宇ID获取分公司
     * @author zhenggangwei
     * @date   2019-01-29
     * @param  integer     $id 楼宇ID
     * @return integer         分公司ID
     */
    public static function getOrgIdById($id)
    {
        $build = self::findOne($id);
        return !$build ? 1 : $build->org_id;
    }

    /**
     * 获取楼宇所属的上级机构名称
     * @author zhenggangwei
     * @date   2019-04-22
     * @param  integer     $orgId   机构ID
     * @param  array       $orgList 机构列表
     * @return string               上级机构名称
     */
    public function getParentOrgName($orgId, $orgList)
    {
        if ($orgId && $orgList) {
            $orgIdParentIdList = \yii\helpers\ArrayHelper::map($orgList, 'org_id', 'parent_id');
            $orgIdNameList     = \yii\helpers\ArrayHelper::map($orgList, 'org_id', 'org_name');
            $parentId          = empty($orgIdParentIdList[$orgId]) ? '' : $orgIdParentIdList[$orgId];
            if ($parentId && !empty($orgIdNameList[$parentId])) {
                return $orgIdNameList[$parentId];
            }
        }
        return '';
    }
    /**
     * 根据机构ID获取点位ID对应的点位名称
     * @author zhenggangwei
     * @date   2020-03-19
     * @param  integer     $orgId 机构ID
     * @return array
     */
    public static function getBuildIdNameList($orgId)
    {
        $data            = ['orgID' => $orgId];
        $buildIdNameList = Json::decode(\backend\modules\service\helpers\Api::postBase('erpapi/build-api/id-name-list', $data));
        if ($buildIdNameList['error_code'] == 0) {
            return $buildIdNameList['data'];
        }
        return [];
    }

}
