<?php

namespace backend\models;

use backend\models\OrderGoods;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderGoodsSearch represents the model behind the search form of `backend\models\OrderGoods`.
 */
class OrderGoodsSearch extends OrderGoods
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'order_id', 'user_id', 'source_status', 'source_id', 'source_number', 'created_at', 'goods_type', 'source_type', 'goods_source_type'], 'integer'],
            [['source_price', 'original_price'], 'number'],
            [['source_name', 'userMobile', 'actual_pay', 'source_price_discount', 'createdFrom', 'createdTo'], 'safe'],
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
        $orderGoodsList = OrderGoods::getOrderGoodsList($params);
        $dataProvider   = [];
        if (isset($orderGoodsList['orderGoodsList'])) {
            foreach ($orderGoodsList['orderGoodsList'] as $key => $orderGoods) {
                $proGroup = new OrderGoods();
                $proGroup->load(['OrderGoods' => $orderGoods]);
                $dataProvider[$orderGoods['goods_id']] = $proGroup;
            }
        }
        $orderGoodsDataSummary = [
            'totalNumber'       => isset($orderGoodsList['totalNumber']) ? $orderGoodsList['totalNumber'] : '',
            'totalFee'          => isset($orderGoodsList['totalFee']) ? $orderGoodsList['totalFee'] : '',
            'sourceID'          => isset($orderGoodsList['sourceID']) ? $orderGoodsList['sourceID'] : '',
            'sourceType'        => isset($orderGoodsList['sourceType']) ? $orderGoodsList['sourceType'] : '',
            'productList'       => isset($orderGoodsList['productList']) ? $orderGoodsList['productList'] : '',
            'groupList'         => isset($orderGoodsList['groupList']) ? $orderGoodsList['groupList'] : '',
            'productActiveList' => isset($orderGoodsList['productActiveList']) ? $orderGoodsList['productActiveList'] : '',
            'groupActiveList'   => isset($orderGoodsList['groupActiveList']) ? $orderGoodsList['groupActiveList'] : '',
        ];
        $orderGoodsList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($orderGoodsList['total']) ? 0 : $orderGoodsList['total'],
        ]);
        $data = [
            'orderGoodsList'        => $orderGoodsList,
            'orderGoodsDataSummary' => $orderGoodsDataSummary,
        ];
        return $data;
    }
}
