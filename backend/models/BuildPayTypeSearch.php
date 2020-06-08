<?php

namespace backend\models;

use backend\models\BuildPayType;
use common\models\ArrayDataProviderSelf;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BuildPayTypeSearch represents the model behind the search form of `backend\models\BuildPayType`.
 */
class BuildPayTypeSearch extends BuildPayType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_pay_type_id', 'create_time'], 'integer'],
            [['build_pay_type_name'], 'safe'],
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
        if (!empty($params['BuildPayTypeSearch'])) {
            $params = $params['BuildPayTypeSearch'];
        }
        $params['page']    = Yii::$app->request->get('page', 0);
        $buildPayType      = PayTypeApi::getBuildPayTypeList($params);
        $buildPayTypeList  = [];
        $buildPayTypeTotal = 0;
        $dataProvider      = [];
        if (!empty($buildPayType['data'])) {
            $buildPayTypeList  = $buildPayType['data']['buildPayTypeList'];
            $buildPayTypeTotal = $buildPayType['data']['total'];
            foreach ($buildPayTypeList as $data) {
                $payType = new BuildPayType();
                $payType->load(['BuildPayType' => $data]);
                $dataProvider[$data['build_pay_type_id']] = $payType;
            }
        }
        return new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => $buildPayTypeTotal,
            'sort'       => [
                'attributes' => ['create_time desc'],
            ],
        ]);
    }
}
