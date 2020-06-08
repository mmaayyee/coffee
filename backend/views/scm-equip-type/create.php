<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title                   = '添加设备类型';
$this->params['breadcrumbs'][] = ['label' => '设备类型管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-equip-type-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
