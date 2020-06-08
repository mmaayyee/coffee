<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scm_total_inventory_gram".
 *
 * @property integer $id
 * @property integer $warehouse_id
 * @property integer $supplier_id
 * @property integer $material_gram
 * @property integer $material_type_id
 */
class ScmTotalInventoryGram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_total_inventory_gram';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'supplier_id', 'material_gram', 'material_type_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'warehouse_id'     => 'Warehouse ID',
            'supplier_id'      => 'Supplier ID',
            'material_gram'    => 'Material Gram',
            'material_type_id' => 'Material Type ID',
        ];
    }

    /**
     * 更新散料总库存
     * @param $warehouseId
     * @param $supplierId
     * @param $materialTypeId
     * @param $materialGram
     * @return bool
     */
    public static function changeInventoryGram($warehouseId, $supplierId, $materialTypeId, $materialGram)
    {
        $inventoryModel = self::findOne(['warehouse_id' => $warehouseId, 'supplier_id' => $supplierId, 'material_type_id' => $materialTypeId]);
        if (!$inventoryModel) {
            $inventoryModel                   = new ScmTotalInventoryGram();
            $inventoryModel->warehouse_id     = $warehouseId;
            $inventoryModel->supplier_id      = $supplierId;
            $inventoryModel->material_type_id = $materialTypeId;
            $inventoryModel->material_gram    = $materialGram;
            return $inventoryModel->save();
        }
        return $inventoryModel->updateCounters(['material_gram' => $materialGram]);
    }

    /**
     * 获取物料散料总库存详情
     * @author  wangxiwen
     * @version 2018-10-10
     * @param   int     $warehouseId 仓库id
     * @param   int     $materialTypeId 物料分类id
     * @return  object
     */
    public static function getInventoryGramDetail($warehouseId, $materialTypeId)
    {
        return self::find()
            ->where([
                'warehouse_id'     => $warehouseId,
                'material_type_id' => $materialTypeId,
            ])
            ->one();
    }

    /**
     * 获取散料的库存信息
     * @param int $warehouseId
     * @return string
     */
    public static function getTotalGram($warehouseId = 0)
    {
        $gramList = self::find()->where(['warehouse_id' => $warehouseId])->asArray()->all();

        $tr = '';
        foreach ($gramList as $gram) {
            $supplierName = ScmSupplier::getField('name', ['id' => $gram['supplier_id']]);
            $materialType = ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $gram['material_type_id']]);
            $tr .= "<tr><td>" . $materialType['material_type_name'] . "</td><td>" . $supplierName . "</td><td>" . $gram['material_gram'] . "</td></tr>";
        }

        return "<table class= 'table table-bordered'><tr><td>物料分类</td><td>供应商</td><td>重量(克)</td></tr>" . $tr . "</table>";
    }

    /**
     * 查询物料的散料总库存
     * @return string
     */
    public static function getTotalInventory()
    {
        $list = self::find()->select('sum(material_gram) material_gram,material_type_id, supplier_id')->groupBy(['material_type_id', 'supplier_id'])->asArray()->all();
        $tr   = '';
        foreach ($list as $gram) {
            $supplierName = ScmSupplier::getField('name', ['id' => $gram['supplier_id']]);
            $materialType = ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $gram['material_type_id']]);
            $tr .= "<tr><td>" . $materialType['material_type_name'] . "</td><td>" . $supplierName . "</td><td>" . $gram['material_gram'] . "</td></tr>";
        }

        return "<table class= 'table table-bordered'><tr><td>物料分类</td><td>供应商</td><td>重量(克)</td></tr>" . $tr . "</table>";
    }
}
