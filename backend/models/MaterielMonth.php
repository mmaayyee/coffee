<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "materiel_month".
 *
 * @property integer $materiel_id
 * @property string $equipment_code
 * @property integer $build_id
 * @property integer $material_type_id
 * @property integer $create_at
 * @property double $consume_total
 */
class MaterielMonth extends \yii\db\ActiveRecord
{   
    public $orgId;
    public $build_name;
    public $build_type;
    public $equip_type_id;
    public $build_id;
    public $material_type_id;
    public $create_at;
    public $consume_total;
    public $consume_total_all;
    public $equipment_code;
    public $material_type_name;
    public $change_value;
    public $startTime;
    public $page;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'material_type_id', 'create_at'], 'integer'],
            [['consume_total'], 'number'],
            [['equipment_code','consume_total_all','material_type_name','orgId','build_type','build_name','equip_type_id','change_value','startTime'], 'safe'],
            [['equipment_code'], 'string', 'max' => 32]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'materiel_id' => 'Materiel ID',
            'equipment_code' => 'Equipment Code',
            'build_id' => 'Build ID',
            'material_type_id' => '物料分类',
            'create_at' => 'Create At',
            'consume_total' => 'Consume Total',
            'orgId' => '地区',
            'build_name'   => '楼宇名称',
            'build_type'   => '渠道类型',
            'equip_type_id'   => '设备类型',
            'consume_total_all' => '消耗总量',
            'startTime'     => '时间'
        ];
    }
}
