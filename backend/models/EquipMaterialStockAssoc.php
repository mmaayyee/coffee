<?php

namespace backend\models;

use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_material_stock_assoc".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property integer $material_stock_id
 * @property integer $material_type
 * @property double $top_value
 * @property double $bottom_value
 * @property integer $change_material_time
 *
 * @property Equipments $equip
 * @property ScmMaterialStock $materialStock
 * @property ScmMaterial $material
 */
class EquipMaterialStockAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_material_stock_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'material_stock_id'], 'required'],
            [['equip_id', 'material_stock_id', 'material_type', 'change_material_time'], 'integer'],
            [['top_value', 'bottom_value'], 'number'],
            [['equip_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipments::className(), 'targetAttribute' => ['equip_id' => 'id']],
            [['material_stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterialStock::className(), 'targetAttribute' => ['material_stock_id' => 'id']],
            [['material_type'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterialType::className(), 'targetAttribute' => ['material_type' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equip_id'             => 'Equip ID',
            'material_stock_id'    => 'Material Stock ID',
            'material_type'        => 'Material Type',
            'top_value'            => 'Top Value',
            'bottom_value'         => 'Bottom Value',
            'change_material_time' => 'Change Material Time',
        ];
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
    public function getMaterialStock()
    {
        return $this->hasOne(ScmMaterialStock::className(), ['id' => 'material_stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasMany(ScmMaterial::className(), ['material_type' => 'material_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type']);
    }
    /*
     * 更新设备料仓换料时间
     * @author wangxiwen
     * @datetime 2018-07-02
     * @param int $equipId 设备ID
     * return boole
     */
    public static function saveRefuelTime($equipId)
    {
        $saveRes = self::updateAll(['change_material_time' => time()], ['equip_id' => $equipId]);
        if ($saveRes === false) {
            return false;
        }
        return true;
    }

    /**
     * 获取产品组料仓信息
     * @author wangxiwen
     * @version 2018-10-15
     * @return
     */
    public static function getEquipStockMaterialDetail()
    {
        $equipStockArr = self::find()
            ->select('e.equip_code, e.build_id, emsa.material_type, emsa.change_material_time refuel_time, emsa.material_stock_id stock_id, sms.stock_code')
            ->alias('emsa')
            ->leftJoin('equipments e', 'e.id = emsa.equip_id')
            ->leftJoin('scm_material_stock sms', 'sms.id = emsa.material_stock_id')
            ->andWhere(['>', 'e.build_id', 0])
            ->andWhere(['in', 'e.operation_status', [
                Equipments::COMMERCIAL_OPERATION,
                Equipments::INTERNAL_USE,
                Equipments::TEMPORARY_OPERATIONS,
            ]])
            ->orderBy('e.build_id,emsa.material_stock_id')
            ->asArray()
            ->all();
        $equipStockList = [];
        foreach ($equipStockArr as $equipStock) {
            $equipCode                              = $equipStock['equip_code'];
            $stockCode                              = $equipStock['stock_code'];
            $equipStockList[$equipCode][$stockCode] = $equipStock;
        }
        return $equipStockList;
    }

    /**
     * 添加设备物料料仓上下线值对应关系数据
     * @param [type] $data [description]
     */
    public static function addAll($syncData)
    {
        set_time_limit(0);
        $i    = 0;
        $data = $equipList = [];
        if ($syncData) {
            // 接口中的数据
            foreach ($syncData as $groupId => $groupList) {
                // 根据产品组获取设备id和设备类型id
                $equipList = Equipments::getEquipList('id, equip_type_id', ['pro_group_id' => $groupId]);
                if (!$equipList) {
                    continue;
                }
                foreach ($groupList as $stockCode => $limitArr) {
                    if (!is_array($limitArr)) {
                        continue;
                    }
                    // 遍历设备数组
                    foreach ($equipList as $equipObj) {
                        // 根据料仓编号获取料仓id
                        $material_stock_id = ScmMaterialStock::getField('id', ['stock_code' => $stockCode]);
                        if (!$material_stock_id) {
                            continue;
                        }
                        $data[$i]['equip_id']             = $equipObj->id;
                        $data[$i]['material_stock_id']    = $material_stock_id;
                        $data[$i]['material_type']        = $limitArr['material_type_id'];
                        $data[$i]['top_value']            = $limitArr['gstockTop'];
                        $data[$i]['bottom_value']         = $limitArr['gstockBottom'];
                        $changeMaterialTime               = self::getField('change_material_time', ['equip_id' => $equipObj->id, 'material_stock_id' => $material_stock_id]);
                        $data[$i]['change_material_time'] = $changeMaterialTime ? $changeMaterialTime : time();
                        $i++;
                    }
                }
            }
        }
        if (!$data) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        $delres      = self::deleteAll();
        $addres      = Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['equip_id', 'material_stock_id', 'material_type', 'top_value', 'bottom_value', 'change_material_time'], $data)->execute();
        if ($delres === false || $addres === false) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
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
     * 获取设备料仓和物料以及上下线值
     * @param  string $field [description]
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public static function getMaterialStockList($field = '*', $where = [])
    {
        return self::find()->select($field)->where($where)->all();
    }

    /**
     * 获取料仓的换料时间
     * @param  int $equip_id      设备id
     * @param  int $material_type 物料分类
     * @return int                时间
     */
    public static function getField($field, $where)
    {
        $model = self::findOne($where);
        return $model ? $model->$field : 0;
    }

    /**
     * 获取料仓编号
     * @author  zgw
     * @version 2016-08-31
     * @return  [type]     [description]
     */
    public static function getMaterialStockCode($where)
    {
        $model = self::findOne($where);
        return $model ? $model->materialStock->stock_code : '';
    }

    /**
     * 根据设备id获取料仓和物料分类关联以及其对应的物料列表（手机端配送任务时用）
     * @author  zgw
     * @version 2016-09-20
     * @param   [type]     $equipId     [description]
     * @param   [type]     $materialArr [description]
     * @return  [type]                  [description]
     */
    public static function getMaterialList($equipId, $materialArr)
    {
        // 获取设备料仓和物料分类对应列表
        $materialStockList = self::find()->where(['equip_id' => $equipId])->all();
        if (!$materialStockList) {
            return [];
        }
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
            // 获取料仓id
            $materialList[$materialStockObj->material_stock_id]['material_type_id'] = $materialStockObj->material_type;
            // 获取料仓名称
            $materialList[$materialStockObj->material_stock_id]['materialStockName'] = $materialStockObj->materialType->type == 1 ? $materialStockObj->materialStock->name : '';
            // 获取物料分类名称
            $materialList[$materialStockObj->material_stock_id]['materialTypeName'] = $materialStockObj->materialType->material_type_name;
            // 获取设备该分类的固定物料
            $fixedMaterialObj = ScmEquipTypeMaterialAssoc::getEquipTypeMaterialObj(['equip_type_id' => $materialStockObj->equip->equip_type_id, 'material_type_id' => $materialStockObj->material_type]);
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
                    if (isset($materialArr[$materialStockObj->material_type]['packets'])) {
                        $totalSurplusMaterial = $materialObj->weight > 0 ? $materialObj->weight * $materialArr[$materialStockObj->material_type]['packets'] : 0;
                    }
                }
            }

            // 获取配送任务中的物料id
            $materialList[$materialStockObj->material_stock_id]['selectId'] = isset($materialArr[$materialStockObj->material_type]['material_id']) ? $materialArr[$materialStockObj->material_type]['material_id'] : '';

            $materialList[$materialStockObj->material_stock_id]['material_out_gram'] = '';
            //查询料仓剩余物料
            $equipmentInfo                                                          = Equipments::getField('equip_code', ['id' => $equipId]);
            $surplusMaterial                                                        = EquipSurplusMaterial::getSurplusMaterial($equipmentInfo, $materialStockObj->material_type);
            $materialList[$materialStockObj->material_stock_id]['surplus_material'] = $surplusMaterial;

            $materialList[$materialStockObj->material_stock_id]['change_surplus_material'] = '';
            //添加的物料加上剩余的物料
            $materialList[$materialStockObj->material_stock_id]['total_surplus_material'] = $totalSurplusMaterial + $surplusMaterial;

            // 获取配送任务中物料的包数
            if (!in_array($materialStockObj->material_type, $materialTypeIdArr) && isset($materialArr[$materialStockObj->material_type]['packets'])) {
                $materialList[$materialStockObj->material_stock_id]['packets'] = $materialArr[$materialStockObj->material_type]['packets'];
            } else {
                $materialList[$materialStockObj->material_stock_id]['packets'] = '';
            }
            // 获取物料分类单位
            $materialList[$materialStockObj->material_stock_id]['unit']        = $materialStockObj->materialType->unit;
            $materialList[$materialStockObj->material_stock_id]['weight_unit'] = $materialStockObj->materialType->weight_unit;
            // 物料分类id数组
            $materialTypeIdArr[] = $materialStockObj->material_type;

        }
        return $materialList;
    }

    /**
     * 把料仓编号和上下线值按设备编号和物料分类组装数组
     * @author  zgw
     * @version 2016-10-27
     * @return  [type]     [description]
     */
    public static function getEquipMaterialTypeList()
    {
        $materialStockList     = [];
        $materialTypeStockList = self::getMaterialStockList();
        foreach ($materialTypeStockList as $materialTypeStockObj) {
            if (!isset($materialTypeStockObj->equip->equip_code) || !isset($materialTypeStockObj->materialStock->stock_code)) {
                continue;
            }
            // 获取给物料分类的料仓编号
            $materialStockList[$materialTypeStockObj->equip->equip_code][$materialTypeStockObj->material_type][$materialTypeStockObj->material_stock_id]['material_stock_code'] = $materialTypeStockObj->materialStock->stock_code;
            // 上限值
            $materialStockList[$materialTypeStockObj->equip->equip_code][$materialTypeStockObj->material_type][$materialTypeStockObj->material_stock_id]['top_value'] = $materialTypeStockObj->top_value;
            // 下限值
            $materialStockList[$materialTypeStockObj->equip->equip_code][$materialTypeStockObj->material_type][$materialTypeStockObj->material_stock_id]['bottom_value'] = $materialTypeStockObj->bottom_value;
        }
        return $materialStockList;
    }

}
