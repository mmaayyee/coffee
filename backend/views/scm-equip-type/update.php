<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title                   = '修改设备类型: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备类型管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="scm-equip-type-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
