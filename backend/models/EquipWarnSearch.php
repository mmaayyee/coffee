<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipWarn;

/**
 * EquipWarnSearch represents the model behind the search form about `backend\models\EquipWarn`.
 */
class EquipWarnSearch extends EquipWarn
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'warn_content', 'notice_type', 'report_num', 'continuous_number', 'is_report', 'create_time'], 'integer'],
            [['userid', 'report_setting'], 'safe'],
            [['interval_time'], 'number'],
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
        $query = EquipWarn::find();

        $query->orderby('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'warn_content' => $this->warn_content,
            'notice_type' => $this->notice_type,
            'report_num' => $this->report_num,
            'continuous_number' => $this->continuous_number,
            'interval_time' => $this->interval_time,
            'is_report' => $this->is_report,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'userid', $this->userid])
            ->andFilterWhere(['like', 'report_setting', $this->report_setting]);

        return $dataProvider;
    }
}
