<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LightProgramAssoc;

/**
 * LightProgramAssocSearch represents the model behind the search form about `backend\models\LightProgramAssoc`.
 */
class LightProgramAssocSearch extends LightProgramAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'program_id', 'build_id'], 'integer'],
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
        $query = LightProgramAssoc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'program_id' => $this->program_id,
            'build_id' => $this->build_id,
        ]);

        return $dataProvider;
    }
}
