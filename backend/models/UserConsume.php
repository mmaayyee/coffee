<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "user_consume".
 *
 * @property int $id
 */
class UserConsume extends \yii\db\ActiveRecord
{
    /** 是否退还 0-否 1-是 */
    const REFUND_NO  = 0;
    const REFUND_YES = 1;

    /**
     * 是否退还列表
     * @var array
     */
    public $isRefundList = [
        ''               => '请选择',
        self::REFUND_NO  => '已消费',
        self::REFUND_YES => '已退还',
    ];
    /*
     * 申请起始日期
     */
    public $createdFrom;

    /*
     * 申请截止日期
     */
    public $createdTo;

    /*
     * 开始时间
     */
    public $createdTimeFrom;

    /*
     * 截止时间
     */
    public $createdTimeTo;

    /*
     * 申请人手机号
     */
    public $userMobile;

    /*
     * 申请人姓名
     */
    public $userName;

    /*
     * 所在楼宇
     */
    public $building;

    /*
     * 所在分公司
     */
    public $branch;

    /*
     * 是否真实付费
     */
    public $isFee;

    public $orgId; //所属分公司(检索)

    public $orgType; //机构类型
    /**
     * 兑换券名称
     * @var [type]
     */
    public $couponName;

    public $couponAmount;
    public $user_consume_id;
    public $order_id;
    public $user_id;
    public $product_id;
    public $actual_fee;
    public $coupon_value;
    public $fetch_time;
    public $static;
    public $source_price;
    public $equipment_code;
    public $product_number;
    public $beans_num;
    public $beans_amount;
    public $user_type;
    public $product_type;
    public $equipment;
    public $equipment_static;
    public $total_taxable;
    public $build_number;
    public $consume_type;
    public $realPrice;
    public $user_consume_sugar;
    public $consume_amount;
    public $delivery_cost;
    public $is_refund;
    public $refund_time;
    public $refundFrom, $refundTo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_consume';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_consume_id', 'couponName', 'order_id', 'userMobile', 'product_id', 'actual_fee', 'coupon_value', 'building', 'fetch_time', 'static', 'source_price', 'equipment_code', 'isFee', 'product_number', 'beans_num', 'beans_amount', 'user_type', 'product_type', 'equipment', 'equipment_static', 'total_taxable', 'user_id', 'build_number', 'consume_type', 'realPrice', 'user_consume_sugar', 'consume_amount', 'delivery_cost', 'orgId', 'orgType', 'is_refund', 'refund_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_consume_id'    => '消费ID',
            'goods_id'           => '商品ID',
            'order_id'           => '订单号',
            'user_id'            => '用户ID',
            'product_id'         => '产品',
            'fetch_time'         => '领取时间',
            'equipment_code'     => '设备编号',
            'createdFrom'        => '消费开始日期',
            'createdTo'          => '消费截止日期',
            'userMobile'         => '用户号码',
            'userName'           => '申请人',
            'building'           => '点位名称',
            'build_number'       => '点位编号',
            'product_number'     => '消费数量',
            'createdTimeFrom'    => '开始时间',
            'createdTimeTo'      => '截止时间',
            'branch'             => '分公司',
            'isFee'              => '是否付费',
            'consume_type'       => '领取方式',
            'beans_num'          => '咖豆数量',
            'beans_amount'       => '咖豆价值',
            'actual_fee'         => '付款金额',
            'couponName'         => '兑换券名称',
            'source_price'       => '产品价格',
            'user_type'          => '用户类型',
            'product_type'       => '产品类型',
            'equipment'          => '分公司',
            'equipment_static'   => '运营状态',
            'total_taxable'      => '应税',
            'user_consume_sugar' => '消费糖量',
            'delivery_cost'      => '配送费',
            'orgId'              => '所属分公司',
            'orgType'            => '机构类型',
            'is_refund'          => '状态',
            'refund_time'        => '退还时间',
            'refundFrom'         => '退还开始日期',
            'refundTo'           => '退还结束日期',
        ];
    }

    /**
     * 获取支付方式数组
     * @return array 支付方式数组
     */
    public function getFeetypeArray()
    {
        return [
            ''  => '请选择',
            '0' => '免费',
            '1' => '付费',
        ];
    }

    public function getExchangeCouponIDName()
    {
        $couponName = self::getBase("user-consumes-api/get-exchange-coupon-id-name");
        return !$couponName ? [] : Json::decode($couponName);
    }

    /**
     * 获取消费记录列表
     * @Author  : GaoYongLi
     * @DateTime: 2018/6/1
     */
    public static function getUserConsumesList($params)
    {
        $orgID            = Manager::getManagerBranchID();
        $params['branch'] = $orgID; // 分公司ID
        $page             = isset($params['page']) ? $params['page'] : 0;
        $UserconsumesList = self::postBase("user-consumes-api/get-user-consume-list", $params, '?page=' . $page);
        return !$UserconsumesList ? [] : Json::decode($UserconsumesList);
    }

    /**
     * 导出专用方法
     * @Author   GaoYongli
     * @DateTime 2018-06-08
     * @param    [param]
     * @param    [type]     $params [description]
     */
    public static function ExportUserConsumeList($params)
    {
        $orgID            = Manager::getManagerBranchID();
        $params['branch'] = $orgID; // 分公司ID
        $page             = isset($params['page']) ? $params['page'] : 0;
        $UserconsumesList = self::postBase("/user-consumes-api/export-user-consume-list", $params, '?page=' . $page);
        return !$UserconsumesList ? [] : Json::decode($UserconsumesList);
    }

    /**
     *
     * @Author   GaoYongli
     * @DateTime 2018-06-02
     * @param    [param]
     * @param    [type]     $UserConsumeID [description]
     * @return   [type]                    [description]
     */
    public static function getConsumptionInformation($UserConsumeID)
    {
        $Consume = self::getBase("user-consumes-api/get-consumption-information", '?id=' . $UserConsumeID);
        return !$Consume ? [] : Json::decode($Consume);
    }
    /**
     * 获取单品名字
     * @Author  : GaoYongLi
     * @DateTime: 2018/6/1
     * @return array|mixed
     */
    public function getAllProductName()
    {
        $productNameList = self::getBase("user-consumes-api/get-product-name");
        return !$productNameList ? [] : Json::decode($productNameList);
    }

    /**
     * 消费记录导出
     * @Author   GaoYongli
     * @DateTime 2018-06-08
     * @param    [param]
     * @param    string $filename [description]
     * @return   [type]               [description]
     */
    public static function getUserConsumesexport($filename = 'consume-')
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '4096M');
        $params = Yii::$app->request->queryParams;
        if (empty($params["UserConsumeSearch"]["createdFrom"]) ||
            empty($params["UserConsumeSearch"]["createdTo"]) ||
            substr($params["UserConsumeSearch"]["createdFrom"], 0, 10) != substr($params["UserConsumeSearch"]["createdTo"], 0, 10)) {
            echo "please input the same start and end date!";
            exit;
        }
        $bodyLine = "订单号|点位编号|设备编号|点位名称|一级机构|二级机构|商品名称|销售单价|消费数量|优惠价格|付款金额|咖豆数量|咖豆价值|配送费|";
        if ($filename == 'agent-consume-') {
            $bodyLine .= "优惠券价值|实际支付（含咖豆价值和优惠券价值）|";
        } else {
            $bodyLine .= "实际支付（含咖豆价值和优惠券价值）|";
        }
        $bodyLine .= "消费日期|消费时间|产品类型|手机号|用户类型|楼宇运营状态|支付方式|兑换券名称（纯用兑换券)|状态|退还时间";
        $bodyLine .= "\n";
        $goodsList = self::ExportUserConsumeList(Yii::$app->request->queryParams);
        foreach ($goodsList['consumeRecordsList'] as $goods) {
            $bodyLine .= $goods['order_id'] . "|";
            $bodyLine .= $goods['build_number'] . "|";
            $bodyLine .= $goods['equipment_code'] . "|";
            $bodyLine .= $goods['building'] . "|";
            $bodyLine .= $goods['one'] . "|";
            $bodyLine .= $goods['two'] . "|";
            $bodyLine .= $goods['product_id'] . "|";
            $bodyLine .= $goods['source_price'] . "|";
            $bodyLine .= $goods['product_number'] . "|";
            $bodyLine .= $goods['discount_price'] . "|";
            $bodyLine .= $goods['actual_fee'] . "|";
            $bodyLine .= $goods['beans_num'] . "|";
            $bodyLine .= $goods['beans_value'] . "|";
            $bodyLine .= $goods['delivery_cost'] . "|";
            if ($filename == 'agent-consume-') {
                $bodyLine .= $goods['coupon_value'] . "|";
                $bodyLine .= $goods['real_price'] . "|";
            } else {
                $bodyLine .= $goods['real_price'] . "|";
            }
            $bodyLine .= $goods['fetch_date'] . "|";
            $bodyLine .= $goods['fetch_time'] . "|";
            $bodyLine .= self::getProductType($goods['source_type']) . "|";
            $bodyLine .= $goods['userMobile'] . "|";
            $bodyLine .= $goods['user_id'] . "|";
            $bodyLine .= $goods['equipment_static'] . "|";
            $bodyLine .= $goods['pay_type'] . "|";
            $bodyLine .= $goods['couponName'] . "|";
            $bodyLine .= $goods['is_refund'] . "|";
            $bodyLine .= $goods['refund_time'] . "\n";
        }
        header('Content-type: text/plain');
        //下载显示的名字
        $fileName = $filename . date("Y-m-d") . ".txt";
        header('Content-Disposition: attachment; filename=' . $fileName);
        echo $bodyLine;
    }

    public static function getProductType($type)
    {
        //产品类型0单品,1套餐,2红利套餐,3单品活动,4套餐活动',
        $array = [
            '0' => '单品',
            '1' => '套餐',
            '2' => '套餐',
            '3' => '单品活动',
            '4' => '套餐',
            '5' => '自组合',
            '6' => '拼团',
            '7' => '轻食单品',
            '8' => '轻食套餐',
        ];
        return $array[$type];
    }
    public static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data);die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }

    /**
     * 更新消费记录退还状态
     * @author zhenggangwei
     * @date   2019-07-26
     * @param  integer     $consumeId 消费记录ID
     * @return boolen               true-成功 false-失败
     */
    public static function updateRefundStatus($consumeId)
    {
        $consume = self::getBase("user-consumes-api/update-refund-status", '?consumeId=' . $consumeId);
        return $consume;
    }
}
