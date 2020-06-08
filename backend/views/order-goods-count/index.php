<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderGoodsCountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '咖啡汇总';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-goods-count-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '本日购入数量',
                'value' => function($model){
                    return $model->today_pay_total;
                }
            ],
            [
                'label' => '本日退款数量',
                'value' => function($model){
                    return $model->today_refund_total;
                }
            ],
            [
                'label' => '本日消费数量',
                'value' => function($model){
                    return $model->today_consume_total;
                }
            ],
            [
                'label' => '总未消费数量',
                'value' => function($model){
                    return $model->no_consume_total;
                }
            ],
            [
                'label' => '日期',
                'value' => function($model){
                    return $model->created_at;
                }
            ],
        ],
    ]); ?>
</div>
