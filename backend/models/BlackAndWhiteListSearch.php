<?php

namespace backend\models;

use backend\models\BlackAndWhiteList;
use common\models\ArrayDataProviderSelf;
use common\models\CoffeeBackApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserTagSearch represents the model behind the search form about `backend\models\UserTag`.
 */
class BlackAndWhiteListSearch extends BlackAndWhiteList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_type', 'user_list_type', 'market_type'], 'integer'],
            [['add_type', 'user_list_type', 'market_type', 'user_content', 'buildname', 'username'], 'safe'],
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
        $data['UserTagSearch'] = !isset($params['BlackAndWhiteListSearch']) ? [] : $params['BlackAndWhiteListSearch'];
        $data['page']          = !isset($params['page']) ? 0 : $params['page'];
        $blackAndWhiteList     = CoffeeBackApi::getBlackAndWhiteList($data);
        $dataProvider          = [];
        if ($blackAndWhiteList) {
            foreach ($blackAndWhiteList['blackAndWhiteList'] as $key => $data) {
                $BlackAndWhiteList = new BlackAndWhiteList();
                $BlackAndWhiteList->load(['BlackAndWhiteList' => $data]);
                $dataProvider[$key] = $BlackAndWhiteList;
            }
        }
        $blackAndWhiteList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => empty($blackAndWhiteList['total']) ? 0 : $blackAndWhiteList['total'],
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);

        return $blackAndWhiteList;
    }
}
