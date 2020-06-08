<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LightBeltSolutionScenarioAssoc;

/**
 * LightBeltSolutionScenarioAssocSearch represents the model behind the search form about `backend\models\LightBeltSolutionScenarioAssoc`.
 */
class LightBeltSolutionScenarioAssocSearch extends LightBeltSolutionScenarioAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'program_id', 'scenario_id', 'is_default', 'default_strategy_id'], 'integer'],
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
        $query = LightBeltSolutionScenarioAssoc::find();

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
            'program_id' => $this->program_id,
            'scenario_id' => $this->scenario_id,
            'is_default' => $this->is_default,
            'default_strategy_id' => $this->default_strategy_id,
        ]);

        return $dataProvider;
    }
}
