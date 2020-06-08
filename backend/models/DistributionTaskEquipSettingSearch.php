<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DistributionTaskEquipSetting;

/**
 * DistributionTaskEquipSettingSearch represents the model behind the search form about `backend\models\DistributionTaskEquipSetting`.
 */
class DistributionTaskEquipSettingSearch extends DistributionTaskEquipSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'equip_type_id', 'org_id', 'cleaning_cycle', 'material_id','refuel_cycle', 'day_num'], 'integer'],
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
        $query = DistributionTaskEquipSetting::find();

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

        if (isset($params['DistributionTaskEquipSettingSearch']['build_id']) && $params['DistributionTaskEquipSettingSearch']['build_id']) {
            $query->joinWith('build')->andFilterWhere(['like', 'building.name', $params['DistributionTaskEquipSettingSearch']['build_id']]);
        }

        if ($this->org_id) {
            $query->andFilterWhere([
                'org_id' => $this->org_id,
            ]);
        }
        if ($this->equip_type_id) {
            $query->andFilterWhere([
                'equip_type_id' => $this->equip_type_id,
            ]);
        }

        if($this->material_id){
            $query->andFilterWhere([
                'material_id' => $this->material_id
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'build_id' => $this->build_id,
            'cleaning_cycle' => $this->cleaning_cycle,
            'refuel_cycle' => $this->refuel_cycle,
            'day_num' => $this->day_num,
        ]);

        return $dataProvider;
    }
}
