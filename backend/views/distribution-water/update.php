<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWater */

$this->title = '修改水单: ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => '水单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="distribution-water-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
