<?php

namespace backend\models;

use Yii;
use backend\models\ScmMaterialType;
use backend\models\ScmEquipType;
/**
 * This is the model class for table "scm_equip_type_matstock_assoc".
 *
 * @property integer $id
 * @property integer $equip_type_id
 * @property integer $matstock_id
 *
 * @property ScmEquipType $equipType
 * @property ScmMaterialStock $matstock
 */
class ScmEquipTypeMatstockAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_equip_type_matstock_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'matstock_id'], 'integer'],
            [['equip_type_id', 'matstock_id'], 'unique', 'targetAttribute' => ['equip_type_id', 'matstock_id'], 'message' => 'The combination of Equip Type ID and Matstock ID has already been taken.'],
            [['equip_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmEquipType::className(), 'targetAttribute' => ['equip_type_id' => 'id']],
            [['matstock_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterialStock::className(), 'targetAttribute' => ['matstock_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'equip_type_id' => '设备类型',
            'matstock_id'   => '料仓',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipType()
    {
        return $this->hasOne(ScmEquipType::className(), ['id' => 'equip_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatstock()
    {
        return $this->hasOne(ScmMaterialStock::className(), ['id' => 'matstock_id']);
    }

    /**
     * 根据设备类型获取料仓id
     * @author  zgw
     * @version 2016-11-24
     * @param   [type]     $equipTypeId [description]
     * @return  [type]                  [description]
     */
    public static function getmatstockIdArr($equipTypeId)
    {
        $list = self::find()->select('matstock_id')->where(['equip_type_id' => $equipTypeId])->all();
        return \yii\helpers\ArrayHelper::getColumn($list, 'matstock_id');
    }

    
    /**
     * 根据设备类型ID查询出，料仓和物料类型的数据组
     * [{"stockName":"G","materialTypeList":{"3":"咖啡豆","4":"奶","5":"糖"}},{"stockName":"1","materialTypeList":{"3":"咖啡豆","4":"奶","5":"糖"}}]
     * @author  zmy
     * @version 2017-08-29
     * @param   [string]     $equipTypeID [设备类型]
     * @param   [string]     $isType      [是否不区分料仓和物料类型 '1'=>'区分'，''=>'不区分']
     * @return  [array]                   [数组]
     */
    public static function getEquipTypeStockList($equipTypeID, $isType='1')
    {
        $query              = self::find()->leftJoin('scm_material_stock', 'scm_equip_type_matstock_assoc.matstock_id = scm_material_stock.id');
        if($isType){
            $equipTypeStockObj  = $query->where(['equip_type_id'=>$equipTypeID, 'type'=>0])->all(); 
        }else{
            $equipTypeStockObj  = $query->where(['equip_type_id'=>$equipTypeID])->all();
        }
        $equipTypeStockList = [];
        $stockList          = [];
        $materialTypeList   = [];
        foreach ($equipTypeStockObj as $key => $equipTypeStock) {
            $stockList[$key]['stockName'] = isset($equipTypeStock->matstock->name) ? $equipTypeStock->matstock->name : "";
            $stockList[$key]['stockID']   = isset($equipTypeStock->matstock->id) ? $equipTypeStock->matstock->id : "";
            $stockList[$key]['stockCode'] = isset($equipTypeStock->matstock->stock_code) ? $equipTypeStock->matstock->stock_code : "";
        }
        if($isType){
            $materialTypeList   =  ScmMaterialType::getIdNameArr(0, ['type'=>1]);
        }else{
            $materialTypeList   =  ScmMaterialType::getIdNameArr(0);
        }
        $equipTypeStockList = ['equipTypeStockList'=> $stockList, 'materialTypeList'=> $materialTypeList];
        return $equipTypeStockList;
    }

    /**
     * 料仓和物料类型的数据组
     * @author  zmy
     * @version 2017-09-01
     * @return  [type]     [description]
     */
    public static function getEquipTypeStockListAll()
    {
        $query              = self::find()->leftJoin('scm_material_stock', 'scm_equip_type_matstock_assoc.matstock_id = scm_material_stock.id');
        $equipTypeStockObj  = $query->where(['type'=>0])->orderBy('equip_type_id, matstock_id')->all();
        $stockList          = [];
        foreach ($equipTypeStockObj as $key => $equipTypeStock) {
//            var_dump($equipTypeStock->equipType->readable_attribute);die;
            $stockList[$equipTypeStock->equip_type_id]['equip_type_id']                                =  isset($equipTypeStock->equipType->id) ? $equipTypeStock->equipType->id : "";
            $stockList[$equipTypeStock->equip_type_id]['equip_type_name']                              =  isset($equipTypeStock->equipType->model) ? $equipTypeStock->equipType->model : "";
            $stockList[$equipTypeStock->equip_type_id]['stock'][$equipTypeStock->matstock->stock_code] =  isset($equipTypeStock->matstock->name) ? $equipTypeStock->matstock->name : "";
            $stockList[$equipTypeStock->equip_type_id]['readableAttribute']                            =  isset($equipTypeStock->equipType->readable_attribute) ? self::readableAttributeNameList($equipTypeStock->equipType->readable_attribute) : [];
        }
        return $stockList;
    }


    /**
     * 组合配方属性名称数组
     * @author  zmy
     * @version 2017-09-05
     * @param   [string]     $readableAttribute [设备类型单品配方可选属性json]
     * @return  [Array]                         [可选属性数组]
     */
    public static function readableAttributeNameList($readableAttribute)
    {
        $attributeList = [];
        if(empty($readableAttribute)){
            return $attributeList;
        }
        $readableAttributeList = json_decode( $readableAttribute, true);
        $attributeNameList = ScmEquipType::getReadableAttribute();
        foreach ($readableAttributeList as $key => $readableAttribute) {
            if(!isset($attributeNameList[$readableAttribute])){
                continue;
            }
            $attributeList[$readableAttribute] =    $attributeNameList[$readableAttribute];
        }
        return $attributeList;
    }

}
