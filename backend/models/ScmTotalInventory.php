<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scm_total_inventory".
 *
 * @property integer $id
 * @property string $organization_id
 * @property string $material_id
 * @property string $total_number
 */
class ScmTotalInventory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_total_inventory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'material_id', 'total_number'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'warehouse_id' => '库名称',
            'material_id'  => '物料名称',
            'total_number' => '物料总数/包',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(ScmWarehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * 获取物料总库存详情
     * @author  wangxiwen
     * @version 2018-10-10
     * @param   int     $warehouseId 仓库id
     * @param   int     $materialId 物料id
     * @return  object
     */
    public static function getInventoryDetail($warehouseId, $materialId)
    {
        return self::find()
            ->where([
                'warehouse_id' => $warehouseId,
                'material_id'  => $materialId,
            ])
            ->one();
    }

    /**
     * 更新总库存
     * @author  zgw
     * @version 2016-12-05
     * @param   integer     $warehouseId 仓库id
     * @param   integer     $materialId  物料id
     * @param   integer     $totalNumber 物料数量
     * @return  boole
     */
    public static function changeInventory($warehouseId, $materialId, $totalNumber)
    {
        $inventoryModel = self::findOne(['warehouse_id' => $warehouseId, 'material_id' => $materialId]);
        if (!$inventoryModel) {
            $inventoryModel               = new ScmTotalInventory();
            $inventoryModel->warehouse_id = $warehouseId;
            $inventoryModel->material_id  = $materialId;
            $inventoryModel->total_number = $totalNumber;
            return $inventoryModel->save();
        }
        return $inventoryModel->updateCounters(['total_number' => $totalNumber]);
    }

    /**
     * 获取仓库的库存信息
     * @param int $warehouseId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getWarehouseInventory($warehouseId = 0)
    {
        return self::find()->where(['warehouse_id' => $warehouseId])->asArray()->all();
    }
}
