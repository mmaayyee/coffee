<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipTypeProductConfig;

/**
 * EquipTypeProductConfigSearch represents the model behind the search form about `backend\models\EquipTypeProductConfig`.
 */
class EquipTypeProductConfigSearch extends EquipTypeProductConfig
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equip_type_id', 'product_id', 'cf_choose_sugar', 'brew_up', 'brew_down'], 'integer'],
            [['half_sugar', 'full_sugar'], 'number'],
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
        $query = EquipTypeProductConfig::find();

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
            'equip_type_id' => $this->equip_type_id,
            'product_id' => $this->product_id,
            'cf_choose_sugar' => $this->cf_choose_sugar,
            'half_sugar' => $this->half_sugar,
            'full_sugar' => $this->full_sugar,
            'brew_up' => $this->brew_up,
            'brew_down' => $this->brew_down,
        ]);

        return $dataProvider;
    }
}
