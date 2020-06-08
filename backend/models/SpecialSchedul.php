<?php

namespace backend\models;

use common\models\EquipProductGroupApi;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "special_schedul".
 *
 * @property integer $id
 * @property string $special_schedul_name
 * @property string $start_time
 * @property string $end_time
 * @property integer $state
 */
class SpecialSchedul extends \yii\db\ActiveRecord
{
    /** 活动状态 */
    // 上架
    const ACTIVE_ONLINE = 1;
    // 下架
    const ACTIVE_DOWN = 0;

    /** 是否支持优惠券 */
    // 支持优惠券
    const SUPPORT_COUPON = 1;
    // 不支持优惠券
    const NOT_SUPPORT_COUPON = 0;

    /** 用户类型 */
    // 全部用户
    const ALL_USER = 0;
    // 注册用户
    const REGISTER_USER = 1;

    /** 限购方式常量定义 */
    // 每人
    const EVERYONE = 1;
    // 每天
    const EVERYDAY = 2;
    // 总数
    const TOTALNUM = 3;

    /**
     * 活动状态
     * @var [type]
     */
    public $stateList = [
        ''                  => '请选择',
        self::ACTIVE_ONLINE => '上架',
        self::ACTIVE_DOWN   => '下架',
    ];

    /**
     * 是否支持优惠券
     * @var [type]
     */
    public $isCoupon = [
        ''                       => '请选择',
        self::SUPPORT_COUPON     => '支持优惠券',
        self::NOT_SUPPORT_COUPON => '不支持优惠券',
    ];

    /**
     * 用户类型
     * @var [type]
     */
    public $userType = [
        ''                  => '请选择',
        self::ALL_USER      => '所有用户',
        self::REGISTER_USER => '注册用户',
    ];

    public $id; // id
    public $special_schedul_name;
    public $start_time; // 活动开始时间
    public $end_time; // 活动结束时间
    public $state; // 活动状态
    public $user_type; // 用户类型
    public $is_coupons; // 是否支持使用优惠券 （0-不支持 1-支持）
    public $restriction_type; // 限购方式（单天，用户总量，活动总数量）
    public $where_string; // 楼宇搜索条件
    public $buildIdList; // 排期楼宇信息
    public $specialSchedulProductList; //排期产品信息
    public $isCopy; // 是否为复制特价排期 1-是 0-否
    public $copySpecialID; // 复制的特价排期ID
    public $buy_total; //总购买数量
    public $build_name;
    // 优惠方式
    public $discountType = [
        '1' => ['name' => '特价', 'typeParam' => ['activity_value' => '特价', 'activity_name' => '特价名称']],
        '2' => ['name' => '买赠', 'typeParam' => ['buy_cups' => '买', 'gift_cups' => '赠', 'activity_name' => '买赠名称']],
        '3' => ['name' => '加价购', 'typeParam' => ['add_money' => '加', 'gift_cups' => '赠', 'activity_name' => '加价购名称']],
        '4' => ['name' => '第N杯N元', 'typeParam' => ['last_value' => '价格', 'activity_name' => '第N杯N元名称']],
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state', 'special_schedul_name', 'is_coupons', 'user_type'], 'required'],
            [['state'], 'integer'],
            [['special_schedul_name'], 'string', 'max' => 50],
            [['restriction_type', 'where_string', 'id', 'start_time', 'end_time', 'buy_total', 'build_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'special_schedul_name' => '活动名称',
            'start_time'           => '活动开始时间',
            'end_time'             => '活动结束时间',
            'state'                => '活动状态',
            'is_coupons'           => '是否支持优惠券',
            'user_type'            => '用户类型',
            'buy_total'            => '总购买数量',
            'build_name'           => '点位名称',
        ];
    }

    /**
     * 获取排期状态名称
     * @author  zgw
     * @version 2017-10-15
     * @return  string     排期状态名称
     */
    public function getState()
    {
        if (!empty($this->stateList[$this->state])) {
            return $this->stateList[$this->state];
        }
        return '';
    }

    /**
     * 获取排期状态名称
     * @author  zgw
     * @version 2017-10-15
     * @return  string     排期状态名称
     */
    public function getIsCoupon()
    {
        if (!empty($this->isCoupon[$this->is_coupons])) {
            return $this->isCoupon[$this->is_coupons];
        }
        return '';
    }
    /**
     * 获取排期状态名称
     * @author  zgw
     * @version 2017-10-15
     * @return  string     排期状态名称
     */
    public function getUserType()
    {
        if (!empty($this->userType[$this->user_type])) {
            return $this->userType[$this->user_type];
        }
        return '';
    }

    /**
     * 获取特价排期对象
     * @author  zgw
     * @version 2017-10-15
     * @param   integer     $id       特价排期id
     * @param   string      $isUpdate 区分编辑和详情 1-编辑 0-详情
     * @return  object                当前对象
     */
    public function getModel($id = 0, $isUpdate = 0)
    {
        $specialSchedulRet  = EquipProductGroupApi::getSpecialSchedulInfo($id, $isUpdate);
        $specialSchedulInfo = $specialSchedulRet['specialSchedul'];
        $this->load(['SpecialSchedul' => $specialSchedulInfo]);
        $this->buildIdList               = Json::encode($specialSchedulRet['buildIdList']);
        $this->specialSchedulProductList = Json::encode($specialSchedulRet['specialSchedulProductList']);
        $this->start_time                = date('Y-m-d H:i', $this->start_time);
        $this->end_time                  = date('Y-m-d H:i', $this->end_time);
        $this->where_string              = Json::decode($this->where_string);
        return $this;
    }

    /**
     * 获取限购内容
     * @author  zgw
     * @version 2017-03-08
     * @return  [type]     [description]
     */
    public function getRestriction()
    {
        if (!$this->restriction_type) {
            return '不限购';
        }
        $restrictionList = Json::decode($this->restriction_type);
        $content         = '';
        if (is_array($restrictionList)) {
            foreach ($restrictionList as $restrictionType => $restrictionNum) {
                $content .= $this->getRestrictionType(2, $restrictionType) . '限购' . $restrictionNum . '杯<br/>';
            }
        }
        return $content;
    }

    /**
     * 获取限购方式
     * @author  zgw
     * @version 2017-03-08
     * @param   integer    $type       [description]
     * @param   string     $marketType [description]
     * @return  [type]                 [description]
     */
    private function getRestrictionType($type = 1, $restrictionType = '')
    {
        $restrictionTypeArr = [];
        if ($type == 1) {
            $restrictionTypeArr[''] = '请选择';
        }
        $restrictionTypeArr[self::EVERYONE] = '每人总数';
        $restrictionTypeArr[self::EVERYDAY] = '每人每天总数';
        $restrictionTypeArr[self::TOTALNUM] = '活动总数';
        if ($restrictionType) {
            return !isset($restrictionTypeArr[$restrictionType]) ? '' : $restrictionTypeArr[$restrictionType];
        }
        return $restrictionTypeArr;
    }

}
