<?php

namespace backend\models;

use Yii;
use common\models\Building;

/**
 * This is the model class for table "distribution_task_equip_setting".
 *
 * @property integer $id
 * @property integer $build_id
 * @property integer $equip_type_id
 * @property integer $org_id
 * @property integer $material_id
 * @property integer $cleaning_cycle
 * @property integer $refuel_cycle
 * @property integer $day_num
 */
class DistributionTaskEquipSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_task_equip_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'equip_type_id', 'org_id', 'material_id', 'cleaning_cycle', 'refuel_cycle', 'day_num'], 'integer'],
            [['build_id', 'equip_type_id', 'org_id', 'material_id'], 'default', 'value' => 0],
            ['build_id', 'validateBuildId', 'skipOnEmpty' => true],
            ['cleaning_cycle', 'integer', 'min' => 0, 'max' => 5],
            [['refuel_cycle', 'day_num'], 'integer', 'min' => 0, 'max' => 50],
            ['cleaning_cycle', 'required', 'when' => function ($model) {
                return $model->day_num == 0 && $model->refuel_cycle == 0;
            }],
            ['day_num', 'required', 'when' => function ($model) {
                return $model->cleaning_cycle == 0 && $model->refuel_cycle == 0;
            }],
            ['refuel_cycle', 'required', 'when' => function ($model) {
                return $model->day_num == 0 && $model->cleaning_cycle == 0;
            }],
            ['equip_type_id', 'validateEquipTypeId', 'when' => function ($model) {
                return empty($model->build_id) && $model->material_id == 0;
            }],
            ['org_id', 'validateOrgId', 'when' => function ($model) {
                return empty($model->build_id) && $model->material_id == 0;
            }],
            ['material_id','validateMaterial', 'when' => function ($model) {
                return $model->refuel_cycle > 0 && ($model->build_id == 0) && ($model->equip_type_id == 0) && ($model->org_id == 0);
            }]
        ];
    }

    /**
     * 验证楼宇不同的任务类型只能设置一条
     * @param $attribute
     */
    public function validateBuildId($attribute)
    {
        if ($this->$attribute) {
            if ($this->cleaning_cycle) {
                $count = self::getTaskSetting($this->$attribute, 'cleaning_cycle > 0');
            } elseif ($this->refuel_cycle) {
                $count = self::getTaskSetting($this->$attribute, 'refuel_cycle > 0');
            } elseif ($this->day_num) {
                $count = self::getTaskSetting($this->$attribute, 'day_num > 0');
            }
            $count > 0 ? $this->addError($attribute, '不能重复设置相同条件的日常任务') : '';
        };
    }

    /**
     * 验证物料是否选择
     * @param $attribute
     */
    public function validateMaterial($attribute)
    {
        $exist = self::find()->where(['material_id' => $this->$attribute])->andWhere('refuel_cycle > 0')->exists();

        (!$exist && $this->$attribute > 0) ? "" : $this->addError($attribute, '不能重复设置相同条件的日常任务');
    }

    /**
     * 验证设备类型和分公司
     * 不同的任务类型,设备类型和分公司组合唯一,设备类型唯一,分公司唯一
     * @param $attribute
     */
    public function validateEquipTypeId($attribute)
    {

        if ($this->cleaning_cycle) {
            $where = 'cleaning_cycle > 0';
        } elseif ($this->refuel_cycle) {
            $where = 'refuel_cycle > 0';
        } elseif ($this->day_num) {
            $where = 'day_num > 0';
        }
        $result = false;
        if ($this->$attribute && $this->org_id) {
            $result = self::find()->where(['equip_type_id' => $this->$attribute, 'org_id' => $this->org_id])->andWhere($where)->exists();
        } elseif ($this->$attribute) {
            $result = self::find()->where(['equip_type_id' => $this->$attribute])->andWhere($where)->exists();
        } elseif ($this->org_id) {
            $result = self::find()->where(['org_id' => $this->org_id])->andWhere($where)->exists();
        }
        $result ? $this->addError($attribute, '不能重复设置相同条件的日常任务') : '';
    }

    /**
     * 验证设备类型和分公司
     * 不同的任务类型,设备类型和分公司组合唯一,设备类型唯一,分公司唯一
     * @param $attribute
     */
    public function validateOrgId($attribute)
    {
        $result = false;
        if ($this->cleaning_cycle) {
            $where = 'cleaning_cycle > 0';
        } elseif ($this->refuel_cycle) {
            $where = 'refuel_cycle > 0';
        } elseif ($this->day_num) {
            $where = 'day_num > 0';
        }

        if ($this->$attribute && $this->equip_type_id) {
            $result = self::find()->where(['equip_type_id' => $this->equip_type_id, 'org_id' => $this->$attribute])->andWhere($where)->exists();
        } elseif ($this->$attribute) {
            $result = self::find()->where(['org_id' => $this->$attribute])->andWhere($where)->exists();
        } elseif ($this->equip_type_id) {
            $result = self::find()->where(['equip_type_id' => $this->equip_type_id])->andWhere($where)->exists();
        }
        $result ? $this->addError($attribute, '不能重复设置相同条件的日常任务') : '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'build_id' => '楼宇',
            'equip_type_id' => '设备类型',
            'org_id' => '分公司',
            'material_id' => '物料',
            'cleaning_cycle' => '清洗周期(天)',
            'refuel_cycle' => '换料周期(天)',
            'day_num' => '配送周期(天)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * 查询楼宇任务周期
     * @param $buildId
     * @param $where
     * @return int|string
     */
    public static function getTaskSetting($buildId, $where)
    {
        return self::find()->where(['build_id' => $buildId])->andWhere($where)->count();
    }

    public static function getField($filed,$where){
        return self::find()->select($filed)->where($where)->one();
    }
}
