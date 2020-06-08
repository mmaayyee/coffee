<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "delivery_person".
 *
 * @property string $person_id
 * @property string $person_name 配送人员名
 * @property string $wx_number 配送人员企业微信号
 * @property string $mobile 配送员手机号
 */
class DeliveryPerson extends \yii\db\ActiveRecord
{
    public $person_id;
    public $person_name;
    public $wx_number;
    public $mobile;
    public $person_status;
    public $building_info;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delivery_person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_name', 'wx_number', 'mobile'], 'required'],
            [['person_id', 'person_status'], 'integer'],
            [['person_name'], 'string', 'max' => 15],
            [['wx_number'], 'string', 'max' => 20],
            [['mobile'], 'string', 'max' => 11],
            [['building_info'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'person_id'     => 'Person ID',
            'person_name'   => '配送人员名',
            'wx_number'     => '企业微信号',
            'mobile'        => '配送人员手机号',
            'person_status' => '人员状态',
        ];
    }

}
