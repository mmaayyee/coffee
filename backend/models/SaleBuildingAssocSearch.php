<?php

namespace backend\models;

use backend\models\SaleBuildingAssoc;
use common\models\Api;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CouponSendTaskSearch represents the model behind the search form about `backend\models\CouponSendTask`.
 */
class SaleBuildingAssocSearch extends SaleBuildingAssoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_id', 'build_id'], 'integer'],
            [['qrcode_img', 'build_name', 'sale_email', 'sale_name', 'sale_phone'], 'string'],
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
        $this->sale_arr  = Api::getSaleIdNameList();
        $this->build_arr = Api::getBuildIdNameList();
        $this->sale_list = Api::getSaleAllList();
        $this->load($params);
        $params['page']     = isset($params['page']) ? $params['page'] : 0;
        $saleBuildAssocList = Api::getSaleBuildAssocJsonObj($params);
        $dataProvider       = [];
        if ($saleBuildAssocList) {
            foreach ($saleBuildAssocList['saleBuildAssocList'] as $key => $data) {
                $saleBuildAssoc = new SaleBuildingAssoc();
                $saleBuildAssoc->load(['SaleBuildingAssoc' => $data]);
                $dataProvider[$key] = $saleBuildAssoc;
            }
        }
        $saleBuildAssocList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($saleBuildAssocList['total']) && !empty($saleBuildAssocList['total']) ? $saleBuildAssocList['total'] : 0,
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);
        return $saleBuildAssocList;
    }
}
