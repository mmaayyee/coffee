<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scm_stock_gram".
 *
 * @property integer $id
 * @property integer $scm_stock_id
 * @property integer $supplier_id
 * @property integer $material_gram
 * @property integer $material_type_id
 */
class ScmStockGram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_stock_gram';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['scm_stock_id', 'supplier_id', 'material_gram', 'material_type_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scm_stock_id' => 'Scm Stock ID',
            'supplier_id' => 'Supplier ID',
            'material_gram' => 'Material Gram',
            'material_type_id' => 'Material Type ID',
        ];
    }

    /**
     * 获取散料的信息
     * @param int $scmStockId
     * @return string
     */
    public static function getScmStockGram($scmStockId = 0)
    {
        $gramList = ScmStockGram::find()->where(['scm_stock_id' => $scmStockId])->asArray()->all();
        if(empty($gramList)){
            return '';
        }
        $tr = '';
        foreach ($gramList as $gram) {
            $supplierName = ScmSupplier::getField('name', ['id' => $gram['supplier_id']]);
            $materialType = ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $gram['material_type_id']]);
            $tr .= "<tr><td>" . $materialType['material_type_name'] . "</td><td>" . $supplierName . "</td><td>" . $gram['material_gram'] . "</td></tr>";
        }
        return "<table class= 'table table-bordered'><tr><td>物料名称</td><td>供应商</td><td>重量(克)</td></tr>" . $tr . "</table>";
    }

    /**
     * 查询入库的散料
     * @param int $stockId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getStockGram($stockId = 0){
       return ScmStockGram::find()->where(['scm_stock_id' => $stockId])->asArray()->all();
    }
}
