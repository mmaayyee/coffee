<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LightBeltScenario;

/**
 * LightBeltScenarioSearch represents the model behind the search form about `backend\models\LightBeltScenario`.
 */
class LightBeltScenarioSearch extends LightBeltScenario
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'start_time', 'end_time'], 'integer'],
            [['scenario_name', 'product_group_name', 'strategy_name', 'equip_scenario_name'], 'safe'],
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
        $query = LightBeltScenario::find();

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
            'equip_scenario_name' => $this->equip_scenario_name,
            'product_group_name' => $this->product_group_name,
            'strategy_name' => $this->strategy_name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $query->andFilterWhere(['like', 'scenario_name', $this->scenario_name]);

        return $dataProvider;
    }
}
