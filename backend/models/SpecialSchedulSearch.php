<?php

namespace backend\models;

use backend\models\SpecialSchedul;
use common\models\ArrayDataProviderSelf;
use common\models\EquipProductGroupApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SpecialSchedulSearch represents the model behind the search form about `backend\models\SpecialSchedul`.
 */
class SpecialSchedulSearch extends SpecialSchedul
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'state', 'is_coupons', 'user_type', 'is_coupons'], 'integer'],
            [['special_schedul_name', 'start_time', 'end_time', 'build_name'], 'safe'],
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
        $specialSchedulList = EquipProductGroupApi::getSpecialSchedulList($params);
        $dataProvider       = [];
        if (isset($specialSchedulList['specialSchedulList'])) {
            foreach ($specialSchedulList['specialSchedulList'] as $key => $data) {
                $specialSchedul = new SpecialSchedul();
                $specialSchedul->load(['SpecialSchedul' => $data]);
                $dataProvider[$data['id']] = $specialSchedul;
            }
        }
        $coffeeProList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$specialSchedulList['total'] ? 0 : $specialSchedulList['total'],
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);
        return $coffeeProList;
    }
}
