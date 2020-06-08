<?php

namespace backend\models;

use backend\models\ScmStock;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScmStockSearch represents the model behind the search form about `backend\models\ScmStock`.
 */
class ScmStockSearch extends ScmStock
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'reason', 'material_id', 'material_num', 'ctime'], 'integer'],
            [['warehouse_id', 'distribution_clerk_id'], 'string'],
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
        $query        = ScmStock::find()->orderBy("is_sure, sure_time DESC, ctime DESC");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $query->joinWith('warehouse w')->andFilterWhere([
                'w.organization_id' => $managerOrgId,
            ]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id'                    => $this->id,
            'reason'                => $this->reason,
            'distribution_clerk_id' => $this->distribution_clerk_id,
            'material_id'           => $this->material_id,
            'material_num'          => $this->material_num,
        ]);
        if ($params['ScmStockSearch']['warehouse_id']) {
            $query->andFilterWhere([
                'warehouse_id' => $this->warehouse_id,
            ]);
        }
        //日期查询
        if (!empty($params["ScmStockSearch"]["startTime"]) && empty($params["ScmStockSearch"]["endTime"])) {
            $startTime       = strtotime($params["ScmStockSearch"]["startTime"]);
            $endTime         = strtotime(date("Y-m-d")) + (23 * 60 + 59) * 60;
            $this->startTime = $params["ScmStockSearch"]["startTime"];
            $this->endTime   = $params["ScmStockSearch"]["endTime"];
            $query->andFilterWhere(['>=', 'scm_stock.ctime', $startTime]);
            $query->andFilterWhere(['<=', 'scm_stock.ctime', $endTime]);
        }
        if (!empty($params["ScmStockSearch"]["startTime"]) && !empty($params["ScmStockSearch"]["endTime"])) {
            $startTime       = strtotime($params["ScmStockSearch"]["startTime"]);
            $endTime         = strtotime($params["ScmStockSearch"]["endTime"]) + (23 * 60 + 59) * 60;
            $this->startTime = $params["ScmStockSearch"]["startTime"];
            $this->endTime   = $params["ScmStockSearch"]["endTime"];
            $query->andFilterWhere(['>=', 'scm_stock.ctime', $startTime]);
            $query->andFilterWhere(['<=', 'scm_stock.ctime', $endTime]);
        }
        return $dataProvider;
    }
}
