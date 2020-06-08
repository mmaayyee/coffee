<?php

namespace backend\models;

use backend\models\UserConsume;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserConsumeSearch represents the model behind the search form of `backend\models\UserConsume`.
 */
class UserConsumeSearch extends UserConsume
{
    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['user_consume_id', 'couponName', 'order_id', 'userMobile', 'product_id', 'actual_fee', 'coupon_value', 'building', 'fetch_time', 'static', 'source_price', 'equipment_code', 'isFee', 'createdFrom', 'createdTo', 'product_number', 'beans_num', 'beans_amount', 'user_type', 'product_type', 'equipment', 'equipment_static', 'total_taxable', 'user_id', 'build_number', 'consume_type', 'branch', 'realPrice', 'orgId', 'orgType', 'is_refund', 'refund_time', 'refundFrom', 'refundTo'], 'safe'],
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
        $consumeRecordsList = UserConsume::getUserConsumesList($params);
        $dataProvider       = [];
        if (isset($consumeRecordsList['consumeRecordsList'])) {
            foreach ($consumeRecordsList['consumeRecordsList'] as $key => $consumeRecords) {
                $proGroup = new UserConsume();
                $proGroup->load(['UserConsume' => $consumeRecords]);
                $dataProvider[$consumeRecords['user_consume_id']] = $proGroup;
            }
        }
        $refundList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($consumeRecordsList['total']) ? 0 : $consumeRecordsList['total'],
        ]);
        $data = [
            'refundList'    => $refundList,
            'buildingArray' => $consumeRecordsList['buildingArray'],
            'realPrice'     => $consumeRecordsList['realPrice'],
            'consumeAmount' => $consumeRecordsList['consumeAmount'],
        ];
        return $data;
    }
}
