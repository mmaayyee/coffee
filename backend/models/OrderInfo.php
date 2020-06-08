<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "order_info".
 *
 * @property int $order_id 订单ID
 * @property int $user_id 用户ID
 * @property int $pay_type 支付方式,0现金、1微信、2通用券3、人脸支付4、任务支付 5、咖豆支付 6、积分兑换
 * @property int $order_status 订单状态（0未付款，1已付款,2已退款，3退款申请）
 * @property int $order_type 订单类型0线下订单1线上订单
 * @property double $total_fee 订单总价
 * @property double $actual_fee 实际支付
 * @property double $gift_fee 卡券支付
 * @property double $discount_fee 总优惠金额
 * @property int $order_cups 咖啡总杯数
 * @property int $created_at 创建时间
 * @property int $pay_at 支付时间
 * @property string $order_code 订单编码(现金设备编号+时间戳，线上用户ID+时间戳）
 * @property int $paid 投入现金
 * @property int $changes 找零金额
 * @property int $is_company 是否协议咖啡订单 0否 1是
 * @property string $equipment_code 设备编号
 * @property int $order_version 版本号(0-设备， 1-公众号)
 * @property int $beans_num 消费的咖豆数量
 * @property string $beans_amount 咖豆实际抵扣金额
 * @property int $is_refunds 是否退款（0-退款，1-不退款）
 */
class OrderInfo extends \yii\db\ActiveRecord
{
    public $order_id;
    public $user_id;
    public $pay_type;
    public $order_status;
    public $order_type;
    public $total_fee;
    public $actual_fee;
    public $gift_fee;
    public $order_cups;
    public $created_at;
    public $pay_at;
    public $order_code;
    public $paid;
    public $changes;
    public $beans_num;
    public $beans_amount;
    public $coupon_real_value;
    public $source_price;
    public $user_mobile;
    public $total;
    public $totalFee;
    public $actualFee;
    public $totalCups;
    public $totalBenasNum;
    public $source_price_discount;
    public $realPrice;
    public $averageCup;
    public $count;
    public $discount_fee;
    public $source_type;
    public $delivery_cost;

    /*
     * 订单状态-未付款
     */
    const STATUS_UNPAY = 0;

    /*
     * 订单状态-已付款
     */
    const STATUS_PAYED = 1;

    /*
     * 订单状态-已退款
     */
    const STATUS_REFUND = 2;
    // 部分退款
    const STATUS_PART_REFUND = 4;
    /*
     * 订单状态-申请退款
     */
    const STATUS_REFUND_APP = 3;

    const REFUNDS = 0; // 退款

    const NO_REFUNDS = 1; // 不退款
    /**
     * order_version
     * 订单区分
     */
    const EQUIP_BUY = 1; // 设备购买

    const PUBLIC_BUY = 0; // 公众号购买

    /** 定义支付方式常量 0-现金 1-微信 2-优惠券 3-人脸支付 4-任务支付 5-咖豆支付 6-积分对换 7-银联支付 8建行支付*/
    const PAY_CASH        = 0;
    const PAY_WECHAT      = 1;
    const PAY_COUPON      = 2;
    const PAY_FACE        = 3;
    const PAY_TASK        = 4;
    const PAY_BEANS       = 5;
    const REDEMPTION_CODE = 6;
    const PAY_UNION       = 7;
    const PAY_CCB         = 8;
    const PAY_QR_CODE     = 9;
    const PAY_CMB         = 10;

    /** 订单来源 0-标准 1-自组合 2-拼团 3-外卖 */
    const SOURCE_TYPE_COMMON      = 0;
    const SOURCE_TYPE_SELF_COMBIN = 1;
    const SOURCE_TYPE_GROUP       = 2;
    const SOURCE_TYPE_DELIVERY    = 3;

    /*
     * 创建起始日期
     */
    public $createdFrom;

    /*
     * 创建截止日期
     */
    public $createdTo;

    /*
     * 支付起始日期
     */
    public $payFrom;
    /*
     * 支付截止日期
     */
    public $payTo;
    /*
     * 申请人手机号
     */
    public $userMobile;

    /*
     * 申请人姓名
     */
    public $userName;
    // 优惠劵名称
    public $coupon_name;

    /**
     * 订单来源
     * @var [type]
     */
    public static $orderSourceType = [
        ''                            => '请选择',
        self::SOURCE_TYPE_COMMON      => '标准',
        self::SOURCE_TYPE_SELF_COMBIN => '自组合',
        self::SOURCE_TYPE_GROUP       => '拼团',
        self::SOURCE_TYPE_DELIVERY    => '外卖',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'pay_type', 'order_status', 'order_type', 'order_cups', 'created_at', 'pay_at'], 'integer'],
            [['pay_type', 'order_type', 'total_fee', 'actual_fee', 'gift_fee', 'order_cups', 'created_at'], 'required'],
            [['total_fee', 'actual_fee', 'gift_fee', 'paid', 'changes', 'beans_amount'], 'number'],
            [['beans_amount'], 'default', 'value' => 0],
            [['order_id', 'order_code', 'beans_num', 'createdFrom', 'createdTo', 'userName', 'coupon_name', 'coupon_real_value', 'source_price', 'user_mobile', 'source_price_discount', 'discount_fee', 'source_type', 'delivery_cost'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id'          => '订单ID',
            'user_id'           => '用户ID',
            'pay_type'          => '支付方式',
            'order_status'      => '订单状态',
            'order_type'        => '订单类型',
            'total_fee'         => '订单总价',
            'actual_fee'        => '付款金额',
            'gift_fee'          => '卡券支付',
            'order_cups'        => '咖啡总杯数',
            'created_at'        => '创建时间',
            'pay_at'            => '支付时间',
            'createdFrom'       => '创建时间',
            'createdTo'         => '至',
            'userMobile'        => '手机号',
            'userName'          => '姓名',
            'order_code'        => '订单编号',
            'paid'              => '投币',
            'changes'           => '找零',
            'beans_num'         => '咖豆数量',
            'beans_amount'      => '咖豆价值',
            'coupon_name'       => '优惠券名称',
            'source_price'      => '优惠汇总',
            'coupon_real_value' => '优惠券价值',
            'user_mobile'       => '手机号',
            'coupon_name'       => '优惠券名称',
            'discount_fee'      => '用户优惠',
            'source_type'       => '订单来源',
            'payFrom'           => '支付时间',
            'payTo'             => '至',
        ];
    }

    /**
     *  获取订单管理列表全部数据
     * @param $params
     * @return array|mixed
     */
    public static function getOrderInfoList($params)
    {
        $page = 1;
        if (isset($params['page'])) {
            $page = $params['page'];
            unset($params['page']);
        }
        $orderInfoList = self::postBase("order-info-api/order-info-list", $params, '?page=' . $page);
        return !$orderInfoList ? [] : Json::decode($orderInfoList);
    }

    /**
     * 支付信息汇总接口
     * @Author  : GaoYongLi
     * @DateTime: 2018/6/4
     */
    public static function getPaymentInfo($params)
    {
        $paymentInfo = self::postBase("order-info-api/get-payment-information", $params);
        return !$paymentInfo ? [] : Json::decode($paymentInfo);
    }

    /**
     * 获取支付方式
     * @return string 支付方式
     */
    public function getPaytype($paytype = '')
    {
        $paytypeList = [
            ''                    => '请选择',
            self::PAY_CASH        => '现金',
            self::PAY_WECHAT      => '微信',
            self::PAY_COUPON      => '优惠券',
            self::PAY_FACE        => '人脸支付',
            self::PAY_TASK        => '任务支付',
            self::PAY_BEANS       => '咖豆支付',
            self::REDEMPTION_CODE => '积分兑换',
            self::PAY_UNION       => '银联闪付',
            self::PAY_CCB         => '建行龙卡支付',
            self::PAY_QR_CODE     => '银联二维码',
            self::PAY_CMB         => '招行支付',

        ];
        if ($paytype !== '' && !empty($paytypeList[$paytype])) {
            return $paytypeList[$paytype];
        }
        return $paytypeList;
    }

    /**
     * 获取支付方式数组
     * @return array 支付方式数组
     */
    public function getPaytypeArray()
    {
        return array(
            ''                    => '请选择',
            self::PAY_CASH        => '现金',
            self::PAY_WECHAT      => '微信',
            self::PAY_COUPON      => '优惠券',
            self::PAY_FACE        => '人脸支付',
            self::PAY_TASK        => '任务支付',
            self::PAY_BEANS       => '咖豆支付',
            self::REDEMPTION_CODE => '积分兑换',
            self::PAY_UNION       => '银联闪付',
            self::PAY_QR_CODE     => '银联二维码',
            self::PAY_CCB         => '建行龙卡支付',
            self::PAY_CMB         => '招行支付',
        );
    }
    /**
     * 获取订单状态
     * @return string 订单状态
     */
    public function getOrderStatus($orderStatus = '')
    {
        $orderStatusList = [
            ''                       => '请选择',
            self::STATUS_UNPAY       => '未付款',
            self::STATUS_PAYED       => '已付款',
            self::STATUS_REFUND      => '已退款',
            self::STATUS_PART_REFUND => '部分退款',
            self::STATUS_REFUND_APP  => '申请退款',

        ];
        if ($orderStatus !== '' && !empty($orderStatusList[$orderStatus])) {
            return $orderStatusList[$orderStatus];
        }
        return $orderStatusList;
    }
    /**
     * 获取订单状态数组
     * @return array 订单状态数组
     */
    public function getStatusArray()
    {
        return array(
            ''                       => '请选择',
            self::STATUS_UNPAY       => '未付款',
            self::STATUS_PAYED       => '已付款',
            self::STATUS_REFUND      => '全部退款',
            self::STATUS_PART_REFUND => '部分退款',
            self::STATUS_REFUND_APP  => '申请退款',
        );
    }

    /**
     * 获取订单类型数组
     * @return array 订单类型数组
     */
    public function getOrderTypeArray($orderType = '')
    {
        $orderStatusList = [
            ''  => '请选择',
            '0' => '线下订单',
            '1' => '线上订单',

        ];
        if ($orderType !== '' && !empty($orderStatusList[$orderType])) {
            return $orderStatusList[$orderType];
        }
        return $orderStatusList;
    }

    /**
     * 获取订单来源类型
     * @author zhenggangwei
     * @date   2018-08-27
     * @return string     订单来源类型
     */
    public function getSourceType()
    {
        $sourceTypeList = self::$orderSourceType;
        unset($sourceTypeList['']);
        if (empty($sourceTypeList[$this->source_type])) {
            return '';
        }
        return $sourceTypeList[$this->source_type];
    }

    /**
     * 获取优惠券ID和name的列表
     * @author zhenggangwei
     * @date   2019-01-17
     * @return array
     */
    public static function getCouponIdNameList()
    {
        $couponList       = self::getBase('coupon-api/coupon-id-name-list');
        $couponList       = Json::decode($couponList);
        $couponIdNameList = [];
        if ($couponList && $couponList['error_code'] == 0) {
            $couponIdNameList = $couponList['data'];
        }
        return $couponIdNameList;
    }

    public static function postBase($action, $data = [], $params = '')
    {
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }

    public static function getBase($action, $params = '')
    {
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
