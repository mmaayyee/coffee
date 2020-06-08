<?php

namespace backend\models;

use backend\models\PayType;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;

/**
 * PayTypeSearch represents the model behind the search form of `\backend\models\PayType`.
 */
class PayTypeSearch extends PayType
{
    public function search($params)
    {
        $this->load($params);
        $payTypeList = PayTypeApi::getPayTypeList($params);
        if ($payTypeList['data']) {
            foreach ($payTypeList['data'] as $data) {
                $payType = new PayType();
                $payType->load(['PayType' => $data]);
                $dataProvider[$data['pay_type_id']] = $payType;
            }
        }
        $payTypeList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 500,
            ],
            'totalCount' => count($payTypeList['data']),
            'sort'       => [
                'attributes' => ['weight desc update_time desc'],
            ],
        ]);
        return $payTypeList;
    }
}
