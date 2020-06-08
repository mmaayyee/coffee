<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EstimateStatistics */

$this->title = '运维预估单配货';
$this->params['breadcrumbs'][] = ['label' => '运维预估单', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['index']];
$this->params['breadcrumbs'][] = '配货';
?>
<div class="estimate-statistics-distribution">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_distribution_form', [
        'model' => $model,
    ]) ?>

</div>
