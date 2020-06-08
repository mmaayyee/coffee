<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MaterielLog */

$this->title = 'Create Materiel Log';
$this->params['breadcrumbs'][] = ['label' => 'Materiel Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
