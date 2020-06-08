<?php

namespace backend\models;

use backend\models\Grind;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GrindSearch represents the model behind the search form about `app\models\Grind`.
 */
class GrindSearch extends Grind
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grind_id', 'grind_switch', 'grind_type', 'grind_time', 'org_id', 'interval_time'], 'integer'],
            [['grind_where', 'grinf_number', 'equipTypeId', 'is_all', 'buildingList', 'orgName', 'equipmentCode', 'grind_number', 'buildName', 'grind_remark'], 'safe'],
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

    public function searchGrindList($params)
    {
        $this->load($params);
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $grindList      = Api::getGrindList($params);
        $dataProvider   = [];
        if ($grindList) {
            foreach ($grindList['grindList'] as $key => $data) {
                $grind = new Grind();
                $grind->load(['Grind' => $data]);
                $dataProvider[$key] = $grind;
            }
        }
        $grindList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($grindList['totalCount']) && !empty($grindList['totalCount']) ? $grindList['totalCount'] : 0,
        ]);
        return $grindList;
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
        $this->load($params);
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $buildingList   = Api::getBuildGrindBuilding($params);
        $dataProvider   = [];
        if ($buildingList) {
            foreach ($buildingList['buildingList'] as $key => $data) {
                $grind = new Grind();
                $grind->load(['Grind' => $data]);
                $dataProvider[$key] = $grind;
            }
        }
        $buildingList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($buildingList['total']) && !empty($buildingList['total']) ? $buildingList['total'] : 0,
        ]);
        return $buildingList;
    }
}
