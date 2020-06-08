<?php

namespace backend\models;

use backend\models\UserLaxinRewardRecord;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

/**
 * UserLaxinRewardRecordSearch represents the model behind the search form of `backend\models\UserLaxinRewardRecord`.
 */
class UserLaxinRewardRecordSearch extends UserLaxinRewardRecord
{
    public $start_time;
    public $end_time;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['laxin_reward_record_id', 'share_userid', 'laxin_userid', 'beans_number', 'coupon_group_id', 'coupon_number', 'reward_time', 'share_mobile', 'bind_time', 'bind_mobile', 'start_time', 'end_time','group_name','beans_number','is_register','created_at'], 'safe'],
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
     * 分享者绑定用户列表
     * @param array $params
     * @return ActiveDataProvider
     */

    public function search($params)
    {
        $this->load($params);
        $proList      = UserLaxinRewardRecord::shareReward($params);
//        echo "<pre>";
//        print_r($proList);
//        die;
        $proList      = Json::decode($proList);
        $dataProvider = [];
        foreach ($proList['shareRewardList'] as $key => $data) {
            $userLaxinRewardRecord = new UserLaxinRewardRecord();
            $userLaxinRewardRecord->load(['UserLaxinRewardRecord' => $data]);
            $dataProvider[] = $userLaxinRewardRecord;
        }
        $coffeeProList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$proList['total'] ? 0 : $proList['total'],
            'sort'       => [
                'attributes' => ['id DESC'],
            ],
        ]);

        return $coffeeProList;
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function bindSearch($params)
    {
//        echo "<pre>";
//        print_r($params);
//        die;

        $this->load($params);
        $proList      = UserLaxinRewardRecord::shareBindUser($params);
//                echo "<pre>";
//        print_r($proList);
//        die;
        $proList      = Json::decode($proList);

        $dataProvider = [];
        foreach ($proList['shareBindUserList'] as $key => $data) {
            $userLaxinRewardRecord = new UserLaxinRewardRecord();
            $userLaxinRewardRecord->load(['UserLaxinRewardRecord' => $data]);
            $dataProvider[] = $userLaxinRewardRecord;
        }
        $coffeeProList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$proList['total'] ? 0 : $proList['total'],
            'sort'       => [
                'attributes' => ['id DESC'],
            ],
        ]);
        return $coffeeProList;
    }
}
