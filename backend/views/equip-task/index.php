<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if (!$searchModel->type) {
    $this->title                   = '任务列表';
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="equip-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (!$searchModel->type) {?>
    <p>
        <?=Yii::$app->user->can('添加设备任务') ? Html::a('添加任务', ['create'], ['class' => 'btn btn-success']) : ''?>
        <?=Yii::$app->user->can('添加设备任务') ? Html::a('添加附件任务', ['create-extra-task'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?php }?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'build_id',
            'value'     => function ($model) {
                return \common\models\Building::getBuildingDetail('name', array('id' => $model->build_id))['name'];
            },
        ],
        [
            'label'     => '任务详情',
            'attribute' => 'content',
            'format'    => 'html',
            'value'     => function ($model) {
                return \common\models\EquipTask::getMalfunctionContent($model->content, $model->task_type);
            },
        ],
        'remark',
        [
            'attribute' => 'assign_userid',
            'value'     => function ($model) {
                return $model->assignMemberName ? $model->assignMemberName->name : '';
            },
        ],
        'create_user',
        [
            'attribute' => 'task_type',
            'value'     => function ($model) {
                return \common\models\EquipTask::$task_type[$model->task_type];
            },
        ],
        [
            'attribute' => 'recive_time',
            'label'     => '任务接收状态',
            'value'     => function ($model) {
                return $model->recive_time ? '已接收' : '未接收';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}{update}{delete}',
            'buttons'  => [
                'update' => function ($url, $model) {
                    return (!$model->start_repair_time && Yii::$app->user->can('编辑设备任务')) ? Html::a('<span ></span>', $url, ['class ' => "glyphicon glyphicon-pencil", 'title' => '编辑']) : '';
                },
                'delete' => function ($url, $model) {
                    return (!$model->start_repair_time && Yii::$app->user->can('删除设备任务')) ? Html::a('', $url, ['onclick' => 'return confirm("确定删除吗？");', 'class' => 'glyphicon glyphicon-trash', 'title' => '删除']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
