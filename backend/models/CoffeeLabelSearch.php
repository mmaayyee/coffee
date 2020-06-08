<?php

namespace backend\models;

use backend\models\CoffeeLabel;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CoffeeLabelSearch represents the model behind the search form of `backend\models\CoffeeLabel`.
 */
class CoffeeLabelSearch extends CoffeeLabel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'online_status', 'access_status', 'sort', 'status'], 'integer'],
            [['label_name', 'desk_img_url', 'label_img_url', 'desk_selected_img_url', 'product_name'], 'safe'],
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
        $coffeeLabelList = CoffeeLabel::getCoffeeLabelList($params);
        $dataProvider    = [];
        if ($coffeeLabelList) {
            foreach ($coffeeLabelList['labelList'] as $key => $data) {
                $coffeeLabel = new CoffeeLabel();
                $coffeeLabel->load(['CoffeeLabel' => $data]);
                $dataProvider[$data['id']] = $coffeeLabel;
            }
        }
        $coffeeLabelList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($coffeeLabelList['total']) && !empty($coffeeLabelList['total']) ? $coffeeLabelList['total'] : 0,
            'sort'       => [
                'attributes' => ['sort asc'],
            ],
        ]);
        return $coffeeLabelList;
    }
}
