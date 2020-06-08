<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightProgramAssoc */

$this->title = 'Create Light Program Assoc';
$this->params['breadcrumbs'][] = ['label' => 'Light Program Assocs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-program-assoc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
