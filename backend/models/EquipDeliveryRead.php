<?php

namespace backend\models;

use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "equip_delivery_read".
 *
 * @property integer $Id
 * @property string $userId
 * @property integer $read_status
 * @property string $read_time
 * @property string $read_feedback
 * @property string $delivery_id
 */
class EquipDeliveryRead extends \yii\db\ActiveRecord
{
    /** 阅读类型 */
    // 预投放类型
    const PRE_READ = 0;
    // 投放类型
    const READ_TYPE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_delivery_read';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['read_status', 'read_time', 'delivery_id', 'read_type'], 'integer'],
            [['userId'], 'string', 'max' => 50],
            [['read_feedback'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id'            => 'ID',
            'userId'        => '用户',
            'read_status'   => '阅读状态',
            'read_time'     => '阅读时间',
            'read_feedback' => '阅读反馈',
            'delivery_id'   => '投放单id',
            'read_type'     => '阅读类型',
        ];
    }

    /**
     *  获取关联的投放信息
     *
     **/
    public function getDeliver()
    {
        return $this->hasOne(EquipDelivery::className(), ['Id' => 'delivery_id']);
    }

    /**
     *  获取关联的人员
     *
     **/
    public function getMember()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'userId']);
    }

    public static function getDetail($field, $where)
    {
        return self::find()->select($field)->where($where)->one();
    }

}
