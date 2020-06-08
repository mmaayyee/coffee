<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grind_equip_assoc".
 *
 * @property integer $grind_equip_id
 * @property string $equipment_code
 * @property integer $grind_id
 *
 * @property Equipments $equipmentCode
 * @property Grind $grind
 */
class GrindEquipAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'grind_equip_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grind_id'], 'integer'],
            [['equipment_code'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grind_equip_id' => '磨豆楼宇设备表主键id',
            'equipment_code' => '设备编号',
            'grind_id' => '磨豆关联id',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCode()
    {
        return $this->hasOne(Equipments::className(), ['equipment_code' => 'equipment_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrind()
    {
        return $this->hasOne(Grind::className(), ['grind_id' => 'grind_id']);
    }
}
