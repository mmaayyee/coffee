<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouse */

$this->title = '修改库信息: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '库信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="scm-warehouse-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
