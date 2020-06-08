<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 10:32
 */
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\TemporaryAuthorization;

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '申请临时开门记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-view">
    <h1><?= Html::encode($this->title); ?></h1>
    <?=DetailView::widget([
        'model' => $model,
        'attributes' => [
            'build_name',
            'wx_member_name',
            [
                'attribute' => 'application_time',
                'value' => $model->application_time ? date("Y-m-d H:i:s",$model->application_time) : '' ,
            ],
            [
                'attribute' => 'audit_time',
                'value' => $model->audit_time ? date("Y-m-d H:i:s",$model->audit_time) : '',
            ],
            [
                'attribute' => 'state',
                'value' => $model->getStatusName(),
            ],
        ],
    ]); ?>
</div>

