<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuickSendCoupon */

$this->title                   = '添加';
$this->params['breadcrumbs'][] = ['label' => '快速发券管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quick-send-coupon-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'           => $model,
    'couponList'      => $couponList,
    'couponGroupList' => $couponGroupList,
])?>

</div>
