<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ServiceCategory;
use common\models\ArrayDataProviderSelf;

/**
 * ServiceCategorySearch represents the model behind the search form of `backend\models\ServiceCategory`.
 */
class ServiceCategorySearch extends ServiceCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_time'], 'integer'],
            [['category'], 'safe'],
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
        $categoryList = ServiceCategory::getCategoryList($params);
         $dataProvider = [];
        if(isset($categoryList['categoryList'])){
            foreach ($categoryList['categoryList'] as $key => $data) {
                $proGroup = new ServiceCategory();
                $proGroup->load(['ServiceCategory' => $data]);
                $dataProvider[$data['id']] = $proGroup;
            }
        }
        $categoryList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 10,
            ],
            'totalCount' => !isset($categoryList['total']) ? 0 : $categoryList['total'],
            'sort'       => [
                'attributes' => ['id asc'],
            ],
        ]);
        return $categoryList;
      /*  $query = ServiceCategory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_time' => $this->created_time,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category]);

        return $dataProvider;
      */
    }
}
