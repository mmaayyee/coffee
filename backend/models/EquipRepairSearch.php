<?php

namespace backend\models;

use backend\models\EquipRepair;
use common\models\EquipTask;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipRepairSearch represents the model behind the search form about `backend\models\EquipRepair`.
 */
class EquipRepairSearch extends EquipRepair
{
    public $build_id;
    public $type = 1;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_time', 'recive_time', 'is_accept', 'process_status','type'], 'integer'],
            [['content', 'remark', 'author', 'build_name'], 'safe'],
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
        $query = EquipRepair::find()->orderby('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->joinWith('equip e')->andFilterWhere(['e.org_id' => $orgId]);
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'          => $this->id,
            'create_time' => $this->create_time,
        ]);

        if(!empty($params['EquipRepairSearch']['equip_id'])){
            $query->andFilterWhere(['equip_repair.equip_id' => $params['EquipRepairSearch']['equip_id']]);
        }
        //筛选客服上报状态
        if($this->process_status){
            switch ($this->process_status){
                case 1:
                    $query->joinWith('task t')->andFilterWhere(['t.start_repair_time' => '0']);
                    break;
                case 2:
                    $query->joinWith('task t')->andFilterWhere(['t.end_repair_time' => '0']);
                    $query->joinWith('task t')->andFilterWhere(['>','t.start_repair_time','0']);
                    break;
                case 3:
                    $query->joinWith('task t')->andFilterWhere(['t.process_result' => EquipTask::RESULT_SUCCESS]);
                    break;
                case 4:
                    $query->joinWith('task t')->andFilterWhere(['t.process_result' => EquipTask::RESULT_FAILURE]);
                    break;
            }
        };

        if ($this->is_accept) {
            if ($this->is_accept == 1) {
                $query->andFilterWhere(['>', 'equip_repair.recive_time', 1]);
            } else if ($this->is_accept == 2) {
                $query->andFilterWhere(['equip_repair.recive_time' => 0]);
            }
        }
        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'build_name', $this->build_name])
            ->andFilterWhere(['like', 'remark', $this->remark]);
        return $dataProvider;
    }


    public function searchRepair($params){
        $query = EquipRepair::find()->orderby('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->joinWith('equip e')->andFilterWhere(['e.org_id' => $orgId]);
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'          => $this->id,
            'create_time' => $this->create_time,
        ]);

        if(!empty($params['equip_id'])){
            $query->andFilterWhere(['equip_repair.equip_id' => $params['equip_id']]);
        }

        //筛选客服上报状态
        if($this->process_status){
            switch ($this->process_status){
                case 1:
                    $query->joinWith('task t')->andFilterWhere(['t.start_repair_time' => '0']);
                    break;
                case 2:
                    $query->joinWith('task t')->andFilterWhere(['t.end_repair_time' => '0']);
                    $query->joinWith('task t')->andFilterWhere(['>','t.start_repair_time','0']);
                    break;
                case 3:
                    $query->joinWith('task t')->andFilterWhere(['t.process_result' => EquipTask::RESULT_SUCCESS]);
                    break;
                case 4:
                    $query->joinWith('task t')->andFilterWhere(['t.process_result' => EquipTask::RESULT_FAILURE]);
                    break;
            }
        };
        if($this->content){
            $query->andWhere('FIND_IN_SET('.$this->content.',equip_repair.content)');
        }
        $query->andFilterWhere(['like', 'author', $this->author]);
        return $dataProvider;
    }
}
