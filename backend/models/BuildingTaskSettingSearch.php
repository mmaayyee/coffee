<?php

namespace backend\models;

use backend\models\BuildingTaskSetting;
use common\models\Api;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BuildingTaskSettingSearch represents the model behind the search form about `backend\models\BuildingTaskSetting`.
 */
class BuildingTaskSettingSearch extends BuildingTaskSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'building_id', 'cleaning_cycle', 'refuel_cycle', 'day_num'], 'integer'],
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
        $query = BuildingTaskSetting::find()
            ->addOrderBy('id DESC');
        $orgId = Manager::getManagerBranchID();
        if ($orgId > 1) {
            $orgId = Api::getOrgIdArray(['parent_path' => $orgId]);
            $query->joinWith('building b')
                ->andFilterWhere(['b.org_id' => $orgId]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id'             => $this->id,
            'building_id'    => $this->building_id,
            'cleaning_cycle' => $this->cleaning_cycle,
            'refuel_cycle'   => $this->refuel_cycle,
            'day_num'        => $this->day_num,
        ]);

        return $dataProvider;
    }
}
