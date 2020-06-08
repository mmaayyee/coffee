<?php

namespace backend\models;

use backend\models\Activity;
use common\models\ActivityApi;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ActivitySearch represents the model behind the search form about `backend\models\Activity`.
 */
class ActivitySearch extends Activity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'created_at', 'status', 'start_time', 'end_time', 'activity_sort', 'activity_type_id'], 'integer'],
            [['activity_name', 'activity_desc', 'createFrom', 'createTo', 'activity_url'], 'safe'],
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
    public function couponActivitySearch($params)
    {
        $this->load($params);
        $activityList = ActivityApi::getCouponActivityList($params);
        $dataProvider = [];
        if ($activityList) {
            foreach ($activityList['data'] as $key => $data) {
                $proGroup = new Activity();
                $proGroup->load(['Activity' => $data]);
                $dataProvider[$data['activity_id']] = $proGroup;
            }
        }
        $activityData = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => empty($activityList['total']) ? 0 : $activityList['total'],
            'sort'       => [
                'attributes' => ['activity_id desc'],
            ],
        ]);
        return $activityData;
    }

    /**
     * 通过接口获取九宫格抽奖活动数据
     * @author  zmy
     * @version 2017-11-21
     * @param   [Array]     $params [查询参数数组]
     * @return  [Obj]               [对象]
     */
    public function nineLotterySearch($params)
    {
        $this->load($params);
        $activityList = ActivityApi::getNineLotteryList($params);
        $dataProvider = [];
        foreach ($activityList['nineLotteryList'] as $key => $data) {
            $proGroup = new Activity();
            $proGroup->load(['Activity' => $data]);
            $dataProvider[$data['activity_id']] = $proGroup;
        }

        $activityList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$activityList['total'] ? 0 : $activityList['total'],
            'sort'       => [
                'attributes' => ['activity_id desc'],
            ],
        ]);
        return $activityList;

    }

}
