<?php

namespace backend\models;

use backend\models\ScmWarehouse;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Api;

/**
 * ScmWarehouseSearch represents the model behind the search form about `backend\models\ScmWarehouse`.
 */
class ScmWarehouseSearch extends ScmWarehouse {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'ctime', 'organization_id'], 'integer'],
            [['name', 'address', 'use'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = ScmWarehouse::find();
        $this->orgArr = Api::getOrgIdNameArray();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) { //  分公司
            $this->organization_id = $managerOrgId;
            $query->andFilterWhere([
                'organization_id' => $this->organization_id,
            ]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($managerOrgId == 1) { // 总公司
            $query->andFilterWhere([
                'organization_id' => $this->organization_id,
            ]);
        }
        $query->andFilterWhere([
            'id'    => $this->id,
            'ctime' => $this->ctime,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'use', $this->use]);
        // echo $query->createCommand()->getRawSql();exit();
        return $dataProvider;
    }

}
