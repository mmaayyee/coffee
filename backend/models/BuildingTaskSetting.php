<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\Building;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "building_task_setting".
 *
 * @property integer $id
 * @property integer $building_id
 * @property integer $cleaning_cycle
 * @property string $refuel_cycle
 * @property integer $day_num
 */
class BuildingTaskSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building_task_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['building_id', 'cleaning_cycle', 'day_num', 'error_value'], 'integer'],
            ['refuel_cycle', 'string'],
            [['cleaning_cycle', 'refuel_cycle', 'day_num'], 'required'],
            ['cleaning_cycle', 'integer', 'integerOnly' => true, 'max' => 5],
            [['day_num'], 'integer', 'min' => 0, 'max' => 100],
            ['building_id', 'default', 'value' => 0],
            ['building_id', 'unique', 'message' => '不能重复设置相同楼宇的日常任务'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'building_id'    => '楼宇',
            'cleaning_cycle' => '清洗天数',
            'refuel_cycle'   => '换料天数',
            'day_num'        => '配送天数',
            'error_value'    => '误差值',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * 获取楼宇日常任务设置
     * @param $buildId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getBuildTaskSettingByBuildId($buildId)
    {
        return self::find()->where(['building_id' => $buildId])->asArray()->one();
    }

    /**
     * 获取设备日常任务设置
     * @param $model
     */
    public static function getEquipmentTaskSetting($model)
    {
        $model->refuelCycle = 0;
        $model->dayNum      = 0;
        $buildSetting       = BuildingTaskSetting::getBuildTaskSettingByBuildId($model->build_id);
        if (!empty($buildSetting)) {
            $model->cleaningCycle = $buildSetting['cleaning_cycle'] && $buildSetting['cleaning_cycle'] > 0 ? $buildSetting['cleaning_cycle'] : 3;
            $model->refuelCycle   = $buildSetting['refuel_cycle'];
            $model->dayNum        = $buildSetting['day_num'];
            return $model;
        }
        $equipSetting = EquipmentTaskSetting::getEquipTypeOrgTaskSetting($model->org_id, $model->equip_type_id);
        if (!empty($equipSetting)) {
            $model->cleaningCycle = $equipSetting['cleaning_cycle'] && $equipSetting['cleaning_cycle'] > 0 ? $equipSetting['cleaning_cycle'] : 3;
            $model->refuelCycle   = $equipSetting['refuel_cycle'];
            $model->dayNum        = $equipSetting['day_num'];
            return $model;
        }
        $model->cleaningCycle = $model->cleaningCycle > 0 ? $model->cleaningCycle : 3; //默认3天
        return $model;
    }

    /**
     * 获取物料换料信息
     * @param string $json
     * @return string
     */
    public static function getRuelCycle($json = '')
    {
        $refuelCycle = json_decode($json);
        if (!$refuelCycle) {
            return '';
        }
        //获取物料分类id名字数组
        $materialType = ScmMaterialType::getIdNameArr();
        $str          = '';
        foreach ($refuelCycle as $k => $item) {
            $str .= $materialType[$item->material_type] . ' : ' . $item->refuel_cycle . '天 ';
        }
        return $str;
    }

    /**
     * 获取已设置的楼宇
     * @return mixed
     */
    public static function getBuildingId()
    {
        $list = self::find()->select('building_id')->distinct()->asArray()->all();
        return ArrayHelper::getColumn($list, 'building_id');
    }

    /**
     * 过滤已设置的楼宇
     * @return array
     */
    public static function getBuildList($buildingId = 0)
    {
        $allBuild = Building::getBusinessBuildByOrgId();
        //已经设置的设备
        $buildIds = self::getBuildingId();

        foreach ($allBuild as $key => $item) {
            if (in_array($key, $buildIds) && $key != $buildingId) {
                unset($allBuild[$key]);
            }
        }
        return $allBuild;
    }

    /**
     * 获取根据楼宇设置的配送周期，清洗周期，换料周期数据
     * @author wangxiwen
     * @version 2018-05-17
     * @return array
     */
    public static function getBuildSetting()
    {
        $buildTaskSettingList = [];
        $buildSetting         = self::find()
            ->select('building_id,cleaning_cycle,refuel_cycle,day_num')
            ->asArray()
            ->all();
        $buildSettingList = [];
        foreach ($buildSetting as $setting) {
            $buildId     = $setting['building_id'];
            $refuelCycle = $setting['refuel_cycle'] ? Json::decode($setting['refuel_cycle']) : [];

            $buildSettingList[$buildId]['cleaning_cycle'] = $setting['cleaning_cycle'];
            $buildSettingList[$buildId]['day_num']        = $setting['day_num'];
            $buildSettingList[$buildId]['refuel_cycle']   = Tools::map($refuelCycle, 'material_type', 'refuel_cycle', null, null);
        }
        return $buildSettingList;
    }

}
