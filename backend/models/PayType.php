<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pay_type".
 *
 * @property int $pay_type_id 支付方式ID
 * @property string $pay_type_name 支付方式名称
 * @property string $logo_pic 支付方式log图
 * @property string $bg_pic 支付方式背景图
 * @property int $is_open 默认是否打开 0-不打开 1-打开
 * @property int $is_support_discount 默认是否支持优惠策略
 * @property int $discount_holicy_id 支付方式优惠策略ID
 * @property int $weight 权重值 从大到小排序
 * @property int $create_time 添加时间
 * @property int $update_time 更新时间
 */
class PayType extends \yii\db\ActiveRecord
{
    /** 是否开启支付方式 0-未开启 1-开启 */
    const IS_OPEN_NO  = 0;
    const IS_OPEN_YES = 1;
    /** 是否支持优惠策略 0-不支持 1-支持 */
    const IS_SURPOT_NO  = 0;
    const IS_SURPOT_YES = 1;

    /** 支付方式ID 1-微信 2-银联二维码 3-银联闪付 4-招行支付 5-任务支付 6-建行支付*/
    const PAY_TYPE_WECHAT       = 1;
    const PAY_TYPE_UNION_QRCODE = 2;
    const PAY_TYPE_UNION        = 3;
    const PAY_TYPE_CMB          = 4;
    const PAY_TYPE_TASK         = 5;
    const PAY_TYPE_CCB          = 6;

    public $pay_type_id;
    public $pay_type_name;
    public $is_open;
    public $is_support_discount;
    public $discount_holicy_id;
    public $weight;
    public $create_time;
    public $update_time;
    public $logo_pic;
    public $bg_pic;
    public $is_use_build;
    public $isOpenList = [
        ''                => '请选择',
        self::IS_OPEN_NO  => '关闭',
        self::IS_OPEN_YES => '开启',
    ];
    public $isDiscountList = [
        ''                  => '请选择',
        self::IS_SURPOT_NO  => '关闭',
        self::IS_SURPOT_YES => '开启',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weight', 'create_time', 'update_time'], 'integer'],
            [['pay_type_name'], 'string', 'max' => 30],
            [['logo_pic', 'bg_pic'], 'string', 'max' => 300],
            [['is_open', 'is_support_discount', 'discount_holicy_id', 'pay_type_id', 'is_use_build'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_type_id'         => '支付方式ID',
            'pay_type_name'       => '支付方式名称',
            'logo_pic'            => '支付方式图标',
            'bg_pic'              => '二维码中心图标',
            'is_open'             => '默认是否打开',
            'is_support_discount' => '默认是否支持优惠策略',
            'discount_holicy_id'  => '支付方式优惠策略',
            'weight'              => '顺序',
            'create_time'         => '添加时间',
            'update_time'         => '更新时间',
        ];
    }

}
