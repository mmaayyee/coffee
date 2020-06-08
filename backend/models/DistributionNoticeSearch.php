<?php

namespace backend\models;

use backend\models\DistributionNotice;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DistributionNoticeSearch represents the model behind the search form about `backend\models\DistributionNotice`.
 */
class DistributionNoticeSearch extends DistributionNotice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'create_time', 'send_num'], 'integer'],
            [['sender', 'content', 'receiver'], 'safe'],
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
        $query = DistributionNotice::find()->orderBy('distribution_notice.id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $query->joinWith('manager')->andFilterWhere(['manager.branch' => $managerOrgId]);
        }
        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'Id'          => $this->Id,
            'create_time' => $this->create_time,
            'send_num'    => $this->send_num,
        ]);

        $query->andFilterWhere(['like', 'sender', $this->sender])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'receiver', $this->receiver]);

        return $dataProvider;
    }
}
