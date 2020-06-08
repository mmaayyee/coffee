<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AppVersionManagement */

$this->title = '修改App版本号';
$this->params['breadcrumbs'][] = ['label' => 'App版本号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="app-version-management-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
