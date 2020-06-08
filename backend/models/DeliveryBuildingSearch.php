<?php

namespace backend\models;

use backend\models\DeliveryBuilding;
use common\models\ArrayDataProviderSelf;
use common\models\DeliveryApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryBuildingSearch represents the model behind the search form of `common\models\DeliveryBuilding`.
 */
class DeliveryBuildingSearch extends DeliveryBuilding
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_building_id', 'building_id', 'min_consum', 'business_status'], 'integer'],
            [['coverage_radius', 'business_time', 'person_info', 'building_name', 'delivery_person'], 'safe'],
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
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $this->load($params);

        $deliveryBuildingList = DeliveryApi::getDeliveryBuildingList($params);
        $dataProvider         = [];
        if ($deliveryBuildingList) {
            foreach ($deliveryBuildingList['list'] as $key => $data) {
                $deliveryBuilding = new DeliveryBuilding();
                $deliveryBuilding->load(['DeliveryBuilding' => $data]);
                $dataProvider[$data['delivery_building_id']] = $deliveryBuilding;
            }
        }
        $list = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($deliveryBuildingList['total']) && !empty($deliveryBuildingList['total']) ? $deliveryBuildingList['total'] : 0,
            'sort'       => [
                'attributes' => ['delivery_building_id desc'],
            ],
        ]);
        return $list;
    }
}
