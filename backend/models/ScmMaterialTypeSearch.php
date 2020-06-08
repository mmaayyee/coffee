<?php

namespace backend\models;

use backend\models\ScmMaterialType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmMaterialTypeSearch represents the model behind the search form about `backend\models\ScmMaterialType`.
 */
class ScmMaterialTypeSearch extends ScmMaterialType {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'type'], 'integer'],
            [['material_type_name', 'unit'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = ScmMaterialType::find();

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
            'id'   => $this->id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'material_type_name', $this->material_type_name])
            ->andFilterWhere(['like', 'unit', $this->unit]);

        return $dataProvider;
    }
}
