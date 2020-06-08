<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielLog */

$this->title = 'Update Materiel Log: ' . $model->materiel_log_id;
$this->params['breadcrumbs'][] = ['label' => 'Materiel Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->materiel_log_id, 'url' => ['view', 'id' => $model->materiel_log_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="materiel-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
