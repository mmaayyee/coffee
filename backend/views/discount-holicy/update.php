<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DiscountHolicy */

$this->title = '修改';
$this->params['breadcrumbs'][] = ['label' => '优惠策略管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="discount-holicy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
