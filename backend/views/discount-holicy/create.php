<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DiscountHolicy */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '优惠策略管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-holicy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
