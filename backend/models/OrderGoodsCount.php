<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "order_goods_count".
 *
 * @property int $id
 * @property int $today_pay_total 每天购买量
 * @property int $today_consume_total 每天消费量
 * @property int $no_consume_total 总未消费量
 * @property int $created_at 时间今天存昨天的数据
 */
class OrderGoodsCount extends \yii\db\ActiveRecord
{
    public $id;
    public $today_pay_total;
    public $today_consume_total;
    public $no_consume_total;
    public $created_at;
    public $createdFrom;
    public $createdTo;
    public $today_refund_total;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods_count';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['today_pay_total', 'today_consume_total', 'no_consume_total', 'created_at','today_refund_total'], 'integer'],
            [['id','createdFrom','createdTo'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'today_pay_total' => '今天购买量',
            'today_consume_total' => '今天消费量',
            'today_refund_total' => '今天退款量',
            'no_consume_total' => '总未制作量',
            'created_at' => '日期',
            'createdFrom' => '开始时间',
            'createdTo' => '结束时间',
        ];
    }

    public static function getOrderGoodsCountList($params)
    {
        $page          = isset($params['page']) ? $params['page'] : 0;
        $OrderGoodsCountList = self::postBase("order-goods-count-api/get-order-goods-count-list", $params, '?page=' . $page);
        return !$OrderGoodsCountList ? [] : Json::decode($OrderGoodsCountList);
    }
    public static function postBase($action, $data = [], $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }
    public static function getBase($action, $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
