<?php
/**
 * Created by PhpStorm.
 * User: 蒋峰
 * Date: 2018/11/15
 * Time: 17:40
 */

namespace backend\models;

use common\models\ArrayDataProviderSelf;
use common\models\DeliveryApi;
use yii\base\Model;

class DeliveryRegionSearch extends DeliveryRegion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_region_id', 'region_name'], 'string'],
            [['business_status'], 'integer'],
            [['min_lng', 'min_lat', 'max_lng', 'max_lat', 'min_consum', 'business_time'], 'string'],
            [['coverage_range'], 'string', 'max' => 10000],
            [['province', 'city'], 'string', 'max' => 50],
            [['build_list', 'person_list'], 'safe'],
            [['region_name', 'start_time', 'end_time'], 'string', 'max' => 30],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $this->load($params);
        $list         = DeliveryApi::getDeliveryRegionList($params);
        $dataProvider = [];
        if ($list['data']) {
            foreach ($list['data']['list'] as $key => $data) {
                $deliveryPerson = new DeliveryRegion();
                $deliveryPerson->load(['DeliveryRegion' => $data]);
                $deliveryPerson->build_list                = $data['build_list'];
                $deliveryPerson->person_list               = $data['person_list'];
                $dataProvider[$data['delivery_region_id']] = $deliveryPerson;
            }
        }
        $list = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($list['total']) && !empty($list['total']) ? $list['total'] : 0,
            'sort'       => [
                'attributes' => ['person_id desc'],
            ],
        ]);
        return $list;
    }
}