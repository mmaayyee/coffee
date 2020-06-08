<?php

namespace backend\models;

use backend\models\Organization;
use common\models\Api;
use common\models\EquipTask;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipTaskSearch represents the model behind the search form about `backend\models\EquipTask`.
 */
class EquipTaskSearch extends EquipTask
{
    public $type;
    public $start_time;
    public $end_time;
    public $org_id;
    public $org_type;
    public $equip_code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'task_type', 'create_time', 'recive_time', 'start_repair_time', 'end_repair_time', 'is_use_fitting', 'process_result', 'type', 'equip_id'], 'integer'],
            [['build_id', 'content', 'assign_userid', 'malfunction_reason', 'process_method', 'remark', 'create_user', 'start_time', 'end_time', 'org_id', 'org_type', 'equip_code'], 'safe'],
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

        $query = EquipTask::find()
            ->joinWith('build b')
            ->orderby('is_userid desc, create_time desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        $this->org_id = $this->org_id ? $this->org_id : Manager::getManagerBranchID();
        if (empty($this->type)) {
            $query->andFilterWhere(['process_result' => EquipTask::UNTREATED]);
        } else {
            if ($this->start_time) {
                $query->andFilterWhere([
                    '>=',
                    'equip_task.create_time',
                    strtotime($this->start_time),
                ]);
            }
            if ($this->end_time) {
                $query->andFilterWhere([
                    '<=',
                    'equip_task.create_time',
                    strtotime(date("Y-m-d") . ' 23:59:59'),
                ]);
            }
            $query->andFilterWhere(['>', 'process_result', EquipTask::UNTREATED]);
            $query->andFilterWhere(['task_type' => $this->type]);
        }
        if ($this->org_id > 1) {
            if ($this->org_type === '0') {
                $orgIdArr = $this->org_id;
            } else if ($this->org_type == '1') {
                $where    = ['parent_path' => $this->org_id, 'org_id_no' => $this->org_id, 'is_replace_maintain' => Organization::INSTEAD_YES];
                $orgIdArr = Api::getOrgIdArray($where);
            } else {
                $where    = ['parent_path' => $this->org_id, 'is_replace_maintain' => Organization::INSTEAD_YES];
                $orgIdArr = Api::getOrgIdArray($where);
            }
            $query->andWhere(['b.org_id' => $orgIdArr]);
        }

        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->create_user) {
            $query->andFilterWhere(['like', 'create_user', $this->create_user]);
        }
        if ($this->build_id) {
            $query->andFilterWhere(['like', 'b.name', $this->build_id]);
        }
        $query->andFilterWhere([
            'id'                => $this->id,
            'equip_id'          => $this->equip_id,
            'task_type'         => $this->task_type,
            'recive_time'       => $this->recive_time,
            'start_repair_time' => $this->start_repair_time,
            'end_repair_time'   => $this->end_repair_time,
            'is_use_fitting'    => $this->is_use_fitting,
            'process_result'    => $this->process_result,
            'assign_userid'     => $this->assign_userid,
        ]);
        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'malfunction_reason', $this->malfunction_reason])
            ->andFilterWhere(['like', 'process_method', $this->process_method])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        //echo $query->createCommand()->getRawSql();exit();
        return $dataProvider;
    }

    /**
     *  投放记录管理查询
     *  @param $aprams
     *
     **/
    public function searchCheckDelivery($params)
    {
        $query = EquipTask::find();

        $query->orderby('is_userid desc, update_time desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'                => $this->id,
            'build_id'          => $this->build_id,
            'equip_id'          => $this->equip_id,
            'task_type'         => $this->task_type,
            'recive_time'       => $this->recive_time,
            'start_repair_time' => $this->start_repair_time,
            'end_repair_time'   => $this->end_repair_time,
            'is_use_fitting'    => $this->is_use_fitting,
            'process_result'    => $this->process_result,
        ]);

        return $dataProvider;
    }

    /**
     * 故障记录查询
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchCheckTrouble($params)
    {
        $query = EquipTask::find()->leftJoin('building b', 'b.id = equip_task.build_id');
        $query->andFilterWhere(['task_type' => EquipTask::MAINTENANCE_TASK]);
        $query->orderby('is_userid desc, update_time desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->content) {
            $query->andWhere('FIND_IN_SET(' . $this->content . ',equip_task.content)');
        }

        if ($this->build_id) {
            $query->andFilterWhere(['like', 'b.province', $this->build_id]);
            $query->orFilterWhere(['like', 'b.city', $this->build_id]);
        }

        if ($this->start_time) {
            $query->andFilterWhere(['>=', 'equip_task.create_time', strtotime($this->start_time)]);
        }

        if ($this->end_time) {
            $query->andFilterWhere(['<=', 'equip_task.create_time', strtotime($this->end_time)]);
        }

        return $dataProvider;
    }
}
