<?php

namespace backend\models;

use backend\models\DistributionWater;
use backend\models\Manager;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DistributionWaterSearch represents the model behind the search form about `backend\models\DistributionWater`.
 */
class DistributionWaterSearch extends DistributionWater
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'build_id', 'order_time', 'upload_time', 'supplier_id', 'distribution_task_id', 'completion_status', 'orgId'], 'integer'],
            [['surplus_water', 'need_water'], 'number'],
            [['startTime', 'endTime'], 'string'],
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
        //显示未下单和待配送的订单
        $query = DistributionWater::find()->joinWith(['build b'])->orderBy('create_time DESC');

        $statusWhere = ['<>','completion_status',DistributionWater::ALREADY_SEND];

        if(isset($params['DistributionWaterSearch']['completion_status']) && $params['DistributionWaterSearch']['completion_status'] !== ''){
            $statusWhere = ['completion_status' => $params['DistributionWaterSearch']['completion_status']];
        };
        $query->andFilterWhere($statusWhere);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $this->managerOrgId = Manager::getManagerBranchID();
        if ($this->managerOrgId > 1) {
            $query->andFilterWhere(['b.org_id' => $this->managerOrgId]);
        } else {
            $query->andFilterWhere(['b.org_id' => $this->orgId]);
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'build_id'    => $this->build_id,
            'supplier_id' => $this->supplier_id,
        ]);

        return $dataProvider;
    }

    public function searchRecord($params)
    {
        //  订单已配送完成的
        $query = DistributionWater::find()->joinWith(['build b'])->where(['completion_status' => DistributionWater::ALREADY_SEND])->orderBy('create_time DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $this->managerOrgId = Manager::getManagerBranchID();
        if ($this->managerOrgId > 1) {
            $query->andFilterWhere(['b.org_id' => $this->managerOrgId]);
        } else {
            $query->andFilterWhere(['b.org_id' => $this->orgId]);
        }

        //日期查询
        $this->startTime = $this->startTime ? $this->startTime : date('Y-m') . '-01';
        $this->endTime   = $this->endTime ? $this->endTime : date('Y-m-d');
        $query->andFilterWhere(['>=', 'upload_time', strtotime($this->startTime)]);
        $query->andFilterWhere(['<=', 'upload_time', strtotime($this->endTime . ' 23:59:59')]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'build_id'    => $this->build_id,
            'supplier_id' => $this->supplier_id,
        ]);

        return $dataProvider;
    }
}
