<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "delivery_building".
 *
 * @property string $delivery_building_id
 * @property int $building_id 楼宇id
 * @property string $coverage_radius 覆盖半径范围(米)
 * @property string $business_time 营业时间  以~分割
 * @property int $min_consum 起送价格
 * @property int $business_status 营业状态 1=正常 2=暂停
 */
class DeliveryBuilding extends \yii\db\ActiveRecord
{
    public $delivery_building_id;
    public $delivery_person;
    public $building_id;
    public $coverage_radius;
    public $business_time;
    public $min_consum;
    public $business_status;
    public $person_info;
    public $building_name;
    public $end_time;
    //定义常量外卖点位状态
    const STATUS_VALID     = 1; //正常
    const STATUS_NOT_VALID = 2; //异常
    const STATUS_DEL       = 3; //已删除
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delivery_building';
    }

    //点位状态
    public $business = [
        1 => '正常营业',
        2 => '暂停营业',
    ];

    //配送员
    public $person = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['building_id', 'coverage_radius', 'business_time', 'end_time', 'min_consum'], 'required'],
            [['delivery_building_id', 'building_id', 'business_status', 'coverage_radius'], 'integer'],
            [['min_consum'], 'number'],
            [['business_time', 'end_time'], 'string', 'max' => 30],
            [['coverage_radius', 'business_time', 'person_info', 'building_name','delivery_person'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_building_id' => 'Delivery Building ID',
            'building_id'          => '楼宇',
            'coverage_radius'      => '覆盖范围(米 以点位为中心的方圆半径)',
            'business_time'        => '营业时间(开始)',
            'end_time'             => '营业时间(结束)',
            'min_consum'           => '起送价格(元)',
            'building_name'        => '楼宇名称',
            'business_status'      => '状态',
            'delivery_person'      => '配送员',
        ];
    }
}
