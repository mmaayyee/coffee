<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LaxinActivityConfig;

/**
 * LaxinActivityConfigSearch represents the model behind the search form of `backend\models\LaxinActivityConfig`.
 */
class LaxinActivityConfigSearch extends LaxinActivityConfig
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['laxin_activity_id', 'rebate_node', 'is_repeate', 'new_coupon_groupid', 'old_coupon_groupid', 'share_coupon_groupid', 'new_beans_number', 'old_beans_number', 'share_beans_number', 'start_time', 'end_time', 'create_time'], 'integer'],
            [['no_register_content', 'activity_description', 'new_user_content', 'old_user_content'], 'safe'],
            [['share_beans_percentage'], 'number'],
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
        $query = LaxinActivityConfig::find();

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
            'laxin_activity_id' => $this->laxin_activity_id,
            'rebate_node' => $this->rebate_node,
            'is_repeate' => $this->is_repeate,
            'new_coupon_groupid' => $this->new_coupon_groupid,
            'old_coupon_groupid' => $this->old_coupon_groupid,
            'share_coupon_groupid' => $this->share_coupon_groupid,
            'new_beans_number' => $this->new_beans_number,
            'old_beans_number' => $this->old_beans_number,
            'share_beans_number' => $this->share_beans_number,
            'share_beans_percentage' => $this->share_beans_percentage,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'no_register_content', $this->no_register_content])
            ->andFilterWhere(['like', 'activity_description', $this->activity_description])
            ->andFilterWhere(['like', 'new_user_content', $this->new_user_content])
            ->andFilterWhere(['like', 'old_user_content', $this->old_user_content]);

        return $dataProvider;
    }
}
