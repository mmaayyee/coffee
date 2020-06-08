<?php

namespace backend\modules\service\models;

use backend\modules\service\helpers\Api;
use Yii;
use yii\helpers\Url;

class Complaint extends \yii\base\Model
{
    /** 客户区分 0-客户 1-联营方 */
    const CUSTOMER_USER             = 0;
    const CUSTOMER_PARTNER          = 1;
    public static $customerTypeList = [
        self::CUSTOMER_USER    => '客户',
        self::CUSTOMER_PARTNER => '联营方',
    ];

    public $complaint_id; //客诉id
    public $manager_id; // 添加该客诉的后台用户ID
    public $manager_name; // 记录该客诉的后台用户名
    public $org_id; // 机构id
    public $org_city; // 机构城市
    public $advisory_type_id; // 咨询类型id
    public $question_type_id; // 问题类型id
    public $question_describe; // 问题描述
    public $building_name; // 点位名称
    public $building_id; // 点位id
    public $equipment_last_log; // 该点位对应设备的最后一条日志
    public $equipment_type; // 设备类型名称
    public $customer_name; // 客户名称
    public $register_mobile; // 用户注册手机号
    public $callin_mobile; // 用户打入手机号
    public $user_id; // 注册用户ID
    public $nikename; // 用户昵称
    public $pay_type; // 支付方式
    public $buy_type; // 购买品种
    public $buy_time; // 购买时间
    public $solution_id; // 解决方案id
    public $retired_coffee_type; // 退款咖啡品种
    public $order_refund_price; // 退款金额
    public $order_code; // 订单编号
    public $latest_refund_time; // 最迟退款日期
    public $real_refund_time; // 实际退款日期
    public $process_status; // 处理状态.0未处理，1已处理
    public $is_consumption; // 是否消费。0未消费，1已消费
    public $add_time; // 添加日期
    public $update_time; // 更新日期
    public $user_consume_id; // 消费记录ID
    public $begin_time; // 查询开始时间
    public $end_time; // 查询结束时间
    public $complaint_code; // 客诉编号
    public $customer_type; //客户区分 0-客户 1-联营方
    public $complete_time; //处理完成时间

    public static function loadingData($conplaintInfo)
    {
        $conplaintModel = new self();
        $conplaintModel->load(['Complaint' => $conplaintInfo]);
        $conplaintModel->buy_time           = empty($conplaintModel->buy_time) ? "" : date('Y-m-d H:i:s', $conplaintModel->buy_time);
        $conplaintModel->latest_refund_time = empty($conplaintModel->latest_refund_time) ? "" : date('Y-m-d H:i:s', $conplaintModel->latest_refund_time);
        $conplaintModel->real_refund_time   = empty($conplaintModel->real_refund_time) ? "" : date('Y-m-d H:i:s', $conplaintModel->real_refund_time);
        $conplaintModel->order_code         = empty($conplaintModel->order_code) ? "" : $conplaintModel->order_code;
        return $conplaintModel;
    }

    /**
     * 获取客诉全部列表
     * @param $complaintInfo
     * @return mixed
     */
    public static function getComplaint($complaintInfo)
    {
        $page            = empty($complaintInfo['page']) ? 0 : $complaintInfo['page'];
        $complaintSearch = !empty($complaintInfo['ComplaintSearch']) ? $complaintInfo['ComplaintSearch'] : [];
        $complaintList   = Api::getComplaintList($complaintSearch, $page);
        return $complaintList['data'];
    }
    /**
     *  导出客服列表全部数据
     * @param $params
     * @return array|mixed
     */
    public static function exportComplaint($complaintInfo)
    {
        $complaintSearch = !empty($complaintInfo['ComplaintSearch']) ? $complaintInfo['ComplaintSearch'] : [];
        $complaintList   = Api::exportComplaintList($complaintSearch);
        return $complaintList['data'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_describe', 'advisory_type_id', 'question_type_id', 'process_status'], 'required'],
            [['manager_id', 'org_id', 'user_id', 'pay_type', 'solution_id', 'process_status', 'is_consumption', 'add_time', 'update_time'], 'integer'],
            [['order_refund_price', 'customer_type'], 'number'],
            [['manager_name'], 'string', 'max' => 16],
            [['org_city', 'equipment_type', 'nikename', 'buy_time', 'latest_refund_time', 'real_refund_time'], 'string', 'max' => 50],
            [['question_describe'], 'string', 'max' => 500],
            [['order_code'], 'string', 'max' => 510],
            [['building_name'], 'string', 'max' => 30],
            [['equipment_last_log'], 'string', 'max' => 200],
            [['customer_name', 'register_mobile', 'callin_mobile'], 'string', 'max' => 32],
            [['buy_type', 'retired_coffee_type'], 'string', 'max' => 255],
            [['user_consume_id'], 'default', 'value' => 0],
            [['complaint_id', 'complaint_code', 'end_time', 'begin_time'], 'safe'],
            ['callin_mobile', 'filter', 'filter' => 'trim'],
            ['callin_mobile', 'match', 'pattern' => '/^(0?(13[0-9]|15[012356789]|17[013678]|18[0-9]|14[57])[0-9]{8})|(400|800)([0-9\\-]{7,10})|(([0-9]{4}|[0-9]{3})(-| )?)?([0-9]{7,8})((-| |转)*([0-9]{1,4}))?$/'],
            ['register_mobile', 'filter', 'filter' => 'trim'],
            [['building_id', 'complete_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'complaint_id'        => '客诉ID',
            'manager_id'          => '客服ID',
            'manager_name'        => '工号',
            'org_id'              => '机构名称',
            'org_city'            => '所在城市',
            'advisory_type_id'    => '咨询类型',
            'question_type_id'    => '问题类型',
            'question_describe'   => '问题描述',
            'building_name'       => '点位名称',
            'building_id'         => '楼宇ID',
            'equipment_last_log'  => '设备最新日志',
            'equipment_type'      => '设备类型',
            'customer_name'       => '客户名称',
            'register_mobile'     => '注册电话',
            'callin_mobile'       => '来电号码',
            'user_id'             => '用户ID',
            'nikename'            => '用户昵称',
            'pay_type'            => '支付方式',
            'buy_type'            => '购买品种',
            'buy_time'            => '购买时间',
            'solution_id'         => '解决方案id',
            'retired_coffee_type' => '退款咖啡品种',
            'order_refund_price'  => '退款金额',
            'order_code'          => '订单编号',
            'latest_refund_time'  => '最迟退款日期',
            'real_refund_time'    => '实际退款日期',
            'process_status'      => '处理状态',
            'is_consumption'      => '是否消费',
            'add_time'            => '添加日期',
            'update_time'         => '更新日期',
            'user_consume_id'     => '消费记录ID',
            'begin_time'          => '开始时间',
            'end_time'            => '结束时间',
            'complaint_code'      => '客诉编号',
        ];
    }
    public static function getProcessStatusList()
    {
        return [
            1 => '已解决',
            0 => '未解决',
        ];
    }

    /**
     *  获取客服系统里面订单编号的前三位
     * @author sulingling
     * @dateTime 2018-11-23
     * @version  [version]
     * @return   string     [description]
     */
    public function getOrderCode()
    {
        $codeArr   = explode(',', $this->order_code);
        $orderCode = '';
        foreach ($codeArr as $number => $code) {
            if (empty($code)) {
                continue;
            }
            if ($number == 3) {
                $orderCode = $orderCode . "......";
            }
            if ($number < 3) {
                $orderCode .= "<a href='" . Url::to(['/order-info/view', 'id' => 0, 'order_code' => $code]) . "'>" . $code . "</a>,";
            }

        }
        return $orderCode;
    }
}
