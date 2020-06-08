<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EquipExtra;

/**
 * EquipExtraSearch represents the model behind the search form about `common\models\EquipExtra`.
 */
class EquipExtraSearch extends EquipExtra
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_del'], 'integer'],
            [['extra_name'], 'safe'],
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
        $query = EquipExtra::find();

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
            'is_del' => $this->is_del,
        ]);

        $query->andFilterWhere(['like', 'extra_name', $this->extra_name]);

        return $dataProvider;
    }
}
