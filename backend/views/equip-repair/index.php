<?php

use common\models\EquipTask;
use yii\grid\GridView;
use yii\helpers\Html;

if ($searchModel['type'] != 2) {
    $this->title                   = '报修记录';
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="equip-repair-index">
    <?php $equipId = isset($equipId) ? $equipId : '';?>

    <h1><?=Html::encode($this->title)?></h1>
    <?php if ($searchModel['type'] == 2) {echo $this->render('_repair_search', ['model' => $searchModel,'equip_id' => $equipId ,'type' => 2]);}?>

    <?php if ($searchModel['type'] != 2) {echo $this->render('_search', ['model' => $searchModel]);?>
    <p>
        <?=Yii::$app->user->can('上报新故障') ? Html::a('上报新故障', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?php } else {?>
    <p>
        <?php $eId = isset($_GET['EquipRepairSearch']['equip_id']) ? $_GET['EquipRepairSearch']['equip_id'] : $equipId;?>
        <?=Html::a('返回上一页', '/equipments/view?id=' .$eId , ['class' => 'btn btn-success'])?>
    </p>
    <?php }?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'build_name',
            'value'     => function ($model) {
                return $model->build_name;
            },
        ],
        [
            'attribute' => 'build_address',
            'value'     => function ($model) {
                return $model->build_address;
            },
        ],
        [
            'attribute' => 'content',
            'format'    => 'html',
            'value'     => function ($model) {
                return EquipTask::getMalfunctionContent($model->content, 1);
            },
        ],
        [

            'attribute'     => 'remark',
            'value'         => function ($model) {
                return $model->remark;
            },
            'headerOptions' => ['width' => '220'],
        ],
        [
            'attribute'     => 'create_time',
            'value'         => function ($model) {
                return $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
            'headerOptions' => ['width' => '120'],
        ],
        [
            'attribute'     => 'recive_time',
            'value'         => function ($model) {
                return $model->recive_time ? date('Y-m-d H:i:s', $model->recive_time) : '';
            },
            'headerOptions' => ['width' => '120'],
        ],
        [
            'label'         => '完成时间',
            'value'         => function ($model) {
                return $model->getSuccessTime();
            },
            'headerOptions' => ['width' => '120'],
        ],
        [
            'label' => '处理状态',
            'value' => function ($model) {
                return $model->getRepairStatus();
            },
        ],
        [
            'label' => '处理结果',
            'value' => function ($model) {
                return $model->getRepairResult();
            },
        ],
        'author',

    ],
]);?>

</div>
