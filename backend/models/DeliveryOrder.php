<?php

namespace backend\models;

use yii\base\Model;

/**
 * 外卖订单
 * This is the model class for table "delivery_order".
 *
 * @property string $id
 * @property string $delivery_order_code 订单编号
 * @property int $order_id 关联系统订单id
 * @property int $user_id 关联用户id
 * @property int $address_id 收货地址id
 * @property int $build_id 外卖订单购买楼宇
 * @property int $delivery_order_status 外卖订单状态
 * @property int $fail_reason_id 失败原因id
 * @property int $delivery_person_id 配送人员id
 * @property int $actual_service_time 实际送达时间
 */
class DeliveryOrder extends \yii\db\ActiveRecord
{
    //定义外卖状态常量
    const ORDER_STATUS_WAIT_PAY    = 1; //待支付
    const ORDER_STATUS_WAIT_PICK   = 2; //待接单
    const ORDER_STATUS_PICK        = 3; //已接单
    const ORDER_STATUS_MAKE        = 4; //制作中
    const ORDER_STATUS_DISTR       = 5; //配送中
    const ORDER_STATUS_DELI_FINISH = 6; //已送达
    const ORDER_STATUS_WAIT_EVAL   = 7; //待评价(未用)
    const ORDER_STATUS_COMP        = 8; //已完成
    const ORDER_STATUS_SHUT        = 9; //订单失效

    //定义订单失败原因(对应 存表delivery_order_fail_reason id=1,2数据)
    const ORDER_FAIL_TIME_OUT    = 1; //超时支付
    const ORDER_FAIL_USER_CANCLE = 2; //用户取消

    public $delivery_order_id;
    public $delivery_region_id;
    public $delivery_order_code;
    public $order_id;
    public $user_id;
    public $address_id;
    public $build_id;
    public $delivery_order_status = [];
    public $delivery_order_status_name;
    public $fail_reason_id;
    public $delivery_person_id;
    public $expect_service_time;
    //下单用户微信昵称
    public $nickname;
    //收货人姓名
    public $receiver;
    //收货人地址
    public $address;
    //收货人电话
    public $phone;
    //外卖订单
    public $sequence_number;
    //创建时间
    public $deliveryOrderLogs = [];
    //支付配送费用
    public $delivery_cost;
    //配送员列表
    public $person = [
        0 => '未接单',
    ];
    public $building_list = [];
    public $diachronic;
//    public $diachronic_list = [
//        1 => '半小时内',
//        2 => '半小时至一小时',
//        3 => '一小时以上',
//    ];
    public static $select_sugar = [
        1=>'无糖',
        2=>'半糖',
        3=>'全糖',
    ];

    public $product_num;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_order_id', 'order_id', 'user_id', 'address_id', 'build_id', 'delivery_order_status', 'fail_reason_id', 'delivery_person_id', 'expect_service_time', 'diachronic'], 'integer'],
            [['delivery_order_code', 'delivery_region_id', 'nickname', 'receiver', 'address', 'phone', 'create_time','delivery_person_id','delivery_cost','building_list','diachronic_list', 'product_num', 'sequence_number'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_order_id'     => 'ID',
            'sequence_number'       => '外卖订单',
            'delivery_order_code'   => '外卖订单编号',
            'order_id'              => '系统订单id',
            'user_id'               => '用户id',
            'address_id'            => '地址id',
            'build_id'              => '楼宇id',
            'delivery_order_status' => '外卖订单状态',
            'fail_reason_id'        => '失败原因id',
            'delivery_person_id'    => '外卖配送人员',
            'expect_service_time'   => '预计送达时间',
            'phone'                 => '收货电话',
            'address'               => '收货地址',
            'nickname'              => '微信昵称',
            'receiver'              => '收货人',
            'delivery_cost'         => '配送费用',
            'diachronic'            => '全单历时大于',
        ];
    }

    /**
     * 获取订单系列号
     * @author jiangfeng
     * @version 2018/11/19
     * @param $number
     * @return string
     */
    public static function getOrderSequenceNumber($number)
    {
        return str_pad(substr($number, -4),4,0,STR_PAD_LEFT);
    }
    /**
     * 订单状态列表
     */
    public static function deliveryOrderStatus()
    {
        return [
//            ''                             => '请选择',
            self::ORDER_STATUS_WAIT_PAY    => '待支付',
            self::ORDER_STATUS_WAIT_PICK   => '待接单',
            self::ORDER_STATUS_PICK        => '已接单',
            self::ORDER_STATUS_MAKE        => '制作中',
            self::ORDER_STATUS_DISTR       => '配送中',
//            self::ORDER_STATUS_DELI_FINISH => '已送达',
//            self::ORDER_STATUS_WAIT_EVAL   => '待评价',
            self::ORDER_STATUS_COMP        => '已完成',
            self::ORDER_STATUS_SHUT        => '订单失效',
        ];
    }
    /**
     * 获取当前外卖订单状态
     * @param [string]  $orderStatus 当前订单状态
     */
    public static function getDeliveryOrderStatus($orderStatus = self::ORDER_STATUS_WAIT_PAY)
    {
        //获取当前列表
        $orderStatusList = self::deliveryOrderStatus();
        if ($orderStatus && isset($orderStatusList[$orderStatus])) {
            return $orderStatusList[$orderStatus];
        }
        return '';
    }

    /**
     * 返回两个时间的相距时间，*年*月*日*时*分*秒
     * @param int $one_time 时间一
     * @param int $two_time 时间二
     * @param int $return_type 默认值为0，0/不为0则拼接返回，1/*秒，2/*分*秒，3/*时*分*秒/，4/*日*时*分*秒，5/*月*日*时*分*秒，6/*年*月*日*时*分*秒
     * @param array $format_array 格式化字符，例，array('年', '月', '日', '时', '分', '秒')
     * @return String or false
     */
    function getRemainderTime($one_time, $two_time, $return_type=0, $format_array=array('年', '月', '天', '小时', '分钟', '秒'))
    {
        if ($return_type < 0 || $return_type > 6) {
            return false;
        }
        if (!(is_numeric($one_time) && is_numeric($two_time))) {
            return false;
        }
        $remainder_seconds = abs($one_time - $two_time);
        //年
        $years = 0;
        if (($return_type == 0 || $return_type == 6) && $remainder_seconds - 31536000 > 0) {
            $years = floor($remainder_seconds / (31536000));
        }
        //月
        $monthes = 0;
        if (($return_type == 0 || $return_type >= 5) && $remainder_seconds - $years * 31536000 - 2592000 > 0) {
            $monthes = floor(($remainder_seconds - $years * 31536000) / (2592000));
        }
        //日
        $days = 0;
        if (($return_type == 0 || $return_type >= 4) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - 86400 > 0) {
            $days = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000) / (86400));
        }
        //时
        $hours = 0;
        if (($return_type == 0 || $return_type >= 3) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - 3600 > 0) {
            $hours = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400) / 3600);
        }
        //分
        $minutes = 0;
        if (($return_type == 0 || $return_type >= 2) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - 60 > 0) {
            $minutes = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600) / 60);
        }
        //秒
        $seconds = $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - $minutes * 60;
        $return = false;
        switch ($return_type) {
            case 0:
                if ($years > 0) {
                    $return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($monthes > 0) {
                    $return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($days > 0) {
                    $return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($hours > 0) {
                    $return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($minutes > 0) {
                    $return = $minutes . $format_array[4] . $seconds . $format_array[5];
                } else {
                    $return = $seconds . $format_array[5];
                }
                break;
            case 1:
                $return = $seconds . $format_array[5];
                break;
            case 2:
                $return = $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 3:
                $return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 4:
                $return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 5:
                $return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 6:
                $return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            default:
                $return = false;
        }
        return $return;
    }
}
