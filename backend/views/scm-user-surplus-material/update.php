<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmUserSurplusMaterial */

$this->title = 'Update Scm User Surplus Material: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Scm User Surplus Materials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scm-user-surplus-material-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
