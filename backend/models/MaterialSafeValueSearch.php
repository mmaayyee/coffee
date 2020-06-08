<?php

namespace backend\models;

use common\models\Api;
use common\models\Building;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MaterialSafeValueSearch represents the model behind the search form about `backend\models\MaterialSafeValue`.
 */
class MaterialSafeValueSearch extends MaterialSafeValue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equipment_id', 'material_stock_id', 'safe_value', 'build_id', 'org_id', 'org_type'], 'integer'],
            [['equip_code'], 'safe'],
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
        $query = MaterialSafeValue::find()->groupBy('equipment_id');
        $query->addOrderBy('id DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        $this->org_id = $this->org_id ? $this->org_id : Manager::getManagerBranchID();
        if ($this->build_id) {
            $buildModel = Building::findOne($this->build_id);
            $equipID    = isset($buildModel->equip->id) ? $buildModel->equip->id : 0;
            $query->andFilterWhere(['equipment_id' => $equipID]);
        }
        if ($this->equip_code) {
            $query->joinWith('equipment e')
                ->andFilterWhere(['e.equip_code' => $this->equip_code]);
        }
        if ($this->org_id > 1) {
            if (!$this->equip_code) {
                $query->joinWith('equipment e');
            }
            if ($this->org_type === '0') {
                $orgIdArr = $this->org_id;
            } else if ($this->org_type == '1') {
                $where    = ['parent_path' => $this->org_id, 'org_id_no' => $this->org_id, 'is_replace_maintain' => Organization::INSTEAD_YES];
                $orgIdArr = Api::getOrgIdArray($where);
            } else {
                $where    = ['parent_path' => $this->org_id, 'is_replace_maintain' => Organization::INSTEAD_YES];
                $orgIdArr = Api::getOrgIdArray($where);
            }
            $query->andWhere(['e.org_id' => $orgIdArr]);
        }
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id'                => $this->id,
            'material_stock_id' => $this->material_stock_id,
            'safe_value'        => $this->safe_value,
        ]);

        return $dataProvider;
    }
}
