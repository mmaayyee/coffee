<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Activity;
use common\models\ActivityApi;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '营销游戏';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?= Html::a('添加营销游戏', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '活动名称',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->activity_name;
                },
            ],
            
            [
                'label' => '活动开始时间',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->start_time ? date("Y-m-d H:i", $model->start_time) : "";
                },
            ],
            [
                'label' => '活动结束时间',
                'format'=>'text',
                'value' => function ($model){
                    return $model->end_time ? date("Y-m-d H:i", $model->end_time) : "";
                },
            ],
            [
                'label' => '活动地址',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->activity_url ? $model->activity_url : "";
                },
            ],

            [
                'label' => '活动状态',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->status ? Activity::getStatus($model) : "";
                },
            ],
            // [
            //     'label' => '活动排序',
            //     'format'=>'text',
            //     'value' => function ($model){ 
            //         return $model->activity_sort ? $model->activity_sort : "";
            //     },
            // ],
            [
                'label' => '创建时间',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->created_at ? date("Y-m-d H:i", $model->created_at) : "";
                },
            ],
            
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{detail} {view} {copy} {update}  {delete}',
                'buttons'  => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'detail' => function ($url, $model, $key) {
                        return \Yii::$app->user->can('营销游戏查看') && $model->activity_type_id==Activity::LOTTERY_CLASS ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url) : '';
                    },
                    'view' => function ($url, $model, $key) {
                        return \Yii::$app->user->can('中奖信息查看') && $model->activity_type_id==Activity::LOTTERY_CLASS ? Html::a('<span class="">中奖信息查看</span>', $url) : '';
                    },

                    'copy'     => function ($url, $model, $key) {
                        return !Yii::$app->user->can('营销游戏复制') ? '' : Html::a('复制活动', '/activity/update?id=' . $model->activity_id . '&copy=1');
                    },

                    'update' => function ($url, $model, $key) {
                        return \Yii::$app->user->can('营销游戏编辑') && Activity::getIsDisplayUpdate($model) ?  Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'onclick' => 'return confirm("确定删除吗？");',
                        ];
                        return \Yii::$app->user->can('营销游戏删除') && Activity::getIsDisplayDelete($model)  ?  Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options) : '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
