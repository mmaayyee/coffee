<?php

namespace backend\models;

use backend\models\CoffeeProduct;
use common\models\ArrayDataProviderSelf;
use common\models\CoffeeProductApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CoffeeProductSearch represents the model behind the search form about `app\models\CoffeeProduct`.
 */
class CoffeeProductSearch extends CoffeeProduct
{
    public $old_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cf_product_id', 'cf_product_status', 'cf_product_hot', 'cf_product_type'], 'integer'],
            [['cf_product_name', 'cf_product_thumbnail', 'cf_texture', 'cf_source_id', 'old_name', 'equipment_type'], 'safe'],
            [['cf_product_price', 'cf_market_type'], 'number'],
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
        $proList      = CoffeeProductApi::getCoffeeProductList($params);
        $dataProvider = [];
        if ($proList) {
            foreach ($proList['cofProductList'] as $key => $data) {
                $cofeeProduct = new CoffeeProduct();
                $cofeeProduct->load(['CoffeeProduct' => $data]);
                $dataProvider[$data['cf_product_id']] = $cofeeProduct;
            }
        }
        $coffeeProList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($proList['total']) ? 0 : $proList['total'],
            'sort'       => [
                'attributes' => ['cf_product_id desc'],
            ],
        ]);
        return $coffeeProList;
    }

}
