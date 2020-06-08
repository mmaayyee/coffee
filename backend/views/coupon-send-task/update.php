<?php

use backend\models\CouponSendTask;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponSendTask */

$this->title                   = '编辑任务: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑任务';
$form                          = $model->send_type == CouponSendTask::SEND_SEARCH ? '_search_form' : '_import_form';
?>
<div class="coupon-send-task-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render($form, [
    'model' => $model,
])?>

</div>

