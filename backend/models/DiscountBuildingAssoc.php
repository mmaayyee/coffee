<?php

namespace backend\models;

use common\models\Api;
use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 */
class DiscountBuildingAssoc extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public $holicy_type;

    public $holicy_payment;

    public $building_id;

    public $holicy_id;

    public $holicy_name;

    public $buildingNumber;

    public $holicy_time;

    public $build_name;

    public $weight;

    public $build_pay_type_id; //楼宇支付方式ID

    public $build_pay_type_name; //楼宇支付方式名称

    public function rules()
    {
        return [
            [['building_id', 'holicy_id', 'holicy_type', 'holicy_payment', 'buildingNumber', 'holicy_time'], 'integer'],
            [['holicy_name', 'build_name'], 'string'],
            [['weight', 'build_pay_type_id', 'build_pay_type_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'building_id'         => '楼宇id',
            'holicy_id'           => '优惠策略id',
            'holicy_payment'      => '支付方式',
            'holicy_type'         => '优惠类型',
            'build_name'          => '楼宇',
            'build_pay_type_name' => '楼宇支付策略名称',
        ];
    }
    /**
     *  优惠策略楼宇添加
     */
    public static function disBuildingAssocCreate($params = array())
    {
        if (Api::disBuildingAssocCreate($params)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  根据楼宇名称获取楼宇id
     */
    public static function getBuilidingIDList($params = array())
    {
        return Api::getBuilidingIDList($params);
    }
}
