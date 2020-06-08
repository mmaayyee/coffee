<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ServiceCount;
use common\models\ArrayDataProviderSelf;

/**
 * ServiceCountSearch represents the model behind the search form of `backend\models\ServiceCount`.
 */
class ServiceCountSearch extends ServiceCount
{
    /**
     * @inheritdoc
     */
     public function rules()
    {
        return [
            [['id', 'count', 'people'], 'integer'],
            [[ 'create_time','begin_time', 'end_time','category'], 'safe'],
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
         $this->load($params);
        //return ServiceCount::getServiceCountList($params);
       // $query = ServiceCount::find();
        $query = ServiceCount::getServiceCountList($params);

        // add conditions that should always apply here

        $query = new ArrayDataProviderSelf([
            'allModels'  => $query,
            'pagination' => [
                'pageSize' => 10,
            ],

        ]);
        return $query;

        /*
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
            'count' => $this->count,
            'people' => $this->people,
            'create_time' => $this->create_time,
        ]);

        return $dataProvider;*/
    }
}
