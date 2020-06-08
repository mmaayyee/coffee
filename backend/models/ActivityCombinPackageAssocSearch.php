<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\ActivityApi;
use yii\data\ActiveDataProvider;
use common\models\ArrayDataProviderSelf;
use backend\models\ActivityCombinPackageAssoc;

/**
 * ActivityCombinPackageAssocSearch represents the model behind the search form of `backend\models\ActivityCombinPackageAssoc`.
 */
class ActivityCombinPackageAssocSearch extends ActivityCombinPackageAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['combin_package_id', 'is_refund', 'order_user_num', 'order_num', 'sales_volume', 'total_income', 'activity_id'], 'integer'],
            [['not_part_city', 'point_type', 'product_information_json', 'product_id_str', 'activity_name', 'status', 'is_refund', 'createFrom', 'createTo', 'activity_type'], 'safe'],
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
     * 搜索接口调用
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        if ($params) {
            if (isset($params['page'])) {
                $data['page'] = $params['page'];
                unset($params['page']);
            }
            foreach ($params as $key => $value) {
                $data['ActivitySearch'] = $value;
            }
            $params = $data;
        }
        $activityList = ActivityApi::getCombinPackageAssocIndex($params);
        $dataProvider = [];
        foreach ($activityList['combinPackageAssocList'] as $key => $data) {
            $combinPackageAssoc = new ActivityCombinPackageAssoc();
            $combinPackageAssoc->load(['ActivityCombinPackageAssoc' => $data]);
            $dataProvider[$data['activity_id']] = $combinPackageAssoc;
        }
        $activityList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$activityList['total'] ? 0 : $activityList['total'],
            'sort'       => [
                'attributes' => ['activity_id desc'],
            ],
        ]);
        return $activityList;
    }
}
