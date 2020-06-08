<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponSendTask */

$this->title                   = '搜索发券';
$this->params['breadcrumbs'][] = ['label' => '发券任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-send-task-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_search_form', [
    'model' => $model,
])?>

</div>
