<?php

namespace backend\models;

use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CoffeeLanguageSearch represents the model behind the search form of `backend\models\CoffeeLanguage`.
 */
class CoffeeLanguageSearch extends CoffeeLanguage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language_type', 'language_product', 'language_static', 'language_equipment', 'language_time'], 'integer'],
            [['language_name', 'language_content', 'start_time', 'end_time', 'language_sort'], 'safe'],
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
    public function search($searchParams)
    {
        $this->load($searchParams);
        $coffeeLanguageList = CoffeeLanguage::getCoffeeLanguageList($searchParams);
        $dataProvider       = [];
        if (isset($coffeeLanguageList['CoffeeLanguage'])) {
            foreach ($coffeeLanguageList['CoffeeLanguage'] as $coffeeLanguage) {
                $CoffeeLanguageModels = new CoffeeLanguage();
                $CoffeeLanguageModels->load(['CoffeeLanguage' => $coffeeLanguage]);
                $dataProvider[$coffeeLanguage['id']] = $CoffeeLanguageModels;
            }
        }
        $orderInfoList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($coffeeLanguageList['total']) ? 0 : $coffeeLanguageList['total'],
        ]);

        return $orderInfoList;
    }
}
