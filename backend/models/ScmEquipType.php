<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\Api;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "scm_equip_type".
 *
 * @property integer $id
 * @property string $model
 * @property integer $supplier_id
 * @property integer $create_time
 *
 * @property DistributionTaskSetting[] $distributionTaskSettings
 * @property Equipments[] $equipments
 * @property ScmSupplier $supplier
 * @property ScmEquiptypeMatstockAssoc[] $scmEquiptypeMatstockAssoc
 * @property ScmMaterialStock[] $matstocks
 */
class ScmEquipType extends \yii\db\ActiveRecord
{
    public $matstock;
    public $miscellaneousMaterial; //物料杂项
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_equip_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'supplier_id', 'readable_attribute', 'matstock'], 'required'],
            [['supplier_id', 'create_time', 'stock_num'], 'integer'],
            [['empty_box_weight'], 'safe'],
            [['model'], 'string', 'max' => 20],
            [['equip_type_alias'], 'string', 'max' => 10],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmSupplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['model'], 'unique'],
            [['model'], 'in', 'range' => self::getEquipTypeNameArr(), 'on' => ['create', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'model'                 => '设备类型',
            'supplier_id'           => '供应商',
            'equip_type_alias'      => '设备类型别名',
            'create_time'           => '入库时间',
            'matstock'              => '料仓信息',
            'miscellaneousMaterial' => '物料杂项信息',
            'stock_num'             => '料仓数量',
            'readable_attribute'    => '配方属性',
            'empty_box_weight'      => '空料盒重量',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributionTaskSettings()
    {
        return $this->hasMany(DistributionTaskSetting::className(), ['equip_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasMany(Equipments::className(), ['equip_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(ScmSupplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScmEquiptypeMatstockAssoc()
    {
        return $this->hasMany(ScmEquiptypeMatstockAssoc::className(), ['equip_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatstocks()
    {
        return $this->hasMany(ScmMaterialStock::className(), ['id' => 'matstock_id'])->viaTable('scm_equiptype_matstock_assoc', ['equip_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getmaterialStocks($id)
    {
        $deviceMatStocks = ScmEquipTypeMatstockAssoc::find()->select(['matstock_id'])->where(['equip_type_id' => $id])->asArray()->all();
        $materialStocks  = array();
        foreach ($deviceMatStocks as $key => $value) {
            $materialStocks[] = ScmMaterialStock::find()->select(['name'])->where(['id' => $value])->asArray()->all();
        }
        $retStockName = '';
        foreach ($materialStocks as $key => $value) {
            foreach ($value as $k => $val) {
                $retStockName .= $val['name'] . ',';
            }
        }
        $retStockName = trim($retStockName, ',');
        return $retStockName;
    }

    /**
     *  获取material_id的一维索引数组
     *  @return array 一维索引
     **/
    public static function getDeviceMaterial()
    {
        $equipTypeId               = $_GET['id'];
        $equipTypeMaterialAssocArr = ArrayHelper::getColumn(ScmEquipTypeMaterialAssoc::find()->where(['equip_type_id' => $equipTypeId])->all(), 'material_id');
        return $equipTypeMaterialAssocArr;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getMiscellaneousMaterial($id)
    {
        $miscellaneousMaterial = ScmEquipTypeMaterialAssoc::find()->select(['material_id'])->where(['equip_type_id' => $id])->asArray()->all();

        $miscellaneousMaterialArr = array();
        $miscellaneousMaterialStr = '';
        foreach ($miscellaneousMaterial as $key => $value) {
            $miscellaneousMaterialArr[] = ScmMaterial::find()->where(['id' => $value["material_id"]])->asArray()->one();
        }
        foreach ($miscellaneousMaterialArr as $key => $value) {
            if (!$value['material_type']) {
                continue;
            }
            $miscellaneousMaterialStr .= ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $value['material_type']])['material_type_name'] . '--供应商:' . ScmSupplier::getSurplierDetail("name", ['id' => $value['supplier_id']])['name'] . '--' . $value['name'] . '--' . $value['weight'] . '规格' . '<br/>';
        }
        return $miscellaneousMaterialStr;
    }

    /**
     *   获取供应商数组
     *   @return array $materialArray
     **/
    public function getSupplierArray()
    {
        $suppliers = ScmSupplier::find()->select(['id', 'name'])->where([
            'type' => ScmSupplier::EQUIPMENT])->asArray()->all();
        $supplierArray     = array();
        $supplierArray[''] = '请选择';
        foreach ($suppliers as $supplier) {
            $supplierArray[$supplier['id']] = $supplier['name'];
        }
        return $supplierArray;
    }

    /**
     *   获取供应商名称
     *   @return array $materialName
     **/
    public function getSupplierName($id)
    {
        $SupplierName = ScmSupplier::find()->select(['name'])->where(['id' => $id])->asArray()->one()['name'];

        return $SupplierName;
    }

    /**
     * 静态方法获取供应商名称
     */
    public static function getSupplierNameById($id)
    {
        $model = new ScmEquipType();
        return $model->getSupplierName($id);
    }

    /**
     * 获取设备类型相关信息
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getEquipTypeDetail($field, $where)
    {
        $detail = self::find()
            ->select($field)
            ->where($where)
            ->asArray()
            ->one();
        return $detail;
    }

    /**
     *   获取料仓数组
     *   @return array $cupstyleArray
     **/
    public function getmaterialStockArray()
    {
        $materialStocks     = ScmMaterialStock::find()->select(['id', 'name'])->asArray()->where(['!=', 'id', 15])->all();
        $materialStockArray = array();
        foreach ($materialStocks as $materialStock) {
            $materialStockArray[$materialStock['id']] = $materialStock['name'];
        }
        return $materialStockArray;
    }

    /**
     *   获取料仓和设备类型关联表中的关联料仓Id数组
     *   @param $id
     *   @return array $matstockIdArr
     **/
    public static function getMatstockIdArr($id)
    {
        $deviceMatStocks = ScmEquipTypeMatstockAssoc::find()->select(['matstock_id'])->where(['equip_type_id' => $id])->asArray()->all();
        $matstockIdArr   = array();
        foreach ($deviceMatStocks as $key => $value) {
            $matstockIdArr[] = $value['matstock_id'];
        }
        return $matstockIdArr;
    }

    /**
     * 设备类型idName数组
     * @return [type] [description]
     */
    public static function equipTypeIdNameArr()
    {
        return self::find()->all();
    }

    /**
     *  添加/修改方法 操作料仓关联表
     *  @param $postData, $transaction
     **/
    public static function scmMatStock($postData, $model, $transaction, $sign)
    {
        if ($postData['ScmEquipType']['matstock']) {
            if ($sign == 'update') {
                $customer = ScmEquipTypeMatstockAssoc::deleteAll(['equip_type_id' => $model->id]);
            }
            foreach ($postData['ScmEquipType']['matstock'] as $key => $value) {
                $devicStockModel                = new ScmEquipTypeMatstockAssoc();
                $devicStockModel->equip_type_id = $model->id;
                $devicStockModel->matstock_id   = $value;
                $ret                            = $devicStockModel->save();
            }
            if (!$ret) {
                Yii::$app->getSession()->setFlash('error', '添加料仓失败，请检查。');
                $transaction->rollback();
            }
        }
    }

    /**
     *  添加/修改方法 操作物料关联表
     *  @param $postData, $transaction
     **/
    public static function scmMaterial($postData, $model, $transaction, $sign)
    {
        if (isset($postData['miscellaneous_material'])) {
            if ($sign == 'update') {
                $customer = ScmEquipTypeMaterialAssoc::deleteAll(['equip_type_id' => $model->id]);
            }
            foreach ($postData['miscellaneous_material'] as $materialTypeId => $value) {
                if (!$value['material_id']) {
                    continue;
                }
                $equipTypeMaterialAssocModel                   = new ScmEquipTypeMaterialAssoc();
                $equipTypeMaterialAssocModel->equip_type_id    = $model->id;
                $equipTypeMaterialAssocModel->material_type_id = $materialTypeId;
                $equipTypeMaterialAssocModel->material_id      = $value['material_id'];
                $assocRet                                      = $equipTypeMaterialAssocModel->save();
            }
            if (isset($assocRet) && !$assocRet) {
                Yii::$app->getSession()->setFlash('error', '添加物料杂项失败，请检查。');
                $transaction->rollback();exit();
            }
        }
    }

    /**
     * 根据设备类型id获取设备类型
     * @author  zgw
     * @version 2016-08-16
     * @param   int     $id     设备类型id
     * @return  string          设备类型
     */
    public static function getModel($id)
    {
        $model = self::findOne($id);
        return $model ? $model->model : '';
    }

    /**
     * 获取所有设备类型列表
     * @author  zgw
     * @version 2016-08-29
     * @return  array     设备类型列表
     */
    public static function getEquipTypeList()
    {
        return self::find()->asArray()->all();
    }

    /**
     * 获取所有设备类型（设备类型列表页搜索时自动补全使用）
     * @author  zgw
     * @version 2016-09-22
     * @return  [type]     [description]
     */
    public static function getEquipTypeNameArr()
    {
        return ArrayHelper::getColumn(self::find()->all(), 'model');
    }

    /**
     * 获取设备类型数组
     * @author  zgw
     * @version 2016-09-26
     * @return  [type]     [description]
     */
    public static function getEquipTypeIdNameArr()
    {
        return Tools::map(self::find()->all(), 'id', 'model');
    }

    /**
     * 获取可调参数 [出料顺序、料仓数量、延时、加水量、出量时间、出料马达速度、搅拌马达速度、搅拌时间]
     * @author  zmy
     * @version 2017-08-26
     * @return  [array]     [array]
     */
    public static function getReadableAttribute()
    {
        return array(
            'stock_code'   => '料仓编号',
            'order_number' => '序号',
            'water'        => '加水量',
            'delay'        => '延时',
            'volume'       => '出料时间',
            'stir'         => '搅拌时间',
            'blanking'     => '出料马达速度',
            'mixing'       => '搅拌马达速度',
            'consume'      => '物料消耗量',
        );
    }

    /**
     * 查询出可读属性
     * @author  zmy
     * @version 2017-09-21
     * @param   [type]     $readableAttr [description]
     * @return  [type]                   [description]
     */
    public static function getReadableAttributeValue($readableAttr)
    {
        $readableAttr     = json_decode($readableAttr, 1);
        $readableAttrList = self::getReadableAttribute();
        $readableAttrStr  = '';
        if (!$readableAttr) {
            return trim($readableAttrStr, '，');
        }
        foreach ($readableAttr as $key => $value) {
            if (isset(self::getReadableAttribute()[$value])) {
                $readableAttrStr .= ' ' . self::getReadableAttribute()[$value] . ' , ';
            }
        }
        return trim($readableAttrStr, '，');
    }

    /**
     * 发送设备类型同步接口
     * @author  zmy
     * @version 2017-08-28
     * @param   [object]     $model [设备类型对象]
     * @return  [boolean ]          [true/false]
     */
    public static function sendEquipTypeSync($model)
    {
        $data['equip_type_id']      = $model->id;
        $data['equipment_name']     = $model->model;
        $data['readable_attribute'] = $model->readable_attribute;
        $data['stock_num']          = $model->stock_num;
        $data['supplier_id']        = $model->supplier_id;
        $data['create_time']        = $model->create_time;
        $data['empty_box_weight']   = $model->empty_box_weight;
        $data['equip_type_alias']   = $model->equip_type_alias;
        return Api::equipTypeSync($data);
    }
}
