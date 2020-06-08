<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\EquipmentTaskSetting;

/**
 * EquipmentTaskSettingSearch represents the model behind the search form about `backend\models\EquipmentTaskSetting`.
 */
class EquipmentTaskSettingSearch extends EquipmentTaskSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equipment_type_id', 'organization_id', 'cleaning_cycle', 'refuel_cycle', 'day_num'], 'integer'],
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
        $query = EquipmentTaskSetting::find();

        // add conditions that should always apply here

        $query->addOrderBy('id DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $query->andFilterWhere(['organization_id' => $orgId]);
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'equipment_type_id' => $this->equipment_type_id,
            'organization_id' => $this->organization_id,
            'cleaning_cycle' => $this->cleaning_cycle,
            'refuel_cycle' => $this->refuel_cycle,
            'day_num' => $this->day_num,
        ]);

        return $dataProvider;
    }
}
