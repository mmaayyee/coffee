<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ArrayDataProviderSelf;
use backend\models\OrderGoodsCount;

/**
 * OrderGoodsCountSearch represents the model behind the search form of `common\models\OrderGoodsCount`.
 */
class OrderGoodsCountSearch extends OrderGoodsCount
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'today_pay_total', 'today_consume_total', 'no_consume_total', 'created_at'], 'integer'],
            [['createdFrom','createdTo','today_refund_total'], 'safe'],
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
        $orderGoodsCountList = OrderGoodsCount::getOrderGoodsCountList($params);

        $dataProvider  = [];
        if (isset($orderGoodsCountList['orderGoodsCountList'])) {
            foreach ($orderGoodsCountList['orderGoodsCountList'] as $key => $countList) {
                $proGroup = new OrderGoodsCount();
                $proGroup->load(['OrderGoodsCount' => $countList]);
                $dataProvider[$countList['id']] = $proGroup;
            }
        }
        $orderGoodsCountList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($orderGoodsCountList['total']) ? 0 : $orderGoodsCountList['total'],
        ]);
        return $orderGoodsCountList;
    }
}
