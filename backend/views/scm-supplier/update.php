<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmSupplier */

$this->title = '更新供应商: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '供应商管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="scm-supplier-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
