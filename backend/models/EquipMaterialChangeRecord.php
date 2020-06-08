<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_material_change_record".
 *
 * @property integer $equip_id
 * @property integer $material_stock_id
 * @property integer $material_type
 * @property integer $change_material_time
 */
class EquipMaterialChangeRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_material_change_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'material_stock_id'], 'required'],
            [['equip_id', 'material_stock_id', 'material_type', 'change_material_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equip_id' => 'Equip ID',
            'material_stock_id' => 'Material Stock ID',
            'material_type' => 'Material Type',
            'change_material_time' => 'Change Material Time',
        ];
    }

    /**
     * 获取最后一次换料时间
     * @param $equipmentId
     * @param $materialType
     * @return int|mixed
     */
    public static function getRefuelTime($equipmentId,$materialType){
        $store = self::find()->select('change_material_time')->where(['equip_id' => $equipmentId,'material_type' => $materialType])->asArray()->one();
        return $store['change_material_time'] ? $store['change_material_time'] : '';
    }

    /**
     * 保存设备料仓修改记录
     * @param int $equipId
     * @param int $materialStockId
     * @param int $materialType
     * @return bool
     */
    public static function changeEquipStockTime($equipId = 0 ,$materialStockId = 0,$materialType = 0){
        $model = self::findOne(['equip_id' => $equipId, 'material_stock_id' => $materialStockId]);
        if($model){
            $model->change_material_time = time();
            return $model->save();
        }else{
            $newModel = new EquipMaterialChangeRecord();
            $newModel->equip_id = $equipId;
            $newModel->material_type = $materialType;
            $newModel->material_stock_id = $materialStockId;
            $newModel->change_material_time = time();
            return $newModel->save();
        }

    }
}
