<?php

namespace backend\models;

use backend\models\ScmTotalInventory;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

/**
 * ScmTotalInventorySearch represents the model behind the search form about `backend\models\ScmTotalInventory`.
 */
class ScmTotalInventorySearch extends ScmTotalInventory {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'material_id', 'total_number', 'warehouse_id'], 'integer'],
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
        $query        = ScmTotalInventory::find()->groupBy('warehouse_id');
        $pages          = new Pagination(['totalCount' => $query->count(), 'pageSize' => '2']);
        $query = $query->offset($pages->offset)->limit($pages->limit);
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
            return ['dataProvider' => $dataProvider, 'pages' => $pages];
        }

        if ($params['ScmTotalInventorySearch']['warehouse_id']) {
            $query->andFilterWhere([
                'warehouse_id' => $this->warehouse_id,
            ]);
        }
        $query->andFilterWhere([
            'id'           => $this->id,
            'material_id'  => $this->material_id,
            'total_number' => $this->total_number,
        ]);
        return ['dataProvider' => $dataProvider, 'pages' => $pages];
    }

    public function totalSearch() {
        $query        = ScmTotalInventory::find()->select(['sum(total_number) total_number', 'material_id', 'warehouse_id'])->groupBy('material_id');
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $query->joinWith('warehouse w')->andFilterWhere([
                'w.organization_id' => $managerOrgId,
            ]);
        }
        return $query->asArray()->all();
    }
}
