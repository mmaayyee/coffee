<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "product_material_stock_assoc".
 *
 * @property integer $pro_group_id
 * @property integer $material_stock_id
 * @property integer $material_type
 * @property double $top_value
 * @property double $bottom_value
 * @property integer $pre_second_gram
 */
class ProductMaterialStockAssoc extends \yii\db\ActiveRecord
{
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
            [['pro_group_id', 'material_stock_id'], 'required'],
            [['pro_group_id', 'material_stock_id', 'material_type'], 'integer'],
            [['top_value', 'bottom_value', 'pre_second_gram'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pro_group_id'      => 'Pro Group ID',
            'material_stock_id' => 'Material Stock ID',
            'material_type'     => 'Material Type',
            'top_value'         => 'Top Value',
            'bottom_value'      => 'Bottom Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialStock()
    {
        return $this->hasOne(ScmMaterialStock::className(), ['id' => 'material_stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasMany(ScmMaterial::className(), ['material_type' => 'material_type'])->andWhere(['is_operation' => ScmMaterial::IS_OPERATION]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasMany(Equipments::className(), ['pro_group_id' => 'pro_group_id']);
    }

    /**
     * 获取设备料仓和物料以及上下线值
     * @param  string $field [description]
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public static function getDetail($field = '*', $where = [])
    {
        return self::find()->select($field)->where($where)->one();
    }

    /**
     * 获取产品组料仓和物料以及上下线值
     * @param string $field
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getProductGroupMaterialList($field = '*', $where = [])
    {
        return self::find()->select($field)->where($where)->all();
    }

    /**
     * 根据设备id获取料仓和物料分类关联以及其对应的物料列表（手机端配送任务时用）
     * @param $equipId
     * @param $materialArr
     * @return array
     */
    public static function getMaterialList($equipId, $materialArr, $taskType)
    {
        $pro_group_id      = Equipments::getField('pro_group_id', ['id' => $equipId]);
        $equipTypeId       = Equipments::getField('equip_type_id', ['id' => $equipId]);
        $materialStockList = self::getProductGroupMaterialList('*', ['pro_group_id' => $pro_group_id]);
        // 获取设备料仓和物料分类对应列表
        //$materialStockList = self::find()->where(['equip_id' => $equipId])->all();
        if (!$materialStockList) {
            return [];
        }
        //获取料仓对应的料仓编码
        $stockCode = ScmMaterialStock::getMaterialStockIdToCode();

        //查询料仓剩余物料
        $equipmentCode         = Equipments::getField('equip_code', ['id' => $equipId]);
        $surplusMaterialNumber = EquipSurplusMaterial::getEquipmentStockMaterial($equipmentCode);

        // 初始化物料列表数组
        $materialList = [];
        // 初始化物料分类数组
        $materialTypeIdArr = [];
        // 遍历料仓和物料分类对应列表
        foreach ($materialStockList as $materialStockObj) {
            // 判断料仓、物料分类、物料是否真实存在
            if (!$materialStockObj->materialStock || !$materialStockObj->materialType || !$materialStockObj->material || !$materialStockObj->material_type) {
                continue;
            }
            //配送任务中的配送物料ID
            $distributionMaterialId = isset($materialArr[$materialStockObj->material_type]['material_id']) ? $materialArr[$materialStockObj->material_type]['material_id'] : '';
            //配送任务中配送物料的包数
            $distributionMaterialPacks = isset($materialArr[$materialStockObj->material_type]['packets']) ? $materialArr[$materialStockObj->material_type]['packets'] : 0;

            // 获取料仓id
            $materialList[$materialStockObj->material_stock_id]['material_type_id'] = $materialStockObj->material_type;
            // 获取料仓名称
            $materialList[$materialStockObj->material_stock_id]['materialStockName'] = $materialStockObj->materialType->type == 1 ? $materialStockObj->materialStock->name : '';
            // 获取物料分类名称
            $materialList[$materialStockObj->material_stock_id]['materialTypeName'] = $materialStockObj->materialType->material_type_name;
            // 获取设备该分类的固定物料
            $fixedMaterialObj = ScmEquipTypeMaterialAssoc::getEquipTypeMaterialObj(['equip_type_id' => $equipTypeId, 'material_type_id' => $materialStockObj->material_type]);
            //添加料后的剩余值
            $totalSurplusMaterial = 0;
            // 固定物料存在
            if ($fixedMaterialObj) {
                $materialList[$materialStockObj->material_stock_id]['material'][$fixedMaterialObj->material_id]['option'] = $fixedMaterialObj->material->weight > 0 ? $fixedMaterialObj->material->supplier->name . '--' . $fixedMaterialObj->material->name . '--' . $fixedMaterialObj->material->weight . '--' . $materialStockObj->materialType->spec_unit : $fixedMaterialObj->material->supplier->name . '--' . $fixedMaterialObj->material->name;
                //默认选中的规格
                $materialList[$materialStockObj->material_stock_id]['material'][$fixedMaterialObj->material_id]['default_val'] = $fixedMaterialObj->material->weight;
            } else {
                // 获取该分类的物料列表
                foreach ($materialStockObj->material as $key => $materialObj) {
                    $materialList[$materialStockObj->material_stock_id]['material'][$materialObj->id]['option'] = $materialObj->weight > 0 ? $materialObj->supplier->name . '--' . $materialObj->name . '--' . $materialObj->weight . '--' . $materialStockObj->materialType->spec_unit : $materialObj->supplier->name . '--' . $materialObj->name;
                    //默认选中的规格
                    $materialList[$materialStockObj->material_stock_id]['material'][$materialObj->id]['default_val'] = $materialObj->weight;
                    //配送任务配送的物料包数>0并且物料ID相同
                    if ($distributionMaterialPacks > 0 && $distributionMaterialId == $materialObj->id) {
                        $totalSurplusMaterial = $materialObj->weight > 0 ? $materialObj->weight * intval($distributionMaterialPacks) : 0;
                    }
                }
            }

            // 获取配送任务中的物料id
            $materialList[$materialStockObj->material_stock_id]['selectId'] = $distributionMaterialId;

            $materialList[$materialStockObj->material_stock_id]['material_out_gram'] = '';
            //料仓预估剩余物料
            $surplusMaterial = isset($surplusMaterialNumber[$stockCode[$materialStockObj->material_stock_id]]) ? $surplusMaterialNumber[$stockCode[$materialStockObj->material_stock_id]] : 0;

            $materialList[$materialStockObj->material_stock_id]['surplus_material'] = intval($surplusMaterial);

            $materialList[$materialStockObj->material_stock_id]['change_surplus_material'] = '';

            $materialList[$materialStockObj->material_stock_id]['is_add'] = 'checked';

            $materialList[$materialStockObj->material_stock_id]['is_change'] = '';

            $materialList[$materialStockObj->material_stock_id]['change_surplus_material'] = '';

            //添加的物料加上剩余的物料
            $materialList[$materialStockObj->material_stock_id]['total_surplus_material'] = in_array(DistributionTask::REFUEL, $taskType) ? $totalSurplusMaterial : $totalSurplusMaterial + $surplusMaterial;

            //获取料仓的上限值
            $materialList[$materialStockObj->material_stock_id]['stock_top'] = $materialStockObj->top_value;

            //获取料仓的下限值
            //$materialList[$materialStockObj->material_stock_id]['stock_bottom'] = $materialStockObj->bottom_value;

            // 获取配送任务中物料的包数
            if (!in_array($materialStockObj->material_type, $materialTypeIdArr) && $distributionMaterialPacks > 0) {
                $materialList[$materialStockObj->material_stock_id]['packets'] = intval($distributionMaterialPacks);
                if (in_array(DistributionTask::REFUEL, $taskType)) {
                    $materialList[$materialStockObj->material_stock_id]['is_add']    = '';
                    $materialList[$materialStockObj->material_stock_id]['is_change'] = 'checked';
                }
            } else {
                $materialList[$materialStockObj->material_stock_id]['packets'] = '';
            }
            // 获取物料分类单位
            $materialList[$materialStockObj->material_stock_id]['unit']              = $materialStockObj->materialType->unit;
            $materialList[$materialStockObj->material_stock_id]['weight_unit']       = $materialStockObj->materialType->weight_unit;
            $materialList[$materialStockObj->material_stock_id]['is_stock_material'] = $materialStockObj->materialType->type;
            // 物料分类id数组
            $materialTypeIdArr[] = $materialStockObj->material_type;

        }
        return $materialList;
    }

    /**
     * 处理换料日常任务
     * @param $equipObj
     * @param $materialType
     * @param $author
     * @return array
     */
    public static function detailChangeMaterial($equipObj, $materialType, $author, $taskData, $cleanCycle, $holiday)
    {
        // 初始化是否生成清洗任务
        $isClean = 1;
        // 获取上限值、下限值
        $productMaterialStockList = self::getProductGroupMaterialList('*', ['pro_group_id' => $equipObj->pro_group_id, 'material_type' => $materialType]);

        if (!$productMaterialStockList) {
            return $taskData;
        }

        //判断设备类型是否是节假日不运维的设备
        $stopBuildingList = BuildingHolidayStatus::getSettingStopBuildingID();
        if (in_array($equipObj->build_id, $stopBuildingList) && in_array(date('Y-m-d', strtotime('1 day')), $holiday)) {
            return $taskData;
        }

        foreach ($productMaterialStockList as $productMaterialStockObj) {
            // 如果不为放入料仓中的物料
            if ($productMaterialStockObj->materialType->type == 2) {
                continue;
            }
            // 获取该料仓中最小规格的物料信息
            $materialObj = ScmMaterial::getWeightLeast($productMaterialStockObj->material_type);
            //物料规格小于1则不生成换料任务
            if (!$materialObj || $materialObj->weight < 1) {
                continue;
            }

            // 配送的包数
            $packetNum = ceil($productMaterialStockObj->top_value / $materialObj->weight);

            $refuelArr = [
                'distribution_userid' => $author,
                'build_id'            => $equipObj->build_id,
                'org_id'              => $equipObj->org_id,
                'consume_material'    => 0,
                'material_id'         => $materialObj->id,
                'packet_num'          => $packetNum,
                'weight'              => $materialObj->weight,
                'date'                => date('Y-m-d', strtotime('1 day')),
                'remark'              => '需要换料',
            ];
            $taskData[] = $refuelArr;
            $isClean    = 0;
        }

        // 没有换料任务生成则生成清洗任务
        if ($isClean == 1) {
            $taskData[] = self::generateClearTask($equipObj, $cleanCycle, $author);
        }

        return $taskData;
    }

    /**
     * 生成清洗任务数据
     * @author wxl
     * @param $equipObj
     * @param $cleanCycle
     * @param $author
     * @return array
     */
    public static function generateClearTask($equipObj, $cleanCycle, $author)
    {
        // 获取清洗任务数据
        $cleanTaskData = DistributionDailyTask::getCleanTaskData($equipObj, $cleanCycle, $author);
        if ($cleanTaskData) {
            return $cleanTaskData;
        }
    }

    /**
     * 添加设备物料料仓上下线值对应关系数据
     * @param $syncData
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function addAll($syncData)
    {
        set_time_limit(0);
        $i    = 0;
        $data = $equipList = [];
        if ($syncData) {
            // 获取系统配置中的特殊料仓和物料的对应关系数组
            // $specialMaterialType = json_decode(Sysconfig::getConfig('specialMaterialType'), 1);
            // // 特殊料仓数组
            // $specialStockArr = array_keys($specialMaterialType);
            // 接口中的数据
            foreach ($syncData as $groupId => $groupList) {
                foreach ($groupList as $stockCode => $limitArr) {
                    if (!is_array($limitArr)) {
                        continue;
                    }

                    // 根据料仓编号获取料仓id
                    $material_stock_id = ScmMaterialStock::getField('id', ['stock_code' => $stockCode]);
                    if (!$material_stock_id) {
                        continue;
                    }
                    $data[$i]['pro_group_id']      = $groupId;
                    $data[$i]['material_stock_id'] = $material_stock_id;
                    $data[$i]['material_type']     = $limitArr['material_type_id'];
                    $data[$i]['top_value']         = $limitArr['gstockTop'];
                    $data[$i]['bottom_value']      = $limitArr['gstockBottom'];
                    $data[$i]['pre_second_gram']   = $limitArr['second']; // 出料速度 second
                    $data[$i]['warning_value']     = $limitArr['warning_value']; // 预警值
                    // $data[$i]['change_material_time'] = self::getField('change_material_time', ['equip_id' => $equipObj->id, 'material_stock_id' => $material_stock_id]);
                    $i++;
                }
            }
        }
        if (!$data) {
            return true;
        }
        // 批量添加，产品组料仓关联表
        return self::batchInsertProMaterialStockAssoc($data);

    }

    /**
     * 批量添加，产品组料仓关联表
     * @author  zmy
     * @version 2017-09-26
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function batchInsertProMaterialStockAssoc($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $delres      = self::deleteAll();
        $addres      = Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['pro_group_id', 'material_stock_id', 'material_type', 'top_value', 'bottom_value', 'pre_second_gram', 'warning_value'], $data)->execute();
        if ($delres === false || $addres === false) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }

    /**
     * 把料仓编号和上下线值按设备编号和物料分类组装数组
     * @author wangxl
     * @return array
     */

    public static function getEquipMaterialTypeList()
    {
        $materialStockList     = [];
        $materialTypeStockList = self::getProductGroupMaterialList();
        foreach ($materialTypeStockList as $materialTypeStockObj) {
            if (!isset($materialTypeStockObj->pro_group_id)) {
                continue;
            }
            $equipments = Equipments::getEquipList('equip_code', ['pro_group_id' => $materialTypeStockObj->pro_group_id]);
            foreach ($equipments as $k => $equipment) {

                if (!isset($equipment->equip_code)) {
                    continue;
                }

                // 获取给物料分类的料仓编号
                $materialStockList[$equipment->equip_code][$materialTypeStockObj->material_type][$materialTypeStockObj->material_stock_id]['material_stock_code'] = $materialTypeStockObj->materialStock->stock_code;
                // 上限值
                $materialStockList[$equipment->equip_code][$materialTypeStockObj->material_type][$materialTypeStockObj->material_stock_id]['top_value'] = $materialTypeStockObj->top_value;
                // 下限值
                $materialStockList[$equipment->equip_code][$materialTypeStockObj->material_type][$materialTypeStockObj->material_stock_id]['bottom_value'] = $materialTypeStockObj->bottom_value;

            }
        }
        return $materialStockList;
    }

    /**
     * 获取产品组料仓对应的物料类型
     * @param $productGroupId
     * @return array
     */
    public static function getStockIdOfMaterialType($productGroupId)
    {
        $stocks = self::find()->select('material_stock_id,material_type')->where(['pro_group_id' => $productGroupId])->asArray()->all();
        return Tools::map($stocks, 'material_stock_id', 'material_type', null, 2);
    }

    /**
     * 获取产品组料仓对应的速率
     * @param $productGroupId
     * @return array
     */
    public static function getStockIdOfSecond($productGroupId)
    {
        $stocks = self::find()->select('material_stock_id,pre_second_gram')->where(['pro_group_id' => $productGroupId])->asArray()->all();
        return Tools::map($stocks, 'material_stock_id', 'pre_second_gram', null, 2);
    }
}
