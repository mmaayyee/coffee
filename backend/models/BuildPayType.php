<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "build_pay_tyoe".
 *
 * @property int $build_pay_type_id 楼宇支付方式ID
 * @property string $build_pay_type_name 楼宇支付方式名称
 * @property int $create_time 添加时间
 */
class BuildPayType extends \yii\db\ActiveRecord
{

    public $build_pay_type_id;
    public $build_pay_type_name;
    public $create_time;
    public $build_number;
    public $update_time;
    public $pay_type_number;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'build_number', 'build_pay_type_id', 'update_time', 'pay_type_number'], 'integer'],
            [['build_pay_type_name'], 'string', 'max' => 100],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'build_pay_type_id'   => '楼宇支付策略ID',
            'build_pay_type_name' => '楼宇支付策略名称',
            'create_time'         => '添加时间',
        ];
    }
}
