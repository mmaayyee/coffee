<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ActivityApi;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LotteryWinningHintSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动提示语管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lottery-winning-hint-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?= Html::a('添加活动提示', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '成功提示语',
                'format'=>'text',
                'value' => function ($model){ return $model->hint_success_text;},
            ],
            [
                'label' => '失败提示语',
                'format'=>'text',
                'value' => function ($model){ return $model->hint_error_text;},
            ],
            [
                'label' => '活动类型',
                'format'=>'text',
                'value' => function ($model){ return ActivityApi::getActivityTypeList(2, 1)[$model->activity_type_id];},
            ],
            [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}  {delete}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('活动提示语信息编辑') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'delete' => function ($url, $model, $key) {
                    $options = [
                        'onclick' => 'return confirm("确定删除吗？");',
                    ];
                    return !\Yii::$app->user->can('活动提示语信息删除') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
        ],
    ]); ?>
</div>
