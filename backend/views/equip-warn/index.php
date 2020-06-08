<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\EquipWarn;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipWarnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '异常报警设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-warn-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->user->can('添加异常报警设置') ? Html::a('添加异常报警设置', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'warn_content', 'value'=>function($model){
                return EquipWarn::$warnContent[$model->warn_content];
            }],
            ['attribute' => 'userid', 'value'=>function($model){
                return EquipWarn::getPositionNameStr($model->userid);
            }],
            ['attribute' => 'notice_type', 'value'=>function($model){
                return $model->notice_type ? EquipWarn::reportType($model->notice_type) : '';
            }],
            [
                'attribute' => 'report_num',
                'value' => function($model) {
                    return $model->report_num ? EquipWarn::$reportNum[$model->report_num]: '';
                }
            ],
            [
                'attribute' => 'continuous_number',
                'value' => function($model) {
                    return $model->continuous_number ? EquipWarn::$continuousNumber[$model->continuous_number]: '';
                }
            ],
            [
                'attribute' => 'interval_time',
                'value' => function($model) {
                    return $model->interval_time ? EquipWarn::$intervalTime[$model->interval_time] : '';
                }
            ],
            [
                'attribute' => 'is_report',
                'value' => function($model) {
                    return $model->is_report == 1 ? '是' : '否';
                }
            ],
            [
                'attribute' => 'report_setting',
                'value' => function($model) {
                    return $model->report_setting ? EquipWarn::reportSetting($model->report_setting) : '';
                }
            ],
            [
                'attribute' => 'create_time',
                'value' => function($model) {
                    return $model->create_time ? date('Y-m-d H:i:s',$model->create_time) : '';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'update' => function($url) {
                        return Yii::$app->user->can('编辑异常报警设置') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']) : '';
                    },
                    'delete' => function($url) {
                        return Yii::$app->user->can('删除异常报警设置') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash', 'title' => '删除', 'onClick' => "return confirm('确定要删除吗?')"]) : '';
                    }
                ]
            ],
        ],
    ]); ?>

</div>
