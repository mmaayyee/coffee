<?php

namespace backend\models;

use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderInfoSearch represents the model behind the search form of `backend\models\OrderInfo`.
 */
class OrderInfoSearch extends OrderInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'pay_type', 'order_status', 'order_type', 'order_cups', 'created_at', 'pay_at', 'paid', 'changes', 'is_company', 'order_version', 'beans_num', 'is_refunds'], 'integer'],
            [['total_fee', 'actual_fee', 'gift_fee', 'discount_fee', 'beans_amount'], 'number'],
            [['order_code', 'equipment_code', 'coupon_name', 'coupon_real_value', 'source_price', 'user_mobile', 'payFrom', 'payTo',
                'createdFrom', 'createdTo', 'source_price_discount', 'realPrice', 'totalCups', 'averageCup', 'count', 'discount_fee', 'source_type', 'delivery_cost'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $orderInfoList = OrderInfo::getOrderInfoList($params);
        $dataProvider  = [];
        if (isset($orderInfoList['orderInfoList'])) {
            foreach ($orderInfoList['orderInfoList'] as $key => $orderInfo) {
                $proGroup = new OrderInfo();
                $proGroup->load(['OrderInfo' => $orderInfo]);
                $dataProvider[$orderInfo['order_id']] = $proGroup;
            }
        }
        $realPrice     = isset($orderInfoList['realPrice']) ? $orderInfoList['realPrice'] : '';
        $totalCups     = isset($orderInfoList['totalCups']) ? $orderInfoList['totalCups'] : '';
        $averageCup    = isset($orderInfoList['averageCup']) ? $orderInfoList['averageCup'] : '';
        $count         = isset($orderInfoList['count']) ? $orderInfoList['count'] : '';
        $orderInfoList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($orderInfoList['total']) ? 0 : $orderInfoList['total'],
        ]);
        $data = [
            'orderInfoList' => $orderInfoList,
            'realPrice'     => $realPrice,
            'totalCups'     => $totalCups,
            'averageCup'    => $averageCup,
            'count'         => $count,
        ];
        return $data;
    }
    /**
     * 支付信息汇总方法
     * @Author  : GaoYongLi
     * @DateTime: 2018/6/4
     * @param $params
     * @return array|mixed
     */
    public function paySearch($params)
    {
        $this->load($params);
        return OrderInfo::getPaymentInfo($params);
    }
}
