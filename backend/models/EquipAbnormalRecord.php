<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_abnormal_record".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property string $abnormal_content
 * @property integer $create_time
 */
class EquipAbnormalRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_abnormal_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'create_time'], 'integer'],
            [['abnormal_content'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equip_id' => 'Equip ID',
            'abnormal_content' => 'Abnormal Content',
            'create_time' => 'Create Time',
        ];
    }
}
