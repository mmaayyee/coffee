<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CoffeeProductSetup;

/**
 * CoffeeProductSetupSearch represents the model behind the search form about `backend\models\CoffeeProductSetup`.
 */
class CoffeeProductSetupSearch extends CoffeeProductSetup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['setup_id', 'product_id', 'equip_type_id', 'order_number', 'blanking', 'mixing'], 'integer'],
            [['water', 'delay', 'volume', 'stir'], 'number'],
            [['stock_code'], 'safe'],
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
        $query = CoffeeProductSetup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'setup_id' => $this->setup_id,
            'product_id' => $this->product_id,
            'equip_type_id' => $this->equip_type_id,
            'order_number' => $this->order_number,
            'water' => $this->water,
            'delay' => $this->delay,
            'volume' => $this->volume,
            'stir' => $this->stir,
            'blanking' => $this->blanking,
            'mixing' => $this->mixing,
        ]);

        $query->andFilterWhere(['like', 'stock_code', $this->stock_code]);

        return $dataProvider;
    }
}
