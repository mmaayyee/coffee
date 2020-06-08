<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ServiceCount */

$this->title = 'Create Service Count';
$this->params['breadcrumbs'][] = ['label' => 'Service Counts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-count-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
