<?php

namespace backend\models;

use backend\models\DeliveryOrder;
use common\models\ArrayDataProviderSelf;
use common\models\DeliveryApi;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryOrderSearch represents the model behind the search form of `common\models\DeliveryOrder`.
 */
class DeliveryOrderSearch extends DeliveryOrder
{
    //开始时间
    public $start_time;
    //结束时间
    public $end_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_order_id', 'order_id', 'user_id', 'address_id', 'build_id', 'delivery_order_status', 'fail_reason_id', 'delivery_person_id', 'expect_service_time', 'diachronic','sequence_number'], 'integer'],
            [['delivery_order_code', 'receiver', 'phone', 'address', 'start_time', 'end_time', 'delivery_order_status', 'nickname','delivery_person_id','delivery_cost','building_list', 'diachronic_list', 'product_num'], 'safe'],
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
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        //设置默认检索时间
        if (!isset($params['DeliveryOrderSearch'])){
            $params['DeliveryOrderSearch']['start_time'] = date('Y-m-d H:i:s', strtotime('today'));
            $params['DeliveryOrderSearch']['end_time'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime('+1 day')))-1);
            $params['DeliveryOrderSearch']['delivery_order_status'] = [
                self::ORDER_STATUS_WAIT_PICK,
                self::ORDER_STATUS_PICK,
                self::ORDER_STATUS_MAKE,
                self::ORDER_STATUS_DISTR,
            ];
        }

        $this->load($params);
        $getDeliveryOrderList = DeliveryApi::getDeliveryOrderList($params);

        $dataProvider = [];
        if ($getDeliveryOrderList) {
            foreach ($getDeliveryOrderList['list'] as $key => $data) {
                $deliveryOrder = new DeliveryOrder();
                $deliveryOrder->load(['DeliveryOrder' => $data]);
                $deliveryOrder->deliveryOrderLogs = $data['deliveryOrderLogs'];
                //加一个订单状态名称
                $deliveryOrder->delivery_order_status_name = self::getDeliveryOrderStatus($data['delivery_order_status']);
                $deliveryOrder->person =  $getDeliveryOrderList['personList'];
                $deliveryOrder->building_list = $getDeliveryOrderList['buildingList'];
                $dataProvider[$data['delivery_order_id']]  = $deliveryOrder;
            }
            $this->person = $getDeliveryOrderList['personList'];
            $this->building_list = $getDeliveryOrderList['buildingList'];
        }
        $list = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => isset($getDeliveryOrderList['total']) && !empty($getDeliveryOrderList['total']) ? $getDeliveryOrderList['total'] : 0,
            'sort'       => [
                'attributes' => ['delivery_order_id desc'],
            ],
        ]);
        return $list;
    }
}
