<?php

namespace backend\models;

use common\models\ArrayDataProviderSelf;
use common\models\CoffeeBackApi;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "point_position".
 *
 * @property int $point_id 点位ID
 * @property string $point_name 点位名称
 * @property string $province 省
 * @property string $city 市
 * @property string $area 区
 * @property string $point_img 点位图
 * @property string $point_description 点位描述
 * @property int $point_type_id 点位类型ID
 * @property double $day_peoples 日人流量
 * @property int $cooperation_type 合作方式
 * @property int $pay_cycle 付费周期
 * @property int $point_status 点位状态
 * @property int $create_time 创建时间
 */
class PointPosition extends \yii\db\ActiveRecord
{
    /** 点位状态 0-可投放 1-已锁定 2-已投放 */
    const STATUS_CAN_PUT_IN = 0;
    const STATUS_LOCKED     = 1;
    const STATUS_PUT_IN     = 2;

    /** 付款周期 0-年付 1-半年付 */
    const CYCLE_YEAR      = 0;
    const CYCLE_HALF_YEAR = 1;

    /** 合作方式 0-直签 1-第三方 */
    const TYPE_SELF        = 0;
    const TYPE_THiRD_PARTY = 1;

    /**
     * 点位状态
     * @var [type]
     */
    public static $pointStatusList = [
        self::STATUS_CAN_PUT_IN => '可投放',
        self::STATUS_LOCKED     => '已锁定',
        self::STATUS_PUT_IN     => '已投放',
    ];

    /**
     * 点位星级
     * @var [type]
     */
    public static $starLevelList = [
        '0'   => '0星',
        '0.5' => '0.5星',
        '1'   => '1星',
        '1.5' => '1.5星',
        '2'   => '2星',
        '2.5' => '2.5星',
        '3'   => '3星',
        '3.5' => '3.5星',
        '4'   => '4星',
        '4.5' => '4.5星',
        '5'   => '5星',
    ];

    /**
     * 付费周期
     * @var [type]
     */
    public static $payCycleList = [
        self::CYCLE_YEAR      => '年付',
        self::CYCLE_HALF_YEAR => '半年付',
    ];

    /**
     * 合作方式
     * @var [type]
     */
    public static $cooperationTypeList = [
        self::TYPE_SELF        => '直签',
        self::TYPE_THiRD_PARTY => '第三方',
    ];

    public $point_id;
    public $point_name;
    public $province;
    public $city;
    public $area;
    public $address;
    public $point_img;
    public $point_description;
    public $point_type_id;
    public $day_peoples;
    public $cooperation_type;
    public $pay_cycle;
    public $point_status;
    public $create_time;
    public $startTime;
    public $endTime;
    public $point_list;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['point_type_id', 'cooperation_type', 'pay_cycle', 'point_status', 'day_peoples', 'point_name', 'province', 'city', 'area', 'address', 'point_description'], 'required', 'on' => ['create', 'update']],
            [['point_img'], 'required', 'on' => 'create'],
            [['point_type_id', 'cooperation_type', 'pay_cycle', 'point_status', 'create_time', 'point_id'], 'integer'],
            [['day_peoples'], 'match', 'pattern' => "/^(1000|([1-9]\d{2})|([1-9]\d)|(\d))(\.[1-9])?$/", 'message' => '日人流量只能是0~1000最大一位小数的数字'],
            [['point_name', 'province', 'city', 'area'], 'string', 'max' => 32],
            [['point_img', 'point_description'], 'string', 'max' => 300],
            [['address'], 'string', 'max' => 64],
            [['point_list'], 'string', 'max' => 1000],
            [['point_img'], 'file', 'extensions' => 'jpeg, jpg, png', 'maxSize' => '204800'],
            [['startTime', 'endTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'point_id'          => '点位ID',
            'point_name'        => '楼宇名称',
            'province'          => '省',
            'city'              => '市',
            'area'              => '区',
            'point_img'         => '楼宇图',
            'point_description' => '楼宇描述',
            'point_type_id'     => '楼宇类型',
            'day_peoples'       => '日人流量',
            'cooperation_type'  => '合作方式',
            'pay_cycle'         => '付费周期',
            'point_status'      => '楼宇状态',
            'create_time'       => '创建时间',
            'point_list'        => '楼宇列表',
            'address'           => '详细地址',
        ];
    }

    public function search($params)
    {
        $this->load($params);
        $this->validate(true);
        $searchParams         = $params['PointPosition'] ?? [];
        $searchParams['page'] = $params['page'] ?? 1;
        $pointList            = CoffeeBackApi::getPointPostionList($searchParams);
        $pointTypeList        = $pointList['buildTypeList'] ?? [];
        $dataProvider         = [];
        if ($pointList) {
            foreach ($pointList['pointPositionList'] as $key => $data) {
                $pointPosition = new PointPosition();
                $pointPosition->load(['PointPosition' => $data]);
                $dataProvider[$key] = $pointPosition;
            }
        }
        $pointPositionList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => $pointList['pageSize'],
            ],
            'totalCount' => $pointList['total'] ?? 0,
            'sort'       => [
                'attributes' => ['point_id desc'],
            ],
        ]);
        return [$pointPositionList, $pointTypeList];
    }

    /**
     * 获取楼宇销量星级
     * @author zhenggangwei
     * @date   2020-05-06
     * @return float
     */
    public function getStarLevel()
    {
        $pointList = Json::decode($this->point_list);
        if ($this->point_status == self::STATUS_PUT_IN) {
            $totalStar = array_sum(array_column($pointList, 2));
            $total     = count($pointList);
            $scale     = $totalStar / $total;
            $scaleArr  = explode('.', $scale);
            if (empty($scaleArr[1])) {
                return $scale . '星';
            }
            if ($scale > $scaleArr[0] . ".5") {
                return ($scaleArr[0] + 1) . '星';
            } else {
                return $scaleArr[0] . ".5星";
            }
        }
        return '无星级';
    }

}
