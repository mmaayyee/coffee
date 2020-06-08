<?php

namespace backend\modules\service\models;

use backend\modules\service\models\Complaint;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerServiceComplaintSearch represents the model behind the search form of `backend\models\CustomerServiceComplaint`.
 */
class ComplaintSearch extends Complaint
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['complaint_id', 'manager_id', 'user_consume_id', 'org_id', 'advisory_type_id', 'question_type_id', 'building_id', 'user_id', 'pay_type', 'buy_time', 'solution_id', 'latest_refund_time', 'real_refund_time', 'process_status', 'is_consumption', 'add_time', 'update_time'], 'integer'],
            [['manager_name', 'org_city', 'question_describe', 'building_name', 'equipment_last_log', 'equipment_type', 'customer_name', 'register_mobile', 'callin_mobile', 'nikename', 'buy_type', 'retired_coffee_type', 'order_code', 'complaint_code', 'end_time', 'begin_time', 'customer_type'], 'safe'],
            [['order_refund_price'], 'number'],
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
        $customerComplaintList = Complaint::getComplaint($params);
        $dataProvider          = [];
        if (isset($customerComplaintList['customerComplaint'])) {
            foreach ($customerComplaintList['customerComplaint'] as $key => $customerComplaint) {
                $proGroup = new Complaint();
                $proGroup->load(['Complaint' => $customerComplaint]);
                $dataProvider[$customerComplaint['complaint_id']] = $proGroup;
            }
        }
        $customerComplaintList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($customerComplaintList['total']) ? 0 : $customerComplaintList['total'],
        ]);
        return $customerComplaintList;
    }
}
