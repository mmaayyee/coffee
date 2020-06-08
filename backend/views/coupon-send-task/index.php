<?php

use yii\grid\GridView;
use yii\helpers\Html;
use backend\models\QuickSendCoupon;
use backend\models\CouponSendTask;

$this->title                   = '任务列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-send-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (Yii::$app->user->can('添加发券任务')){ ?>
    <p>
       <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '任务名称',
            'value' => function ($model) {
                return $model->task_name;
            },
        ],
        [
            'label' => '任务审核状态',
            'value' => function ($model) {
                return CouponSendTask::$taskStatus[$model->check_status];
            },
        ],
        [
            'label' => '所发优惠券',
            'value' => function ($model) {
                return $model->coupon_group_id ? '(套餐)'.$model->coupon_name : $model->coupon_name;
            },
        ],
        [
            'label' => '用户数量',
            'value' => function ($model) {
                return $model->user_num;
            },
        ],
        [
            'label' => '发送时间',
            'value' => function ($model) {
                return date("Y-m-d H:i", $model->send_time);
            },
        ],
        [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {sendtask-check}',
                'buttons' => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'view' =>function ($url, $model, $key) {                  
                            return !\Yii::$app->user->can('查看发券任务') ?  '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/coupon-send-task/view?id=' . $model->id);
                        },

                    'update' =>function ($url, $model, $key) {
                            //这里需要判断是否显示编辑
                            // wbq  2018-6-13
                            if($model->check_status == 0 || $model->check_status == 2)
                            {
                                return !\Yii::$app->user->can('编辑发券任务') ?  '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/coupon-send-task/update?id=' . $model->id);
                            }
                        },  
                    'sendtask-check' => function ($url, $model, $key) {
                            if ($model->check_status == 0) {
                                //return !\Yii::$app->user->can('审核发券任务') ? '' : Html::a('<span class="glyphicon glyphicon-ok"><input class="equip" type="hidden" value="' . $model->id . '"/></span>', '/coupon-send-task/audit-coupon-send-task-success?id='.$model->id);
                                return !\Yii::$app->user->can('查看发券任务') ?  '' : Html::a('<span class="glyphicon glyphicon-ok"></span>', '/coupon-send-task/view?id=' . $model->id);
                            }
                        },                  
                ],
            ],
    ],
]);?>

</div>
