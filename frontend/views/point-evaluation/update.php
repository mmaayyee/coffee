<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PointEvaluation */

$this->title = 'Update Point Evaluation: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Point Evaluations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="point-evaluation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
