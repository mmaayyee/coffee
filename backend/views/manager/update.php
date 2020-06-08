<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Manager */

$this->title = '编辑管理员: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '管理员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="manager-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
