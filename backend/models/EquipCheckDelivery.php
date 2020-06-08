<?php

namespace backend\models;
use common\models\Building;
use Yii;

/**
 * This is the model class for table "equip_delivery".
 *
 * @property string $Id
 * @property string $build_id
 * @property string $equip_type_id
 * @property integer $delivery_result
 * @property string $delivery_time
 * @property string $sales_person
 * @property integer $delivery_status
 * @property string $reason
 * @property string $remark
 * @property string $create_time
 * @property string $delivery_number
 * @property integer $is_ammeter
 * @property integer $is_lightbox
 * @property string $special_require
 * @property string $update_time
 * @property string $grounds_refusal
 *
 * @property Building $build
 * @property EquipDeliveryDebugAssoc[] $equipDeliveryDebugAssocs
 * @property EquipDeliveryLightBoxAssoc[] $equipDeliveryLightBoxAssocs
 */
class EquipCheckDelivery extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'equip_delivery';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['build_id', 'equip_type_id', 'delivery_result', 'delivery_status', 'create_time', 'delivery_number', 'update_time'], 'integer'],
            [['reason', 'remark', 'special_require', 'grounds_refusal'], 'string', 'max' => 255],
            [['sales_person'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'Id'              => 'ID',
            'build_id'        => '楼宇',
            'equip_type_id'   => '设备类型',
            'delivery_result' => '投放结果',
            'delivery_time'   => '投放时间',
            'sales_person'    => '销售责任人',
            'delivery_status' => '投放状态',
            'reason'          => '原因',
            'remark'          => '备注',
            'create_time'     => '创建时间',
            'delivery_number' => '投放数量',
            'is_ammeter'      => '是否需要电表',
            'is_lightbox'     => '是否外包灯箱',
            'special_require' => '特殊要求',
            'update_time'     => '修改时间',
            'grounds_refusal' => '驳回理由',
            'people_num'      => '楼宇人数',
            'build_type'      => '楼宇类型',
            'voice_type'      => '设置声音',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild() {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

}
