<?php

namespace backend\models;

use backend\models\DistributionTask;
use backend\models\Organization;
use common\models\Api;
use common\models\WxMember;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * DistributionTaskSearch represents the model behind the search form about `backend\models\DistributionTask`.
 */
class DistributionTaskSearch extends DistributionTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'build_id', 'create_time', 'equip_id', 'start_delivery_time', 'end_delivery_time', 'is_sue'], 'integer'],
            [['content', 'assign_userid', 'remark', 'task_type', 'result', 'start_time', 'end_time', 'is_finish', 'no_finish', 'date'], 'safe'],
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
        $query = DistributionTask::find()->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params['DistributionTaskSearch']['is_sue'])) {
            $status = $params['DistributionTaskSearch']['is_sue'];
            $where  = DistributionTask::getSearchWhere($status);
            $query->andFilterWhere($where);
        }

        if (!empty($params['DistributionTaskSearch']['assign_userid'])) {
            $query->andFilterWhere(['assign_userid' => $params['DistributionTaskSearch']['assign_userid']]);
        }
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $orgId = Api::getOrgIdArray(['parent_path' => $orgId, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->joinWith('build b')->andFilterWhere(['b.org_id' => $orgId]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'build_id' => $this->build_id,
            'equip_id' => $this->equip_id,
        ]);
        $query->andFilterWhere(['like', 'task_type', $this->task_type]);

        //起始日期
        if ($this->start_delivery_time) {
            $query->andFilterWhere(['>=', 'end_delivery_date', $this->start_delivery_time]);
        }
        //截止日期
        if ($this->end_delivery_time) {
            $query->andFilterWhere(['>=', 'end_delivery_date', $this->end_delivery_time]);
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchRecord($params)
    {
        $query = DistributionTask::find()->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere([
            'is_sue'    => 2,
            'task_type' => [1, 3, 4, 5, 6],
        ]);
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $orgId = Api::getOrgIdArray(['parent_path' => $orgId, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->joinWith('build b')->andFilterWhere(['b.org_id' => $orgId]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'equip_id'      => $this->equip_id,
            'assign_userid' => $this->assign_userid,
        ]);
        //起始日期
        if ($this->start_time) {
            $query->andFilterWhere(['>=', 'end_delivery_date', $this->start_time]);
        }
        //截止日期
        if ($this->end_time) {
            $query->andFilterWhere(['<=', 'end_delivery_date', $this->end_time]);
        }
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function repairSearch($params)
    {
        $query = DistributionTask::find()->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere([
            'is_sue'    => 2,
            'task_type' => [2, 3],
        ]);
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $orgId = Api::getOrgIdArray(['parent_path' => $orgId, 'is_replace_maintain' => Organization::INSTEAD_YES]);
            $query->joinWith('build b')->andFilterWhere(['b.org_id' => $orgId]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'equip_id'      => $this->equip_id,
            'assign_userid' => $this->assign_userid,
        ]);
        return $dataProvider;
    }

    /**
     * 运维任务统计管理
     * @author wangxiwen
     * @version 2018-06-08
     * @param array $params
     * @return ActiveDataProvider
     */
    public function statisticsSearch($params)
    {
        $this->load($params);
        $param        = !empty($params) ? $params['DistributionTaskSearch'] : [];
        $query        = DistributionTask::find()->orderBy('create_time DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort(false);
        $orgId = Manager::getManagerBranchID();
        $where = [];
        if ($orgId > 1) {
            $useridList = WxMember::getMemberIDArr($orgId);
            $where      = ['in', 'assign_userid', $useridList];
        }
        if (!empty($param['date'])) {
            $times = DistributionTask::getTime($param['date']);
        } else {
            //获取当前时间所在月份的天数
            $date  = date('Y-m', time());
            $times = DistributionTask::getTime($date);
        }
        if (!empty($param['assign_userid'])) {
            $userWhere = ['assign_userid' => $param['assign_userid']];
        } else {
            $userWhere = [];
        }
        $dateStartWhere = ['>=', 'create_time', $times['start']];
        $dateEndWhere   = ['<', 'create_time', $times['end']];
        $query->andWhere($where)
            ->andWhere($dateStartWhere)
            ->andWhere($dateEndWhere)
            ->andWhere($userWhere)
            ->select(new Expression("assign_userid,count(case is_sue when 1 then '未完成' end) no_finish,count(case is_sue when 2 then '已完成' end) is_finish,FROM_UNIXTIME(create_time,'%Y-%m') date"))
            ->groupBy('assign_userid')
            ->all();
        return $dataProvider;
    }

}
