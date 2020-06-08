<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quick_send_coupon_assoc".
 *
 * @property integer $quick_send_coupon_id
 * @property string $send_phone
 */
class QuickSendCouponAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quick_send_coupon_id', 'send_phone'], 'required'],
            [['quick_send_coupon_id'], 'integer'],
            [['send_phone'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'quick_send_coupon_id' => 'Quick Send Coupon ID',
            'send_phone' => 'Send Phone',
        ];
    }
}
