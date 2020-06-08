<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equip_task_fitting".
 *
 * @property integer $id
 * @property integer $task_id
 * @property string $fitting_name
 * @property string $fitting_model
 * @property string $factory_number
 * @property integer $num
 * @property string $remark
 *
 * @property EquipTask $task
 */
class EquipTaskFitting extends \yii\db\ActiveRecord
{
    const EquipmentTask    = 0; //�豸����
    const DistributionTask = 1; //��������
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_task_fitting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'num'], 'integer'],
            [['fitting_name'], 'string', 'max' => 50],
            [['fitting_model', 'factory_number'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'task_id'        => 'Task ID',
            'fitting_name'   => 'Fitting Name',
            'fitting_model'  => 'Fitting Model',
            'factory_number' => 'Factory Number',
            'num'            => 'Num',
            'remark'         => 'Remark',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(EquipTask::className(), ['id' => 'task_id']);
    }
    /**
     * ��ȡά�������Ϣ
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $taskId ����ID
     * @return array
     */
    public static function getEquipTaskFitting($taskId)
    {
        return self::find()
            ->where(['task_id' => $taskId, "task_type" => self::DistributionTask])
            ->asArray()
            ->all();
    }
}
