<?php

namespace backend\models;

use backend\models\CouponSendTask;
use common\models\ArrayDataProviderSelf;
use common\models\CoffeeBackApi;
use yii\base\Model;
use yii\helpers\Json;
use common\models\TaskApi;

/**
 * CouponSendTaskSearch represents the model behind the search form about `backend\models\CouponSendTask`.
 */
class CouponSendTaskSearch extends CouponSendTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coupon_group_id', 'check_status', 'examine_time', 'user_num','coupon_num', 'send_time', 'create_time'], 'integer'],
            [['coupon_id_num_map'], 'string', 'max' => 255],
            [['task_name', 'examine_opinion', 'mobile_file_path', 'mobile_string', 'mobile_file_url', 'black_mobile_file_url', 'startTime', 'endTime', 'user_total_num', 'user_coupn_total_num'], 'safe'],
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
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $taskList       = TaskApi::couponSendTaskIndex($params);
        $dataProvider   = [];
        foreach ($taskList['sendTaskList'] as $key => $data) {
            $couponSendTask = new CouponSendTask();
            $couponSendTask->load(['CouponSendTask' => $data]);
            $dataProvider[$key] = $couponSendTask;
        }
      
        $couponSendTaskList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !$taskList['total'] ? 0 : $taskList['total'],
            'sort'       => [
                'attributes' => ['id desc'],
            ],
        ]);
        return $couponSendTaskList;
    }
}
