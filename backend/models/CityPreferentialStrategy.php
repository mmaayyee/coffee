<?php

namespace backend\models;

use yii\base\Model;

/**
 * 按城市设置用户注册时享受的优惠策略
 *
 * @property integer $id
 * @property string $city_name
 * @property integer $coupon_group_id
 */
class CityPreferentialStrategy extends Model
{
    public $city_name;
    public $coupon_group_id;
    public $id;
    public $coupon_group_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_name'], 'required'],
            [['coupon_group_id'], 'integer'],
            [['city_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'city_name'         => '城市名称',
            'coupon_group_id'   => '优惠券套餐id',
            'coupon_group_name' => '优惠策略名称',
        ];
    }
}
