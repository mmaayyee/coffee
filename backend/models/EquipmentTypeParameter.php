<?php

namespace backend\models;
use common\models\EquipmentTypeParameterApi;
use Yii;

/**
 * This is the model class for table "equipment_type_parameter".
 *
 * @property string $id
 * @property int $equipment_type_id 设备类别id
 * @property string $parameter_name 类别参数名
 * @property int $max_parameter 最大值
 * @property int $min_parameter 最小值
 * @property int $status 状态
 */
class EquipmentTypeParameter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_type_parameter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_type_id', 'parameter_name'], 'required'],
            [['equipment_type_id', 'max_parameter', 'min_parameter', 'status'], 'integer'],
            [['parameter_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_type_id' => 'Equipment Type ID',
            'parameter_name' => 'Parameter Name',
            'max_parameter' => 'Max Parameter',
            'min_parameter' => 'Min Parameter',
            'status' => 'Status',
        ];
    }
    /**
     * 获取/搜索设备类型参数
    */
    public static function getEquipmentTypeParameterList($where = []){
        //直接从api获取
        return EquipmentTypeParameterApi::getEquipmentTypeParameterList($where);
    }
}
