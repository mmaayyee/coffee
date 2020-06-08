<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "distribution_maintenance".
 *
 * @property integer $id
 * @property string $start_repair_time
 * @property string $end_repair_time
 * @property string $malfunction_reason
 * @property string $malfunction_description
 * @property string $process_method
 * @property integer $process_result
 * @property string $distribution_task_id
 */
class DistributionMaintenance extends \yii\db\ActiveRecord
{
    /*维修（验收）成功*/
    const RESULT_SUCCESS = 3;

    /*维修（验收）失败*/
    const RESULT_FAILURE = 2;

    //维修结果
    public static $distribution_maintenance_repair_result = [
        self::RESULT_SUCCESS => '维修成功',
        self::RESULT_FAILURE => '维修失败',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_maintenance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_repair_time', 'end_repair_time', 'process_result', 'distribution_task_id'], 'integer'],
            [['malfunction_reason', 'malfunction_description'], 'string', 'max' => 500],
            [['process_method'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                      => 'ID',
            'start_repair_time'       => '开始维修时间',
            'end_repair_time'         => '结束维修时间',
            'malfunction_reason'      => '故障原因',
            'malfunction_description' => '故障描述',
            'process_method'          => '处理方法',
            'process_result'          => '处理结果',
            'distribution_task_id'    => '配送任务ID',
        ];
    }
    /**
     * 获取维修信息
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $taskId 任务ID
     * @return array
     */
    public static function getDistributionMaintenance($taskId)
    {
        return self::find()
            ->where(['distribution_task_id' => $taskId])
            ->asArray()
            ->all();
    }
}
