<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clear_code".
 *
 * @property integer $clear_code_id
 * @property string $clear_code_name
 * @property string $clear_code
 */
class ClearCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clear_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clear_code_name', 'clear_code'], 'required'],
            [['clear_code_name'], 'string', 'max' => 255],
            [['clear_code'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'clear_code_id' => '状态码主键id',
            'clear_code_name' => '状态码名称',
            'clear_code' => '状态码',
        ];
    }
}
