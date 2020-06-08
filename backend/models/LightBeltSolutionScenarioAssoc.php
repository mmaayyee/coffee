<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "light_belt_solution_scenario_assoc".
 *
 * @property integer $id
 * @property string $program_id
 * @property string $scenario_id
 * @property integer $is_default
 * @property string $default_strategy_id
 */
class LightBeltSolutionScenarioAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'light_belt_solution_scenario_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'scenario_id', 'is_default', 'default_strategy_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Program ID',
            'scenario_id' => 'Scenario ID',
            'is_default' => 'Is Default',
            'default_strategy_id' => 'Default Strategy ID',
        ];
    }
}
