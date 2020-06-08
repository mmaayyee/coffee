<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/18
 * Time: 下午3:45
 */
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '订单管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class'           => 'yii\grid\CheckboxColumn',
            'header'          => Html::checkBox('selection_all', false, [
                'class' => 'select-on-check-all',
                'label' => '全选',
            ]),
            'checkboxOptions' => function ($model) {
                return ["value" => $model['order_id'], 'style' => 'width:50px;height:50px'];
            },
        ],
        [
            'label'     => '订单ID',
            'attribute' => 'order_id',
        ],
        [
            'label'     => '订单号',
            'attribute' => 'order_code',
        ],
        [
            'label' => '快递号',
            'value' => function ($model) {
                return empty($model->express_code) ? '' : $model->express_code;
            },
        ],
        [
            'label' => '收货手机号',
            'value' => function ($model) {
                return !empty($model->phone) ? $model->phone : '';
            },
        ],
        [
            'label' => '注册手机号',
            'value' => function ($model) {
                return !empty($model->mobile) ? $model->mobile : '';
            },
        ],
        [
            'label'  => '商品信息',
            'format' => 'html',
            'value'  => function ($model) use ($orderGoodsList) {
                return $model->getOrderGoodsInfo($model->order_id, $orderGoodsList);
            },
        ],
        [
            'label' => '下单时间',
            'value' => function ($model) {
                return $model->create_time > 0 ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
        [
            'label'  => '状态',
            'format' => 'html',
            'value'  => function ($model) {
                return '<span class="status">' . $model->getOrderStatus($model->order_status) . '</span>';
            },
        ],
    ],
]);?>