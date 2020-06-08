<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "activity_order_assoc".
 *
 * @property int $activity_order_id 活动订单关联id
 * @property int $activity_id 活动id
 * @property int $order_id 订单id
 * @property int $type 类型，1-自组合套餐活动
 * @property int $create_time 添加时间
 */
class ActivityOrderAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_order_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'order_id', 'type', 'create_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activity_order_id' => 'Activity Order ID',
            'activity_id' => 'Activity ID',
            'order_id' => 'Order ID',
            'type' => 'Type',
            'create_time' => 'Create Time',
        ];
    }
}
