<?php

namespace backend\models;

use Yii;
use backend\models\ScmMaterialType;
/**
 * This is the model class for table "scm_warehouse_out_gram".
 *
 * @property integer $id
 * @property integer $warehouse_out_id
 * @property integer $supplier_id
 * @property integer $material_out_gram
 * @property integer $material_type_id
 */
class ScmWarehouseOutGram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_warehouse_out_gram';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_out_id', 'supplier_id', 'material_out_gram', 'material_type_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_out_id' => 'Warehouse Out ID',
            'supplier_id' => 'Supplier ID',
            'material_out_gram' => 'Material Out Gram',
            'material_type_id' => 'Material Type ID',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }

    /**
     * 获取物料的重量
     * @param $filed
     * @param $where
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getMaterialGram($filed, $where)
    {
        return self::find()->select($filed)->where($where)->asArray()->one();
    }

}
