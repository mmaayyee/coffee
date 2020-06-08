<?php
namespace backend\models;

use backend\models\DiscountBuildingAssoc;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CouponSendTaskSearch represents the model behind the search form about `backend\models\CouponSendTask`.
 */
class DiscountBuildingAssocSearch extends DiscountBuildingAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['building_id', 'holicy_id', 'holicy_type', 'holicy_payment', 'buildingNumber', 'holicy_time'], 'integer'],
            [['holicy_name', 'build_name', 'build_pay_type_name'], 'string'],
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
        if (!empty($params['DiscountBuildingAssocSearch'])) {
            $params = $params['DiscountBuildingAssocSearch'];
        }
        $params['page']                  = !empty($params['page']) ? $params['page'] : 1;
        $discountBuildingAssocStatisList = Api::getDisBuildAssocStatisList($params);
        $dataProvider                    = [];
        if ($discountBuildingAssocStatisList) {
            foreach ($discountBuildingAssocStatisList['discountBuildingAssocList'] as $key => $data) {
                $DiscountBuildingAssoc = new DiscountBuildingAssoc();
                $DiscountBuildingAssoc->load(['DiscountBuildingAssoc' => $data]);
                $dataProvider[$key] = $DiscountBuildingAssoc;
            }
        }
        $discountBuildingAssocStatisList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($discountBuildingAssocStatisList['total']) && !empty($discountBuildingAssocStatisList['total']) ? $discountBuildingAssocStatisList['total'] : 0,
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);
        return $discountBuildingAssocStatisList;
    }
}
