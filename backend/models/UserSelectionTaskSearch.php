<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
// use yii\data\ArrayDataProviderSelf;
use common\models\ArrayDataProviderSelf;
use backend\models\UserSelectionTask;
use common\models\TaskApi;

/**
 * UserSelectionTaskSearch represents the model behind the search form of `backend\models\UserSelectionTask`.
 */
class UserSelectionTaskSearch extends UserSelectionTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['selection_task_id', 'selection_task_status', 'selection_task_result', 'mobile_num', 'reference_task_id', 'create_time'], 'integer'],
            [['selection_task_name', 'logic_relation', 'start_time', 'end_time', 'single_query_where', 'mobile_file_path', 'validate_mobile'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $params['page']     = isset($params['page']) ? $params['page'] : 0;
        $selectionTaskList  = TaskApi::getUserSelectionTaskList($params);
        $dataProvider       = [];
        if ($selectionTaskList) {
            foreach ($selectionTaskList['selectionTaskList'] as $key => $data) {
                $quickSendCoupon = new UserSelectionTask();
                $quickSendCoupon->load(['UserSelectionTask' => $data]);
                $dataProvider[$key] = $quickSendCoupon;
            }
        }
        $selectionTaskList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($selectionTaskList['total']) && !empty($selectionTaskList['total']) ? $selectionTaskList['total'] : 0,
            'sort'       => [
                'attributes' => ['selection_task_id desc'],
            ],
        ]);
        return $selectionTaskList;
    }
}
