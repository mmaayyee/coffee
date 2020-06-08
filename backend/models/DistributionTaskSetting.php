<?php

namespace backend\models;

use Yii;
use Yii\helpers\ArrayHelper;

/**
 * This is the model class for table "distribution_task_setting".
 *
 * @property integer $id
 * @property integer $equip_type_id
 * @property integer $cleaning_cycle
 * @property integer $refuel_cycle
 * @property integer $day_num
 *
 * @property ScmEquipType $equipType
 */
class DistributionTaskSetting extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'distribution_task_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['equip_type_id', 'cleaning_cycle', 'refuel_cycle', 'day_num'], 'integer'],
            [['equip_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmEquipType::className(), 'targetAttribute' => ['equip_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'             => 'ID',
            'equip_type_id'  => 'Equip Type ID',
            'cleaning_cycle' => 'Cleaning Cycle',
            'refuel_cycle'   => 'Refuel Cycle',
            'day_num'        => 'Day Num',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipType() {
        return $this->hasOne(ScmEquipType::className(), ['id' => 'equip_type_id']);
    }

    public static function settingObj($where = []) {
        return self::find()->where($where)->one();
    }

    /**
     * 返回以设备类型id为键值的设置数组
     * @return [type] [description]
     */
    public static function settingArr() {
        return ArrayHelper::index(self::find()->asArray()->all(), 'equip_type_id');
    }
    /**
     * 添加配送任务设置（清洗、换料周期，配送天数）
     * @param [type] $data [description]
     */
    public static function addAll($data) {
        if (!$data) {
            return false;
        }

        $result = true;
        foreach ($data as $v) {
            $model = self::find()->where(['equip_type_id' => $v['equip_type_id']])->one();
            if ($model) {
                if (isset($v['cleaning_cycle'])) {
                    $model->cleaning_cycle = $v['cleaning_cycle'] ? $v['cleaning_cycle'] : 0;
                }

                if (isset($v['refuel_cycle'])) {
                    $model->refuel_cycle = $v['refuel_cycle'] ? $v['refuel_cycle'] : 0;
                }

                if (isset($v['day_num'])) {
                    $model->day_num = $v['day_num'] ? $v['day_num'] : 0;
                }

            } else {
                $model = new DistributionTaskSetting();

                if (isset($v['cleaning_cycle'])) {
                    $model->cleaning_cycle = $v['cleaning_cycle'] ? $v['cleaning_cycle'] : 0;
                }

                if (isset($v['refuel_cycle'])) {
                    $model->refuel_cycle = $v['refuel_cycle'] ? $v['refuel_cycle'] : 0;
                }

                if (isset($v['day_num'])) {
                    $model->day_num = $v['day_num'] ? $v['day_num'] : 0;
                }

                $model->equip_type_id = $v['equip_type_id'];
            }
            if (!$model->save()) {
                $result = false;
            }
        }
        return $result;
    }
}
