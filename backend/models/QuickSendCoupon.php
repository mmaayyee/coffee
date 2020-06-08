<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\Api;
use Yii;

/**
 * This is the model class for table "quick_send_coupon".
 *
 * @property integer $id
 * @property integer $coupon_type
 * @property integer $coupon_number
 * @property integer $coupon_id
 * @property integer $create_time
 */
class QuickSendCoupon extends \yii\db\ActiveRecord
{
    public $id;
    public $coupon_type;
    public $coupon_number;
    public $coupon_sort;
    public $send_phone;
    public $create_time;
    public $coupon_id;
    public $isNewRecord;
    public $send_phone_list;
    public $is_product;
    public $coupon_package_id;
    public $phone;
    public $content;
    public $coupon_remarks;
    public $consume_id;
    public $order_code;
    public $caller_number;
    /** 优惠券类型 */
    //定额券
    const CASH_COUPON = 0;
    //兑换券
    const EXCHANGE_COUPON = 1;
    //红利代金券
    const INTEREST_COUPON = 2;
    // 满减券
    const FULL_DOWN_COUPON = 3;
    // 折扣券
    const DISCOUNT_COUPON = 4;
    // 固定价格券
    const FIX_PRICE_COUPON = 5;
    /** 优惠券是单品还是通用 */
    // 通用优惠券
    const COMMON_COUPON = 0;
    // 单品优惠券
    const PRODUCT_COUPON = 1;
    /** 优惠券是套餐还是单个商品 */
    // 单个商品优惠券
    const COUPON_SINGLE_PRODUCT = 2;
    // 套餐优惠券
    const COUPON_PACKAGE = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_phone_list', 'coupon_number', 'coupon_sort', 'coupon_remarks'], 'required'],
            [['coupon_type', 'coupon_id', 'coupon_package_id', 'coupon_number', 'coupon_sort', 'id', 'create_time', 'consume_id', 'order_code'], 'integer'],
            [['content', 'send_phone', 'coupon_remarks'], 'string'],
            [['coupon_remarks'], 'string', 'max' => 200],
            [['caller_number'], 'number'],
            [['coupon_number'], 'integer', 'max' => 5, 'min' => 1],
            ['coupon_package_id', 'requiredParamsCouponPackageIdRules', 'on' => 'create', 'skipOnError' => false, 'skipOnEmpty' => false],
            ['coupon_type', 'requiredParamsQuickSendCouponCouponTypeRules', 'on' => 'create', 'skipOnError' => false, 'skipOnEmpty' => false],
            ['coupon_id', 'requiredParamsQuickSendCouponCouponIdRules', 'on' => 'create', 'skipOnError' => false, 'skipOnEmpty' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'coupon_type'       => '优惠券种类',
            'coupon_number'     => '发券数量',
            'create_time'       => 'Create Time',
            'coupon_sort'       => '优惠券类型',
            'coupon_id'         => '优惠券',
            'send_phone'        => '用户账号',
            'send_phone_list'   => '',
            'is_product'        => '优惠券范围',
            'coupon_package_id' => '优惠券套餐名称',
            'phone'             => '用户账号',
            'content'           => '优惠内容',
            'coupon_remarks'    => '备注',
            'consume_id'        => '消费记录ID',
            'order_code'        => '订单编号',
            'caller_number'     => '来电号码',
        ];
    }
    /**
     *  编辑自定义验证
     */
    public function requiredParamsCouponPackageIdRules($attribute, $params)
    {
        if ($this->coupon_sort == 1) {
            if ($this->coupon_package_id == '') {
                $this->addError($attribute, '优惠券套餐不能为空');
            }
        }
    }
    /**
     *  编辑自定义验证
     */
    public function requiredParamsQuickSendCouponCouponTypeRules($attribute, $params)
    {
        if ($this->coupon_sort == 2) {
            if ($this->coupon_type == '') {
                $this->addError($attribute, '优惠券种类不能为空');
            }
        }
    }
    /**
     *  编辑自定义验证
     */
    public function requiredParamsQuickSendCouponCouponIdRules($attribute, $params)
    {
        if ($this->coupon_sort == 2) {
            if ($this->coupon_id == '') {
                $this->addError($attribute, '优惠券不能为空');
            }
        }
    }
    /**
     * 获取代金券类型数组
     * @return array 代金券类型
     */
    public static function getNewCouponType($type = 1, $couponType = '')
    {
        $couponTypeArr = [];
        if ($type == 1) {
            $couponTypeArr[''] = '请选择';
        }
        $couponTypeArr[self::CASH_COUPON]      = '定额券';
        $couponTypeArr[self::EXCHANGE_COUPON]  = '兑换券';
        $couponTypeArr[self::FULL_DOWN_COUPON] = '满减券';
        $couponTypeArr[self::DISCOUNT_COUPON]  = '折扣券';
        $couponTypeArr[self::FIX_PRICE_COUPON] = '指定价格券';
        if ($couponType !== '') {
            return !isset($couponTypeArr[$couponType]) ? '' : $couponTypeArr[$couponType];
        }
        return $couponTypeArr;
    }
    public static function getCouponeFieldName($type = 0, $id = '')
    {
        $couponTypeArr = [];
        if ($type == 1) {
            $couponTypeArr[''] = '请选择';
        }

        $couponTypeArr[self::COUPON_SINGLE_PRODUCT] = '优惠券';
        $couponTypeArr[self::COUPON_PACKAGE]        = '优惠券套餐';
        if ($id !== '') {
            return !isset($couponTypeArr[$id]) ? '' : $couponTypeArr[$id];
        }
        return $couponTypeArr;
    }
    /**
     * 获取套餐单品类型列表
     * @param  integer $type 当值等于1时，则加上请选择参数 不等于1时不加
     * @param  integer $id   当$id不存在时获取列表，存在时获取对应的类型名称
     * @return array/string
     */
    public static function getCouponType($type = 0, $id = '')
    {
        $couponTypeArr = [];
        if ($type == 1) {
            $couponTypeArr[''] = '请选择';
        }
        $couponTypeArr[self::COMMON_COUPON]  = '通用';
        $couponTypeArr[self::PRODUCT_COUPON] = '单品';
        if ($id !== '') {
            return !isset($couponTypeArr[$id]) ? '' : $couponTypeArr[$id];
        }
        return $couponTypeArr;
    }
    /**
     * 获取套餐列表
     * @author  tuqiang
     * @version 2017-09-23
     * @return  array
     */
    public static function getCouponPackage()
    {
        $quickSendCouponData = Api::getCouponGroupList();
        if ($quickSendCouponData) {
            return Tools::map($quickSendCouponData, 'group_id', 'group_name');
        }
        return [];
    }
    /**
     * 获取有效套餐列表
     * @author  wbq
     * @version 2018-6-5
     * @return  array
     */
    public static function getCouponValidPackage()
    {
        $quickSendCouponData = Api::getCouponGroupValidList();
        if ($quickSendCouponData) {
            return Tools::map($quickSendCouponData, 'group_id', 'group_name');
        }
        return [];
    }
    /**
     * 获取优惠券列表
     * @author  tuqiang
     * @version 2017-09-23
     * @param   array       优惠券种类id,优惠券单品/通用id
     * @return  array       优惠券列表
     */
    public static function getQuickSendCouponList($params)
    {
        $quickSendCouponData = Api::getQuickSendCouponList($params);
        if ($quickSendCouponData) {
            return Tools::map($quickSendCouponData, 'coupon_id', 'coupon_name');
        }
        return [];
    }

    public static function getDetails($id)
    {
        $model = new self();
        $model->load(['QuickSendCoupon' => Api::getQuickSendCouponDetails($id)]);
        return $model;
    }
}
