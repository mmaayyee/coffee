<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\OutStatistics */

$this->title = 'Create Out Statistics';
$this->params['breadcrumbs'][] = ['label' => 'Out Statistics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="out-statistics-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
