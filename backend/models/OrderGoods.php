<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "order_goods".
 *
 * @property int $goods_id 商品ID
 * @property int $order_id 订单ID
 * @property int $user_id 用户ID
 * @property double $source_price 产品价格
 * @property int $source_status 产品状态 0正常，1退货
 * @property int $source_id 产品源ID
 * @property int $source_number 产品数量
 * @property int $created_at 创建时间
 * @property int $goods_type 商品类型 1-购买 2-赠送
 * @property int $source_type 产品类型0单品,1套餐,2红利套餐,3单品活动,4套餐活动
 * @property int $goods_source_type 商品来源类型（1-自组合套餐活动）
 *
 * @property OrderInfo $order
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /** 商品类型常量定义 1-购买 2-赠送 */
    const TYPE_BUY  = 1;
    const TYPE_SEND = 2;
    /*
     *  单品
     */
    const PRODUCT = 0;

    /*
     *  咖啡套餐
     */
    const GROUP_PRODUCT = 1;

    /*
     *  红利套餐
     */
    const GROUP_COUPON = 2;
    // 单品活动
    const PRODUCT_ACTIVE = 3;
    // 套餐活动
    const GROUP_ACTIVE = 4;
    // 自组合
    const SELF_COMBINATION = 5;
    // 拼团
    const FIGHT_GROUP = 6;

    //退货
    const REFUND = 1;

    /**
     *  商品来源类型
     */
    // 自组合套餐
    const SOURCE_COMBINPACKAGE = 1;

    /*
     * 申请起始日期
     */
    public $createdFrom;
    /*
     * 申请截止日期
     */
    public $createdTo;

    /*
     * 申请人手机号
     */
    public $userMobile;

    /*
     * 申请人姓名
     */
    public $userName;
    /**
     * 订单原价
     */
    public $original_price;
    public $goods_id;
    public $order_id;
    public $user_id;
    public $source_price;
    public $source_status;
    public $source_id;
    public $source_number;
    public $created_at;
    public $goods_type;
    public $source_type;
    public $goods_source_type;
    public $source_name;
    public $actual_pay;
    public $source_price_discount;
    public $pay_type;
    public $pay_static;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'source_id'], 'required'],
            [['order_id', 'user_id', 'source_type', 'source_status', 'source_number', 'source_id', 'goods_source_type', 'goods_type'], 'integer'],
            [['source_price', 'original_price'], 'number'],
            [['source_name', 'userMobile', 'actual_pay', 'source_price_discount', 'pay_type', 'pay_static', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id'              => '商品ID',
            'order_id'              => '订单ID',
            'user_id'               => '用户ID',
            'source_id'             => '产品',
            'source_price'          => '产品价格',
            'source_status'         => '产品状态',
            'created_at'            => '购买时间',
            'source_type'           => '产品类型',
            'source_number'         => '产品数量',
            'createdFrom'           => '开始日期',
            'createdTo'             => '截止日期',
            'userMobile'            => '手机号',
            'userName'              => '申请人',
            'goods_type'            => '获取方式',
            'original_price'        => '商品原价',
            'actual_pay'            => '实际支付',
            'source_price_discount' => '优惠金额',
            'pay_type'              => '支付方式',
            'pay_static'            => '支付状态',
        ];
    }
    /**
     *  获取订单商品列表全部数据
     * @param $params
     * @return array|mixed
     */
    public static function getOrderGoodsList($params)
    {
        $page           = isset($params['page']) ? $params['page'] : 0;
        $orderGoodsList = self::postBase("order-info-api/get-order-goods-list", $params, '?page=' . $page);
        return !$orderGoodsList ? [] : Json::decode($orderGoodsList);
    }
    /**
     *  导出订单商品列表全部数据
     * @param $params
     * @return array|mixed
     */
    public static function getExportOrderGoodsList($params)
    {
        $orderGoodsList = self::postBase("order-info-api/export-order-goods-list", $params);
        return !$orderGoodsList ? [] : Json::decode($orderGoodsList);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(OrderInfo::className(), ['order_id' => 'order_id']);
    }
    /**
     * 获取商品类型
     * @return string 商品类型
     */
    public function getSourceType()
    {
        $statusArray = $this->getSourceTypeArray();
        return $statusArray[$this->source_type];
    }

    /**
     * 获取商品类型
     * @return array 商品类型数组
     */
    public function getSourceTypeArray()
    {
        return array(
            ''                     => '请选择',
            self::PRODUCT          => '单品',
            self::GROUP_PRODUCT    => '咖啡套餐',
            self::GROUP_COUPON     => '红利套餐',
            self::PRODUCT_ACTIVE   => '单品活动',
            self::GROUP_ACTIVE     => '套餐活动',
            self::SELF_COMBINATION => '自组合',
            self::FIGHT_GROUP      => '拼团',
        );

    }
    /**
     * 获取商品类型
     * @return string 商品类型
     */
    public function getGoodsType()
    {
        $statusArray = $this->getGoodsTypeArray();
        return !isset($statusArray[$this->goods_type]) ? '' : $statusArray[$this->goods_type];
    }
    /**
     * 获取方式数组
     * @return array 商品类型数组
     */
    public function getGoodsTypeArray()
    {
        return array(
            ''              => '请选择',
            self::TYPE_BUY  => '购买',
            self::TYPE_SEND => '赠送',
        );
    }
    /**
     *  获取用户手机号
     * @param $userID
     * @return array|mixed
     */
    public function getUserMobile($userID)
    {
        $userMobile = self::getBase('order-info-api/get-user-mobile', "?user_id=" . $userID);
        return !$userMobile ? [] : Json::decode($userMobile);
    }
    /**
     * 获取商品状态
     * @return string 商品状态
     */
    public function getStatus()
    {
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->source_status];
    }

    /**
     * 获取商品状态数组
     * @return array 商品状态数组
     */
    public function getStatusArray()
    {
        return array(
            ''  => '请选择',
            '0' => '正常',
            '1' => '退货',
        );
    }

    public static function postBase($action, $data = [], $params = '')
    {
//        echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;
        //        var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));

    }
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
