<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProductGroupStockInfo;

/**
 * ProductGroupStockInfoSearch represents the model behind the search form about `backend\models\ProductGroupStockInfo`.
 */
class ProductGroupStockInfoSearch extends ProductGroupStockInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id'], 'integer'],
            [['product_group_stock_name'], 'safe'],
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
        $query = ProductGroupStockInfo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'equip_type_id' => $this->equip_type_id,
        ]);
        
        $query->andFilterWhere(['like', 'product_group_stock_name', $this->product_group_stock_name]);

        return $dataProvider;
    }
}
