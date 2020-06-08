<?php

namespace backend\models;

use backend\models\UserRefund;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserRefundSearch represents the model behind the search form about `app\models\UserRefund`.
 */
class UserRefundSearch extends UserRefund
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_refund_id', 'order_id', 'refund_status', 'refund_type', 'refundMsg'
                , 'refundCreatedTime', 'refundTime', 'fundMobile', 'createdFrom', 'createdTo', 'refundPrice', 'refundBeansNum']
                , 'safe'],
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
        $userRefundList = UserRefund::getUserRefundList($params);
        $dataProvider   = [];
        if (isset($userRefundList['userRefundList'])) {
            foreach ($userRefundList['userRefundList'] as $key => $userRefund) {
                $proGroup = new UserRefund();
                $proGroup->load(['UserRefund' => $userRefund]);
                $dataProvider[$userRefund['user_refund_id']] = $proGroup;
            }
        }
        $refundList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($userRefundList['total']) ? 0 : $userRefundList['total'],
        ]);
        return $refundList;
    }
}
