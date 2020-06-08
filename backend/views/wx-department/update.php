<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WxDepartment */

$this->title = '更新部门: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '部门管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['index', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新部门';
?>
<div class="wx-department-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
