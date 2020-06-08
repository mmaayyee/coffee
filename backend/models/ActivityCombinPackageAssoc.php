<?php

namespace backend\models;

use common\models\Api;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "activity_combin_package_assoc".
 *
 * @property int $combin_package_id
 * @property int $is_refund 是否允许退款，0-不允许，1-允许
 * @property string $not_part_city  不参与机构
 * @property string $point_type 点位类型，0-普通，1-甄选，（存值方式：以逗号分隔成字符串）
 * @property string $product_information_json 单品信息（Json格式，[{‘num’:xx, ‘price’:xx, ‘is_real_prize’:0}, {‘num’:xx, ‘price’:xx, ‘is_real_prize’:1}], 最多9个梯度价格,单品数量，价格，是否实物 单品信息（Json格式，[{‘num’:xx, ‘price’:xx, ‘is_real_prize’:0}, {‘num’:xx, ‘price’:xx, ‘is_real_prize’:1}], 最多9个梯度价格,单品数量，价格，是否实物 单品信息（Json格式，[{‘num’:xx, ‘price’:xx, ‘is_real_prize’:0}, {‘num’:xx, ‘price’:xx, ‘is_real_prize’:1}], 最多9个梯度价格,单品数量，价格，是否实物
 * @property string $product_id_str 单品id字符串
 * @property int $order_user_num 下单用户总数
 * @property int $order_num 订单总数
 * @property int $sales_volume 销量
 * @property int $total_income 总收入
 * @property int $activity_id 基础活动id
 */
class ActivityCombinPackageAssoc extends \yii\db\ActiveRecord
{
    public $combin_package_id;
    public $is_refund;
    public $not_part_city;
    public $point_type;
    public $product_information_json;
    public $free_single_json;
    public $product_id_str;
    public $order_user_num;
    public $order_num;
    public $sales_volume;
    public $total_income;
    public $activity_id;
    public $banner_photo_url;
    public $product_name; // 获取的单品字符串

    // 活动表中字段
    public $activity_name;
    public $status;
    public $start_time;
    public $end_time;
    public $activity_url;
    public $created_at;
    public $createFrom;
    public $createTo;
    public $activity_type;
    public $free_delivery_cost;
    public static $activityType = [
        1 => '自提',
        2 => '外送',
    ];
    /** 梯度 自由单品优惠类型 */
    const PRICE_ORIGINAL  = 0; //原价
    const PRICE_DISCOUNT  = 1; //折扣
    const PRICE_FIXED     = 2; //固定价格
    const PRICE_REDUCTION = 3; //固定减价
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_refund', 'order_user_num', 'order_num', 'sales_volume', 'total_income', 'activity_id', 'status', 'created_at', 'activity_type'], 'integer'],
            [['product_id_str', 'activity_url'], 'string', 'max' => 255],
            [['point_type', 'activity_name', 'start_time', 'end_time', 'createFrom', 'createTo', 'banner_photo_url'], 'string', 'max' => 50],
            [['product_name', 'not_part_city'], 'string', 'max' => 500],
            [['product_information_json'], 'string', 'max' => 3000],
            [['free_single_json'], 'string', 'max' => 1000],
            [['free_delivery_cost'], 'double'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'combin_package_id'        => '自组合套餐活动ID',
            'is_refund'                => '是否允许退款',
            'not_part_city'            => '不参与机构',
            'point_type'               => '点位类型',
            'product_information_json' => '单品信息',
            'product_id_str'           => '单品id字符串',
            'order_user_num'           => '订单用户数',
            'order_num'                => '订单总数',
            'sales_volume'             => '销量',
            'total_income'             => '总收入',
            'activity_id'              => '活动id',
            'banner_photo_url'         => 'bnner图片',

            'activity_name'            => '活动名称',
            'status'                   => '活动状态',
            'start_time'               => '开始时间',
            'end_time'                 => '结束时间',
            'activity_url'             => '活动链接',
            'created_at'               => '创建时间',
            'createFrom'               => '开始查询时间',
            'createTo'                 => '结束查询时间',
            'activity_type'            => '活动类型',
            'free_delivery_cost'       => '免配送费金额',
        ];
    }

    // 退款数组
    public static $isRefund = [
        ''  => '请选择',
        '0' => '不允许退款',
        '1' => '允许退款',
    ];

    // 活动状态数组
    public static $statusList = [
        ''  => '请选择',
        '1' => '下线',
        '2' => '上线',
        '4' => '已过期',
    ];

    /**
     * 获取点位类型字符串
     * @author  zmy
     * @version 2018-03-27
     * @param   [Array]     $pointType [点位类型数组]
     * @return  [string]               [点位类型字符串]
     */
    public static function getPointType($pointType)
    {
        $pointTypeList = [];
        foreach ($pointType as $key => $value) {
            if ($value == 0) {
                $pointTypeList[] = '普通';
            }
            if ($value == 1) {
                $pointTypeList[] = '甄选';
            }
        }
        return implode(',', $pointTypeList);
    }

    /**
     * 获取单品梯度信息，组合成html中table格式进行返回。
     * @author  zmy
     * @version 2018-03-27
     * @param   [Array]     $productGradient [单品梯度自由套餐数组]
     * @return  [html]                       [table表]
     */
    public static function getProductInformationHtml($productGradient)
    {
        $table = '';
        if ($productGradient) {
            $table = "<table class='table'>";
            $tr    = "<tr><td>商品数量</td><td>优惠价格</td><td>是否给予实物奖励</td></tr>";
            foreach ($productGradient as $product) {
                $tr .= "<tr>
                            <td>" . $product['num'] . "个</td>
                            <td>" . $product['price'] . "元</td>
                            <td>" . ($product['is_real_prize'] == 0 ? '否' : '是') . "</td>
                        </tr>";
            }
            $table = $table . $tr . "</table>";
        }
        return $table;
    }

    /**
     * 获取单品梯度信息，组合成html中table格式进行返回。
     * @author  wangxiwen
     * @version 2018-09-03
     * @param   [Array] $freeSingle [单品梯度自由单品数组]
     * @return  [html]              [table表]
     */
    public static function getFreeSingleHtml($freeSingle)
    {
        $table = '';
        if ($freeSingle) {
            $table = "<table class='table'>";
            $tr    = "<tr><td>商品顺序</td><td>优惠类型</td><td>优惠价格</td></tr>";
            foreach ($freeSingle as $key => $single) {
                $tr .= "<tr>
                            <td>第" . ($key + 1) . "件</td>
                            <td>" . self::getFreeSingleType($single['type']) . "</td>
                            <td>" . self::getFreeSingleDiscount($single['type'], $single['price']) . "</td>
                        </tr>";
            }
            $table = $table . $tr . "</table>";
        }
        return $table;
    }

    /**
     * 获取自由单品类型
     * @author wangxiwen
     * @version 2018-09-03
     * @param  [type] $type [优惠类型]
     * @return [string]       [description]
     */
    private static function getFreeSingleType($type)
    {
        switch ($type) {
            case self::PRICE_DISCOUNT:
                $typeName = '折扣';
                break;
            case self::PRICE_FIXED:
                $typeName = '固定价格';
                break;
            case self::PRICE_REDUCTION:
                $typeName = '固定减价';
                break;
            default:
                $typeName = '原价';
                break;
        }
        return $typeName;
    }
    /**
     * 获取自由单品优惠金额
     * @author wangxiwen
     * @version 2018-09-03
     * @param  [int] $type [优惠类型]
     * @param  [int] $price[优惠金额]
     * @return [string]
     */
    private static function getFreeSingleDiscount($type, $price)
    {
        switch ($type) {
            case self::PRICE_DISCOUNT:
                $discount = $price . '%';
                break;
            case self::PRICE_FIXED:
                $discount = $price . '元';
                break;
            case self::PRICE_REDUCTION:
                $discount = $price . '元';
                break;
            default:
                $discount = '原价';
                break;
        }
        return $discount;
    }
    /**
     * 获取点位单品信息数据
     * @author  zmy
     * @version 2018-03-27
     * @return  [Array]     [点位单品信息数组]
     */
    public static function getPointProductList()
    {
        // 组合的最终点位单品数组
        $pointProductList = [];
        // 线上单品
        $onlineProduct = [];
        // 全部单品
        $allProduct = [];
        // 甄选单品
        $selectionProduct = [];
        $productList      = Json::decode(Api::getProductList());
        if ($productList) {
            $onlineProductI    = 0;
            $allProductI       = 0;
            $selectionProductI = 0;
            foreach ($productList as $key => $product) {
                if($product['cf_source_id'] != 0){
                    continue;
                }
                // 线上普通单品
                if ($product['cf_market_type'] == "1" && $product['cf_product_type'] == "0" && $product['cf_product_status'] == "0") {
                    $onlineProduct[$onlineProductI]['product_id']   = $product['cf_product_id'];
                    $onlineProduct[$onlineProductI]['product_name'] = $product['cf_product_name'];
                    $onlineProductI++;
                }
                // 下线普通单品
                if ($product['cf_market_type'] == "1" && $product['cf_product_type'] == "0" && $product['cf_product_status'] == "1") {
                    $allProduct[$allProductI]['product_id']   = $product['cf_product_id'];
                    $allProduct[$allProductI]['product_name'] = $product['cf_product_name'];
                    $allProductI++;
                }
                // 甄选单品
                if ($product['cf_market_type'] == "1" && $product['cf_product_type'] == '1') {
                    $selectionProduct[$selectionProductI]['product_id']   = $product['cf_product_id'];
                    $selectionProduct[$selectionProductI]['product_name'] = $product['cf_product_name'];
                    $selectionProductI++;
                }
            }
        }
        $pointProductList['online_product']    = $onlineProduct;
        $pointProductList['all_product']       = $allProduct;
        $pointProductList['selection_product'] = $selectionProduct;
        return ($pointProductList);
    }
}
