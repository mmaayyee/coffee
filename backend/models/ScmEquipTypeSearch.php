<?php

namespace backend\models;

use backend\models\ScmEquipType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmEquipTypeSearch represents the model behind the search form about `backend\models\ScmEquipType`.
 */
class ScmEquipTypeSearch extends ScmEquipType {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'supplier_id', 'create_time'], 'integer'],
            [['model'], 'safe'],
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
        $query = ScmEquipType::find()->orderBy("id DESC");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'          => $this->id,
            'supplier_id' => $this->supplier_id,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'model', $this->model]);

        return $dataProvider;
    }
}
