<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "user_refund".
 *
 * @property integer $user_refund_id
 * @property integer $user_id
 * @property integer $refund_status
 * @property integer $order_id
 * @property integer $refund_reason_type
 * @property string $refund_reason_content
 * @property integer $created_at
 * @property integer $refund_at
 */
class UserRefund extends \yii\db\ActiveRecord
{

    /*
     * 退款状态-申请退款
     */
    const STATUS_APP = 0;
    // 主管通过
    const STATUS_DIRECTOR = 3;
    /*
     * 退款状态-退款
     */
    const STATUS_REFUND = 1;

    /*
     * 退款状态-拒绝退款
     */
    const STATUS_REFUSE = 2;

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
    public $fundMobile;

    /*
     * 申请人姓名
     */
    public $fundName;
    public $user_refund_id;
    public $user_id;
    public $refundPrice;
    public $refund_status;
    public $order_id;
    public $refund_type;
    public $refundMsg;
    public $refundCreatedTime;
    public $refundTime;
    public $refuse_content;
    public $refundBeansNum;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_refund';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['user_refund_id', 'refundStatus', 'refundStatus', 'order_id', 'refund_status', 'refund_type', 'refundMsg'
                , 'refundCreatedTime', 'refundTime', 'fundMobile', 'refundPrice', 'refundBeansNum']
                , 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_refund_id'    => '用户退款ID',
            'user_id'           => '用户ID',
            'refund_status'     => '退款状态',
            'order_id'          => '退款订单ID',
            'refund_type'       => '退款类型',
            'refundMsg'         => '用户退款原因',
            'refundCreatedTime' => '申请时间',
            'refundTime'        => '处理时间',
            'createdFrom'       => '开始日期',
            'createdTo'         => '截止日期',
            'fundMobile'        => '手机号',
            'fundName'          => '申请人',
            'refuse_content'    => '拒绝退款原因',
            'refundPrice'       => '退款金额',
            'refundBeansNum'    => '退回咖豆数量',
        ];
    }

    /**
     * 获取退款记录列表数据
     * @Author  : GaoYongLi
     * @DateTime: 2018/5/31
     * @param $params
     * @return array
     */
    public static function getUserRefundList($params)
    {
        $page           = isset($params['page']) ? $params['page'] : 0;
        $userRefundList = self::postBase("user-refund-api/get-user-refund-list", $params, '?page=' . $page);
        return !$userRefundList ? [] : Json::decode($userRefundList);
    }
    /**
     * 获取退款状态
     * @return string 退款状态
     */
    public function getStatus()
    {
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->refund_status];
    }
    /**
     * 获取退款状态数组
     * @return array 退款状态数组
     */
    public function getStatusArray()
    {
        return array(
            ''                    => '请选择',
            self::STATUS_APP      => '申请退款',
            self::STATUS_REFUSE   => '拒绝退款',
            self::STATUS_REFUND   => '已退款',
            self::STATUS_DIRECTOR => '主管通过',
        );
    }
    /**
     * 获取退款类型数组
     * @return array 退款类型数组
     */
    public function getRefundTypeArray()
    {
        return array(
            '' => '请选择',
            0  => '自动退款',
            1  => '手动退款',
        );
    }
    public static function postBase($action, $data = [], $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
