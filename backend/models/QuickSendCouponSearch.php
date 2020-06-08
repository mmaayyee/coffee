<?php

namespace backend\models;

use backend\models\QuickSendCoupon;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * QuickSendCouponSearch represents the model behind the search form about `common\models\QuickSendCoupon`.
 */
class QuickSendCouponSearch extends QuickSendCoupon
{
    public $startTime, $endTime;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coupon_type', 'coupon_number', 'coupon_id', 'coupon_sort', 'create_time', 'consume_id', 'order_code'], 'integer'],
            [['send_phone', 'content', 'coupon_remarks', 'caller_number'], 'string'],
            [['startTime', 'endTime'], 'safe'],
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
        $params['page']      = isset($params['page']) ? $params['page'] : 0;
        $quickSendCouponList = Api::quickSendCouponList($params);
        $dataProvider        = [];
        if ($quickSendCouponList) {
            foreach ($quickSendCouponList['quickSendCouponList'] as $key => $data) {
                $quickSendCoupon = new QuickSendCoupon();
                $quickSendCoupon->load(['QuickSendCoupon' => $data]);
                $dataProvider[$key] = $quickSendCoupon;
            }
        }
        $quickSendCouponList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($quickSendCouponList['total']) && !empty($quickSendCouponList['total']) ? $quickSendCouponList['total'] : 0,
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);
        return $quickSendCouponList;
    }
}
