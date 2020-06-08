<?php

namespace backend\models;

use backend\models\DiscountHolicy;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * discountHolicySearch represents the model behind the search form about `common\models\DiscountHolicy`.
 */
class DiscountHolicySearch extends DiscountHolicy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['holicy_id', 'holicy_type', 'holicy_status', 'holicy_time', 'holicy_payment', 'holicy_cheap'], 'integer'],
            [['holicy_name'], 'safe'],
            [['holicy_price'], 'number'],
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
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $discountList   = Api::getDiscountList($params);
        $dataProvider   = [];
        if ($discountList) {
            foreach ($discountList['discountList'] as $key => $data) {
                $DiscountHolicy = new DiscountHolicy();
                $DiscountHolicy->load(['DiscountHolicy' => $data]);
                $dataProvider[$key] = $DiscountHolicy;
            }
        }
        $discountList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($discountList['total']) && !empty($discountList['total']) ? $discountList['total'] : 0,
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);
        return $discountList;
    }
}
