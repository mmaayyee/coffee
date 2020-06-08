<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title                   = '楼宇列表';
$this->params['breadcrumbs'][] = ['label' => '任务详情', 'url' => ['/coupon-send-task/view?id=' . $id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-send-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php $form = ActiveForm::begin(['action' => '/coupon-send-task/add-build-list', 'method' => 'get']);?>
    <div class="form-inline">
        <div class="form-group">
            <label>楼宇名称</label>
            <?=Html::textInput('name', $name, ['class' => 'form-control'])?>
            <?=Html::hiddenInput('id', $id)?>
        </div>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '编号',
            'value' => function ($model) {
                return $model->id;
            },
        ],
        [
            'label' => '楼宇名称',
            'value' => function ($model) {
                return $model->name;
            },
        ],

    ],
]);?>

</div>
