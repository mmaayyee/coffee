<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "temporary_equip_setting".
 *
 * @property int $id id
 * @property string $equipment_code 设备编号
 * @property string $stock_code 料仓编号
 * @property int $build_id 楼宇id
 * @property int $day_num 配送周期
 * @property int $material_type_id 物料分类id
 * @property double $stock_volume_bound 料仓上限
 * @property double $holiday_consumption 节假日物料平均消耗
 * @property double $work_consumption 工作日物料平均消耗
 */
class TemporaryEquipSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'temporary_equip_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'day_num', 'material_type_id'], 'integer'],
            [['stock_volume_bound', 'holiday_consumption', 'work_consumption'], 'number'],
            [['equipment_code'], 'string', 'max' => 100],
            [['stock_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                  => 'ID',
            'equipment_code'      => 'Equipment Code',
            'stock_code'          => 'Stock Code',
            'build_id'            => 'Build ID',
            'day_num'             => 'Day Num',
            'material_type_id'    => 'Material Type ID',
            'stock_volume_bound'  => 'Stock Volume Bound',
            'holiday_consumption' => 'Holiday Consumption',
            'work_consumption'    => 'Work Consumption',
            'create_date'         => 'Create Date',
        ];
    }

    /**
     * 保存设备基本信息(便于添加临时任务时计算添加量、剩余量、和最终读数)
     * @author wangxiwen
     * @version 2018-10-29
     * @param array $equipSettingArray 设备配置信息
     * @param int $days 天数
     * @return int
     */
    public static function insertEquipSetting($equipSettingArray)
    {
        //清空表内数据
        self::deleteAll();
        //执行插入操作
        foreach ($equipSettingArray as $equipCode => $equipSetting) {
            foreach ($equipSetting['refuel_cycle'] as $stockCode => $setting) {
                $equipSetting                      = new self();
                $equipSetting->equipment_code      = (string) $equipCode;
                $equipSetting->stock_code          = (string) $stockCode;
                $equipSetting->build_id            = $equipSetting['build_id'] ?? 0;
                $equipSetting->day_num             = $equipSetting['day_num'] ?? 0;
                $equipSetting->material_type_id    = $equipSetting['material_type_id'] ?? 0;
                $equipSetting->stock_volume_bound  = $setting['stock_volume_bound'] ?? 0;
                $equipSetting->holiday_consumption = $setting['holiday_consumption'] ?? 0;
                $equipSetting->work_consumption    = $setting['work_consumption'] ?? 0;
                $saveEquipSettingRes               = $equipSetting->save();
                if (!$saveEquipSettingRes) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 获取设备配置信息
     * @author wangxiwen
     * @version 2018-10-29
     * @param string $equipCode 设备编号
     * @return array
     */
    public static function getTemporaryEquipSetting($equipCode)
    {
        $equipSettingArray = self::find()
            ->where(['equipment_code' => $equipCode])
            ->select('stock_code,day_num,stock_volume_bound,holiday_consumption,work_consumption')
            ->asArray()
            ->all();
        $equipSettingList = [];
        foreach ($equipSettingArray as $setting) {
            $stockCode                    = $setting['stock_code'];
            $equipSettingList[$stockCode] = [
                'day_num'             => $setting['day_num'],
                'stock_volume_bound'  => $setting['stock_volume_bound'],
                'holiday_consumption' => $setting['holiday_consumption'],
                'work_consumption'    => $setting['work_consumption'],

            ];
        }
        return $equipSettingList;
    }
}
