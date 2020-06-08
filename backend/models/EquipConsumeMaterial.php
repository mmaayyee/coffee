<?php

namespace backend\models;

use common\models\Equipments;
use common\models\Sysconfig;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "equip_material".
 *
 * @property integer $id
 * @property integer $equip_code
 * @property double $consume_material
 * @property double $top_value
 * @property double $bottom_value
 * @property string $date
 */
class EquipConsumeMaterial extends \yii\db\ActiveRecord
{
    public $material_stock_code = '';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_consume_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_code'], 'string', 'max' => 50],
            [['material_type_id'], 'integer'],
            [['consume_material'], 'number'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equip_code'       => 'Equip ID',
            'consume_material' => 'Consume Material',
            'date'             => 'Date',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['equip_code' => 'equip_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }

    /**
     * 批量添加数据
     * @param [type] $data [description]
     */
    public static function addAll($data)
    {
        $consumeMaterialArr = [];
        $i                  = 0;
        foreach ($data as $equipCode => $materialStockArr) {
            // 根据设备编号获取设备id
            foreach ($materialStockArr as $materialTypeId => $consumeMaterial) {
                $consumeMaterialArr[$i]['equip_code']       = $equipCode;
                $consumeMaterialArr[$i]['material_type_id'] = $materialTypeId;
                $consumeMaterialArr[$i]['consume_material'] = $consumeMaterial;
                $consumeMaterialArr[$i]['date']             = date('Y-m-d', strtotime("-1 day"));
                $i++;
            }
        }

        // 开启事务
        $transactoin = Yii::$app->db->beginTransaction();
        $delRes      = self::deleteAll();
        // 添加最近三天的数据
        $addRes = Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['equip_code', 'material_type_id', 'consume_material', 'date'], $consumeMaterialArr)->execute();

        if ($delRes === false || $addRes === false) {
            $transactoin->rollBack();
            return false;
        }

        $transactoin->commit();
        return true;
    }

    /**
     * 获取设备平均消耗物料数据
     * @author  zgw
     * @version 2016-08-29
     * @return  [type]     [description]
     */
    public static function getConsumeMaterialList()
    {
        return self::find()->orderBy('date desc')->asArray()->all();
    }

    /**
     * 获取所有的设备编号
     * @author  zgw
     * @version 2016-08-29
     * @return  array     设备编号
     */
    public static function getColumn()
    {
        return ArrayHelper::getColumn(self::find()->select('equip_code')->distinct()->all(), 'equip_code');
    }

    /**
     * 获取设备平均消耗物料数据
     * @author  zgw
     * @version 2016-08-29
     * @return  [type]     [description]
     */
    public static function getConsumeMaterialArr()
    {
        // 初始化平均消耗物料和料仓物料对应关系数组
        $equipConsumeMaterialArr = [];
        // 获取所有设备的平均消耗物料
        $consumeMaterialList = self::find()->all();
        // 料仓编号和上下线值按设备编号和物料分类组装数组
        $materialStockAssocList = ProductMaterialStockAssoc::getEquipMaterialTypeList();
        // 获取特殊料仓和物料分类的对应关系
        $specialMaterialType = json_decode(Sysconfig::getConfig('specialMaterialType'), 1);
        if (!$consumeMaterialList || !$materialStockAssocList) {
            return [];
        }

        foreach ($consumeMaterialList as $consumeMaterialArr) {
            // 验证设备和设备对应料仓是否存在
            if (!$consumeMaterialArr->equip || !isset($materialStockAssocList[$consumeMaterialArr->equip_code]) || !isset($materialStockAssocList[$consumeMaterialArr->equip_code][$consumeMaterialArr->material_type_id])) {
                continue;
            }

            // 是否为杯子等物料
            $type = $consumeMaterialArr->materialType->type;
            // 物料分类id
            $materialTypeId = $consumeMaterialArr->material_type_id;
            // 设备类型id
            $equipTypeId = $consumeMaterialArr->equip->equip_type_id;
            // 如果是杯子则加入杯盖、杯套、搅拌棒
            if ($materialTypeId == 11) {
                $equipMaterialTypeArr = array_keys($materialStockAssocList[$consumeMaterialArr->equip_code]);
                $materialTypeIdArr    = array_intersect($specialMaterialType, $equipMaterialTypeArr);
                $materialObj          = [];
                $materialTypeIdArr[]  = 11;
                foreach ($materialTypeIdArr as $materialTypeIds) {
                    // 获取料仓对应物料信息
                    $materialObj[] = self::getMaterialObj($type, $materialTypeIds, $equipTypeId);
                }
            } else {
                $materialObj = self::getMaterialObj($type, $materialTypeId, $equipTypeId);
            }

            if (!$materialObj) {
                continue;
            }

            foreach ($materialStockAssocList[$consumeMaterialArr->equip_code][$consumeMaterialArr->material_type_id] as $materialStockAssoc) {
                // 获取上限值
                $equipConsumeMaterialArr[$consumeMaterialArr->equip_code][$materialStockAssoc['material_stock_code']]['top_value'] = $materialStockAssoc['top_value'];
                // 获取下限值
                $equipConsumeMaterialArr[$consumeMaterialArr->equip_code][$materialStockAssoc['material_stock_code']]['bottom_value'] = $materialStockAssoc['bottom_value'];
                // 获取平均消耗物料
                $equipConsumeMaterialArr[$consumeMaterialArr->equip_code][$materialStockAssoc['material_stock_code']]['consume_material'] = $consumeMaterialArr->consume_material;
                // 获取是否为杯子等
                $equipConsumeMaterialArr[$consumeMaterialArr->equip_code][$materialStockAssoc['material_stock_code']]['is_stock'] = $consumeMaterialArr->materialType->type;
                // 物料规格
                $equipConsumeMaterialArr[$consumeMaterialArr->equip_code][$materialStockAssoc['material_stock_code']]['materialObj'] = $materialObj;
            }
        }
        return $equipConsumeMaterialArr;
    }

    /**
     * 获取设备料仓对应物料信息
     * @author  zgw
     * @version 2016-10-26
     * @param   [type]     $type                [description]
     * @param   [type]     $equipTypeId         [description]
     * @param   [type]     $equipMaterialTypeId [description]
     * @return  [type]                          [description]
     */
    public static function getMaterialObj($type, $materialTypeId, $equipTypeId)
    {

        // 初始化根据设备型号和物料分类获取物料数据的对象
        $materialObj = '';
        // 如果不为放入料仓中的物料
        if ($type == 2) {
            // 根据设备型号和物料分类获取物料数据
            $materialObj = ScmEquipTypeMaterialAssoc::getEquipTypeMaterialObj(['equip_type_id' => $equipTypeId, 'material_type_id' => $materialTypeId]);
            $materialObj = $materialObj ? $materialObj->material : '';
        }
        // 获取物料id和物料规格
        if (!$materialObj) {
            // 获取该料仓中最小规格的物料信息
            $materialObj = ScmMaterial::getWeightLeast($materialTypeId);
        }
        return $materialObj;
    }

}
