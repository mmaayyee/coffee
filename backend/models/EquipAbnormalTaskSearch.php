<?php

namespace backend\models;

use common\models\WxMember;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipAbnormalTask;

/**
 * EquipAbnormalTaskSearch represents the model behind the search form of `backend\models\EquipAbnormalTask`.
 */
class EquipAbnormalTaskSearch extends EquipAbnormalTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'build_id', 'org_id',  'task_status'], 'integer'],
            [['equip_code', 'abnormal_id','create_time'], 'safe'],
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
        $query = EquipAbnormalTask::find();
        $orgId= Manager::getManagerBranchID();
        if($orgId>1){
            $query->andWhere(['org_id'=>$orgId]);
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
        if($this->create_time){

            $start_time = strtotime(($this->create_time.' 00:00:00'))-60*60*8;
            $end_time = strtotime(($this->create_time.' 23:59:59'))-60*60*8;
            $query->andFilterWhere(['>','create_time',$start_time]);
            $query->andFilterWhere(['<=','create_time',$end_time]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'task_id' => $this->task_id,
            'build_id' => $this->build_id,
            'task_status' => $this->task_status,
        ]);

        $query->andFilterWhere(['like', 'equip_code', $this->equip_code])
            ->andFilterWhere(['like', 'abnormal_id', $this->abnormal_id]);

        return $dataProvider;
    }
}
