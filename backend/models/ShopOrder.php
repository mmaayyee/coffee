<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/18
 * Time: 下午3:11
 */
namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

class ShopOrder extends \yii\db\ActiveRecord
{
    //ID
    public $order_id;
    //订单号
    public $order_code;
    //用户ID
    public $user_id;
    //收货ID
    public $address_id;
    //总额
    public $total_fee;
    //实付金额
    public $actual_fee;
    //咖豆数量
    public $beans_num;
    //下单时间
    public $create_time;
    //快递单号
    public $express_code;
    //快递公司
    public $express_company;
    //快递费用
    public $express_money;
    //订单状态
    public $order_status;
    //发货时间
    public $send_time;
    //收货时间
    public $receive_time;
    //总优惠金额
    public $discount_fee;
    //商品数量
    public $goods_num;
    //商品名称
    public $goods_name;
    //下单开始时间
    public $begin_time;
    //下单结束时间
    public $end_time;
    // 注册手机号
    public $mobile;
    // 收货手机号
    public $phone;
    //待支付
    const WAIT_PAY = 0;
    //待发货
    const WAIT_EXPRESS = 1;
    //待收货
    const WAIT_RECEIVE = 2;
    //已完成
    const FINISHED = 3;
    //待退款
    const WAIT_REFUND = 4;
    //已退款
    const REFUNDED = 5;
    //已失效
    const LOSED = 6;

    public function rules()
    {
        return [
            [['order_id', 'user_id', 'beans_num', 'create_time', 'order_status', 'send_time', 'receive_time'], 'integer'],
            [['total_fee', 'actual_fee', 'express_money', 'discount_fee', 'mobile', 'phone'], 'number'],
            [['order_code', 'express_company'], 'string', 'max' => 50],
            [['express_code'], 'string', 'max' => 80],
        ];
    }
    /**
     * 获取订单的状态
     * @author wxl
     * @date 2018-01-22
     * @param int $status
     * @return array
     */
    public static function getOrderStatus($status = '')
    {
        $statusList = ['' => '请选择', 0 => '待支付', 1 => '待发货', 2 => '待收货', 3 => '已完成', 4 => '待退款', 5 => '已退款'];
        return $status !== '' ? $statusList[$status] : $statusList;
    }

    public function attributeLabels()
    {
        return [
            'order_id'        => '订单ID',
            'order_code'      => '订单号',
            'user_id'         => '用户ID',
            'address_id'      => '收货ID',
            'total_fee'       => '总金额',
            'actual_fee'      => '实付金额',
            'create_time'     => '创建时间',
            'express_money'   => '快递费用',
            'express_code'    => '快递单号',
            'order_status'    => '订单状态',
            'send_time'       => '发货时间',
            'discount_fee'    => '总优惠金额',
            'receive_time'    => '收货时间',
            'express_company' => '快递公司',
            'phone'           => '收货手机号',
            'mobile'          => '注册手机号',
        ];
    }

    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  wangxl
     * @version 2017-12-21
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params . Json::encode($data);die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));
    }

    /**
     * get提交数据共用方法
     * @author  wangxl
     * @version 2017-12-21
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);
    }

    /**
     * 获取订单列表
     * @author wxl
     * @date 2018-01-22
     * @return array
     */
    public static function getOrderListByParam($params)
    {
        $page = isset($params['page']) ? $params['page'] : 0;
        $list = self::postBase("shop-api/shop-orders", $params, '&page=' . $page);
        return !$list ? [] : Json::decode($list);
    }

    /**
     * 获取订单的详情
     * @author wxl
     * @date 2018-01-22
     * @param int $orderId 订单ID
     * @return array
     */
    public static function getOrderInfoByOrderId($orderId = 0)
    {
        $order = self::getBase('shop-api/order-detail', "&order_id=" . $orderId);
        return !$order ? [] : Json::decode($order);
    }

    /**
     * 获取订单商品详情
     * @author wxl
     * @date 2018-01-22
     * @param int $orderId 订单ID
     * @return array
     */
    public static function getOrderGoods($orderId = 0)
    {
        $order = self::getBase('shop-api/shop-order-goods', "&id=" . $orderId);
        return !$order ? [] : $order;
    }

    /**
     * 获取订单商品信息
     * @author wxl
     * @date 2018-01-22
     * @param int $orderId 订单ID
     * @return string
     */
    public function getOrderGoodsInfo($orderId = 0, $orderGoodsList, $export = 0)
    {
        $string = '';
        foreach ($orderGoodsList as $key => $goods) {
            if (($goods['order_id'] != $orderId)) {
                continue;
            }
            if ($export) {
                $string .= $goods['goods_name'] . $goods['goods_attribute'] . ' × ' . $goods['goods_num'] . "\n";
            } else {
                $string .= '<strong>' . $goods['goods_name'] . ' </strong>' . $goods['goods_attribute'] . ' × ' . $goods['goods_num'] . '<br/>';
            }
        }
        return $string;
    }

    /**
     * 返回详情页商品数据
     * @author wxl
     * @date 2018-01-22
     * @param int $orderId 订单ID
     * @return mixed
     */
    public function getOrderGoodsDetail($orderId = 0)
    {
        $orderGoods = Json::decode(self::getOrderGoods($orderId));
        return $orderGoods;
    }

    /**
     * 审核退款
     * @author wxl
     * @date 2017-11-11
     * @param array $orderStore
     * @return array|boole
     */
    public static function shopOrderRefund($orderStore = [])
    {
        $list = self::postBase("shop-api/order-refund", $orderStore);
        return isset($list) ? $list : [];
    }

    /**
     * 订单发货
     * @author wxl
     * @date 2017-11-11
     * @param string $orderExpress 订单快递信息
     * @return array
     */
    public static function updateOrderExpress($orderExpress = '')
    {
        $list = self::postBase("shop-api/order-express", $orderExpress);
        return isset($list) ? $list : [];
    }

    /**
     * 订单申请退款
     * @author wxl
     * @date 2017-11-11
     * @param string $refundInfo 退款信息
     * @return boole
     */
    public static function applyOrderRefund($refundInfo = '')
    {
        return self::postBase("shop-api/order-apply-refund", $refundInfo);
    }

    /**
     * 获取用户的手机号
     * @author wxl
     * @date 2017-11-11
     * @param int $userId
     * @return array|int
     */
    public function getUserPhoneByUserId($userId = 0)
    {
        return self::getBase('shop-api/user-phone', "&user_id=" . $userId);
    }

    /**
     * 退款申请记录
     * @author wxl
     * @date 2017-11-11
     * @param int $orderId 订单ID
     * @return string
     */
    public static function detailRefundInfo($orderId = 0)
    {
        $refundInfos = Json::decode(self::getOrderRefundInfo($orderId));

        $refundDes = '';
        //查询退款记录表
        if ($refundInfos) {
            foreach ($refundInfos as $refundInfo) {
                $username = Manager::getField('realname', ['id' => $refundInfo['apply_user_id']]);
                $refundDes .= '<br>申请时间:' . date("Y-m-d H:i:s", $refundInfo['apply_time']) . '; ' . $username .
                    '发起了申请退款; <br>申请原因:' . $refundInfo['refund_reason'] . '; <br>';
                if ($refundInfo['check_result'] > 0) {
                    $action        = $refundInfo['check_result'] == 1 ? '通过' : '拒绝';
                    $refundReason  = $refundInfo['check_result'] == 2 ? '拒绝原因:' . $refundInfo['refuse_reason'] . ';' : '';
                    $refundReasons = '拒绝原因:' . $refundInfo['refuse_reason'] ? '拒绝原因:' . $refundInfo['refuse_reason'] . ';' : '';
                    $manager       = Manager::getField('realname', ['id' => $refundInfo['approve_user_id']]);
                    $refundDes .= $refundReasons . '<br>审核时间:' . date("Y-m-d H:i:s", $refundInfo['check_time']) . '; ' . $manager . $action . '了退款申请; ' . $refundReason .
                        '<br> ----------';
                }
            }

        }

        return $refundDes;
    }

    /**
     * 获取订单退款信息
     * @author wxl
     * @date 2017-11-11
     * @param int $orderId 订单ID
     * @return array|int
     */
    private static function getOrderRefundInfo($orderId = 0)
    {

        return self::getBase('shop-api/get-refund-info', "&order_id=" . $orderId);
    }

}
