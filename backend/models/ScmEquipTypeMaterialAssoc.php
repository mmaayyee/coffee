<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scm_equip_type_material_assoc".
 *
 * @property integer $id
 * @property integer $equip_type_id
 * @property integer $material_id
 * @property integer $material_type_id
 *
 * @property ScmEquipType $equipType
 * @property ScmMaterial $material
 * @property ScmMaterialType $materialType
 */
class ScmEquipTypeMaterialAssoc extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'scm_equip_type_material_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['equip_type_id', 'material_id', 'material_type_id'], 'integer'],
            [['equip_type_id', 'material_type_id'], 'unique', 'targetAttribute' => ['equip_type_id', 'material_type_id'], 'message' => '该数据已存在.'],
            [['equip_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmEquipType::className(), 'targetAttribute' => ['equip_type_id' => 'id']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterial::className(), 'targetAttribute' => ['material_id' => 'id']],
            [['material_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterialType::className(), 'targetAttribute' => ['material_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'               => 'ID',
            'equip_type_id'    => '设备型号的id',
            'material_id'      => '物料id（物料ID多种，物料的分类）',
            'material_type_id' => '物料分类id',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipType() {
        return $this->hasOne(ScmEquipType::className(), ['id' => 'equip_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial() {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType() {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }

    /**
     * 根据设备类型获取物料id
     * @author  zgw
     * @version 2016-09-23
     * @param   [type]     $equipTypeId [description]
     * @return  [type]                  [description]
     */
    public static function getMaterialId($equipTypeId) {
        return \yii\helpers\ArrayHelper::getColumn(self::find()->where(['equip_type_id' => $equipTypeId])->all(), 'material_id');
    }

    /**
     * 获取设备类型物料数据
     * @author  zgw
     * @version 2016-10-21
     * @param   array     $where 查询条件
     * @return  array            查询结果
     */
    public static function getEquipTypeMaterialObj($where) {
        return self::find()->where($where)->one();
    }
}
