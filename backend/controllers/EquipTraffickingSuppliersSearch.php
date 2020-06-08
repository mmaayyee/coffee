<?php

namespace backend\controllers;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EquipTraffickingSuppliers;

/**
 * EquipTraffickingSuppliersSearch represents the model behind the search form about `common\models\EquipTraffickingSuppliers`.
 */
class EquipTraffickingSuppliersSearch extends EquipTraffickingSuppliers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_time'], 'integer'],
            [['name', 'userid', 'mobile', 'email'], 'safe'],
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
        $query = EquipTraffickingSuppliers::find();

        // add conditions that should always apply here
        $query -> orderby('id desc');

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
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'userid', $this->userid])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
