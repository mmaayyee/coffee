<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PointEvaluation */

$this->title = 'Create Point Evaluation';
$this->params['breadcrumbs'][] = ['label' => 'Point Evaluations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-evaluation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
