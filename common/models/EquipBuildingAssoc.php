<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equip_building_assoc".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property integer $build_id
 *
 * @property Equipments $equip
 * @property Building $build
 */
class EquipBuildingAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_building_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'build_id'], 'integer'],
            [['equip_id', 'build_id'], 'unique', 'targetAttribute' => ['equip_id', 'build_id'], 'message' => '该楼宇与该设备已经绑定']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equip_id' => '设备id',
            'build_id' => '楼宇id',
        ];
    }

    public static function getEquipOrBuildid($field,$where)
    {
        return self::find()
            -> select($field)
            -> where($where)
            -> asArray()
            -> one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['id' => 'equip_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }
}
