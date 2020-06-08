<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "building_holiday_status".
 *
 * @property integer $id
 * @property integer $building_id
 * @property integer $is_running
 * @property integer $create_userid
 */
class BuildingHolidayStatus extends \yii\db\ActiveRecord
{
    const STOP_STATUS = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building_holiday_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['building_id', 'is_running', 'create_userid'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'building_id'   => 'Building ID',
            'is_running'    => 'Is Running',
            'create_userid' => 'Create Userid',
        ];
    }

    /**
     * 获取节假日不运维的楼宇
     * @author wxl
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getSettingStopBuildingID()
    {
        $list = self::find()->select('building_id')->where(['is_running' => self::STOP_STATUS])->asArray()->all();
        return $list ? ArrayHelper::getColumn($list, 'building_id') : [];
    }

    /**
     * 批量插入不运维楼宇
     * @author wxl
     * @param array $buildingIds
     * @return int
     * @throws \yii\db\Exception
     */
    public static function addBuildingStop($buildingIds = [])
    {

        $userId        = Yii::$app->user->identity->id;
        $buildingArray = [];
        foreach ($buildingIds as $k => $buildId) {
            $buildingArray[] = ['building_id' => $buildId, 'is_running' => self::STOP_STATUS, 'create_userid' => $userId];
        }

        // 批量插入数据库中
        $result = Yii::$app->db->createCommand()->batchInsert(BuildingHolidayStatus::tableName(), ['building_id', 'is_running', 'create_userid'], $buildingArray)->execute();

        return $result;
    }

    /**
     * 批量删除楼宇不运维
     * @author wxl
     * @param array $buildingIds
     * @return int
     * @throws \yii\db\Exception
     */
    public static function removeBuildingStop($buildingIds = [])
    {

        return Yii::$app->db->createCommand()->delete(BuildingHolidayStatus::tableName(), ['in', 'building_id', $buildingIds])->execute();
    }

    /**
     * 获取节假日不运维楼宇(日常任务使用)
     * @author wangxiwen
     * @version 2018-10-16
     * @return
     */
    public static function getHolidayInoperateBuild()
    {
        return self::find()
            ->select('building_id')
            ->where(['is_running' => self::STOP_STATUS])
            ->column();
    }
}
