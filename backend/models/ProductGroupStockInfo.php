<?php

namespace backend\models;

use backend\models\ScmMaterialStock;
use Yii;

/**
 * This is the model class for table "product_group_stock_info".
 *
 * @property integer $id
 * @property string $product_group_stock_name
 * @property string $equip_type_id
 */
class ProductGroupStockInfo extends \yii\db\ActiveRecord
{
    public $id;
    public $is_operation;
    public $equip_type_id;
    public $product_group_stock_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_material_stock_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'product_group_stock_name'], 'required', 'on' => ['create', 'update']],
            [['equip_type_id', 'is_operation'], 'integer'],
            [['product_group_stock_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                       => 'ID',
            'product_group_stock_name' => '产品组料仓信息名称',
            'equip_type_id'            => '设备类型',
        ];
    }

    /**
     * 同步产品组料仓信息(编辑产品组料仓信息时)
     * @author wangxiwen
     * @version 2018-09-07
     * @param  [array] $productGroupInfo [产品组料仓信息]
     * @param  [int] $groupId [产品组ID]
     * @return [type]                   [description]
     */
    public static function saveProductGroupStockInfo($productGroupInfo, $groupId)
    {
        //删除原有产品组料仓信息数据
        self::deleteAll(['pro_group_id' => $groupId]);
        //获取料仓ID和料仓编号对应关系
        $stockInfo = ScmMaterialStock::getMaterialStockCodeToId();
        //新增产品组料仓信息数据
        foreach ($productGroupInfo['stockList'] as $stock) {
            $model                    = new self();
            $model->pro_group_id      = $groupId;
            $model->material_stock_id = $stockInfo[$stock['stock_code']];
            $model->material_type     = $stock['material_type_id'];
            $model->top_value         = $stock['top_value'];
            $model->bottom_value      = $stock['bottom_value'];
            $model->warning_value     = $stock['warning_value'];
            $model->pre_second_gram   = $stock['blanking_rate'];
            $ret                      = $model->save();
            if (!$ret) {
                return false;
            }
        }
        return true;
    }

    /**
     * 同步产品组料仓信息(编辑产品组时)
     * @author wangxiwen
     * @version 2018-09-10
     * @param  [array] $productGroupInfo [产品组料仓信息]
     * @param  [int] $groupId [产品组ID]
     * @return [type]                   [description]
     */
    public static function syncProductGroupStockInfo($syncData)
    {
        //删除原有产品组料仓信息数据
        self::deleteAll(['pro_group_id' => $syncData['groupId']]);
        //获取料仓ID和料仓编号对应关系
        $stockInfo = ScmMaterialStock::getMaterialStockCodeToId();
        //新增产品组料仓信息数据
        foreach ($syncData['productGroupStockInfo'] as $stock) {
            $model                    = new self();
            $model->pro_group_id      = $syncData['groupId'];
            $model->material_stock_id = $stockInfo[$stock['stock_code']];
            $model->material_type     = $stock['material_type_id'];
            $model->top_value         = $stock['stock_volume_bound'];
            $model->bottom_value      = $stock['bottom_value'];
            $model->warning_value     = $stock['warning_value'];
            $model->pre_second_gram   = $stock['blanking_rate'];
            $ret                      = $model->save();
            if (!$ret) {
                return false;
            }
        }
        return true;
    }
}
