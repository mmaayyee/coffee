<?php

namespace backend\models;

use backend\models\Sale;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CouponSendTaskSearch represents the model behind the search form about `backend\models\CouponSendTask`.
 */
class SaleSearch extends Sale
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_id'], 'integer'],
            [['sale_phone', 'sale_email', 'sale_name'], 'string'],
            [['sale_id'], 'safe'],
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
        $saleList     = Api::getSaleList($params);
        $dataProvider = [];
        if ($saleList) {
            foreach ($saleList['saleList'] as $key => $data) {
                $sale = new Sale();
                $sale->load(['Sale' => $data]);
                $dataProvider[$key] = $sale;
            }
        }
        $saleList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($saleList['total']) && !empty($saleList['total']) ? $saleList['total'] : 0,
            'sort'       => [
                'attributes' => ['sale_id desc'],
            ],
        ]);
        return $saleList;
    }
}
