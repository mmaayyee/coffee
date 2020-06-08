<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "light_program_assoc".
 *
 * @property integer $id
 * @property string $program_id
 * @property string $build_id
 */
class LightProgramAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'light_program_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'build_id'], 'integer']
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
            'build_id' => 'Build ID',
        ];
    }
    

}
