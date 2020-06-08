<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmUserSurplusMaterialSureRecord */

$this->title = 'Update Scm User Surplus Material Sure Record: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Scm User Surplus Material Sure Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scm-user-surplus-material-sure-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
