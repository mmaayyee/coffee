<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LightBeltStrategy;

/**
 * LightBeltStrategySearch represents the model behind the search form about `backend\models\LightBeltStrategy`.
 */
class LightBeltStrategySearch extends LightBeltStrategy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'total_length_time', 'light_belt_type', 'light_status'], 'integer'],
            [['strategy_name', 'light_belt_color'], 'safe'],
            [['flicker_frequency'], 'number'],
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
        $query = LightBeltStrategy::find();

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
            'id' => $this->id,
            'total_length_time' => $this->total_length_time,
            'light_belt_type' => $this->light_belt_type,
            'light_status' => $this->light_status,
            'flicker_frequency' => $this->flicker_frequency,
        ]);

        $query->andFilterWhere(['like', 'strategy_name', $this->strategy_name])
            ->andFilterWhere(['like', 'light_belt_color', $this->light_belt_color]);

        return $dataProvider;
    }
}
