<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GroupBookingApi;
use common\models\ArrayDataProviderSelf;
use backend\models\GroupActivity;

/**
 * GroupActivitySearch represents the model behind the search form of `common\models\GroupActivity`.
 */
class GroupActivitySearch extends GroupActivity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'status', 'type', 'new_type', 'drink_num', 'residue_num'], 'integer'],
            [['main_title', 'subhead', 'price_ladder', 'drink_ladder', 'activity_img', 'activity_details_img'], 'safe'],
            [['duration', 'original_cost'], 'number'],
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

        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $list = GroupActivity::GetShow($params);
        $dataProvider   = [];
        if ($list) {
            foreach ($list['searchModel'] as $key => $data) {
                $GroupActivity = new GroupActivity();
                $GroupActivity->load(['GroupActivity' => $data]);
                $dataProvider[$key] = $GroupActivity;
            }
        }
        $list = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20, //每页显示条数
            ],
            'totalCount' => isset($list['count']) && !empty($list['count']) ? $list['count'] : 0,
        ]);
        return $list;
    }
}
