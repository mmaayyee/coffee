<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ArrayDataProviderSelf;
use common\models\ActivityApi;
use backend\models\ActivityCombinPackageDelivery;

/**
 * ActivityCombinPackageDeliverySearch represents the model behind the search form of `backend\models\ActivityCombinPackageDelivery`.
 */
class ActivityCombinPackageDeliverySearch extends ActivityCombinPackageDelivery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'activity_id', 'order_id','address_id', 'distribution_type', 'is_delivery', 'create_time'], 'integer'],
            [['distribution_user_name', 'courier_number', 'address', 'receiver', 'commodity_num', 'user_mobile', 'createFrom', 'createTo', 'distribution_user_id'], 'safe'],
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
        $this->load($params);
        $activityList = ActivityApi::getCombinPackageDeliveryIndex($params);
        $dataProvider = [];
        foreach ($activityList['combinPackageDeliveryList'] as $key => $data) {
            $combinPackageDelivery = new ActivityCombinPackageDelivery();
            $combinPackageDelivery->load(['ActivityCombinPackageDelivery' => $data]);
            $dataProvider[$data['delivery_id']] = $combinPackageDelivery;
        }
        $activityList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$activityList['total'] ? 0 : $activityList['total'],
            'sort'       => [
                'attributes' => ['delivery_id desc'],
            ],
        ]);
        return $activityList;
    }
}
