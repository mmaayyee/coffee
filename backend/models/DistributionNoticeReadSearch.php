<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DistributionNoticeRead;

/**
 * DistributionNoticeReadSearch represents the model behind the search form about `backend\models\DistributionNoticeRead`.
 */
class DistributionNoticeReadSearch extends DistributionNoticeRead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'read_status', 'read_time', 'read_feedback', 'notice_id'], 'integer'],
            [['userId'], 'safe'],
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
        $query = DistributionNoticeRead::find();

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
            'Id' => $this->Id,
            'read_status' => $this->read_status,
            'read_time' => $this->read_time,
            'read_feedback' => $this->read_feedback,
            'notice_id' => $this->notice_id,
        ]);

        $query->andFilterWhere(['like', 'userId', $this->userId]);

        return $dataProvider;
    }


     public function searchById($params, $id)
    {
        $query = DistributionNoticeRead::find()->where(['notice_id'=> $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
