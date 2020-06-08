<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "activity_combin_package_delivery".
 *
 * @property int $delivery_id 发货id
 * @property int $activity_id 自组合套餐活动id
 * @property int $order_id 订单id
 * @property int $address_id 商城用户地址id
 * @property int $distribution_type 配送方式，1-运维，2-快递
 * @property int $distribution_user_id 运维人员id
 * @property string $distribution_user_name 运维人员名称
 * @property string $courier_number 快递单号
 * @property int $is_delivery 是否发货 0-未发货，1-已发货
 * @property int $create_time 添加时间
 */
class ActivityCombinPackageDelivery extends \yii\db\ActiveRecord
{
    public $delivery_id;
    public $activity_id;
    public $order_id;
    public $user_id;
    public $address_id;
    public $distribution_type;
    public $distribution_user_id;
    public $distribution_user_name;
    public $courier_number;
    public $is_delivery;
    public $create_time;
    public $user_mobile;

    public $activity_name;
    public $commodity_num;
    public $receiver;
    public $address;
    public $distribution_type_info;

    public $createFrom;
    public $createTo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'activity_id', 'order_id', 'address_id', 'distribution_type', 'is_delivery', 'create_time', 'user_id'], 'integer'],
            [['distribution_user_name', 'user_mobile', 'receiver', 'commodity_num', 'address', 'activity_name'], 'string', 'max' => 50],
            [['courier_number', 'distribution_type_info', 'distribution_user_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_id' => '发货ID',
            'activity_id' => '活动ID',
            'order_id' => '订单ID',
            'address_id' => '收货地址ID',
            'distribution_type' => '配送方式',
            'distribution_user_id' => '运维人员',
            'distribution_user_name' => '运维人员名称',
            'courier_number' => '快递单号',
            'is_delivery' => '是否发货',
            'create_time' => '购买时间',
            'user_mobile' => '用户手机号',
            'activity_name' =>  '活动名称',
            'commodity_num' =>  '商品数量',
            'receiver'      =>  '收货人',
            'address'       =>  '收货地址',
            'distribution_type_info' => '配送方式',
            'distributio_type'  =>  '配送方式',
            'createFrom'    =>  '查询开始时间',
            'createTo'      =>  '查询结束时间',
        ];
    }

    /**
     * 获取配送方式
     * @author  zmy
     * @version 2018-04-04
     * @return  [type]     [description]
     */
    public static function getdistributioTypeList()
    {
        return [
            ''  =>   '请选择',
            '1' =>   '运维',
            '2' =>   '快递',
        ];
    }

}
