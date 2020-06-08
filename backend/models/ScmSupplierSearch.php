<?php

namespace backend\models;

use backend\models\ScmSupplier;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmSupplierSearch represents the model behind the search form about `backend\models\ScmSupplier`.
 */
class ScmSupplierSearch extends ScmSupplier {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'create_time', 'org_id'], 'integer'],
            [['name', 'type', 'username', 'tel', 'email'], 'safe'],
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
        $query = ScmSupplier::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'          => $this->id,
            'create_time' => $this->create_time,
            'org_id'      => $this->org_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
