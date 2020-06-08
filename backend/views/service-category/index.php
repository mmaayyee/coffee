<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ServiceCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '类别管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (Yii::$app->user->can('添加类别')) {?>
        <p>
            <?= Html::a('添加类别', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php }?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
             [
                'label' => '类别名称',
                'attribute' => 'category',
            ],
             [
                'label' => '状态',
                'value' => function($model){
                    return $model->status ? $model->getStatus($model->status) : '';
                }
            ],
            [
                'label' => '添加时间',
                'value' => function($model){
                    return date('Y-m-d',$model->created_time);
                }
            ],
           [
                'class' => 'yii\grid\ActionColumn',
                'header'   => '操作',
                'template' => '{update} ',
                 'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('修改类别') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                }
           ],
           ]
           
        ],
    ]); ?>
</div>
