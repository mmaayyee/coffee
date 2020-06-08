<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildType */

$this->title = '编辑楼宇类型: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '楼宇类型', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="build-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
