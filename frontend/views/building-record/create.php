<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BuildingRecord */

$this->title = 'Create Building Record';
$this->params['breadcrumbs'][] = ['label' => 'Building Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
