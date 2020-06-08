<?php

namespace backend\models;

use backend\models\EquipAbnormalSendRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipAbnormalSendRecordSearch represents the model behind the search form about `backend\models\EquipAbnormalSendRecord`.
 */
class EquipAbnormalSendRecordSearch extends EquipAbnormalSendRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'abnormal_id', 'report_num', 'is_process_success', 'send_time', 'process_time', 'org_id'], 'integer'],
            [['equip_code', 'build_id', 'send_users'], 'safe'],
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
        $query  = EquipAbnormalSendRecord::find()->orderBy('id desc');
        $org_id = Manager::getManagerBranchID();
        if ($org_id > 1) {
            $query->andFilterWhere([
                'equip_abnormal_send_record.org_id' => $org_id,
            ]);
        }
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
            'abnormal_id'        => $this->abnormal_id,
            'report_num'         => $this->report_num,
            'is_process_success' => $this->is_process_success,
            'send_time'          => $this->send_time,
            'process_time'       => $this->process_time,
            // 'build_id'           => $this->build_id,
        ]);
        if ($this->build_id) {
            $query->joinWith('build b')->andFilterWhere(['like', 'b.name', $this->build_id]);
        }

        $query->andFilterWhere(['like', 'equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'send_users', $this->send_users]);

        return $dataProvider;
    }
}
