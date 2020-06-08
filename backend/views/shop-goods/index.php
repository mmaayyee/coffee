<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '商品管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?=Yii::$app->session->getFlash('error');?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class' => 'yii\grid\SerialColumn',
        ],
        [
            'class'           => 'yii\grid\CheckboxColumn',
            'header'          => Html::checkBox('selection_all', false, [
                'class' => 'select-on-check-all',
                'label' => '全选',
            ]),
            'checkboxOptions' => function ($model) {
                return ["value" => $model['goods_id'], 'style' => 'width:50px;height:50px'];
            },
        ],

        [
            'label'     => '商品ID',
            'attribute' => 'goods_id',
        ],
        [
            'label'     => '商品名称',
            'attribute' => 'goods_name',
        ],
        [
            'label'  => '商品属性',
            'format' => 'html',
            'value'  => function ($model) {
                return $model->getGoodsAttr();
            },
        ],
        [
            'label'  => '图片',
            'format' => 'html',
            'value'  => function ($model) {
                $image = $model->image;
                if ($position = strpos($model->image, ',')) {
                    $image = substr($model->image, 0, $position);
                }
                return $model->image ? '<img src="' . Yii::$app->params['fcoffeeUrl'] . $image . '" alt="商品图片" width="80">' : "";
            },
        ],
        [
            'label'  => '状态',
            'format' => 'html',
            'value'  => function ($model) {
                return $model->status ? '<span class="status">' . $model->getStatus($model->status) . '</span>' : '';
            },
        ],
        [
            'label'     => '添加时间',
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return $model->create_time > 0 ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
    ],
]);?>

</div>