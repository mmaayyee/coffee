<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "delivery_person".
 *
 * @property string $person_id
 * @property string $person_name 配送人员名
 * @property string $wx_number 配送人员企业微信号
 * @property string $mobile 配送员手机号
 */
class DeliveryRegion extends \yii\db\ActiveRecord
{
    public $delivery_region_id;
    public $region_name;
    public $coverage_range;
    public $province;
    public $start_time;
    public $end_time;
    public $min_consum;
    public $business_time;
    public $business_status;
    public $build_list;
    public $person_list;
    //定义常量外卖点位状态
    const STATUS_VALID     = 1; //正常
    const STATUS_NOT_VALID = 2; //异常
    const STATUS_DEL       = 3; //已删除
    public $business = [
        1 => '正常',
        2 => '暂停',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delivery_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_region_id', 'region_name'], 'required'],
            [['business_status'], 'integer'],
            [['min_lng','min_lat','max_lng','max_lat','min_consum','business_time'], 'string'],
            [['coverage_range'], 'string', 'max' => 10000],
            [['province', 'city'], 'string', 'max' => 50],
            [['region_name', 'start_time', 'end_time'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_region_id'    => 'Person ID',
            'region_name'           => '区域名称',
            'business_status'       => '营业状态',
            'min_lng'               => '最小经度',
            'min_lat'               => '最小维度',
            'max_lng'               => '最大经度',
            'max_lat'               => '最大维度',
            'coverage_range'        => '区域坐标',
            'start_time'            => '营业时间',
            'end_time'              => '至',
        ];
    }

}
