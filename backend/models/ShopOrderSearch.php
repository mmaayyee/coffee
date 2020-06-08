<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 18/2/2
 * Time: 上午10:44
 */
namespace backend\models;

use common\helpers\Tools;
use common\models\ArrayDataProviderSelf;
use yii\base\Model;

class ShopOrderSearch extends ShopOrder
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['order_id', 'order_code', 'user_id', 'total_fee', 'create_time', 'express_code', 'order_status', 'is_disabled', 'phone', 'mobile', 'begin_time', 'end_time'], 'safe'],
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
        $orderList      = ShopOrder::getOrderListByParam($params);
        $orderGoodsList = $orderList['orderGoodsList'];
        if (!empty($params['export'])) {
            $shopOrder = new ShopOrder();
            $dataList  = [];
            foreach ($orderList['shopOrderList'] as $order) {
                $dataList[] = [
                    $order['order_id'],
                    $order['order_code'],
                    $order['express_code'] ?? '',
                    $order['phone'] ?? '',
                    $order['mobile'] ?? '',
                    $shopOrder->getOrderGoodsInfo($order['order_id'], $orderGoodsList, 1),
                    $order['create_time'] > 0 ? date('Y-m-d H:i:s', $order['create_time']) : '',
                    $shopOrder->getOrderStatus($order['order_status']),
                ];
            }
            $title  = '周边商城订单';
            $header = ['订单ID', '订单编号', '快递号', '收货手机号', '注册手机号', '商品信息', '下单时间', '状态'];
            Tools::exportData($title, $header, $dataList);die;
        }
        $dataProvider = [];
        if (isset($orderList['shopOrderList'])) {
            foreach ($orderList['shopOrderList'] as $key => $data) {
                $orderStore = new ShopOrder();
                $orderStore->load(['ShopOrder' => $data]);
                $dataProvider[$data['order_id']] = $orderStore;
            }
        }
        $orderList = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => !isset($orderList['total']) ? 0 : $orderList['total'],
            'sort'       => [
                'attributes' => ['order_id desc'],
            ],
        ]);
        return [$orderList, $orderGoodsList];
    }
}
