<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MaterialChangeSetting;

/**
 * MaterialChangeSettingSearch represents the model behind the search form about `backend\models\MaterialChangeSetting`.
 */
class MaterialChangeSettingSearch extends MaterialChangeSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'material_id', 'refuel_cycle'], 'integer'],
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
        $query = MaterialChangeSetting::find();

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
            'material_id' => $this->material_id,
            'refuel_cycle' => $this->refuel_cycle,
        ]);

        return $dataProvider;
    }
}
