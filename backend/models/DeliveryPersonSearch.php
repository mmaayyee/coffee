<?php

namespace backend\models;

use backend\models\DeliveryPerson;
use common\models\ArrayDataProviderSelf;
use common\models\DeliveryApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryPersonSearch represents the model behind the search form of `backend\models\DeliveryPerson`.
 */
class DeliveryPersonSearch extends DeliveryPerson
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id'], 'integer'],
            [['person_name', 'wx_number', 'mobile', 'person_status'], 'safe'],
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
        $list         = DeliveryApi::getDeliveryPersonList($params);
        $dataProvider = [];
        if ($list) {
            foreach ($list['list'] as $key => $data) {
                $deliveryPerson = new DeliveryPerson();
                $deliveryPerson->load(['DeliveryPerson' => $data]);
                $dataProvider[$data['person_id']] = $deliveryPerson;
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
