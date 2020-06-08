<?php

use backend\models\EquipAbnormalTask;
use backend\models\EquipWarn;
use backend\models\Organization;
use common\models\Building;
use common\models\EquipTask;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipAbnormalTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备故障任务列表';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="equip-abnormal-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'     => '设备编号',
            'attribute' => 'equip_code',
            'value'     => function ($model) {
                return $model->equip_code;
            },
        ],
        [
            'label'     => '所属楼宇',
            'attribute' => 'build_id',
            'value'     => function ($model) {
                $name = Building::getBuildingName($model->build_id);
                return $name['name'];
            },
        ],
        [
            'label'     => '分公司ID',
            'attribute' => 'org_id',
            'value'     => function ($model) {
                $name = Organization::getOrganizationName($model->org_id);
                return $name['org_name'];
            },
        ],
        [
            'attribute' => 'create_time',
            'label'     => '任务添加时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->create_time);
            },
        ],
        [
            'attribute' => 'abnormal_id',
            'label'     => '异常报警',
            'format'    => 'html',
            'value'     => function ($model) {
                $abnormal_id = Json::decode($model->abnormal_id);
                $abnormals   = '';
                if (!empty($abnormal_id)) {
                    foreach ($abnormal_id as $abnormal) {
                        $abnormals .= EquipWarn::$warnContent[$abnormal] . "<br/>";
                    }
                }
                return !empty($model->abnormal_id) ? $abnormals : '暂无';
            },
        ],
        [
            'attribute' => 'repair',
            'label'     => '客服上报',
            'format'    => 'html',
            'value'     => function ($model) {
                if (!empty($model->repair)) {
                    $repair = implode(',', Json::decode($model->repair));
                }

                return !empty($model->repair) ? EquipTask::getMalfunctionContent($repair, 1) : '暂无';
            },
        ],
        [
            'attribute' => 'task_status',
            'label'     => '任务状态',
            'value'     => function ($model) {
                if ($model->task_status == EquipAbnormalTask::Untreated) {
                    $status = '未处理';
                } elseif ($model->task_status == EquipAbnormalTask::LowerHair) {
                    $status = '已下发';
                } elseif ($model->task_status == EquipAbnormalTask::NEXTDAY) {
                    $status = '已转次日';
                } elseif ($model->task_status == EquipAbnormalTask::COMPLETE) {
                    $status = '已完成';
                }
                return $status;
            },
        ],
//            [
        //                'attribute' => 'type',
        //                'label'     => '生成方式',
        //                'value'     => function ($model) {
        //                    return $model->type == 1?'异常报警':'客服上报';
        //                },
        //            ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{delete}{assign}{next}',
            'buttons'  => [

                'delete' => function ($url, $model) {
                    if ($model->task_status == EquipAbnormalTask::Untreated) {
                        return Yii::$app->user->can('删除故障任务记录') ? Html::a('', 'delete?task_id=' . $model->task_id, ['onclick' => 'return confirm("确定删除吗？");', 'class' => 'glyphicon glyphicon-trash', 'style' => 'margin-right:2px', 'title' => '删除']) : '';
                    }
                },
                'assign' => function ($url, $model) {
                    if ($model->task_status == EquipAbnormalTask::Untreated) {
                        return Yii::$app->user->can('下发故障任务记录') ? Html::a('', '/distribution-temporary-task/assign?task_id=' . $model->task_id, ['onclick' => 'return confirm("确定下发吗？");', 'class' => 'glyphicon glyphicon-send', 'style' => 'margin-right:2px', 'title' => '下发']) : '';
                    }
                },
                'next'   => function ($url, $model) {
                    if ($model->task_status == EquipAbnormalTask::Untreated) {
                        return Yii::$app->user->can('故障任务记录转次日') ? Html::a('', 'next?task_id=' . $model->task_id, ['onclick' => 'return confirm("确定转到明天吗？");', 'class' => 'glyphicon glyphicon-hourglass', 'style' => 'margin-right:2px', 'title' => '转次日']) : '';
                    }
                },
            ],
        ],
    ],
]);?>
</div>
