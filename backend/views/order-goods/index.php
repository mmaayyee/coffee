<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '订单产品';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="order-goods-index">
    <h1><?=Html::encode($this->title)?></h1>
<?php echo $this->render('_search', ['model' => $searchModel, 'productList' => $productList, 'groupList' => $groupList, 'sourceID' => $sourceID, 'sourceType' => $sourceType, 'productActiveList' => $productActiveList, 'groupActiveList' => $groupActiveList]); ?>
    <div>
<span>单品数量：<?php echo $totalNumber; ?></span> <span>单品金额：<?php echo $totalFee; ?> </span>
    </div
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'attribute' => '订单ID',
            'format'    => 'raw',
            'value'     => function ($model) {return "<a href='" . Url::to(['order-info/view', 'id' => $model->order_id]) . "'>" . $model->order_id . "</a>";},
        ],
        [
            'label'  => '手机号',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->userMobile) ? $model->userMobile : '';
            },
        ],
        [
            'label'  => '产品名称',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->source_name) ? $model->source_name : '';
            },
        ],
        [
            'label'  => '产品价格',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->source_price) ? $model->source_price : '';
            },
        ],
        [
            'label'  => '产品数量',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->source_number) ? $model->source_number : '';
            },
        ],
        [
            'attribute' => '产品状态',
            'format'    => 'text',
            'value'     => function ($model) {return $model->source_status;},
        ],
        [
            'attribute' => '产品类型',
            'format'    => 'text',
            'value'     => function ($model) {return $model->source_type;},
        ],
        [
            'attribute' => '购买时间',
            'format'    => 'text',
            'value'     => function ($model) {return $model->created_at;},
        ],
        [
            'attribute' => '获取方式',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->goods_type;
            },
        ],
    ],
]);?>

</div>
