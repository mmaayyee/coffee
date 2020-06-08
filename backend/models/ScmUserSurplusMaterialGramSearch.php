<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ScmUserSurplusMaterialGram;

/**
 * ScmUserSurplusMaterialGramSearch represents the model behind the search form about `backend\models\ScmUserSurplusMaterialGram`.
 */
class ScmUserSurplusMaterialGramSearch extends ScmUserSurplusMaterialGram
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'material_type_id', 'supplier_id', 'gram'], 'integer'],
            [['author', 'date'], 'safe'],
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
        $query = ScmUserSurplusMaterialGram::find();

        // add conditions that should always apply here
        $query->andFilterWhere(['>','gram','0']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(isset($params['ScmUserSurplusMaterialGramSearch']['author']) && $params['ScmUserSurplusMaterialGramSearch']['author']){
            $query->andFilterWhere([
                'author' => $params['ScmUserSurplusMaterialGramSearch']['author']
            ]);
        }

        // grid filtering conditions


        $query->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'date', $this->date]);

        return $dataProvider;
    }
}
