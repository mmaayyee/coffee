<?php

namespace backend\models;

use backend\models\GroupBeginTeam;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;

/**
 * GroupBeginTeamSearch represents the model behind the search form of `backend\models\GroupBeginTeam`.
 */
class GroupBeginTeamSearch extends GroupBeginTeam
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['begin_team_id', 'group_id', 'u_id', 'group_booking_status', 'group_booking_num', 'type', 'status'], 'integer'],
            [['main_title', 'begin_datatime', 'end_datatime', 'drink_ladder', 'begin_time', 'activity_img', 'activity_details_img',
                'nicknameHead', 'mobileHead', 'nicknameMember', 'mobileMember', 'subhead', 'status'], 'safe'],
            [['group_booking_price', 'original_cost'], 'number'],
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

    public function search($params)
    {
        $this->load($params);
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $list           = GroupBeginTeam::getIndex($params);
        $dataProvider   = [];
        if ($list) {
            foreach ($list['searchModel'] as $key => $data) {
                $GroupBeginTeam = new GroupBeginTeam();
                $GroupBeginTeam->load(['GroupBeginTeam' => $data]);
                $dataProvider[$key] = $GroupBeginTeam;
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
