<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\ActivityApi;
use yii\data\ActiveDataProvider;
use backend\models\LotteryWinningRecord;
use common\models\ArrayDataProviderSelf;

/**
 * LotteryWinningRecordSearch represents the model behind the search form about `backend\models\LotteryWinningRecord`.
 */
class LotteryWinningRecordSearch extends LotteryWinningRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['winning_record_id', 'activity_id', 'prizes_type', 'user_id', 'is_winning', 'is_ship', 'create_time'], 'integer'],
            [['awards_name', 'prizes_name', 'user_phone', 'user_addr_info', 'receiver_name', 'start_time', 'end_time', 'activity_type_id'], 'safe'],
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
        $activityList = ActivityApi::getNineLotteryRecordList($params);
        $dataProvider = [];
        foreach ($activityList['lotteryWinningRecordList'] as $key => $data) {
            $proGroup = new LotteryWinningRecord();
            $proGroup->load(['LotteryWinningRecord' => $data]);
            $dataProvider[$data['winning_record_id']] = $proGroup;
        }
        $activityList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$activityList['total'] ? 0 : $activityList['total'],
            'sort'       => [
                'attributes' => ['winning_record_id desc'],
            ],
        ]);
        return $activityList;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function nineLotterySearch($params, $id='')
    {
        $this->load($params);
        $activityList = ActivityApi::getNineLotteryRecordList($params, $id);
        $dataProvider = [];
        foreach ($activityList['lotteryWinningRecordList'] as $key => $data) {
            $proGroup = new LotteryWinningRecord();
            $proGroup->load(['LotteryWinningRecord' => $data]);
            $dataProvider[$data['winning_record_id']] = $proGroup;
        }
        
        $activityList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$activityList['total'] ? 0 : $activityList['total'],
            'sort'       => [
                'attributes' => ['winning_record_id desc'],
            ],
        ]);
        return $activityList;
    }
}
