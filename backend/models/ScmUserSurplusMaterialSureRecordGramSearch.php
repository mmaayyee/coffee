<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ScmUserSurplusMaterialSureRecordGram;

/**
 * ScmUserSurplusMaterialSureRecordGramSearch represents the model behind the search form about `backend\models\ScmUserSurplusMaterialSureRecordGram`.
 */
class ScmUserSurplusMaterialSureRecordGramSearch extends ScmUserSurplusMaterialSureRecordGram
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'createTime', 'is_sure', 'sure_time', 'add_reduce', 'supplier_id', 'material_gram', 'material_type_id'], 'integer'],
            [['date', 'reason', 'author'], 'safe'],
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
        $query = ScmUserSurplusMaterialSureRecordGram::find();

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
            'createTime' => $this->createTime,
            'is_sure' => $this->is_sure,
            'sure_time' => $this->sure_time,
            'add_reduce' => $this->add_reduce,
            'supplier_id' => $this->supplier_id,
            'material_gram' => $this->material_gram,
            'author'       => $this->author,
            'material_type_id' => $this->material_type_id,
        ]);

        /*$query->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'author', $this->author]);*/

        return $dataProvider;
    }
}
