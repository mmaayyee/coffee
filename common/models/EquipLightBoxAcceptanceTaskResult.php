<?php

namespace common\models;
use backend\models\EquipLightBoxDebug;
use Yii;

/**
 * This is the model class for table "equip_light_box_acceptance_task_result".
 *
 * @property integer $id
 * @property integer $task_id
 * @property string $breaker_type
 * @property string $ammeter_type
 * @property double $ammeter_number
 * @property string $acceptance_content
 */
class EquipLightBoxAcceptanceTaskResult extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'equip_light_box_acceptance_task_result';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['task_id'], 'integer'],
            [['ammeter_number'], 'number'],
            [['breaker_type', 'ammeter_type'], 'string', 'max' => 64],
            [['acceptance_content'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'                 => 'ID',
            'task_id'            => 'Task ID',
            'breaker_type'       => 'Breaker Type',
            'ammeter_type'       => 'Ammeter Type',
            'ammeter_number'     => 'Ammeter Number',
            'acceptance_content' => 'Acceptance Content',
        ];
    }
    /**
     * 获取验收结果详情
     * @param  array $where 查询条件
     * @return array        结果详情
     */
    public static function getDetail($field = "*", $where) {
        return self::find()->select($field)->where($where)->asArray()->one();
    }

    /**
     * 获取灯箱验收调试项结果
     * @param  [type] $process_result [description]
     * @param  [type] $id             [description]
     * @return [type]                 [description]
     */
    public static function getAcceptanceContent($processResult, $id, $equipId) {
        $lightBoxDebugRecord = [];
        //获取灯箱选项列表
        $lightBoxDebug = EquipLightBoxDebug::find()->asArray()->all();
        // 获取验收结果
        $acceptanceContent = json_decode(self::getDetail('acceptance_content', ['task_id' => $id])['acceptance_content'], true);
        //获取灯箱选项验收结果
        if ($processResult == 3) {
            foreach ($lightBoxDebug as $v) {
                if (isset($acceptanceContent[$v['Id']])) {
                    if ($acceptanceContent[$v['Id']] == 2) {
                        $v['result']           = 2;
                        $lightBoxDebugRecord[] = $v;
                    } else {
                        $v['result']           = 1;
                        $lightBoxDebugRecord[] = $v;
                    }
                }
            }
        } else {
            foreach ($lightBoxDebug as $v) {
                if (isset($acceptanceContent[$v['Id']])) {
                    $v['result']           = 1;
                    $lightBoxDebugRecord[] = $v;
                }
            }
        }
        return $lightBoxDebugRecord;
    }
}
