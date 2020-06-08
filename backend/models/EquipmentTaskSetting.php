<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "equipment_task_setting".
 *
 * @property integer $id
 * @property integer $equipment_type_id
 * @property integer $organization_id
 * @property integer $cleaning_cycle
 * @property integer $refuel_cycle
 * @property integer $day_num
 */
class EquipmentTaskSetting extends \yii\db\ActiveRecord
{
    public $material_type;
    public $refuel_cycle_days;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_task_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_type_id', 'organization_id', 'cleaning_cycle', 'day_num', 'refuel_cycle_days', 'error_value'], 'integer'],
            ['refuel_cycle', 'string'],
            [['cleaning_cycle', 'day_num', 'refuel_cycle_days'], 'integer', 'min' => 1, 'max' => 100],
            [['equipment_type_id', 'organization_id'], 'default', 'value' => 0],
            ['equipment_type_id', 'validateEquipmentTypeId'],
            ['organization_id', 'validateOrganizationId'],
            [['cleaning_cycle', 'refuel_cycle', 'day_num'], 'required'],
        ];
    }

    /**
     * 验证设备类型
     * @param $attribute
     */
    public function validateEquipmentTypeId($attribute)
    {
        $where = '1=1';
        if ($this->id) {
            $where = 'id !=' . $this->id;
        }

        $result = false;
        if ($this->$attribute && $this->organization_id) {
            $result = self::find()->where(['equipment_type_id' => $this->$attribute, 'organization_id' => $this->organization_id])->andWhere($where)->exists();
        } elseif ($this->$attribute) {
            $result = self::find()->where(['equipment_type_id' => $this->$attribute, 'organization_id' => 0])->andWhere($where)->exists();
        } elseif ($this->organization_id) {
            $result = self::find()->where(['equipment_type_id' => 0, 'organization_id' => $this->organization_id])->andWhere($where)->exists();
        }
        $result ? $this->addError($attribute, '不能重复设置相同条件的日常任务') : '';

        if ($this->$attribute === 0 && $this->organization_id === 0) {
            $this->addError($attribute, '设备类型和分公司不能同时为空');
        }
    }

    /**
     * 验证分公司
     * @param $attribute
     */
    public function validateOrganizationId($attribute)
    {
        $where = '1=1';
        if ($this->id) {
            $where = 'id !=' . $this->id;
        }
        $result = false;
        if ($this->$attribute && $this->equipment_type_id) {
            $result = self::find()->where(['equipment_type_id' => $this->equipment_type_id, 'organization_id' => $this->$attribute])->andWhere($where)->exists();
        } elseif ($this->$attribute) {
            $result = self::find()->where(['equipment_type_id' => 0, 'organization_id' => $this->$attribute])->andWhere($where)->exists();
        } elseif ($this->equipment_type_id) {
            $result = self::find()->where(['equipment_type_id' => $this->equipment_type_id, 'organization_id' => 0])->andWhere($where)->exists();
        }
        $result ? $this->addError($attribute, '不能重复设置相同条件的日常任务') : '';

        if ($this->$attribute === 0 && $this->equipment_type_id === 0) {
            $this->addError($attribute, '设备类型和分公司不能同时为空');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'equipment_type_id' => '设备类型',
            'organization_id'   => '分公司',
            'cleaning_cycle'    => '清洗天数',
            'refuel_cycle'      => '换料天数',
            'refuel_cycle_days' => '换料天数',
            'day_num'           => '配送天数',
            'material_type'     => '物料分类',
            'error_value'       => '误差值',
        ];
    }

    /**
     * 获取指定条件的数据
     * @param $filed
     * @param $where
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getField($filed, $where)
    {
        return self::find()->select($filed)->where($where)->asArray()->one();
    }

    /**
     * 获取设备类型分公司日常任务设置
     * @param $organizationId
     * @param $equipmentTypeId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getEquipTypeOrgTaskSetting($organizationId, $equipmentTypeId)
    {
        //根据分公司和设备类型查询,优先级最高
        $setting = self::getField('*', ['organization_id' => $organizationId, 'equipment_type_id' => $equipmentTypeId]);
        if ($setting !== null) {
            return $setting;
        }
        //根据分公司查询优先级居中
        $setting = self::getField('*', ['organization_id' => $organizationId, 'equipment_type_id' => 0]);
        if ($setting !== null) {
            return $setting;
        }

        //根据设备类型查询日常任务设置优先级最低
        $setting = self::getField('*', ['organization_id' => 0, 'equipment_type_id' => $equipmentTypeId]);
        if ($setting !== null) {
            return $setting;
        }
        return null;
    }

    /**
     * 获取根据分公司和设备类型设置的配送周期，清洗周期，换料周期数据
     * @author wangxiwen
     * @version 2018-05-17
     * @return array
     */
    public static function getEquipmentSetting()
    {
        $equipSetting = self::find()
            ->select('equipment_type_id,organization_id,cleaning_cycle,refuel_cycle,day_num')
            ->asArray()
            ->all();
        $equipSettingList = [];
        foreach ($equipSetting as $setting) {
            $equipTypeId = $setting['equipment_type_id'];
            $orgId       = $setting['organization_id'];
            $refuelCycle = $setting['refuel_cycle'] ? Json::decode($setting['refuel_cycle']) : [];

            $equipSettingList[$equipTypeId][$orgId]['cleaning_cycle'] = $setting['cleaning_cycle'];
            $equipSettingList[$equipTypeId][$orgId]['day_num']        = $setting['day_num'];
            $equipSettingList[$equipTypeId][$orgId]['refuel_cycle']   = Tools::map($refuelCycle, 'material_type', 'refuel_cycle', null, null);
        }
        return $equipSettingList;
    }
}
